<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('add_person'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">
        
        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_persons->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
            >
 <fieldset>  
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="person_name" class="control-label">
                    <?php echo lang('person_name'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="person_name" id="person_name" class="form-control"
                       value="<?php echo $this->mdl_persons->form_value('person_name'); ?>">
            </div>
        </div>
        <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="person_cell" class="control-label">
                    <?php echo lang('person_cell'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="person_cell" id="person_cell" class="form-control"
                       value="<?php echo $this->mdl_persons->form_value('person_cell'); ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="person_email" class="control-label">
                    <?php echo lang('person_email'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="person_email" id="person_email" class="form-control"
                       value="<?php echo $this->mdl_persons->form_value('person_email'); ?>">
            </div>
                <input type="hidden" name="person_key" id="person_key" class="form-control"
                       value="<?php echo $puk; ?>">
        </div>
        <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="person_active" class="control-label">
                    <?php echo lang('person_active'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="checkbox" name="person_active" id="person_active" class="form-control"
                       value="<?php echo $this->mdl_persons->form_value('person_active'); ?>">
            </div>
        </div>
    </fieldset>      
        
                <?php if ($this->mdl_persons->form_value('person_id') > 0) : ?>
                    <fieldset>                    
                <?php else : ?>
                    <div class="alert alert-info">
                        <input type="hidden" class="form-control" name="halfstep" id="halfstep" value="1"/>
                    Salva il prodotto prima di aggiungere immagini&nbsp;&nbsp;&nbsp;
                    <button id="btn_submit2" name="btn_submit2" class="btn btn-success btn-sm ajax-loader" value="2">
                        <i class="fa fa-check"></i> Salva  
                    </button>
                    </div>
                    <fieldset disabled="disabled">
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="control-label"><?php echo lang('images'); ?></label>
                    <br/>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-default fileinput-button">
                        <i class="fa fa-plus"></i>
                        <span><?php echo lang('add_files'); ?></span>
                    </span>
                    
                </div>
                <!-- dropzone -->
                <div id="actions" class="col-xs-12 col-sm-12 row">
                    <div class="col-lg-7">
                    </div>
                    <div class="col-lg-5">
                        <!-- The global file processing state -->
                    <span class="fileupload-process">
                        <div id="total-progress" class="progress progress-striped active" role="progressbar"
                             aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="progress-bar progress-bar-success" style="width:0%;"
                                 data-dz-uploadprogress></div>
                        </div>
                    </span>
                    </div>

                    <div class="table table-striped" class="files" id="previews">

                        <div id="template" class="file-row">
                            <!-- This is used as the file preview template -->
                            <div>
                                <span class="preview"><img data-dz-thumbnail/></span>
                            </div>
                            <div>
                                <p class="name" data-dz-name></p>
                                <strong class="error text-danger" data-dz-errormessage></strong>
                            </div>
                            <div>
                                <p class="size" data-dz-size></p>

                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0"
                                     aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success" style="..."
                                         data-dz-uploadprogress></div>
                                </div>
                            </div>
                            <div>
                                <button data-dz-remove class="btn btn-danger btn-sm delete">
                                    <i class="fa fa-trash-o"></i>
                                    <span><?php echo lang('delete'); ?></span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- stop dropzone -->

                </fieldset>                        
    </div>

</form>
<script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/0/'.$puk) ?>", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON("<?php echo site_url('upload/upload_file/0/'.$puk) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo base_url(); ?>uploads/customer_files/' + val.fullname);
                    } else {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo base_url(); ?>assets/default/img/favicon.png');
                    }
                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });

    myDropzone.on("addedfile", function (file) {
        myDropzone.emit("thumbnail", file, '<?php echo base_url(); ?>assets/default/img/favicon.png');
    });

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    });

    myDropzone.on("sending", function (file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1";
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0";
    });

    myDropzone.on("removedfile", function (file) {
        $.ajax({
            url: "<?php echo site_url('upload/delete_file/'.$puk) ?>",
            type: "POST",
            data: {'name': file.name}
        });
    });
</script>
