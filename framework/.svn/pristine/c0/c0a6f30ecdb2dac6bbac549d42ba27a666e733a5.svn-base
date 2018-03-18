<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webuploader/webuploader.min.js"></script>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webuploader/webuploader.css" type="text/css" rel="stylesheet"/>

<script type="text/javascript">
    var site_url = "<?php echo getUrl('base','getUploadParam',array(),false,PROJECT_SITE_URL)?>";
    var upload_url = "<?php echo C('upyun_param')['target_url']?>";
    var upyun_url = "<?php echo UPYUN_SITE_URL.DS?>";
    var swf_url = "<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webupload/Uploader.swf";

    function webuploader2upyun(upload_id, default_dir) {
        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: false,

            // swf文件路径
            swf: swf_url,

            // 文件接收服务端。
            server: upload_url,

            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#' + upload_id,

            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/jpg,image/jpeg,image/png'
            },
            method: 'POST'
        });

        uploader.on('beforeFileQueued', function (e) {
            $.get(site_url, {default_dir: default_dir}, function (response) {
                var obj = $.parseJSON(response);
                uploader.option('formData', {
                    'policy': obj.policy,
                    'signature': obj.signature
                });
                uploader.upload();
            });
        })

        // 文件上传成功
        uploader.on('uploadSuccess', function (file, response) {
            var img_name = response.url.split('/').pop();
            $('input[name=' + upload_id + ']').val(img_name);
            $('input[id=txt_' + upload_id + ']').val(img_name);
            $('#show_' + upload_id).attr('src', upyun_url + response.url).show();
            if ($('.show_' + upload_id + '_div').length > 0) {
                $('.show_' + upload_id + '_div').css('display', 'block');
            }
        });

        // 文件上传失败
        uploader.on('uploadError', function (file) {
            alert('Upload Fail!');
        });
    }
</script>
