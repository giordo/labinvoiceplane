<style>
    #dialog label, #dialog input { display:block; }
    #dialog label { margin-top: 0.5em; }
    #dialog input, #dialog textarea { width: 95%; }
    #tabs { margin-top: 1em; }
    #tabs li .ui-icon { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
    #add_tab { cursor: pointer; }
</style>

<script type="text/javascript">

    $(function () {

        var tabTitle = $( "#tab_title" ),
            tabContent = $( "#tab_content" ),
            tabTemplate = "<li><a href='#{href}'>#{label}</a><span class='ui-icon ui-icon-print' role='presentation' data-toggle='dropdown'>Remove Tab</span>" +
                "<ul class='dropdown-menu'> " +
                    "<li class='checkbox'><label>&nbsp;<input type='checkbox' name='chk-prt[]' id='chk-prt-#{uid}' value='#{uid}' checked='checked'>Stampa</label></li> " +
                    "<li class='checkbox'><label>&nbsp;<input type='checkbox' name='chk-bkp[]' id='chk-bkp-#{uid}' value='#{uid}' checked='checked'>Interruzione di pagina</label></li>" +
                    "<li class='checkbox'><label>&nbsp;<input type='checkbox' name='chk-sum[]' id='chk-sum-#{uid}' value='#{uid}'>Escludi dal totale</label></li>" +
                    "<li class='checkbox chk-delete' id='chk-del-#{uid}'><label>&nbsp;Cancella</label></li>" +
                    "</ul>" +
                "</li>",
            tabCounter = 2;

        var tabs = $( "#tabs" ).tabs();

        tabs.find( ".ui-tabs-nav" ).sortable({
            axis: "x",
            stop: function() {
                tabs.tabs( "refresh" );
            },
            items: '> li:not(.locked)'
        });
		
/* giordotableedit
        $('table.tbl-sortable').Tabledit({
            editButton: true,
            removeButton: true,
            columns: {
                identifier: [0, 'id'],
                editable: [[1, 'Articolo'],[2, 'Descrizione'],[3, 'Quantità'],[4, 'Prezzo'],[5, 'Sc-1'],[6, 'Sc-2'],[7, 'iva', '{"1": "22%", "2": "10%", "3": "4%"}']]
            }
        });
*/
        $("table.tbl-sortable tbody").sortable({
            items: "> tr:not(:first)",
            appendTo: "parent",
            helper: "clone"
        }).disableSelection();

        // modal dialog init: custom buttons and a "close" callback resetting the form inside
        var dialog = $( "#dialog" ).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                Add: function() {
                    addTab();
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //form[ 0 ].reset();
            }
        });

        // addTab form: calls addTab function on submit and closes the dialog
        var form = dialog.find( "form" ).submit(function( event ) {
            addTab();
            dialog.dialog( "close" );
            event.preventDefault();
        });

        function getuid() {
            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                s4() + '-' + s4() + s4() + s4();
        }

        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }

        // actual addTab function: adds new tab using the input from the form above
        function addTab() {
            var uid = getuid();
            var label = tabTitle.val() || "Tab " + uid,
                id = "tabs-" + uid,
                li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ).replace( /#\{uid\}/g, uid ) ),
                tabContentHtml = tabContent.val() || "Tab " + tabCounter + " content.";

            tabs.find( ".ui-tabs-nav > li:nth-last-child(3)" ).after( li );
            tabs.append( "<div id='" + id + "'><p>" + tabContentHtml + "</p></div>" );
            tabs.tabs( "refresh" );
            var index = $(".ui-tabs-nav > li:nth-last-child(3)").index();
            tabs.tabs("option", "active", index);
            tabCounter++;
        }

        // addTab button: just opens the dialog
        $( ".tabsplus" )
            .click(function() {
                dialog.dialog( "open" );
            });

        // addTab button: just opens the dialog
        $( "#add_tab" )
            .button()
            .click(function() {
                dialog.dialog( "open" );
            });

        // close icon: removing the tab on click
        tabs.delegate( "span.ui--icon-print", "click", function() {
            var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
            $( "#" + panelId ).remove();
            tabs.tabs( "refresh" );
        });

        tabs.bind( "keyup", function( event ) {
            if ( event.altKey && event.keyCode === $.ui.keyCode.BACKSPACE ) {
                var panelId = tabs.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
                $( "#" + panelId ).remove();
                tabs.tabs( "refresh" );
            }
        });

        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        $('#quote_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_change_client'); ?>", {
                quote_id: <?php echo $quote_id; ?>,
                client_name: "<?php echo $this->db->escape_str($quote->client_name); ?>"
            });
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_save_quote').click(function () {
            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('quotes/ajax/save'); ?>", {
                    quote_id: <?php echo $quote_id; ?>,
                    quote_number: $('#quote_number').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_date_expires: $('#quote_date_expires').val(),
                    quote_status_id: $('#quote_status_id').val(),
                    template_id: $('#template_id').val(),
                    salesperson_id: $('#salesperson_id').val(),
                    techperson_id: $('#techperson_id').val(),
                    quoteref_id: $('#quoteref_id').val(),
                    destination_id: $('#destination_id').val(),
                    quote_password: $('#quote_password').val(),
                    items: JSON.stringify(items),
                    quote_discount_amount: $('#quote_discount_amount').val(),
                    quote_discount_percent: $('#quote_discount_percent').val(),
                    notes: $('#notes').val(),
                    custom: $('input[name^=custom]').serializeArray()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
                    }
                    else {
                        $('.control-group').removeClass('error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('error');
                        }
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('quotes/generate_pdf/' . $quote_id); ?>', '_blank');
        });

		/* giordodiscount
        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }
            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });
		*/
		
        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });
        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table").sortable({
            helper: fixHelper,
            items: 'tbody'
        });

    });


