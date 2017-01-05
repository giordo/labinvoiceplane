<div ng-controller="AppCtrl">
    <uib-tabset active="tmp.tabSelected">
        <!-- <uib-tab index="$index" ng-repeat="tab in tabs" disable="tab.disabled" > -->
        <!-- $scope.tmpYesNo.idx -->
        <uib-tab ng-repeat="tab in tabs" disable="tab.disabled" select="getCurrentTab($event);">
            <uib-tab-heading>
                {{tab.title}}
                <span class="close-heading glyphicon glyphicon-remove-sign" aria-hidden="true" ng-click="tabRemoveConfirm($index, $event)"></span>
            </uib-tab-heading>
            <br/>
            <input type="index" ng-model="tab.title"/>
            <br/>
            {{tmp.tabSelected}}
            <br/>
            <button type="button" class="btn btn-default" ng-click="openModalItem('lg', false);">Add item</button>
            <div>Total price: {{tab.items|sumByObjKey:'total_row_price'}}</div>
            <ul>
                <li ng-repeat="o in tab.items track by $index">
                    <!-- <li ng-repeat="o in tabs[tmp.tabSelected].items track by tab.tabCounter"> -->
                    {{ o.qty }} - {{ o.label }} - {{o.total_price}} - {{o.total_row_price}}
                    <!--<button type="button" class="btn btn-default" ng-click="openModalDetail('lg', $index, tab.tabCounter)">{{o.code}}</button> -->
					<button type="button" class="btn btn-default" ng-click="openModalDetail('lg', $index, tmp.tabSelected)">{{o.code}}</button>
                    <!-- <button type="button" class="btn btn-default" ng-click="openModalDetail('lg', tab.tabCounter)">{{o.code}}</button> -->

                </li>
            </ul>
        </uib-tab>
        <uib-tab-button handler="addTab();" text="+"></uib-tab-button>
    </uib-tabset>

</div>
<!-- giordo0
<div>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Tende da sole</a> <span class="ui-icon ui-icon-print" role="presentation" data-toggle="dropdown">Open</span>
                <input type="hidden" name="hdn-tab[]" id="hid-tab-#{uid}" value="#{uid}"/>
                <ul class="dropdown-menu">
                    <li class="checkbox"><label>&nbsp;<input type="checkbox" name="chk-prt[]" id="chk-prt-1" value="1" checked="checked">Stampa</label></li>
                    <li class="checkbox"><label>&nbsp;<input type="checkbox" name="chk-bkp[]" id="chk-bkp-1" value="1" checked="checked">Interruzione di pagina</label></li>
                    <li class="checkbox"><label>&nbsp;<input type="checkbox" name="chk-sum[]" id="chk-sum-1" value="1">Escludi dal totale</label></li>
                    <li class="checkbox chk-delete" id="chk-del-1"><label>&nbsp;Cancella</label></li>
                </ul>
            </li>
            <li class="tabsplus locked"><a href="#tabs-2">+</a></li>
            <li class="tabs99 locked"><a href="#tabs-99">Condizioni</a> <span class="ui-icon ui-icon-print" role="presentation" data-toggle="dropdown">Open</span>
                <ul class="dropdown-menu">
                    <li class="checkbox"><label>&nbsp;<input type="checkbox" name="chk-prt[]" id="chk-prt-99" value="99" checked="checked">Stampa</label></li>
                    <li class="checkbox chk-delete" id="chk-del-1"><label>&nbsp;Cancella</label></li>
                </ul>
            </li>
        </ul>
        <div id="tabs-1">
            <table class="tbl-sortable">
                <tbody>
                <tr class="tr-sortable">
                    <td>COL</td>
                    <td>Articolo</td>
                    <td>Descrizione</td>
                    <td>Quantità</td>
                    <td>Prezzo</td>
                    <td>Sc-1</td>
                    <td>Sc-2</td>
                    <td>Iva</td>
                    <td>Riga</td>
                </tr>
                <tr class="tr-sortable">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="tr-sortable">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="tr-sortable">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="tabs-2">
            <p>plus.</p>
        </div>
        <div id="tabs-99" style="float:right;">
            <p>Proin elit arProin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.cu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
        </div>
    </div>
