<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dealer extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model','user');
        if(!$this->session->userdata('logged_in')){
            redirect('login');
        }
    }
    public function dealer_index($id){
        $user = $this->user->get_by_id($id);
        $data['dealer'] = $user;
        $this->load->view('header');
        $this->load->view('dealer_index.html', $data);
        $this->load->view('footer');
    }

    //If Dealer login for first time -> completes profile on first login
    public function complete_profile(){
        $uid = $this->session->userdata('user_id');
        $user = $this->user->get_by_id($uid);
        if(!$user || $user->type !== 'dealer'){
            show_error('Access denied',403);
        }

        // If already complete redirecting to edit page
        if($user->is_profile_complete){
            redirect('dealer/edit/'.$uid);
        }

        $data['user'] = $user;
        $this->load->view('header');
        $this->load->view('dealer_complete_profile.html', $data);
        $this->load->view('footer');
    }

    // submit dealer profile details
    public function save_profile(){
        $uid = $this->session->userdata('user_id');

        $this->form_validation->set_rules('city','City','trim|required');
        $this->form_validation->set_rules('state','State','trim|required');
        $this->form_validation->set_rules('zip','Zip','trim|required');

        if($this->form_validation->run() == FALSE){
            echo json_encode(['status'=>'error','message'=>strip_tags(validation_errors())]);
            return;
        }

        $data = [
            'city' => $this->input->post('city', true),
            'state'=> $this->input->post('state', true),
            'zip'  => $this->input->post('zip', true),
            'is_profile_complete' => 1
        ];

        $updated = $this->user->update($uid, $data);
        if($updated){
            echo json_encode(['status'=>'success','message'=>'Profile saved.','redirect'=>site_url('dealer/edit/'.$uid)]);
        }else{
            echo json_encode(['status'=>'error','message'=>'Failed to save profile.']);
        }
    }

    // edit dealer info
    public function edit($id){
        $current = $this->session->userdata();  
        $user = $this->user->get_by_id($id);
        if(!$user || $user->type !== 'dealer'){
            show_error('Dealer not found',404);
        }

       
        // Dealer edit only their own profile and  Employee can edit any dealer
        if($current['type'] === 'dealer' && $current['user_id'] != $id){
            show_error('Forbidden',403);
        }

        $data['dealer'] = $user;
        $this->load->view('header');
        $this->load->view('edit_dealer.html', $data);
        $this->load->view('footer');
    }

    //update
    public function ajax_update_dealer(){
        $current = $this->session->userdata();
        if(!$current) { echo json_encode(['status'=>'error','message'=>'Not authenticated']); return; }

        $id = (int)$this->input->post('id', true);
        $dealer = $this->user->get_by_id($id);
        if(!$dealer || $dealer->type !== 'dealer'){
            echo json_encode(['status'=>'error','message'=>'Dealer not found']);
            return;
        }

        if($current['type'] === 'dealer' && $current['user_id'] != $id){
            echo json_encode(['status'=>'error','message'=>'Not allowed']);
            return;
        }

        $this->form_validation->set_rules('city','City','trim|required');
        $this->form_validation->set_rules('state','State','trim|required');
        $this->form_validation->set_rules('zip','Zip','trim|required');

        if($this->form_validation->run() == FALSE){
            echo json_encode(['status'=>'error','message'=>strip_tags(validation_errors())]);
            return;
        }

        $data = [
            'city' => $this->input->post('city', true),
            'state'=> $this->input->post('state', true),
            'zip'  => $this->input->post('zip', true),
            'is_profile_complete' => 1
        ];

        $this->user->update($id, $data);
        echo json_encode(['status'=>'success','message'=>'Dealer updated.']);
    }
}
