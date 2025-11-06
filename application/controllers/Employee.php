<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model','user');
        if(!$this->session->userdata('logged_in')){
            redirect('login');
        }

        if($this->session->userdata('type') !== 'employee'){
            show_error('Forbidden',403);
        }
    }

    // Dealer List with pagination and zip filter
    public function dealers(){
        $zip = $this->input->get('zip', true);

        $config = [];
        $config['base_url'] = site_url('employee/dealers');
        $config['per_page'] = 1;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['total_rows'] = $this->user->count_dealers($zip);


        $config['full_tag_open']   = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close']  = '</ul></nav>';

        $config['num_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close']   = '</span></li>';

        $config['cur_tag_open']    = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']   = '</span></li>';

        $config['next_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['next_tag_close']  = '</span></li>';

        $config['prev_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['prev_tag_close']  = '</span></li>';

        $config['first_tag_open']  = '<li class="page-item"><span class="page-link">';
        $config['first_tag_close'] = '</span></li>';

        $config['last_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['last_tag_close']  = '</span></li>';


        $this->pagination->initialize($config);

        $page = (int)$this->input->get('page');
        $offset = ($page && $page > 0) ? $page : 0;

        $data['dealers'] = $this->user->get_dealers($config['per_page'], $offset, $zip);
        $data['pagination'] = $this->pagination->create_links();
        $data['zip'] = $zip;

        $this->load->view('header');
        $this->load->view('employee_dealers_list.html', $data);
        $this->load->view('footer');
    }

    // edit dealer page for employee 
    public function edit_dealer($id){
        $this->load->library('session');
        $this->load->helper('url');
        redirect('dealer/edit/'.$id);
    }
}
