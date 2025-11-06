<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model','user');
    }

    public function index(){
        redirect('login');
    }

    public function register(){
        $this->load->view('header');
        $this->load->view('register.html');
        $this->load->view('footer');
    }

    // check email uniqueness
    public function check_email(){
        $email = $this->input->post('email', true);
        $exists = $this->user->get_by_email($email);
        echo json_encode(['exists' => $exists ? true : false]);
    }

    //rsave egister 
    public function ajax_register(){
      
        $this->form_validation->set_rules('first_name','First Name','trim|required');
        $this->form_validation->set_rules('last_name','Last Name','trim|required');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password','Password','trim|required|min_length[6]');
        $this->form_validation->set_rules('type','Type','trim|required|in_list[employee,dealer]');

        if($this->form_validation->run() == FALSE){
            $errors = validation_errors();
            echo json_encode(['status' => 'error', 'message' => strip_tags($errors)]);
            return;
        }

        $data = [
            'first_name' => $this->input->post('first_name', true),
            'last_name'  => $this->input->post('last_name', true),
            'email'      => $this->input->post('email', true),
            'password'   => password_hash($this->input->post('password', true), PASSWORD_BCRYPT),
            'type'       => $this->input->post('type', true),
        ];

        $id = $this->user->insert($data);
        if($id){
            echo json_encode(['status'=>'success','message'=>'Registration successful. Please login.']);
        }else{
            echo json_encode(['status'=>'error','message'=>'Failed to register.']);
        }
    }

    public function login(){
        $this->load->view('header');
        $this->load->view('login.html');
        $this->load->view('footer');
    }

    //login
    public function ajax_login(){
        $this->form_validation->set_rules('email','Email','trim|required|valid_email');
        $this->form_validation->set_rules('password','Password','trim|required');

        if($this->form_validation->run() == FALSE){
            echo json_encode(['status'=>'error','message'=> strip_tags(validation_errors())]);
            return;
        }

        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        $user = $this->user->get_by_email($email);
        if(!$user){
            echo json_encode(['status'=>'error','message'=>'Invalid credentials.']);
            return;
        }
        if(!password_verify($password, $user->password)){
            echo json_encode(['status'=>'error','message'=>'Invalid credentials.']);
            return;
        }

        // to set session
        $sess = [
            'user_id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'type' => $user->type,
            'logged_in' => true
        ];
        $this->session->set_userdata($sess);

        //Cheking If dealer profile incomplete for first time
        if($user->type === 'dealer' && !$user->is_profile_complete){
            echo json_encode(['status'=>'success','redirect' => site_url('dealer/complete')]);
            return;
        }

        // redirect to employee dealers list page
        if($user->type === 'employee'){
            echo json_encode(['status'=>'success','redirect' => site_url('employee/dealers')]);
            return;
        }

        // dealer default page after second time login
        echo json_encode(['status'=>'success','redirect' => site_url('dealer/edit/'.$user->id)]);
    }

    public function logout(){
        $this->session->sess_destroy();
        redirect('login');
    }
}
