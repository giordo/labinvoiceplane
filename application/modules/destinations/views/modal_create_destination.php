<script type="text/javascript">
    $(function () {
        // Display the create quote modal
        $('#create-destination').modal('show');

        $('#create-destination').on('shown', function () {
            $("#destination_name").focus();
        });        

        // Creates the quote
        $('#destination_create_confirm').click(function () {
            console.log('clicked');
            // Posts the data to validate and create the quote;
            // will create the new client if necessary
            $.post("<?php echo site_url('destinations/ajax/create'); ?>", {
                    destination_client_id: $('#client_id').val(),                    
                    destination_name: $('#destination_name').val(),
                    destination_address_1: $('#destination_address_1').val(),
                    destination_city: $('#destination_city').val(),
                    destination_state: $('#destination_state').val(),
                    destination_contact: $('#destination_contact').val(),                    
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful and quote was created
                        window.location = "<?php echo site_url('destinations/form'); ?>/" + response.destination_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });

</script>

<div id="create-destination" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_create_destination" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('create_destination'); ?></h3>
        </div>
        <div class="modal-body">
           <div class="input-group col-xs-6">
              <span class="input-group-addon">
                <?php echo lang('active_client'); ?>: 
                <input id="destination_active" name="destination_active" type="checkbox" value="1"
                    <?php if ($this->mdl_clients->form_value('destination_active') == 1
                        or !is_numeric($this->mdl_clients->form_value('destination_active'))
                    ) {
                        echo 'checked="checked"';
                    } ?>
                    >
              </span>
                <input id="destination_name" name="destination_name" type="text" class="form-control"
                       placeholder="<?php echo lang('destination_name'); ?>"
                       value="<?php echo $this->mdl_clients->form_value('destination_name'); ?>">
            </div>            
            
            <div class="form-group">
                <label for="destination_address_1"><?php echo lang('destination_address_1'); ?></label>
                <input type="text" name="destination_address_1" id="destination_address_1" class="form-control"
                    <?php if ($destination_address_1) echo 'value="' . html_escape($destination_address_1) . '"'; ?>>
            </div>
            <div class="form-group">
                <label for="destination_city"><?php echo lang('destination_city'); ?></label>
                <input type="text" name="destination_city" id="destination_" class="form-control"                       
                    <?php if ($destination_city) echo 'value="' . html_escape($destination_city) . '"'; ?>>
            </div>
            <div class="form-group">
                <label for="destination_state"><?php echo lang('destination_state'); ?></label>
                <input type="text" name="destination_state" id="destination_" class="form-control"                       
                    <?php if ($destination_state) echo 'value="' . html_escape($destination_state) . '"'; ?>>
            </div>
            <div class="form-group">
                <label for="destination_contact"><?php echo lang('destination_contact'); ?></label>
                <input type="text" name="destination_contact" id="destination_" class="form-control"                       
                    <?php if ($destination_contact) echo 'value="' . html_escape($destination_contact) . '"'; ?>>
            </div>                    
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success ajax-loader" id="destination_create_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
