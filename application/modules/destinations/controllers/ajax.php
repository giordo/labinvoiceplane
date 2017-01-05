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

class Ajax extends Admin_Controller
{
    public $ajax_controller = TRUE;

    public function save()
    {
        $this->load->model('destinations/mdl_destinations');
        $this->load->library('encrypt');

        $destination_id = $this->input->post('destination_id');

        $this->mdl_destinations->set_id($destination_id);

        if ($this->mdl_destinations->run_validation('validation_rules')) {
                        
            $this->mdl_destinations->save($destination_id, $db_array);
            
            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }       

        echo json_encode($response);
    }


    public function create()
    {
        $this->load->model('destinations/mdl_destinations');

        if ($this->mdl_destinations->run_validation()) {
            $destination_id = $this->mdl_destinations->create();

            $response = array(
                'success' => 1,
                'destination_id' => $destination_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }
        
    public function modal_create_destination()
    {
        $this->load->module('layout');

        $this->load->model('clients/mdl_clients');

        $data = array();

        $this->layout->load_view('destinations/modal_create_destination', $data);
    }

}
