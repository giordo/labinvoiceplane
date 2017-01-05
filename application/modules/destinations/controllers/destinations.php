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

class Destinations extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_destinations');
    }

    public function index($page = 0)
    {
        $this->mdl_destinations->paginate(site_url('destinations/index'), $page);
        $destinations = $this->mdl_destinations->result();

        $this->layout->set('destinations', $destinations);
        $this->layout->buffer('content', 'destinations/index');
        $this->layout->render();
    }

    public function form($id = NULL)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('destinations');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('destination_name') != '') {
            $check = $this->db->get_where('ip_destinations', array('destination_name' => $this->input->post('destination_name')))->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('destination_already_exists'));
                redirect('destinations/form');
            }
        }

       if ($this->mdl_destinations->run_validation()) {
            $newid = $this->mdl_destinations->save($id);
            if(($this->mdl_destinations->form_value('halfstep')))
            {
                redirect('destinations/form/'.$newid);             
            }
            if(($this->mdl_destinations->form_value('btn_submit')))
            {
                redirect('destinations');             
            }
        }        
        
        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_destinations->prep_form($id)) {
                show_404();
            }
            $this->mdl_destinations->set_form_value('is_update', true);
        }

        $this->layout->buffer('content', 'destinations/form');
        $this->layout->render();
    }

    public function delete($id, $url_key = "")
    {
        $this->mdl_destinations->delete($id);
        // Set target invoice to credit invoice
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('upload/mdl_uploads');
        
        $this->mdl_quotes->where('salesdestination_id', $id);
        $this->mdl_quotes->update('ip_quotes', array('salesdestination_id' => 0));

        $this->mdl_quotes->where('techdestination_id', $id);
        $this->mdl_quotes->update('ip_quotes', array('techdestination_id' => 0));
        
        $this->mdl_uploads->delete_by_key($url_key);

        redirect('destinations');
    }

     public function view($quote_id)
    {
         echo 'destinations.php/view';
     }
}
