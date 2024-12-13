<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	protected $data = array();

	function __construct() {
		parent::__construct();
		$this->load->helper('url');

		$this->data = array(
			'page'  => NULL,
			'title' => 'Visual Media Service',
			'user'  => NULL
		);

		if(isset($_SESSION['token'])) {
			$token = $_SESSION['token'];
			$user = $this->db->query("SELECT * from identities i JOIN users u on u.id = i.userid where token = ?", $token)->row();
			if(!$user)
				unset($_SESSION['token']);
			else
				$this->data['user'] = $user;
		}
	}

	protected function isAdmin() {
		return isset($this->data['user']) && $this->data['user']->role == 'admin';
	}
	protected function isLogged() {
		return isset($this->data['user']);
	}

	protected function user() {
		if(!isset($this->data['user'])) return null;
			return $this->data['user'];
	}

	protected function render($view, $template = 'container') {
		if($template == 'json') { // || $this->input->is_ajax_request()) {
			header('Content-Type: application/json');
			echo json_encode($view);

		} elseif($template == 'xml') {
			header('Content-Type: text/xml');
			$this->load->view($view, $this->data);

		} elseif(is_null($template)) {
			$this->load->view($view, $this->data);

		} else {
			$this->data['content'] = (is_null($view)) ? '' : $this->load->view($view, $this->data, TRUE);
			$this->load->view($template, $this->data);
		}
	}

	public function jsonError($error) {
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('error' => $error)));
		$this->output->_display();
		exit();
	}

	public function jsonOk() {
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('status' => 'ok')));
		$this->output->_display();
		exit();
	}

	protected function error($msg) {
		$this->data['msg'] = $msg;
		$this->render('error');
		$this->output->_display();
		exit();
	}

	protected function alert($msg) {
		$this->data['msg'] = $msg;
		$this->render('alert');
		$this->output->_display();
		exit();
	}
}

?>
