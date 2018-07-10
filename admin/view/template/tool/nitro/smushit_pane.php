<div class="row">
  <div class="col-md-8">
    <div class="box-heading">
      <h1>Image Optimization</h1>
    </div>
    <div class="box-content">
        <div class="" style="margin-bottom:15px;">
            <div class="col-md-8">
                <div class="box-minibox">Optimized Images<div class="number" id="smushedNumber"><?php echo !empty($smushit_data['smushed_images_count']) ? $smushit_data['smushed_images_count'] : 0;?></div></div>
                <div class="box-minibox">Already Optimized<div class="number" id="alreadySmushedNumber"><?php echo !empty($smushit_data['already_smushed_images_count']) ? $smushit_data['already_smushed_images_count'] : 'N/A';?></div></div> 
                <div class="box-minibox">Total Images<div class="number" id="totalImages"><?php echo !empty($smushit_data['total_images']) ? $smushit_data['total_images'] : 'N/A';?></div></div>        
                <div class="box-minibox">KB saved<div class="number" id="kbSaved"><?php echo !empty($smushit_data['kb_saved']) ? $smushit_data['kb_saved'] : 0;?> KB</div></div>         
                <div class="box-minibox">Last optimization<div class="number" id="lastSmushTimestamp"><?php echo !empty($smushit_data['last_smush_timestamp']) ? date('D, j M Y H:i:s', $smushit_data['last_smush_timestamp']) : 'N/A';?></div></div>
            </div>
            <div class="col-md-4">
                <div class="progress" style="margin-top: 10px; text-align: center;">
                    <div id="progressBar" class="progress-bar" style="width: 0%;line-height: 20px;color:#000;">0%</div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
         <div class="smushingResult"></div>
        <button type="button" class="btn btn-large btn-primary smushItButton">Start new optimization process</button>
        <button type="button" class="btn btn-default btn-large resumeSmushButton" style="display: none">Resume previous optimization</button>
        <button type="button" class="btn btn-large btn-default pauseSmushButton" style="display: none">Pause</button>
        <div class="empty-smush-div"></div>
        <div class="smush-log">
            <div class="smush-log-entries">
            </div>
        </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="box-heading">
      <h1>Options</h1>
    </div>
    <div class="box-content" style="min-height: 150px;">
        <input class="form-control" type="hidden" name="Nitro[SmushIt][OnDemand]" value="no">
        <table class="form">
          <tr>
            <td style="vertical-align:top;" colspan="2"><button class="btn btn-success hidden" id="optimizeReportedImagesButton">Optimize reported images</button></td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Optimization Method<span class="help">The optimization method can be either <b>Local</b> or <b>Remote</b>. When <b>Local</b> is chosen NitroPack will try to use the image optimization programs locally. This option is recommended. When <b>Remote</b> is chosen, NitroPack will send the images to a remote server which will optimize them and then return them back to your site. Use <b>Remote</b> if <b>Local</b> is not working for you.</span></td>
            <td style="vertical-align:top;">
            <select class="form-control" name="Nitro[Smush][Method]" id="smushMethod">
                <option value="local" <?php echo( (empty($data['Nitro']['Smush']['Method']) || $data['Nitro']['Smush']['Method'] == 'local')) ? 'selected=selected' : ''?>>Local</option>
                <option value="remote" <?php echo (!empty($data['Nitro']['Smush']['Method']) && $data['Nitro']['Smush']['Method'] == 'remote') ? 'selected=selected' : ''?>>Remote</option>
            </select>
            </td>
          </tr>
          <tr style="display: none" id="smushOnDemand">
            <td style="vertical-align:top;">Optimize On-The-Fly<span class="help">If enabled, your images will be optimized on-the-fly, while their cached version is created. Your first-time page load when the cache is being created may be slightly slower.</span></td>
            <td style="vertical-align:top;">
            <select class="form-control" name="Nitro[Smush][OnDemand]">
                <option value="yes" <?php echo( (!empty($data['Nitro']['Smush']['OnDemand']) && $data['Nitro']['Smush']['OnDemand'] == 'yes')) ? 'selected=selected' : ''?>>Enabled</option>
                <option value="no" <?php echo (empty($data['Nitro']['Smush']['OnDemand']) || $data['Nitro']['Smush']['OnDemand'] == 'no') ? 'selected=selected' : ''?>>Disabled</option>
            </select>
            </td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Optimize directory/file<span class="help">Enter path to a specific directory or a single file to be optimized. The path should be relative to the root of your OpenCart installation. Use "/" for directory separator even if your server is running on Windows.<br /><strong>Warning:</strong> The optimization process will overwrite the original images with the optimized ones, so a backup is recommended!</span></td>
            <td style="vertical-align:top;">
            <input class="form-control" id="smushTargetPath" type="text" name="Nitro[Smush][target_path]" value="<?php echo !empty($data['Nitro']['Smush']['target_path']) ? $data['Nitro']['Smush']['target_path'] : ''?>" placeholder="image/cache/">
            </td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Remove Image Cookies<span class="help">Enable this to speed up the delivery of your website images.</span></td>
            <td style="vertical-align:top;">
              <select name="Nitro[ImageCookies][Enabled]" class="form-control NitroImageCookiesEnabled">
                <option value="yes" <?php echo( (!empty($data['Nitro']['ImageCookies']['Enabled']) && $data['Nitro']['ImageCookies']['Enabled'] == 'yes')) ? 'selected=selected' : ''?>>Yes (Recommended)</option>
                <option value="no" <?php echo (empty($data['Nitro']['ImageCookies']['Enabled']) || $data['Nitro']['ImageCookies']['Enabled'] == 'no') ? 'selected=selected' : ''?>>No</option>
              </select>
            </td>
          </tr>
        </table>
    </div>
  </div>
