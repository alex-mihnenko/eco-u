    </div>
    <footer>
        <div class="top-footer">
            <div class="width-1418">
                <div class="f-stick">
                    <div class="clearfix">
                        <a href="/" class="logo-foot">
                            <img src="catalog/view/theme/default/img/logo_2.png" alt="">
                        </a>
                        <ul class="menu-foot">
                            <!--li><a href="#">Блог Eco-u</a></li-->
                            <li><a href="/about/">O проекте</a></li>
                        </ul>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="f-left">
                        <a href="tel:+<?php echo $telephone_href; ?>" class="f-telephone"><?php echo $telephone; ?></a>
                        <a href="mailto:<?php echo $email; ?>" class="f-mail"><?php echo $email; ?></a>
                    </div>
                    <div class="social-networks">
                        <noindex><a href="https://vk.com/eco_u" target="_blank" class="s-n_icon s-n_1" rel="nofollow"></a></noindex>
                        <noindex><a href="https://www.instagram.com/ecou_shop/" target="_blank" class="s-n_icon s-n_2" rel="nofollow"></a></noindex>
                        <noindex><a href="https://www.facebook.com/ecoushop" target="_blank" class="s-n_icon s-n_3" rel="nofollow"></a></noindex>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-footer">
            <div class="b-copyright">© <?php echo date("Y", time()); ?> Ecou Shop. All Rights Reserved</div>
        </div>
    </footer>

    <?php foreach ($scripts as $script) { ?>
    <script src="<?php echo $script; ?>" type="text/javascript"></script>
    <?php } ?>

    <!-- Yandex.Metrika counter -->
    <script>
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter33704824 = new Ya.Metrika({
                        id:33704824,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true,
                        webvisor:true,
                        trackHash:true,
                        ecommerce:"dataLayer"
                    });
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = "https://mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/33704824" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90310299-1"></script>
    <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'UA-90310299-1'); </script>

    <script>(function(w, d, s, h, id) { w.roistatProjectId = id; w.roistatHost = h; var p = d.location.protocol == "https:" ? "https://" : "http://"; var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init"; var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);})(window, document, 'script', 'cloud.roistat.com', 'c92f8cd33d04a501b23d46fc776c62ce');</script>

    </body>
</html>