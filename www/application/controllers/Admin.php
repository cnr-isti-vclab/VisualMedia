<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	function __construct() { 
		parent::__construct();
		if(!$this->user() || $this->user()->role != 'admin')
			show_error("Unauthorized", 401, 'You are not supposed to be here');
	}

	public function index() {
		$this->render('admin/index');
	}

	public function users() {
		$this->data['users'] = $this->db->query("SELECT * FROM users order by id;")->result();
		$this->render('admin/users');
	}

	public function jobs() {
	}

	public function media() {
		$this->data['media'] = $this->db->query(
			"SELECT m.id, m.label, m.title, m.media_type, m.creation, m.status, m.size, m.publish, m.picked, s.email FROM media m JOIN users s on m.userid = s.id order by id desc;")->result();
		$this->render('admin/media');
	}

	public function collection() {

	}

	public function pick() {
		$picked = $_POST['picked'] ? 1 : 0;
		$id = $_POST['media'];
		$this->db->query("UPDATE media SET picked = ? WHERE id = ?", array($picked, $id));
		$this->render(array(), 'json');
	}

	public function publish() {
		$publish = $_POST['publish'] ? 1 : 0;
		$id = $_POST['media'];
		$this->db->query("UPDATE media SET publish = ? WHERE id = ?", array($publish, $id));
		$this->render(array(), 'json');
	}
}
?>
