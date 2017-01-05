<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">
        <style>
            <?php //---it---inizio ?>
         	<?php if (!empty($preview_pdf)): ?>
         		html, body {
				  overflow-y: visible;
				}
         	<?php endif; ?>
        	<?php //---it---fine ?>
            .color-l { color: #c3d3ed; }
            .color-n { color: #7ea9d6; }
            .color-d { color: #0178bc; }
            .border-bottom-l {  border-color: #c3d3ed;  }
            .border-bottom-n {  border-color: #7ea9d6;  }
            .border-bottom-d {  border-color: #0178bc;  }
            .border-top-l {  border-color: #c3d3ed;  }
            .border-top-n {  border-color: #7ea9d6;  }
            .border-top-d {  border-color: #0178bc;  }
            .background-l { background-color: #eaf0f9; }
            .company-name,
            .quote-id {
                color: #0178bc !important;
            }
            .person_image{
                width: 50px;
                height: 50px
            }
            .td_person_image{
                width: 60px;
                height: 60px
            }
            .person_title{
                display:block;
                font-weight:bold;
            }
            .person_detail{
                display:block;
            }
            
            @page {
                margin-top: 0.84cm;
                margin-bottom: 0.84cm;
                margin-left: 1.14cm;
                margin-right: 0.84cm;
            }
            div.mainlistspace{
                font-size: 30px;
            }
            #bottom { 
                display: block; 
                position: fixed; 
                right: 0mm; 
                bottom: 0mm; 
            }
            td table {
                /*border: 1px solid red;  */
                width: 1000px;
            }   
            td {
                vertical-align: top;
            } 
            .td_date{
                width: 10mm;
            }
            td.description{
                width: 300px;
            }
            td.articolo{
                width: 200px;
            }
            .salesperson td, .techperson td{
                font-size: 10px;
            }
        </style>        
    </head>
    <body>
        <table>
            <tr>
                <td>                    
                    <table>
                        <tr>
                            <th colspan="2">
                                <h2 class="quote-id"><?php echo lang('quote'); ?> <?php echo $quote->quote_number; ?></h2>
                            </th>
                        </tr>
                        <tr>
                            <td style="width:30%;">
                                <div class="invoice-to">
                                    <p><?php echo lang('quote_to'); ?>:</p>
                                    <p><b><?php echo $quote->client_name; ?></b>
                                        <?php if ($quote->client_vat_id) {
                                            echo  '<br/>'.lang('vat_id_short') . ': ' . $quote->client_vat_id ;
                                        } ?>
                                        <?php if ($quote->client_tax_code) {
                                            echo '<br/>'.lang('tax_code_short') . ': ' . $quote->client_tax_code;
                                        } ?>
                                        <?php if ($quote->client_address_1) {
                                            echo '<br/>'.$quote->client_address_1 ;
                                        } ?>
                                        <?php if ($quote->client_address_2) {
                                            echo '<br/>'.$quote->client_address_2 ;
                                        } ?>
                                        <?php if ($quote->client_city) {
                                            echo '<br/>'.$quote->client_city . ' ';
                                        } ?>
                                        <?php if ($quote->client_zip) {
                                            echo $quote->client_zip . ' ';
                                        } ?>
                                        <?php if ($quote->client_state) {
                                            echo $quote->client_state ;
                                        } ?>
                                        <?php if ($quote->client_phone) { ?>
                                            <?php echo '<br/>'.lang('phone_abbr'); ?>: <?php echo $quote->client_phone; ?>
                                        <?php } ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="invoice-to">
                                    <p><?php echo lang('quote_where'); ?>:</p>
                                    <p><b><?php echo $quote->client_name; ?></b>
                                        <?php if ($quote->client_address_1) {
                                            echo '<br/>'.$quote->client_address_1 ;
                                        } ?>
                                        <?php if ($quote->client_city) {
                                            echo '<br/>'.$quote->client_city . ' ';
                                        } ?>
                                        <?php if ($quote->client_zip) {
                                            echo $quote->client_zip . ' ';
                                        } ?>
                                        <?php if ($quote->client_state) {
                                            echo $quote->client_state ;
                                        } ?>
                                        <?php if ($quote->client_phone) { ?>
                                            <?php echo '<br/>'.lang('phone_abbr'); ?>: <?php echo $quote->client_phone; ?>
                                        <?php } ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="invoice-details">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="text-right color-n">
                                                    <?php echo lang('quote_date'); ?>: &nbsp;
                                                </td>
                                                <td class="text-right color-n  td_date">
                                                    <?php echo date_from_mysql($quote->quote_date_created, TRUE); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right color-n">
                                                    <?php echo lang('expires'); ?>: &nbsp;
                                                </td>
                                                <td class="text-right color-n td_date">
                                                    <?php echo date_from_mysql($quote->quote_date_expires, TRUE); ?>
                                                </td>
                                            </tr>
                                            <!--
                                            <tr>
                                                <td class="text-right color-n">
                                                    <?php echo lang('total'); ?>: &nbsp;
                                                </td>
                                                <td class="text-right color-n">
                                                    <?php echo format_currency($quote->quote_total); ?>
                                                </td>
                                            </tr>
                                            -->
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>  
                    <div class="mainlistspace">&nbsp;</div>
                    <table class="table-striped table">
                        <thead>
                            <tr class="border-bottom-d">
                                <th class="color-d"><?php echo lang('item'); ?></th>
                                <th class="color-d description"><?php echo lang('description'); ?></th>
                                <th class="text-right color-d"><?php echo lang('qty'); ?></th>
                                <th class="text-right color-d"><?php echo lang('price'); ?></th>
                                <th class="text-right color-d"><?php echo lang('total'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $linecounter = 0;
                            foreach ($items as $item) { ?>
                                <tr class="border-bottom-n <?php echo ($linecounter % 2 ? 'background-l' : '')?>">
                                    <td class="articolo"><?php echo $item->item_name; ?></td>
                                    <td class="description"><?php echo nl2br($item->item_description); ?></td>
                                    <td class="text-right">
                                        <?php echo format_amount($item->item_quantity); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php //echo format_currency($item->item_price); ?>
                                        <?php echo $item->item_price; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php //echo format_currency($item->item_subtotal);?>
                                        <?php echo $item->item_subtotal; ?>
                                    </td>
                                </tr>
                                <?php $linecounter++; ?>
                            <?php } ?>

                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td class="text-right">
                                <table class="amount-summary">
                                    <tr>
                                        <td class="text-right color-n">
                                            <?php echo lang('subtotal'); ?>:
                                        </td>
                                        <td class="text-right color-n">
                                            <?php echo format_currency($quote->quote_item_subtotal); ?>
                                        </td>
                                    </tr>
                                    <?php if ($quote->quote_item_tax_total > 0) { ?>
                                    <tr>
                                        <td class="text-right color-n">
                                            <?php echo lang('item_tax'); ?>:
                                        </td>
                                        <td class="text-right color-n">
                                            <?php echo format_currency($quote->quote_item_tax_total); ?>
                                        </td>
                                    </tr>
                                    <?php } ?>

                                    <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
                                        <tr>    
                                            <td class="text-right color-n">
                                                <?php echo $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>%
                                            </td>
                                            <td class="text-right color-n">
                                                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>

                                    <tr class="amount-total border-top-n">
                                        <td class="text-right color-d">
                                            <b><?php echo lang('total'); ?>:</b>
                                        </td>
                                        <td class="text-right color-d">
                                            <b><?php echo format_currency($quote->quote_total); ?></b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>                    
                </td>
                <td class="text-right" class="company-details" style="width:7cm;">                        
                    <?php echo invoice_logo_pdf(); ?>
                    <div class="company-name text-right">
                        <?php echo $quote->user_company; ?>
                    </div>
                    <p class="text-right">
                        <?php if ($quote->user_vat_id) {
                            echo lang('vat_id_short') . ': ' . $quote->user_vat_id;
                        } ?>
                        <?php if ($quote->user_tax_code) {
                            echo '<br/>'.lang('tax_code_short') . ': ' . $quote->user_tax_code;
                        } ?>
                        <?php if ($quote->user_address_1) {
                            echo '<br/>'.$quote->user_address_1;
                        }?>
                        <?php if ($quote->user_address_2) {
                            echo '<br/>'.$quote->user_address_2;
                        } ?>
                        <?php if ($quote->user_city) {
                            echo '<br/>'.$quote->user_city . ' ';
                        } ?>

                        <?php if ($quote->user_zip) {
                            echo $quote->user_zip.' ';
                        } ?>
                        <?php if ($quote->user_state) {
                            echo $quote->user_state;
                        } ?>
                        <?php if ($quote->user_phone) {
                            ?><?php echo '<br/>'.lang('phone_abbr'); ?>: <?php echo $quote->user_phone; ?><?php
                        } ?>
                        <?php if ($quote->user_fax) {
                            ?><?php echo '<br/>'.lang('fax_abbr'); ?>: <?php echo $quote->user_fax; ?><?php
                        } ?>
                    </p>                     
                </td>
            </tr>
        </table>  
        <div id="bottom">
            <?php if((count($salesperson) + count($techperson)) > 0){ ?>
                <table class="salesperson">
                    <tr class="border-bottom-d">                       
                        <?php if(count($salesperson) > 0){ ?>
                        <td>
                            <table>
                                <tr>
                                    <th colspan="2">
                                        <?php echo '<div class="person_title">'.lang('quote_salesperson'). '</div>';?>                                        
                                    </th>
                                </tr>
                                <tr>
                                    <td class="td_person_image">
                                        <?php 
                                        if ($salesperson[0]->file_name_new){
                                            echo '<img class="person_image" src="'. getcwd() . '/uploads/customer_files/'.$salesperson[0]->file_name_new.'" />';
                                        }
                                        ?>                                        
                                    </td>
                                    <td>
                                        <?php if ($salesperson[0]->person_name) {
                                            echo '<div class="person_detail person_name">'.$salesperson[0]->person_name. '</div>';
                                        } ?>
                                        <?php if ($salesperson[0]->person_cell) {
                                            echo '<div class="person_detail person_cell">'.$salesperson[0]->person_cell. '</div>';
                                        } ?>
                                        <?php if ($salesperson[0]->person_email) {
                                            echo '<div class="person_detail person_email">'.$salesperson[0]->person_email. '</div>';
                                        } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>        
                        <?php } ?>
                        <?php if(count($techperson) > 0){ ?>
                        <td>
                        <table class="techperson">
                            <tr>
                                <th colspan="2">
                                    <?php echo '<div class="person_title">'.lang('quote_techperson'). '</div>';?>                                        
                                </th>
                            </tr>
                            <tr>
                                <td class="td_person_image">
                                    <?php 
                                    if ($techperson[0]->file_name_new){
                                        echo '<img class="person_image" src="'. getcwd() . '/uploads/customer_files/'.$techperson[0]->file_name_new.'" />';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($techperson[0]->person_name) {
                                        echo '<div class="person_detail person_name">'.$techperson[0]->person_name. '</div>';
                                    } ?>
                                    <?php if ($techperson[0]->person_cell) {
                                        echo '<div class="person_detail person_cell">'.$techperson[0]->person_cell. '</div>';
                                    } ?>
                                    <?php if ($techperson[0]->person_email) {
                                        echo '<div class="person_detail person_email">'.$techperson[0]->person_email. '</div>';
                                    } ?>
                                </td>
                            </tr>
                        </table>
                    </td>                    
                    <?php } ?>  
                    <td style="width: 30%;">
                        &nbsp;
                    </td>
                </tr>
            </table>
            <?php } ?>
        </div>
    </body>
</html>
