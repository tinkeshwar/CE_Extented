<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_users() {
        return $this->db->select('username,ip,login_type,last_login,status,created')
                        ->get('users');
    }

    public function add_user() {
        $username = $this->unique($this->input->post('username'));
        $password = $this->encrypt($this->input->post('password'));
        $insert_array = array(
            'login_type' => 's',
            'username' => $username,
            'password' => $password['password'],
            'salt' => $password['salt'],
            'ip' => $this->input->ip_address(),
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s')
        );
        if ($this->db->insert('users', $insert_array)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function encrypt($password, $salt = NULL) {

        $salt = $salt ? $salt : random_string('alnum', '6');
        $hash = crypt($password, $salt);
        return ['password' => $hash, 'salt' => $salt];
    }

    public function unique($username, $primary = NULL, $counter = 1) {
        $primary = $primary ? $primary : $username;
        $count = $this->db->where('username', $username)
                ->get('users')
                ->num_rows();
        if ($count) {
            $username = $primary . '_' . $counter;
            return $this->unique($username, $primary, $counter + 1);
        } else {
            return $username;
        }
    }

    public function authorize($data) {
        $username = $data['username'];
        $count = $this->db->where('username', $username)
                ->where('status', '1')
                ->get('users')
                ->num_rows();
        $salt = NULL;
        if ($count === 1) {
            $userData = $this->db->where('username', $username)
                    ->where('status', '1')
                    ->get('users')
                    ->row();
            if ($userData) {
                $salt = $userData->salt;
            }
        }
        $password = $this->encrypt($data['password'], $salt);
        if ($salt) {
            $user = $this->db->where('username', $username)
                    ->where('password', $password['password'])
                    ->get('users')
                    ->row();
            $user_array = array(
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->user_type
            );
            $this->session->set_userdata('Auth', $user_array);
            $this->session->set_flashdata('message', $this->config->item('success_message') . 'Login Success.' . $this->config->item('_success_message'));
            if ($this->session->userdata('request_for')) {
                $request_for = $this->session->userdata('request_for');
                $this->session->unset_userdata('request_for');
                if ($this->session->userdata('post_data')) {
                    $post = $this->session->userdata('post_data');
                    $this->session->unset_userdata('post_data');
                    $this->redirect_post(base_url($request_for), $post);
                } else if ($this->session->userdata('get_data')) {
                    $get = $this->session->userdata('get_data');
                    $this->session->unset_userdata('get_data');
                    $request_for = $request_for . '?=' . $get;
                    redirect(base_url($request_for));
                } else {
                    redirect(base_url($request_for));
                }
            } else {
                redirect('users');
            }
        }
        $this->session->set_flashdata('message', $this->config->item('error_message') . 'Invalid Credentials.' . $this->config->item('_error_message'));
        return FALSE;
    }

    public function redirect_post($url, array $data, array $headers = null) {
        $params = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        if (!is_null($headers)) {
            $params['http']['header'] = '';
            foreach ($headers as $k => $v) {
                $params['http']['header'] .= "$k: $v\n";
            }
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if ($fp) {
            echo @stream_get_contents($fp);
            die;
        } else {
            throw new Exception("Error loading '$url', $php_errormsg");
        }
    }

}
