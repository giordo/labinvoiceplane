<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Mdl_Destinations extends Response_Model
{
    public $table = 'ip_destinations';
    public $primary_key = 'ip_destinations.destination_id';
    public $date_created_field = 'destination_date_created';
    

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }

    public function alldestinations($onlyactive = true)
    {
        $this->load->model('destinations/mdl_destinations');

        $destination_items = $this->mdl_destinations->get()->result();
        $ret[0] = array('label' => lang('destination_default'), 'value'=> 0);
        foreach ($destination_items as $destination_item) {
            $ret[$destination_item->destination_id] = array(
                'label' => $destination_item->destination_name,
                'value' => $destination_item->destination_id,
                );
            $i++;     
        }
        return $ret;
    }
    
    public function create($db_array = NULL){
        $destination_id = parent::save(NULL, $db_array);
        return $destination_id;
    }
    
    
    public function default_order_by()
    {
        $this->db->order_by('ip_destinations.destination_date_created');
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_destinations.destination_client_id', $client_id);
        return $this;
    }
    
    public function validation_rules()
    {
        return array(
            'destination_name' => array(
                'field' => 'destination_name',
                'label' => lang('destination_name'),
                'rules' => 'required'
            ),
            'destination_date_created' => array(
                'field' => 'destination_date_created',
                'label' => lang('destination_date_created'),
                'rules' => ''
            ),
            'destination_address_1' => array(
                'field' => 'destination_address_1',
                'label' => lang('destination_address_1'),
                'rules' => ''
            ),
            'destination_city' => array(
                'field' => 'destination_city',
                'label' => lang('destination_city'),
                'rules' => ''
            ),
            'destination_state' => array(
                'field' => 'destination_state',
                'label' => lang('destination_state'),
                'rules' => ''
            ),
            'destination_contact' => array(
                'field' => 'destination_contact',
                'label' => lang('destination_contact'),
                'rules' => ''
            ),
            'destination_active' => array(
                'field' => 'destination_active',
                'label' => lang('destination_active'),
                'rules' => ''
            ),
            'destination_client_id' => array(
                'field' => 'destination_client_id'
            )
        );
    }
}
