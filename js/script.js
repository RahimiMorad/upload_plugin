(function($) {
    $(document).ready(function() {
       
        var $uploadForm    = $('.upload-form');
        var $uploadNotice  = $uploadForm.find('.upload-notice');
        var $uploadFile    = $uploadForm.find('.upload-file');

        if ( $uploadForm.length ) {
            $uploadForm.on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData();

                formData.append('action', 'upload-attachment');
                formData.append('async-upload', $uploadFile[0].files[0]);
                formData.append('name', $uploadFile[0].files[0].name);
                formData.append('_wpnonce', xyz_config.nonce);

                $.ajax({
                    url: xyz_config.upload_url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();

                        if ( myXhr.upload ) {
                            myXhr.upload.addEventListener( 'progress', function(e) {
                                if ( e.lengthComputable ) {
                                    var perc = ( e.loaded / e.total ) * 100;
                                    perc = perc.toFixed(2);
                                    var percentValue = perc + '%';
                                    $("#progressDivId").css("display", "block");
                                    $('#progressBar').width(percentValue);
                                    $('#percent').html(percentValue);
                                }
                            }, false );
                        }

                        return myXhr;
                    },
                    type: 'POST',
                    beforeSend: function() {
                        $uploadFile.hide();
                        $uploadNotice.html('Uploading&hellip;').show();
    
                    },
                    

                    success: function(resp) {
                        if ( resp.success ) {
                            $("#progressDivId").hide();
                            $uploadNotice.css("color","green");
                            $uploadNotice.html('Successfully uploaded.');
                            $uploadFile.val('');
                            location.reload();

                        } else {
                            $imgNotice.css("color","red");
                            $imgNotice.html('Fail to upload Files.');
                        }
                    }
                });
            });
        }
    });
})(jQuery);