</div>
-->
<!-- <!-- giordo1
<div class="table-responsive">
    <table id="item_table" class="items table table-condensed table-bordered">
        <thead style="display: none">
        <tr>
            <th></th>
            <th><?php echo lang('item'); ?></th>
            <th><?php echo lang('description'); ?></th>
            <th><?php echo lang('quantity'); ?></th>
            <th><?php echo lang('price'); ?></th>
            <th><?php echo lang('tax_rate'); ?></th>
            <th><?php echo lang('subtotal'); ?></th>
            <th><?php echo lang('tax'); ?></th>
            <th><?php echo lang('total'); ?></th>
            <th></th>
        </tr>
        </thead>

        <tbody id="new_row" style="display: none;">
        <tr>
            <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
            <td class="td-text">
                <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                <input type="hidden" name="item_id" value="">

                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('item'); ?></span>
                    <input type="text" name="item_name" class="input-sm form-control" value="">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                    <input type="text" name="item_quantity" class="input-sm form-control amount" value="">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('price'); ?></span>
                    <input type="text" name="item_price" class="input-sm form-control amount" value="">
                </div>
            </td>
            <td class="td-amount ">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                    <input type="text" name="item_discount_amount" class="input-sm form-control amount"
                           value="" data-toggle="tooltip" data-placement="bottom"
                           title="<?php echo $this->mdl_settings->setting('currency_symbol') . ' ' . lang('per_item'); ?>">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('tax_rate'); ?></span>
                    <select name="item_tax_rate_id" name="item_tax_rate_id"
                            class="form-control input-sm">
                        <option value="0"><?php echo lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>">
                                <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </td>
            <td class="td-icon text-right td-vert-middle"></td>
        </tr>
        <tr>
            <td class="td-textarea">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('description'); ?></span>
                    <textarea name="item_description" class="input-sm form-control"></textarea>
                </div>
            </td>
            <td colspan="2" class="td-amount td-vert-middle">
                <span><?php echo lang('subtotal'); ?></span><br/>
                <span name="subtotal" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?php echo lang('discount'); ?></span><br/>
                <span name="item_discount_total" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?php echo lang('tax'); ?></span><br/>
                <span name="item_tax_total" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?php echo lang('total'); ?></span><br/>
                <span name="item_total" class="amount"></span>
            </td>
        </tr>
        </tbody>

        <?php foreach ($items as $item) { ?>
            <tbody class="item">
            <tr>
                <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
                <td class="td-text">
                    <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>">

                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item'); ?></span>
                        <input type="text" name="item_name" class="input-sm form-control"
                               value="<?php echo html_escape($item->item_name); ?>">
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                        <input type="text" name="item_quantity" class="input-sm form-control amount"
                               value="<?php echo format_amount($item->item_quantity); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('price'); ?></span>
                        <input type="text" name="item_price" class="input-sm form-control amount"
                               value="<?php echo format_amount($item->item_price); ?>">
                    </div>
                </td>
                <td class="td-amount ">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                        <input type="text" name="item_discount_amount" class="input-sm form-control amount"
                               value="<?php echo format_amount($item->item_discount_amount); ?>"
                               data-toggle="tooltip" data-placement="bottom"
                               title="<?php echo $this->mdl_settings->setting('currency_symbol') . ' ' . lang('per_item'); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('tax_rate'); ?></span>
                        <select name="item_tax_rate_id" name="item_tax_rate_id"
                                class="form-control input-sm">
                            <option value="0"><?php echo lang('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                        <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>>
                                    <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle">
                    <a href="<?php echo site_url('quotes/delete_item/' . $quote->quote_id . '/' . $item->item_id); ?>"
                       title="<?php echo lang('delete'); ?>">
                        <i class="fa fa-trash-o text-danger"></i>
                    </a>
                </td>
            </tr>
            <tr>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('description'); ?></span>
                        <textarea name="item_description"
                                  class="input-sm form-control"><?php echo $item->item_description; ?></textarea>
                    </div>
                </td>

                <td colspan="2" class="td-amount td-vert-middle">
                    <span><?php echo lang('subtotal'); ?></span><br/>
                    <span name="subtotal" class="amount">
                        <?php echo format_currency($item->item_subtotal); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('discount'); ?></span><br/>
                    <span name="item_discount_total" class="amount">
                        <?php echo format_currency($item->item_discount); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('tax'); ?></span><br/>
                    <span name="item_tax_total" class="amount">
                        <?php echo format_currency($item->item_tax_total); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('total'); ?></span><br/>
                    <span name="item_total" class="amount">
                        <?php echo format_currency($item->item_total); ?>
                    </span>
                </td>
            </tr>
            </tbody>
        <?php } ?>
    </table>
</div>

-->
<!-- giordo2
<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="btn-group">
            <a href="#" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i>
                <?php echo lang('add_new_row'); ?>
            </a>
            <a href="#" class="btn_add_product btn btn-sm btn-default">
                <i class="fa fa-database"></i>
                <?php echo lang('add_product'); ?>
            </a>
        </div>
        <br/><br/>
    </div>
    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-condensed text-right">
            <tr>
                <td style="width: 40%;"><?php echo lang('subtotal'); ?></td>
                <td style="width: 60%;" class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('item_tax'); ?></td>
                <td class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('quote_tax'); ?></td>
                <td>
                    <?php if ($quote_tax_rates) {
                        foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                            <span class="text-muted">
                            <?php echo anchor('quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quote_tax_rate->quote_tax_rate_id, '<i class="fa fa-trash-o"></i>');
                            echo ' ' . $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>
                                %</span>&nbsp;
                            <span class="amount">
                                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                            </span>
                        <?php }
                    } else {
                        echo format_currency('0');
                    } ?>
                </td>
            </tr>
            <tr>
                <td class="td-vert-middle"><?php echo lang('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_amount" name="quote_discount_amount"
                                   class="discount-option form-control input-sm amount"
                                   value="<?php echo($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : ''); ?>">

                            <div
                                class="input-group-addon"><?php echo $this->mdl_settings->setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_percent" name="quote_discount_percent"
                                   value="<?php echo($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount">

                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><b><?php echo lang('total'); ?></b></td>
                <td class="amount"><b><?php echo format_currency($quote->quote_total); ?></b></td>
            </tr>
        </table>
    </div>
</div>
-->