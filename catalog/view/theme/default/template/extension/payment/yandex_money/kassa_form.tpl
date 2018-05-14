<?php

/**
 * @var \YandexMoneyModule\Model\KassaModel $kassa
 */
?>

<?php if ($fullView) : ?>
<?php echo $header; ?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>
<div class="container">
    <?php echo $content_top; ?>
    <?php endif; ?>

    <?php if (!$kassa->getEPL()) : ?>
    <h3><?php echo $kassa->getDisplayName(); ?></h3>
    <?php endif; ?>
    <form method="post" action="" id="yandex-money-payment-form">
        <?php if ($kassa->getEPL()) : ?>
        <input type="hidden" name="kassa_payment_method" value="" />
        <?php if ($kassa->useYandexButton()) : ?>
        <a href="javascript://" title="<?php echo $language->get('text_continue'); ?>" id="continue-button">
            <img width="165" height="76" src="data:image/png;base64,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" />
        </a>
        <?php endif; ?>
        <?php else: ?>
        <?php $first = true; foreach ($kassa->getEnabledPaymentMethods() as $method): $key = 'text_method_' . $method; ?>
        <label>
            <input type="radio" name="kassa_payment_method" value="<?php echo $method ?>"<?php echo ($first ? ' checked' : ''); ?> />
            <img src="<?php echo $image_base_path . '/' . $method; ?>.png" alt="<?php echo $language->get($key); ?>" />
            <?php echo $language->get($key); $first = false; ?>
        </label>
        <?php if ($method == \YandexCheckout\Model\PaymentMethodType::QIWI) : ?>
        <div id="payment-qiwi" class="additional" style="display: none;">
            <label for="qiwi-phone"><?php echo $language->get('text_payment_method_qiwi_phone'); ?></label><br />
            <input name="qiwiPhone" id="qiwi-phone" value="" />
        </div>
        <?php elseif ($method == \YandexCheckout\Model\PaymentMethodType::ALFABANK) : ?>
        <div id="payment-alfabank" class="additional" style="display: none;">
            <label for="alfa-login"><?php echo $language->get('text_payment_method_alfa_login'); ?></label><br />
            <input name="alfaLogin" id="alfa-login" value="" />
        </div>
        <?php endif; ?>
        <br />
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!$kassa->getEPL() || ($kassa->getEPL() && !$kassa->useYandexButton())) : ?>
        <div class="buttons">
            <div class="pull-right">
                <button class="btn btn-primary" id="continue-button" type="button"><?php echo $language->get('text_continue'); ?></button>
            </div>
        </div>
        <?php endif; ?>
    </form>
    <script type="text/javascript"><!--
        $(document).ready(function() {
            var paymentType = jQuery('input[name=kassa_payment_method]');
            paymentType.change(function () {
                var id = '#payment-' + jQuery(this).val();
                jQuery('.additional').css('display', 'none');
                jQuery(id).css('display', 'block');
            });

            jQuery('#continue-button').bind('click', function () {
                var form = jQuery("#yandex-money-payment-form")[0];
                jQuery.ajax({
                    url: "<?php echo $validate_url; ?>",
                    dataType: "json",
                    method: "GET",
                    data: {
                        paymentType: form.kassa_payment_method.value,
                        qiwiPhone: (form.qiwiPhone ? form.qiwiPhone.value : ''),
                        alphaLogin: (form.alfaLogin ? form.alfaLogin.value : '')
                    },
                    success: function (data) {
                        if (data.success) {
                            document.location = data.redirect;
                        } else {
                            onValidateError(data.error);
                        }
                    },
                    failure: function () {
                        onValidateError('Failed to create payment');
                    }
                });
            });

            function onValidateError(errorMessage) {
                var warning = jQuery('#yandex-money-payment-form .alert');
                if (warning.length > 0) {
                    warning.fadeOut(300, function () {
                        warning.remove();
                        var content = '<div class="alert alert-danger">' + errorMessage + '<button type="button" class="close" data-dismiss="alert">×</button></div>';
                        jQuery('#yandex-money-payment-form').prepend(content);
                        jQuery('#yandex-money-payment-form .alert').fadeIn(300);
                    });
                } else {
                    var content = '<div class="alert alert-danger">' + errorMessage + '<button type="button" class="close" data-dismiss="alert">×</button></div>';
                    jQuery('#yandex-money-payment-form').prepend(content);
                    jQuery('#yandex-money-payment-form .alert').fadeIn(300);
                }
            }

        });
        //--></script>

    <?php if ($fullView) : ?>
    <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>
<?php endif; ?>
