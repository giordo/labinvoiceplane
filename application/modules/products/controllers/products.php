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

class Products extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_products');
    }

    public function index($page = 0)
    {
        $this->mdl_products->paginate(site_url('products/index'), $page);
        $products = $this->mdl_products->result();
        
        $this->layout->set('products', $products);
        $this->layout->buffer('content', 'products/index');
        $this->layout->render();
    }

    public function form($id = NULL)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('products');
        }
        if ($this->mdl_products->run_validation()) {
            $newid = $this->mdl_products->save($id);
            if(($this->mdl_products->form_value('halfstep')))
            {
                redirect('products/form/'.$newid);             
            }
            if(($this->mdl_products->form_value('btn_submit')))
            {
                redirect('products');             
            }
        }
        
        if (!$this->mdl_products->prep_form($id)) {
            show_404();
        }
         
        $this->load->model('families/mdl_families');
        $this->load->model('tax_rates/mdl_tax_rates');

        $this->layout->set(
            array(
                'families' => $this->mdl_families->get()->result(),
                'tax_rates' => $this->mdl_tax_rates->get()->result(), 
                'puk' => (!is_null($id))? $this->mdl_products->form_value('product_key') : $this->mdl_products->get_url_key(),
                //'saved' => (!is_null($id))? $this->mdl_products->form_value('product_id') : null,//$id, // > 0               
            )
        );

        $this->layout->buffer('content', 'products/form');
        $this->layout->render();
    }

    public function delete($id, $product_key = '')
    {
        $this->load->model('upload/mdl_uploads');
        $this->mdl_products->delete($id);        
        $this->mdl_uploads->delete_by_key($product_key);

        redirect('products');
    }

}
