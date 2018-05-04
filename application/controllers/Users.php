<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CE_Controller {

    public function __construct() {
        $this->model = ['user'];
        $this->library = ['table', 'form_validation'];
        $this->helper = ['form', 'string'];
        $this->auth = [];
        parent::__construct();
    }

    public function login() {
        if ($this->input->post()) {
            if (!$this->authorize()) {
                redirect('users/login');
                $this->data['message'] = validation_errors() ? validation_errors($this->config->item('error_message'), $this->config->item('_error_message')) : ($this->session->flashdata('message'));
            }
        }
    }
    
    public function logout(){
        $this->session->sess_destroy();
        redirect('users/login');
    }

    public function index() {
        $config['table_open'] = '<table class="table table-bordered">';
        $config['table_close'] = '<table>';
        $this->table->set_template($config);
        $this->data['users'] = $this->table->generate($this->user->get_users());
        $this->data['message'] = validation_errors() ? validation_errors($this->config->item('error_message'), $this->config->item('_error_message')) : ($this->session->flashdata('message'));
    }

    public function add() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|matches[password]');
        if ($this->form_validation->run()) {
            if ($this->user->add_user()) {
                $this->session->set_flashdata('message', $this->config->item('success_message') . 'User added successfully.' . $this->config->item('_success_message'));
            } else {
                $this->session->set_flashdata('message', $this->config->item('error_message') . 'Something went wrong.' . $this->config->item('_error_message'));
            }
            redirect('users');
        }
        $this->data['message'] = validation_errors() ? validation_errors($this->config->item('error_message'), $this->config->item('_error_message')) : ($this->session->flashdata('message'));
    }

}
