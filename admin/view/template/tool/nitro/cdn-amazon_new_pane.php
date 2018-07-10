<div class="row-fluid">
    <div class="span12">
        <div class="box-heading"><h1>Amazon CloudFront/S3 CDN Service</h1></div>
        <div class="box-content" style="min-height:100px; line-height:20px; padding: 20px;">
            If you have an Amazon CloudFront account, you can configure your Amazon CDN by following this tutorial (only the section "Create a CloudFront Distribution"):<br>
            <br>
            <a target="_blank" href="http://docs.aws.amazon.com/gettingstarted/latest/swh/getting-started-create-cfdist.html#create-distribution"><u>http://docs.aws.amazon.com/gettingstarted/latest/swh/getting-started-create-cfdist.html#create-distribution</u></a>
            <br><br>
            <ol>
                <li>Visit the link above to configure the CDN</li>
                <li>Wait for a few hours for Amazon to fetch your website content.</li>
                <li>Configure your NitroPack Generic CDN using the CloudFront Domain Name (which looks like xxxxxxxxxxxx.cloudfront.net). Press the button below:<br><a id="generic_link" class="btn btn-info" style="margin-top: 10px;">Configure CDN</a></li>
            </ol>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#generic_link').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('a[href="#cdn"]').trigger('click');
    $('input[name="Nitro[CDNStandard][GenericURL]"]').focus();
});
</script>