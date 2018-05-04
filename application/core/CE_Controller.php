<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CE_Controller extends CI_Controller {

    protected $view = '';
    protected $data = [];
    protected $layout;
    protected $inc = [];
    protected $model = [];
    protected $library = [];
    protected $helper = [];
    protected $auth = [];

    public function __construct() {
        parent::__construct();
        $this->load->config('auth');
        $this->authenticate(['login', 'logout']);
        $this->loadModel();
        $this->loadLibrary();
        $this->loadHelper();
    }

    public function _remap($method) {
        if (method_exists($this, $method)) {
            call_user_func_array(array($this, $method), array_slice($this->uri->rsegments, 2));
        } else {
            if (method_exists($this, '_404')) {
                call_user_func_array(array($this, '_404'), array($method));
            } else {
                show_404(strtolower(get_class($this)) . '/' . $method);
            }
        }
        $this->loadView();
    }

    protected function loadView() {
        if ($this->view !== FALSE) {
            $view = (!empty($this->view)) ? $this->view : $this->router->directory . $this->router->class . '/' . $this->router->method;
            $data['content'] = $this->load->view($view, $this->data, TRUE);
            if (!empty($this->inc)) {
                foreach ($this->inc as $name => $file) {
                    $data['content_' . $name] = $this->load->view($file, $this->data, TRUE);
                }
            }
            $data['message'] = '';
            $data = array_merge($this->data, $data);
            $layout = FALSE;
            if (!isset($this->layout)) {
                if (file_exists(APPPATH . 'views/layout/' . $this->router->class . '.php')) {
                    $layout = 'layout/' . $this->router->class;
                } else {
                    $layout = 'layout/default';
                }
            } else if ($this->layout !== FALSE) {
                if (count(explode('/', $this->layout)) == 1) {
                    $layout = 'layout/' . $this->layout;
                } else {
                    $layout = $this->layout;
                }
            }
            if ($layout == FALSE) {
                $this->output->set_output($data['content']);
            } else {
                $this->load->view($layout, $data);
            }
        }
    }

    private function loadModel() {
        if (count($this->model)) {
            foreach ($this->model as $_model) {
                $this->load->model($_model . '_model', $_model);
            }
        }
    }

    private function loadLibrary() {
        if (count($this->library)) {
            foreach ($this->library as $_library) {
                $this->load->library($_library);
            }
        }
    }

    private function loadHelper() {
        if (count($this->helper)) {
            foreach ($this->helper as $_helper) {
                $this->load->helper($_helper);
            }
        }
    }

    private function authenticate($methods) {
        $methods = array_merge($methods, $this->auth);
        $method = $this->router->method;
        if (!in_array($method, $methods) && !$this->session->userdata('Auth')['id']) {
            $this->session->set_userdata('request_for', $this->input->server('REQUEST_URI'));
            $this->session->set_userdata('post_data', $this->input->post());
            $this->session->set_userdata('get_data', $this->input->get());
            redirect('users/login');
        }
        if($this->session->userdata('Auth')['id']){
            
        }
    }

    public function authorize() {
        $data = $this->input->post() ? $this->input->post() : '';
        if ($data) {
            $this->load->model('user_model','user');
            return $this->user->authorize($data);
        }
        return FALSE;
    }

}
