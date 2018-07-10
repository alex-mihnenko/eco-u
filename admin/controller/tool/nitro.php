<?php 
class ControllerToolNitro extends Controller { 
    private $error = array();
    private $session_closed = false;
    private $start_time;
    private $smush_progress_message_limit = 10;

    public function __construct($registry) {
        parent::__construct($registry);

        require_once(DIR_SYSTEM . 'nitro/core/core.php');

        $this->start_time = time();
    }

    public function index() {
        $this->language->load('tool/nitro');

        $this->load->model('tool/nitro');

        $this->load->model('tool/nitro_upgrade');

        $this->model_tool_nitro_upgrade->run_upgrade();

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('view/javascript/nitro/justgage/resources/js/raphael.2.1.0.min.js');
        $this->document->addScript('view/javascript/nitro/justgage/resources/js/justgage.1.0.1.js');
        $this->document->addScript('view/javascript/nitro/nitro.js');
        $this->document->addScript('view/javascript/nitro/nitro.cachemanager.js');
        $this->document->addScript('view/javascript/nitro/nitro.precache.js');
        $this->document->addScript('view/javascript/nitro/nitro.smusher.js');
        $this->document->addScript('view/javascript/nitro/nitro.pagespeed.js');
        $this->document->addScript('view/javascript/nitro/nitro.preminify.js');
        $this->document->addScript('view/javascript/nitro/nitro.cdn.js');
        $this->document->addStyle('view/stylesheet/nitro.css');
        $this->document->addStyle('view/javascript/nitro/font-awesome/css/font-awesome.min.css');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'tool/nitro') == false) {
            $this->session->data['error'] = $this->language->get('text_nopermission');
            $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));				
        }

        $persistence = $this->model_tool_nitro->getPersistence();

        $data['widget']['pagespeed'] = null;
        $data['pagespeedStoreUrl'] = (defined('HTTP_SERVER') && HTTP_SERVER != '') ? dirname(HTTP_SERVER) . '/' : false;
        $data['pagespeedSaveUrl'] = preg_replace("/^https?\:\/\//", "//", html_entity_decode($this->url->link("tool/nitro/savePagespeedResults", "token=" . $this->session->data["token"])));
        $data['pagespeedApiKey'] = $persistence['Nitro']['GooglePageSpeedApiKey'];
        $data['widget']['pagespeed'] = $this->model_tool_nitro->getGooglePageSpeedReport(null, array('mobile', 'desktop'));

        $this->performGetHandlers();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'tool/nitro')) {
            $this->session->data['success'] = '';

            if (empty($this->request->post['Nitro']['Enabled']) || $this->request->post['Nitro']['Enabled'] == 'no') {
                unset($this->request->post['NitroTemp']);
            } else {
                //Check if we are turning NitroPack on after it has been disabled. In such case we want to enable all NitroPack modules
                if (empty($persistence['Nitro']['Enabled']) || $persistence['Nitro']['Enabled'] == 'no') {
                    $this->request->post['NitroTemp']['ActiveModule'] = array(
                        'pagecache' => 'on',
                        'cdn_generic' => 'on',
                        'db_cache' => 'on',
                        'image_cache' => 'on',
                        'jquery' => 'on',
                        'minifier' => 'on',
                        'product_count_fix' => 'on',
                        'system_cache' => 'on',
                        'pagecache_widget' => 'on'
                    );
                }
            }

            $this->model_tool_nitro->setNitroPackModules($this->request->post);

            $this->model_tool_nitro->tryChmod();

            if (!empty($this->request->post['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post['Nitro']['LicensedOn'] = $this->request->post['OaXRyb1BhY2sgLSBDb21'];
            }

            if (!empty($this->request->post['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post['Nitro']['License'] = json_decode(base64_decode($this->request->post['cHRpbWl6YXRpb24ef4fe']),true);
            }

            if ($this->model_tool_nitro->setPersistence($this->request->post)) {
                $this->session->data['success'] .= $this->language->get('text_success');

                $this->model_tool_nitro->applyNitroCacheHTCompressionRules();
                $this->model_tool_nitro->applyNitroCacheHTRules();
                $this->model_tool_nitro->applyNitroCacheHTCookieRules();
                $this->model_tool_nitro->applyNitroCacheHTCdnRules();

                $this->clearpagecache();
            }
        }

        if(
            empty($this->session->data['error']) && 
            (
                (
                    file_exists(DIR_SYSTEM.'nitro/data/googlepagespeed-desktop.tpl') && 
                    !is_writable(DIR_SYSTEM.'nitro/data/googlepagespeed-desktop.tpl')
                ) || (
                    file_exists(DIR_SYSTEM.'nitro/data/googlepagespeed-mobile.tpl') && 
                    !is_writable(DIR_SYSTEM.'nitro/data/googlepagespeed-mobile.tpl')
                )
            )
        ) {
            $this->session->data['error'] = 'Your PHP user does not have permissions to write to files in <strong>system/nitro/data/</strong> Please enable write permissions for thie folder.';	
            $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['data'] = $persistence;
        $data['activeModules'] = $this->model_tool_nitro->getActiveNitroModules();

        $cannotUseMinify = (bool)(phpversion() < '5.3');

        $data['cannotUseMinify'] = $cannotUseMinify;

        if ($cannotUseMinify) {
            $persistence_temp = $persistence;
            $persistence_temp['Nitro']['Mini']['JS'] = 'no';
            $this->model_tool_nitro->setPersistence($persistence_temp);
            $data['data'] = $persistence_temp;
        }

        $cannotUsePageCache = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "store LIMIT 0,1")->num_rows == 0 && inMaintenanceMode();

        $data['cannotUsePageCache'] = $cannotUsePageCache;

        if ($cannotUsePageCache) {
            $persistence_temp = $persistence;
            $persistence_temp['Nitro']['PageCache']['Enabled'] = 'no';
            $this->model_tool_nitro->setPersistence($persistence_temp);
            $data['data'] = $persistence_temp;
        }

        if ($this->user->hasPermission('modify', 'tool/nitro') == false) {
            $data['data']['Nitro']['CDNStandardFTP']['Host'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNStandardFTP']['Port'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNStandardFTP']['Username'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNStandardFTP']['Password'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNStandardFTP']['Root'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNAmazon']['AccessKeyID'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNAmazon']['SecretAccessKey'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNRackspace']['Username'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
            $data['data']['Nitro']['CDNRackspace']['APIKey'] = '&bull;&bull;&bull;&bull;&bull;&bull;';
        }

        $data['cron_error'] = $this->model_tool_nitro->configureCron(!empty($persistence['Nitro']['CRON']) ? $persistence['Nitro']['CRON'] : array());

        $data['cron_command'] = $this->model_tool_nitro->cron_command(!empty($persistence['Nitro']['CRON']) ? $persistence['Nitro']['CRON'] : array(), false);

        $data['heading_title'] = $this->language->get('heading_title');

        $data['cron_token_url'] = substr(HTTP_SERVER, 0, strripos(HTTP_SERVER, '/', -2) + 1) . 'index.php?route=tool/nitro/cron&cron_token={CRON_TOKEN}';

        $data['admin_email'] = $this->config->get('config_email');

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = is_array($this->session->data['success']) ? '<strong>Success</strong><br/><br/>' . implode('<br/>', $this->session->data['success']) : $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/nitro.tpl', $data));
    }

    private function quickClearPageCache() {
        truncateNitroProductCache();

        $filename = getQuickCacheRefreshFilename();
        return touch($filename);
    }

    public function performGetHandlers() {
        $this->language->load('tool/nitro');
        if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['nitroaction'])) {
            $this->load->model('tool/nitro');
            switch($this->request->get['nitroaction']) {
            case 'refreshgps': 
                if ($this->user->hasPermission('modify', 'tool/nitro') == false) {
                    $this->session->data['error'] = $this->language->get('text_nopermission');
                    $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));				
                }
                $this->session->data['success'] = $this->model_tool_nitro->refreshGooglePageSpeedReport();				
                $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
                break;
            }
        }
    }

    public function savePagespeedResults() {
        if (!$this->request->server['REQUEST_METHOD'] == 'POST' || !$this->user->hasPermission('modify', 'tool/nitro')) {
            $this->session->data['error'] = $this->language->get('text_nopermission');
            $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));				
        }

        $data = $this->request->post['data'];
        $strategy = $this->request->post['strategy'];

        $this->load->model("tool/nitro");
        $this->model_tool_nitro->loadCore();
        setGooglePageSpeedReport(json_encode($data), $strategy);
    }

    public function getactivemodules() {
        $this->load->model('tool/nitro');
        if ($this->user->hasPermission('modify', 'tool/nitro') != false) {
            $modules = $this->model_tool_nitro->getActiveNitroModules();
            if ($modules) {
                echo json_encode($modules);
            }
        }
        exit;
    }

    public function googlerawrefresh() {
        $this->load->model('tool/nitro');

        if ($this->user->hasPermission('modify', 'tool/nitro') != false) {
            $data = $this->model_tool_nitro->getGoogleRawData();
            echo "======== Desktop ========\n";
            echo $data['desktop']."\n";
            echo "======== Mobile ========\n";
            echo $data['mobile']."\n";
            exit;
        } else {
            echo 'You do not have permissions to view this data.';
        }
    }

    public function serverinfo() {
        $this->load->model('tool/nitro');

        echo json_encode($this->model_tool_nitro->getServerInfo($this->user->hasPermission('modify', 'tool/nitro')));
    }

    public function performvalidation() {
        $this->load->model('tool/nitro');
        define('MID', 12658);

        $lcode = (!empty($_POST['l'])) ? $_POST['l'] : '';
        $hostname = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '' ;
        $hostname = (strstr($hostname,'http://') === false) ? 'http://'.$hostname: $hostname;
        $context = stream_context_create(array('http' => array('header' => 'Referer: '.$hostname)));
        $license = json_decode(file_get_contents('http://isenselabs.com/licenses/checklicense/'.base64_encode($lcode), false, $context),true);
        // check error 
        if (!empty($license['error'])) {
            echo '<div class="alert alert-danger">'.$license['error'].'</div>';
            return false;
        }
        // check product match
        if ($license['productId'] != MID) {
            echo '<div class="alert alert-danger">Incorrect code - you cannot use license code from another product!</div>';
            return false;			
        }
        // check expire date
        if (strtotime($license['licenseExpireDate']) < time()) {
            echo '<div class="alert alert-danger">Your license has expired on '.$license['licenseExpireDate'].'</div>';
            return false;			
        }

        //checkdomains 
        $domainPresent = false;
        foreach ($license['licenseDomainsUsed'] as $domain) {
            if (strstr($hostname,$domain) !== false) {
                $domainPresent = true;	
            }
        }
        if ($domainPresent == false) {
            echo '<div class="alert alert-danger">Unable to activate license for domain '.$domain.' - Please add your domain to your product license.</div>';
            return false;			
        }

        //success, activate the license
        $nitro = $this->model_tool_nitro->getPersistence(null,true);
        $nitro['Nitro']['LicensedOn'] = time();
        $nitro['Nitro']['License'] = $license;
        $this->model_tool_nitro->setPersistence($nitro,true);
        echo '<div class="alert alert-success">Licensing successful for domain '.$domain.' - please wait... </div><script> setTimeout(function() { document.location = document.location; } , 1000);  </script>';
    }

    private function session_success_to_array() {
        if (isset($this->session->data['success']) && is_array($this->session->data['success'])) return;

        $prev_val = !empty($this->session->data['success']) ? $this->session->data['success'] : '';
        $this->session->data['success'] = array();

        if ($prev_val) $this->session->data['success'][] = $prev_val;
    }

    public function clearimagecache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearImageCache() == true) {
            $this->session->data['success'] = 'The Image Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearimagecacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearImageCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Image Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearsystemcache() {
        $this->load->model('tool/nitro');

        if ($this->model_tool_nitro->clearSystemCache() == true) {
            $this->session->data['success'] = 'The OpenCart System Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearsystemcacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearSystemCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The OpenCart System Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearpagecache() {
        $this->load->model('tool/nitro');
        // if ($this->model_tool_nitro->clearPageCache() == true && $this->model_tool_nitro->clearHeadersCache() == true) {
        if ($this->quickClearPageCache()) {
            if (empty($this->session->data['success'])) {
                $this->session->data['success'] = '';	
            }
            $this->session->data['success'] .= 'The Nitro PageCache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearpagecacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearPageCache(true) == true && $this->model_tool_nitro->clearHeadersCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro PageCache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function cleardbcache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearDBCache() == true) {
            $this->session->data['success'] = 'The Nitro DB Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function cleardbcacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearDBCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro DB Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearjscache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearJSCache() == true && $this->model_tool_nitro->clearTempJSCache() == true) {
            $this->session->data['success'] = 'The Nitro JS Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearjscacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearJSCache(true) == true && $this->model_tool_nitro->clearTempJSCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro JS Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearcsscache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearCSSCache() == true && $this->model_tool_nitro->clearTempCSSCache() == true) {
            $this->session->data['success'] = 'The Nitro CSS Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearcsscacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearCSSCache(true) == true && $this->model_tool_nitro->clearTempCSSCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro CSS Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearvqmodcache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearVqmodCache() == true) {
            $this->session->data['success'] = 'The vQmod Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearvqmodcacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearVqmodCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The vQmod Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearnitrocaches() {
        $this->load->model('tool/nitro');

        $result = 
            $this->model_tool_nitro->clearPageCache() &&
            $this->model_tool_nitro->clearHeadersCache() &&
            $this->model_tool_nitro->clearDBCache() &&
            $this->model_tool_nitro->clearCSSCache() &&
            $this->model_tool_nitro->clearTempCSSCache() &&
            $this->model_tool_nitro->clearJSCache() &&
            $this->model_tool_nitro->clearTempJSCache();

        if ($result) {
            $this->session->data['success'] = 'All NitroPack-generated caches have been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearallcaches() {
        $this->load->model('tool/nitro');

        $result = 
            $this->model_tool_nitro->clearPageCache() &&
            $this->model_tool_nitro->clearHeadersCache() &&
            $this->model_tool_nitro->clearDBCache() &&
            $this->model_tool_nitro->clearImageCache() &&
            $this->model_tool_nitro->clearCSSCache() &&
            $this->model_tool_nitro->clearJSCache() &&
            $this->model_tool_nitro->clearTempJSCache() &&
            $this->model_tool_nitro->clearTempCSSCache() &&
            $this->model_tool_nitro->clearSystemCache() &&
            $this->model_tool_nitro->clearVqmodCache();

        if ($result) {
            $this->session->data['success'] = 'All caches have been cleared successfully!';
        }

        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    private function smush_can_proceed($progress) {
        $lock_filename = NITRO_DATA_FOLDER . 'smush.lock';
        if (file_exists($lock_filename)) return false;

        if (time() - $this->start_time > 20) return false;

        return !$progress->abortCalled();
    }

    public function smush_read_dir($dir, $fh, &$total_images) {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!file_exists($dir)) throw new RuntimeException("<b>Error:</b> Directory/file " . $dir . " could not be found");

        if (is_dir($dir)) {
            $dh = opendir($dir);
            while ( false !== ($entry = readdir($dh)) ) {
                if (in_array($entry, array('.', '..'))) continue;

                $full_path = $dir . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($full_path)) {
                    $this->smush_read_dir($full_path, $fh, $total_images);
                } else {
                    $this->addSmushItemImage($fh, $total_images, $full_path);
                }
            }
            fflush($fh);
            closedir($dh);
        } else {
            $this->addSmushItemImage($fh, $total_images, $dir);
        }
    }

    private function addSmushItemImage($fh, &$total_images, $item) {
        if (preg_match('/\.(jpe?g|png|gif)$/i', $item)) {
            fwrite($fh, $item."\n");
            $total_images++;
        }
    }

    public function smush_init() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $list_filename = NITRO_DATA_FOLDER . 'smush_files.flist';
        $lock_filename = NITRO_DATA_FOLDER . 'smush.lock';
        $total_images = 0;

        $fh = fopen($list_filename, 'w');
        try {
            if (file_exists($lock_filename)) {
                throw new RuntimeException('<b>Error:</b> A smush process is already in progress');
            }
            touch($lock_filename);
            $progress->clear();

            $target_dir = DIR_IMAGE . 'cache';
            $array_parsed = false;

            if (!empty($this->request->post['targetDir'])) {
                $dir = $this->request->post['targetDir'];

                $test_array = json_decode(html_entity_decode($dir), true);

                if (!empty($test_array) && is_array($test_array)) {
                    $array_parsed = true;

                    foreach ($test_array as $json_item) {
                        $json_item = dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.trim(str_replace('/', DIRECTORY_SEPARATOR, urldecode($json_item)), DIRECTORY_SEPARATOR);
                        $this->addSmushItemImage($fh, $total_images, $json_item);
                    }
                } 

                if (!$array_parsed) {
                    $target_dir = dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.trim(str_replace('/', DIRECTORY_SEPARATOR, urldecode($dir)), DIRECTORY_SEPARATOR);
                }
            }

            if (!$array_parsed) {
                $this->smush_read_dir($target_dir, $fh, $total_images);
            }
        } catch (Exception $e) {
            if (file_exists($lock_filename)) unlink($lock_filename);
            echo json_encode(array(
                'status' => 'fail',
                'errors' => array($e->getMessage())
            ));
            exit;
        }
        fclose($fh);
        if (file_exists($lock_filename)) unlink($lock_filename);

        $progress->setMax($total_images);
        $progress->setData('last_pointer_position', 0);
        $progress->setData('b_saved', 0);
        $progress->setData('already_smushed', 0);
        $progress->setData('is_process_active', false);
        echo json_encode(array(
            'status' => 'success'
        ));
        exit;
    }

    private function smush_get_next_image($progress, $fh) {
        $pos = $progress->getData('last_pointer_position');
        fseek($fh, $pos);

        if (feof($fh)) return false;
        do {
            $result = trim(fgets($fh));
        } while (empty($result) && !feof($fh));

        $progress->setData('last_pointer_position', ftell($fh));

        if (empty($result)) return false;
        return $result;
    }

    private function smush($resume = false, $method = 'local') {
        session_write_close();
        loadNitroLib('iprogress');
        loadNitroLib('NitroSmush/NitroSmush');

        $method = in_array($method, array('local', 'remote')) ? $method : 'local';
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $smusher = new NitroSmush();
        $smusher->setTempDir(NITRO_FOLDER . 'temp');
        $list_filename = NITRO_DATA_FOLDER . 'smush_files.flist';
        $fh = fopen($list_filename, 'r');
        if ($resume) {
            fseek($fh, $progress->getData('last_pointer_position'), SEEK_SET);
            $progress->resume();
        }

        $data_sizes = array(
            'KiloBytes' => 1024,
            'MegaBytes' => 1024*1024
        );

        $progress->setData('is_process_active', true);
        $smush_error_log = new Log(date('Y-m-d') . '_nitrosmush_error.txt');

        $progress->setData('last_smush_timestamp', time());
        while ($this->smush_can_proceed($progress) && false !== ($filename = $this->smush_get_next_image($progress, $fh))) {
            $progress->addMsg($filename . " -> Optimizing <i class=\"icon-spinner icon-spin\"></i>");
            set_time_limit(30);
            try {
                switch ($method) {
                case 'local':
                    $res = $smusher->smush($filename);
                    break;
                case 'remote':
                    $res = $smusher->smush($filename, false, true);
                    break;
                }

                if ($res['smushed']) {
                    $savings = $progress->getData('b_saved');
                    $savings += $res['savings_b'];
                    $progress->setData('b_saved', $savings);
                    $saved_size = $res['savings_b'] . ' bytes';
                    foreach ($data_sizes as $k=>$v) {
                        if ($res['savings_b'] > $v) {
                            $saved_size = number_format($res['savings_b'] / $v, 2) . " " . $k;
                        }
                    }
                    $progress->replaceMsg($filename . " -> Saved " . $saved_size . " (<b>" . $res['savings_percent'] . "%</b>)");
                } else if (empty($res['errors'])) {
                    $already_smushed = $progress->getData('already_smushed');
                    $already_smushed++;
                    $progress->setData('already_smushed', $already_smushed);
                    $progress->replaceMsg($filename . " is already optimized");
                } else {
                    foreach ($res['errors'] as $err) {
                        $progress->replaceMsg("<b>ERROR</b> [$filename] => $err");
                        if (NITRO_DEBUG_MODE) {
                            $smush_error_log->write($filename . " | " . $err);
                        }
                    }
                }
            } catch (Exception $e) {
                $progress->replaceMsg("<b>Error</b> [$filename] => " . $e->getMessage());
                if (NITRO_DEBUG_MODE) {
                    $smush_error_log->write($filename . " | " . $e->getMessage());
                }
            }
            $progress->iterateWith(1);
            $progress->setData('last_smush_timestamp', time());
        }
        $progress->setData('is_process_active', false);

        return $progress->getProgress() == $progress->getMax();
    }

    public function smush_start() {
        $method = !empty($this->request->get['method']) ? $this->request->get['method'] : getNitroPersistence('Smush.Method');
        $isReady = $this->smush(false, $method);

        echo (int)$isReady; exit;
    }

    public function smush_resume() {
        $method = !empty($this->request->get['method']) ? $this->request->get['method'] : getNitroPersistence('Smush.Method');
        $isReady = $this->smush(true, $method);

        echo (int)$isReady; exit;
    }

    public function smush_pause() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $progress->abort();
        $progress->setData('is_process_active', false);
        exit;
    }

    public function smush_clear_buffer() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        echo json_encode(array('messages' => $progress->clearMessages())); exit;
    }

    public function smush_get_progress() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $smush_progress = array(
            'processed_images_count' => $progress->getProgress(),
            'already_smushed_images_count' => $progress->getData('already_smushed'),
            'total_images' => $progress->getMax(),
            'b_saved' => $progress->getData('b_saved'),
            'last_smush_timestamp' => $progress->getData('last_smush_timestamp'),
            'is_process_active' => $progress->getData('is_process_active'),
            'messages' => $progress->getMessages()
        );
        echo json_encode($smush_progress);
        exit;
    }

    public function gecacheimagescountnow() {
        echo $this->getCacheImagesCount();
    }


    public function getCacheImagesCount($path = '') {
        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        if (empty($path)) $path = DIR_IMAGE.'cache';
        $size = 0;
        $ignore = array('.','..','cgi-bin','.DS_Store','index.html');
        $files = scandir($path);
        foreach($files as $t) {
            if(in_array($t, $ignore)) continue;
            if (is_dir(rtrim($path, '/') . '/' . $t)) {
                $size += $this->getCacheImagesCount(rtrim($path, '/') . '/' . $t);
            } else {
                $size++;
            }   
        }

        return $size;

    }

    public function cdn() {
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $this->response->setOutput('');
            return;
        }

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        set_error_handler(create_function(
            '$severity, $message, $file, $line',
            'throw new Exception($message . " in file " . $file . " on line " . $line . ". Debug backtrace: <pre>" . print_r(debug_backtrace(), true) . "</pre>");'
        ));

        // Save config
        if (!empty($this->request->post['config']['Nitro'])) {
            $nitro_persistence = getNitroPersistence();

            foreach ($this->request->post['config']['Nitro'] as $key => $config_vals) {
                $nitro_persistence['Nitro'][$key] = $config_vals;
            }

            setNitroPersistence($nitro_persistence);
        }

        if (empty($this->request->post['last'])) {
            $this->request->post['last'] = array(
                'response_type' => 'error',
                'percent' => 0,
                'message' => 'CDN type not valid.'
            );
        }

        $response = &$this->request->post['last'];

        try {
            if (!empty($this->request->post['cdn'])) {
                switch ($this->request->post['cdn']) {
                case 'amazon' : {
                    $this->load->model('tool/nitro_amazon');
                    $this->model_tool_nitro_amazon->upload();
                    break;
                }

case 'rackspace' : {
    $this->load->model('tool/nitro_rackspace');
    $this->model_tool_nitro_rackspace->upload();
    break;
}

case 'ftp' : {
    $this->load->model('tool/nitro_ftp');
    $this->model_tool_nitro_ftp->upload();
    break;
}
                }
            }
        } catch (Exception $e) {
            $response['percent'] = 0;
            $response['response_type'] = 'error';
            $response['message'] = $e->getMessage();
        }

        restore_error_handler();

        $this->response->setOutput(json_encode($response));
    }

    public function get_preminify_stack() {
        header('Content-Type: application/json');

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        require_once NITRO_LIB_FOLDER . 'NitroFiles.php';

        $nf = new NitroFiles(array(
            'root' => DIR_CATALOG,
            'ext' => array('css', 'js')
        ));

        $files = $nf->find();
        if (!empty($files)) {
            $files_linear = array();
            foreach ($files as $file) {
                $files_linear[] = $file['full_path'];
            }
            echo json_encode($files_linear);
        } else {
            echo json_encode(array());
        }

        exit;
    }

    public function minify_file() {
        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        if (!empty($this->request->post['file'])) {
            require_once NITRO_CORE_FOLDER . 'minify_functions.php';

            $file = $this->request->post['file'];
            if (file_exists($file)) {
                $ext = preg_replace('/.*?\.(\w+)$/', '$1', $file);
                $this->config->set('config_url', HTTP_CATALOG);
                $this->config->set('config_ssl', HTTPS_CATALOG);
                minify($ext, array(
                    md5($file) => $file
                ), array());
            }
        }
        exit;
    }

    private function isSessionClosed() {
        return $this->session_closed;
    }

    private function closeSession() {
        if (session_id() && !$this->session_closed) session_write_close();
        $this->session_closed = true;
    }

    private function openSession() {
        if ($this->session_closed) session_start();
        $this->session_closed = false;
        return session_id();
    }
}
