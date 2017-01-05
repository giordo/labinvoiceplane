<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('add_destination'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">
        
        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_destinations->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
            >
 <fieldset>  
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="destination_name" class="control-label">
                    <?php echo lang('destination_name'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="destination_name" id="destination_name" class="form-control"
                       value="<?php echo $this->mdl_destinations->form_value('destination_name'); ?>">
            </div>
        </div>
        
        
     
     
     
        <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="destination_address_1" class="control-label">
                    <?php echo lang('destination_address_1'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="destination_address_1" id="destination_address_1" class="form-control"
                       value="<?php echo $this->mdl_destinations->form_value('destination_address_1'); ?>">
            </div>
        </div>
        <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="destination_city" class="control-label">
                    <?php echo lang('destination_city'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="destination_city" id="destination_city" class="form-control"
                       value="<?php echo $this->mdl_destinations->form_value('destination_city'); ?>">
            </div>
        </div>
     <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="destination_state" class="control-label">
                    <?php echo lang('destination_state'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="destination_state" id="destination_state" class="form-control"
                       value="<?php echo $this->mdl_destinations->form_value('destination_state'); ?>">
            </div>
        </div>
        
        <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="destination_contact" class="control-label">
                    <?php echo lang('destination_contact'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="destination_contact" id="destination_contact" class="form-control"
                       value="<?php echo $this->mdl_destinations->form_value('destination_contact'); ?>">
            </div>
        </div>
       <div class="form-group">        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="destination_active" class="control-label">
                    <?php echo lang('destination_active'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="checkbox" name="destination_active" id="destination_active" class="form-control"
                       value="<?php echo $this->mdl_destinations->form_value('destination_active'); ?>">
            </div>
        </div>
    </fieldset>                                                  
    </div>

</form>
