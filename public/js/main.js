$(function(){
    // button select image
    $('.upload-button').click(function(){
        $('input[type="file"]').trigger('click');
        return false;
    });

    //var bar = $('.progress-bar');
    $('input[type="file"]').fileupload({
        paramName: "files[]",
        //fileInput: $(this),
        //uploadTemplateId: config.target,
        dropZone: null,
        dataType: 'json',
        multipart: false,
        maxFileSize: 3000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        add: function (e, data) {
            //console.error('dfile='+data.files[0].name);
            /*fileName = data.files[0].name;
            fileInfo.html(data.files[0].name+" ("+Math.round(data.files[0].size/1024) + "K"+")");
            fileInfo.show(); // show is needed if there is second try upload show name etc...
            fileSelect.hide();
            btnUpload.show();
            fileProcess.show();*/

            $('.upload-button').hide();
            $('.progress').show();

            var jqXHR = data.submit();

            /*container.find('.action-uploader-upload-btn').unbind("click"); // avoid future uploads

            btnUpload.click(function(){
                container.find('.action-file-preview').html('');
                jqXHR = data.submit();
                $(this).hide();
                run = true;
            });*/
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);

            //fileInfo.hide();
            //bar.show();
            //$('.progress-bar').progressbar({value:progress});
            $('.progress-bar').width(progress+'%');
            //bar.find('.label-bar').text(fileName + '  (' + progress + '%)');
            //run = true;

            if(progress == 100) {
                $('.progress').hide();
                $('.result').show();
                //run = false;
                //bar.hide();
                //fileInfo.show();
            }

        },
        done: function (e, data) {
            //bar.hide();
            var imgurl = "http://www.travoltaconfused.com/img/" + data.result.filename;
            var imgtag = '<img src="'+imgurl+'" alt="your gif!" />';

            $('.result p').html(imgtag);
            $('.link p span.url a').attr('href', imgurl).html(imgurl);
            $('.link p a.down').attr('href', imgurl+'/down');
            $('.link').show();
            //fid = data.result.id;

            //container.find('.file-id').val(data.result.id);
            //if(config.preview)
            //    root.getPreviewDisplayed();
            //root.setFileId(data.result.id);
        }
    });

    // load last
    $( ".lasts" ).load("/lasts");
});
