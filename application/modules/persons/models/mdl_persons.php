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

class Mdl_Persons extends Response_Model
{
    public $table = 'ip_persons';
    public $primary_key = 'ip_persons.person_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }

    public function allpersons($onlyactive = true)
    {
        $this->load->model('persons/mdl_persons');

        $person_items = $this->mdl_persons->get()->result();
        $ret[0] = array('label' => lang('notdefined'), 'value'=> 0);
        foreach ($person_items as $person_item) {
            $ret[$person_item->person_id] = array(
                'label' => $person_item->person_name,
                'value' => $person_item->person_id,
                );
            $i++;     
        }
        return $ret;
    }
    
    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }
    
    public function default_order_by()
    {
        $this->db->order_by('ip_persons.person_name');
    }

    public function validation_rules()
    {
        return array(
            'person_name' => array(
                'field' => 'person_name',
                'label' => lang('person_name'),
                'rules' => 'required'
            ),
            'person_cell' => array(
                'field' => 'person_cell',
                'label' => lang('person_cell'),
                'rules' => 'required'
            ),
            'person_email' => array(
                'field' => 'person_email',
                'label' => lang('person_email'),
                'rules' => 'required|valid_email'
            ),
            'person_key' => array(
                'field' => 'person_key',
                'label' => lang('person_key'),
                'rules' => 'required'
            ),
            'person_active' => array(
                'field' => 'person_active',
                'label' => lang('person_active'),
                'rules' => ''
            )
        );
    }

}
