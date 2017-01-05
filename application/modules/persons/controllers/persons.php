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

class Persons extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_persons');
    }

    public function index($page = 0)
    {
        $this->mdl_persons->paginate(site_url('persons/index'), $page);
        $persons = $this->mdl_persons->result();

        $this->layout->set('persons', $persons);
        $this->layout->buffer('content', 'persons/index');
        $this->layout->render();
    }

    public function form($id = NULL)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('persons');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('person_name') != '') {
            $check = $this->db->get_where('ip_persons', array('person_name' => $this->input->post('person_name')))->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('person_already_exists'));
                redirect('persons/form');
            }
        }

       if ($this->mdl_persons->run_validation()) {
            $newid = $this->mdl_persons->save($id);
            if(($this->mdl_persons->form_value('halfstep')))
            {
                redirect('persons/form/'.$newid);             
            }
            if(($this->mdl_persons->form_value('btn_submit')))
            {
                redirect('persons');             
            }
        }
        /*
        if (!$this->mdl_persons->prep_form($id)) {
            show_404();
        }
                /*
        if ($this->mdl_persons->run_validation()) {
            $this->mdl_persons->save($id);
            redirect('persons');
        }
        */
        
        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_persons->prep_form($id)) {
                show_404();
            }
            $this->mdl_persons->set_form_value('is_update', true);
        }

        $this->layout->set(
            array(
                 'puk' => (!is_null($id))? $this->mdl_persons->form_value('person_key') : $this->mdl_persons->get_url_key(),                
            )
        );
        $this->layout->buffer('content', 'persons/form');
        $this->layout->render();
    }

    public function delete($id, $url_key = "")
    {
        $this->mdl_persons->delete($id);
        // Set target invoice to credit invoice
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('upload/mdl_uploads');
        
        $this->mdl_quotes->where('salesperson_id', $id);
        $this->mdl_quotes->update('ip_quotes', array('salesperson_id' => 0));

        $this->mdl_quotes->where('techperson_id', $id);
        $this->mdl_quotes->update('ip_quotes', array('techperson_id' => 0));
        
        $this->mdl_uploads->delete_by_key($url_key);

        redirect('persons');
    }

}