</script>

<?php echo $modal_delete_quote; ?>
<?php echo $modal_add_quote_tax; ?>
<div id="dialog" title="Tab data">
    <form>
        <fieldset class="ui-helper-reset">
            <label for="tab_title">Title</label>
            <input type="text" name="tab_title" id="tab_title" value="Tab Title" class="ui-widget-content ui-corner-all">
            <label for="tab_content">Content</label>
            <textarea name="tab_content" id="tab_content" class="ui-widget-content ui-corner-all">Tab content</textarea>
        </fieldset>
    </form>
</div>
<div id="headerbar">
    <h1><?php echo lang('quote'); ?> #<?php echo $quote->quote_number; ?></h1>

    <div class="pull-right btn-group">

        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <?php echo lang('options'); ?> <i class="fa fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="#add-quote-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i>
                        <?php echo lang('add_quote_tax'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php echo lang('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php echo lang('send_email'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_quote_to_invoice"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?php echo lang('quote_to_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_quote"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php echo lang('copy_quote'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-quote" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn_add_row btn btn-sm btn-default">
            <i class="fa fa-plus"></i>
            <?php echo lang('add_new_row'); ?>
        </a>
        <a href="#" class="btn_add_product btn btn-sm btn-default">
            <i class="fa fa-database"></i>
            <?php echo lang('add_product'); ?>
        </a>

        <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_quote">
            <i class="fa fa-check"></i>
            <?php echo lang('save'); ?>
        </a>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="quote_form">

        <div class="quote">

            <div class="cf row">

                <div class="col-xs-12 col-md-5">
                    <div class="pull-left">

                        <h2>
                            <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>"><?php echo $quote->client_name; ?></a>
                            <?php if ($quote->quote_status_id == 1) { ?>
                                <span id="quote_change_client" class="fa fa-edit cursor-pointer small"
                                      data-toggle="tooltip" data-placement="bottom"
                                      title="<?php echo lang('change_client'); ?>"></span>
                            <?php } ?>
                        </h2><br>
					<span>
						<?php echo ($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
                        <?php echo ($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
                        <?php echo ($quote->client_city) ? $quote->client_city : ''; ?>
                        <?php echo ($quote->client_state) ? $quote->client_state : ''; ?>
                        <?php echo ($quote->client_zip) ? $quote->client_zip : ''; ?>
                        <?php echo ($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
					</span>
                        <br><br>
                        <?php if ($quote->client_phone) { ?>
                            <span><strong><?php echo lang('phone'); ?>
                                    :</strong> <?php echo $quote->client_phone; ?></span><br>
                        <?php } ?>
                        <?php if ($quote->client_email) { ?>
                            <span><strong><?php echo lang('email'); ?>
                                    :</strong> <?php echo $quote->client_email; ?></span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-7">

                    <div class="details-box">

                        <div class="row">

                            <div class="col-xs-12 col-sm-4">

                                <div class="quote-properties">
                                    <label for="quote_number">
                                        <?php echo lang('quote'); ?> #
                                    </label>

                                    <div class="controls">
                                        <input type="text" id="quote_number" class="form-control input-sm"
                                               value="<?php echo $quote->quote_number; ?>">
                                    </div>
                                </div>

                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_created">
                                        <?php echo lang('date'); ?>
                                    </label>

                                    <div class="input-group">
                                        <input name="quote_date_created" id="quote_date_created"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($quote->quote_date_created); ?>">
		                                <span class="input-group-addon">
		                                    <i class="fa fa-calendar fa-fw"></i>
		                                </span>
                                    </div>
                                </div>

                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_expires">
                                        <?php echo lang('expires'); ?>
                                    </label>

                                    <div class="input-group">
                                        <input name="quote_date_expires" id="quote_date_expires"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($quote->quote_date_expires); ?>">
		                              <span class="input-group-addon">
		                                  <i class="fa fa-calendar fa-fw"></i>
		                              </span>
                                    </div>
                                </div>

                            </div>

                            <div class="col-xs-12 col-sm-4">

                                <div class="quote-properties">
                                    <label for="quote_status_id">
                                        <?php echo lang('status'); ?>
                                    </label>
                                    <select name="quote_status_id" id="quote_status_id"
                                            class="form-control input-sm">
                                        <?php foreach ($quote_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="quote-properties">
                                    <label for="salesperson_id">
                                        <?php echo lang('quote_salesperson'); ?>
                                    </label>
                                    <select name="salesperson_id" id="salesperson_id"
                                            class="form-control input-sm">
                                        <?php foreach ($salespersons as $key => $salesperson) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $quote->salesperson_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $salesperson['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="quote-properties">
                                    <label for="techperson_id">
                                        <?php echo lang('quote_techperson'); ?>
                                    </label>

                                    <select name="techperson_id" id="techperson_id"
                                            class="form-control input-sm">
                                        <?php foreach ($techpersons as $key => $techperson) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $quote->techperson_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $techperson['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="quote-properties">
                                    <!--
                                    <label for="quote_password">
                                        <?php echo lang('quote_password'); ?>
                                    </label>
                                    -->

                                    <div class="controls">
                                        <input type="hidden" id="quote_password" class="form-control input-sm"
                                               value="<?php echo $quote->quote_password; ?>">
                                         <input type="hidden" name="quoteref_id" id="quoteref_id" class="form-control input-sm"
                                               value="<?php echo $quote->quoteref_id; ?>">
                                    </div>
                                </div>

                            </div>
                           <div class="col-xs-12 col-sm-4">
                                <div class="quote-properties">
                                    <label for="template_id">
                                        <?php echo lang('quote_template'); ?>
                                    </label>
                                    <select name="template_id" id="template_id"
                                            class="form-control input-sm">
                                        <?php foreach ($templates as $key => $template) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $quote->template_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $template['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>  
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php $this->layout->load_view('quotes/partial_item_table'); ?>

        <hr/>

        <div class="row">
            <div class="col-xs-12 col-sm-4">

                <div class="form-group">
                    <label class="control-label"><?php echo lang('notes'); ?></label>
                    <textarea name="notes" id="notes" rows="3"
                              class="input-sm form-control"><?php echo $quote->notes; ?></textarea>
                </div>

            </div>
            <div class="col-xs-12 col-sm-8">

                <div class="form-group">
                    <label class="control-label"><?php echo lang('attachments'); ?></label>
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

            </div>
        </div>

        <?php if ($custom_fields): ?>
            <h4 class="no-margin"><?php echo lang('custom_fields'); ?></h4>
        <?php endif; ?>
        <?php foreach ($custom_fields as $custom_field) { ?>
            <label class="control-label">
                <?php echo $custom_field->custom_field_label; ?>
            </label>
            <input type="text" class="form-control"
                   name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                   id="<?php echo $custom_field->custom_field_column; ?>"
                   value="<?php echo form_prep($this->mdl_quotes->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
        <?php } ?>

        <?php if ($quote->quote_status_id != 1) { ?>
            <p class="padded">
                <?php echo lang('guest_url'); ?>:
                <?php echo auto_link(site_url('guest/view/quote/' . $quote->quote_url_key)); ?>
            </p>
        <?php } ?>
</div>
</form>

<script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/' . $quote->client_id. '/'.$quote->quote_url_key) ?>", // Set the url
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
            $.getJSON("<?php echo site_url('upload/upload_file/' . $quote->client_id. '/'.$quote->quote_url_key) ?>", function (data) {
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
            url: "<?php echo site_url('upload/delete_file/'.$quote->quote_url_key) ?>",
            type: "POST",
            data: {'name': file.name}
        });
    });
</script>