</div>


<script>
if (notOptimizedImages.length) {
    $("#optimizeReportedImagesButton").removeClass("hidden");
}

$('#smushMethod').on('change', function() {
    nitro.smusher.setMethod(this.value);
    
    if (this.value == 'local') {
        $('#smushOnDemand').show();
    } else {
        $('#smushOnDemand').hide();
    }
});
$('#smushMethod').trigger('change');
var smushLog = $('.smush-log-entries');
    smushLog.parent().hide();

var formatTimestamp = function (timestamp) {
    if (timestamp == 0) return 'N/A';
    
    var weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var dateObj = new Date(timestamp * 1000);
    return weekDays[dateObj.getDay()] + ', ' + dateObj.getDate() + ' ' + months[dateObj.getMonth()] + ' ' + dateObj.getFullYear() + ' ' + dateObj.getHours() + ':' + dateObj.getMinutes() + ':' + dateObj.getSeconds();
}

function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
        if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}

var updateLog = function(messages) {
    smushLog.parent().show();
    smushLog.html(messages.join('<br><br>'));
    smushLog.slideDown();
    $('.smush-log').css({width: $('.empty-smush-div').width() + 'px'}).animate({
        scrollTop: smushLog.outerHeight()
    }, 1000);
}

nitro.smusher.setToken('<?php echo $_GET['token'] ?>');

nitro.smusher.addSmushPauseEventListener(function() {
    $('.smushItButton').show();
    $('.pauseSmushButton').text('Pause').hide();
    $('.resumeSmushButton').show();
    $('.smushingResult div.smushingDiv').remove();
});

nitro.smusher.addSmushFinishEventListener(function() {
    $('.smushItButton').show();
    $('#optimizeReportedImagesButton').show();
    $('.pauseSmushButton').text('Pause').hide();
    $('.resumeSmushButton').hide();
    $('.smushingResult div.smushingDiv').remove();
});

nitro.smusher.addSmushStartedEventListener(function() {
    $('.pauseSmushButton').show();
    $('.smushItButton').hide();
    $('.resumeSmushButton').hide();
    $('#optimizeReportedImagesButton').hide();
    $('.smushingResult div.smushingDiv').remove();
    $('.smushingResult').html('<div class="smushingDiv"><img src="view/image/nitro/loading.gif" /> Smushing...</div>');
    smushLog.html('');
});

var is_d = function(variable) {
    return typeof variable != 'undefined';
}

nitro.smusher.addSmushUpdateEventListener(function(data) {
    if (is_d(data.messages)) {
        updateLog(data.messages);
    }

    if (is_d(data.processed_images_count) && is_d(data.already_smushed_images_count)) {
        $('#smushedNumber').html(data.processed_images_count - data.already_smushed_images_count);
    }

    if (is_d(data.already_smushed_images_count)) {
        $('#alreadySmushedNumber').html(data.already_smushed_images_count);
    }

    if (is_d(data.b_saved)) {
        $('#kbSaved').html((data.b_saved / 1024).toFixed(2));
    }

    if (is_d(data.total_images)) {
        $('#totalImages').html(data.total_images);
    }

    if (is_d(data.last_smush_timestamp)) {
        $('#lastSmushTimestamp').html(formatTimestamp(data.last_smush_timestamp));
    }

    if (is_d(data.processed_images_count) && is_d(data.total_images)) {
        var progress = parseInt((data.processed_images_count*100)/data.total_images);
        $('#progressBar').css('width', progress + '%').text(progress + '%');
    }

    if (is_d(data.is_process_active) && is_d(data.total_images) && is_d(data.processed_images_count)) {
        if (!data.is_process_active && data.total_images > data.processed_images_count) {
            $('.resumeSmushButton').show();
        } else {
            $('.resumeSmushButton').hide();
        }
    }
});

nitro.smusher.addErrorEventListener(function(data) {
    var errors = data.messages||data.errors||[];
    updateLog(errors);
});

$('.smushItButton').click(function() {
    nitro.smusher.restart();
});

$('#optimizeReportedImagesButton').click(function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var cache_images = [];

    for (var x = 0; x < notOptimizedImages.length; x++) {
        var image_url = notOptimizedImages[x];
        var search = image_url.indexOf("/image/cache/");
        if (search > -1) {
            cache_images.push(image_url.substr(search+1));
        }
    }

    if (cache_images.length) {
        $("#smushTargetPath").val(JSON.stringify(cache_images));//search+1 in order to skip the first "/"
        nitro.smusher.restart();
    } else {
        alert("No images in the cache directory have been reported.");
    }
});

$('.pauseSmushButton').click(function() {
    nitro.smusher.pause();
    $(this).text('Pausing...');
});

$('.resumeSmushButton').click(function() {
    nitro.smusher.resume();
});

$(window).load(function() {
    nitro.smusher.init(smushLog);
});
</script>

<style>
.smushingDiv {
	padding: 10px;
}
</style>
