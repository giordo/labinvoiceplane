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

/*
 * $direction P = portrait; L = landscape
 */
function ggenerate_quote_pdf($quote_id, $stream = TRUE, $quote_template = NULL, $preview = FALSE, $direction = "")
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->model('persons/mdl_persons');

    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }
    
    $salesperson = $CI->db->query(''
            . 'SELECT * from ip_persons '
            . 'JOIN ip_uploads ON ip_uploads.url_key = ip_persons.person_key '
            . 'WHERE ip_persons.person_id = ' .$quote->salesperson_id .' LIMIT 1')->result();
    
    $techperson = $CI->db->query(''
            . 'SELECT * from ip_persons '
            . 'JOIN ip_uploads ON ip_uploads.url_key = ip_persons.person_key '
            . 'WHERE ip_persons.person_id = ' .$quote->techperson_id .' LIMIT 1' )->result();
    
    $data = array(
        'quote' => $quote,
        'salesperson' => $salesperson,
        'techperson' => $techperson,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items' => $CI->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
        'output_type' => 'pdf'
    );
    
    global $pdf_preview; $pdf_preview = $preview;    // ---it---
    $data['preview_pdf'] = $preview;    // ---it--- set preview to override default overflow-y: hidden; in html and body
    $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, TRUE);
    
    //---it---inizio
    if ($preview)
    {
        echo $html;
    }
    else
    {
        //---it---fine
        $CI->load->helper('mpdf');
        
        return gpdf_create($html, lang('quote') . '_' . str_replace(array('\\', '/'), '_', $quote->quote_number), $stream,$quote->quote_password, null, null, $direction);
        //---it---inizio
    }
    //---it---fine
}


/*
 * $direction "" = portrait; L = landscape
 */
function gpdf_create($html, $filename, $stream = TRUE, $password = NULL, $isInvoice = NULL,$isGuest = NULL, $direction = "")
{
    // ---it---inizio
    // Speciale motore stampa dompdf: primo motore stampa FI, poi tolto dalla versione originale e mantenuto nella versione italiana.
    // Questo motore PDF, infatti, mantiene il risultato visualizzato nell'anteprima PDF (a differenza del nuovo motore mPDF).
    $CI = & get_instance();
    if ($CI->mdl_settings->setting('it_print_engine') == 'dompdf')
    {
        return pdf_create_dompdf($html, $filename, $stream);
    }
    // ---it---fine
	
    require_once(APPPATH . 'helpers/mpdf/mpdf.php');
    
    $mpdf = new mPDF('c', 'A4-'.$direction);
    $mpdf->useAdobeCJK = true;
    $mpdf->SetAutoFont();
    $mpdf->SetProtection(array('copy','print'), $password, $password);
    if(!(is_dir('./uploads/archive/') OR is_link('./uploads/archive/') ))
        mkdir ('./uploads/archive/','0777');

    if (strpos($filename, lang('invoice')) !== false) {
        $CI = &get_instance();
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }
        
    $invoice_array = array();
    $mpdf->WriteHTML($html);

    if ($stream) {
        if (!$isInvoice) {
            return $mpdf->Output($filename . '.pdf', 'I');
        }

        foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file) {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) AND $isGuest) {
            rsort($invoice_array);
            header('Content-type: application/pdf');
            return readfile($invoice_array[0]);
        } else
            if ($isGuest){
            //todo flashdata is deleted between requests
            //$CI->session->flashdata('alert_error', 'sorry no Invoice found!');
            redirect('guest/view/invoice/' . end($CI->uri->segment_array()));
        }
        $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
        return $mpdf->Output( $filename . '.pdf', 'I');
    }

    else {

        if($isInvoice) {

            foreach (glob('./uploads/archive/*' .  $filename . '.pdf') as $file) {
                array_push($invoice_array, $file);
            }
            if (!empty($invoice_array) && !is_null($isGuest)) {
                rsort($invoice_array);
                return $invoice_array[0];
            }
            $mpdf->Output('./uploads/archive/' . date('Y-m-d') .'_'. $filename . '.pdf', 'F');
            return './uploads/archive/'.date('Y-m-d').'_'. $filename . '.pdf';
        }
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');

        // DELETE OLD TEMP FILES - Housekeeping
        // Delete any files in temp/ directory that are >1 hrs old
        $interval = 3600;
        if ($handle = @opendir(preg_replace('/\/$/','','./uploads/temp/'))) {
            while (false !== ($file = readdir($handle))) {
                if (($file != "..") && ($file != ".") && !is_dir($file) && ((filemtime('./uploads/temp/'.$file)+$interval) < time()) && (substr($file, 0, 1) !== '.') && ($file !='remove.txt')) { // mPDF 5.7.3
                    unlink('./uploads/temp/'.$file);
                }
            }
            closedir($handle);
        }
        //==============================================================================================================
        return './uploads/temp/' . $filename . '.pdf';
    }
}

function generate_invoice_pdf($invoice_id, $stream = TRUE, $invoice_template = NULL,$isGuest = NULL, $preview = FALSE/*---it---*/)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_items');
    $CI->load->model('invoices/mdl_invoice_tax_rates');
    $CI->load->model('payment_methods/mdl_payment_methods');
    $CI->load->library('encrypt');

    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
    if (!$invoice_template) {
        $CI->load->helper('template');
        $invoice_template = select_pdf_invoice_template($invoice);
    }

    $payment_method = $CI->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
    if ($invoice->payment_method == 0) $payment_method = NULL;

    $data = array(
        'invoice' => $invoice,
        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
        'items' => $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
        'payment_method' => $payment_method,
        'output_type' => 'pdf'
    );

    global $pdf_preview; $pdf_preview = $preview;    // ---it---
    $data['preview_pdf'] = $preview;    // ---it--- set preview to override default overflow-y: hidden; in html and body
    $html = $CI->load->view('invoice_templates/pdf/' . $invoice_template, $data, TRUE);
    
    //---it---inizio
    if ($preview)
    {
        echo $html;
    }
    else
    {
        //---it---fine
	    $CI->load->helper('mpdf');
	    return pdf_create($html, lang('invoice') . '_' . str_replace(array('\\', '/'), '_', $invoice->invoice_number), $stream, $invoice->invoice_password,1,$isGuest);
	//---it---inizio
    }
    //---it---fine
}

function generate_quote_pdf($quote_id, $stream = TRUE, $quote_template = NULL, $preview = FALSE/*---it---*/)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');

    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    $data = array(
        'quote' => $quote,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items' => $CI->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
        'output_type' => 'pdf'
    );
    
    global $pdf_preview; $pdf_preview = $preview;    // ---it---
    $data['preview_pdf'] = $preview;    // ---it--- set preview to override default overflow-y: hidden; in html and body
    $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, TRUE);
    
    //---it---inizio
    if ($preview)
    {
        echo $html;
    }
    else
    {
        //---it---fine
        $CI->load->helper('mpdf');
        
        return pdf_create($html, lang('quote') . '_' . str_replace(array('\\', '/'), '_', $quote->quote_number), $stream,$quote->quote_password);
        //---it---inizio
    }
    //---it---fine
}
