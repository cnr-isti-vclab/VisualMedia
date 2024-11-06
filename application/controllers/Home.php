<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

/*	function __construct() { 
		parent::__construct();
	} */

	public function index() {
		$this->data['page'] = 'home';
		$this->data['media'] = $this->media->search(array("publish = 1", "processed = 1", "picked = 1"));
		$this->data['browsertable'] = $this->load->view('browsertable', $this->data, TRUE);
		$this->render('home');
	}

	public function preupload() {
		$this->data['title'] = 'Upload - Visual Media Service';
		$this->data['description'] = 'Visual media service upload';
		$this->render('preupload');
	}

	public function upload($type) {
		if(!$this->isLogged()) 
			$this->alert("<p>Please log in in order to upload something.</p>");

		$allowed = $this->media->allowed($type);
		if(!$allowed)
			$this->error("<p>Unknown media type!</p>");

		$this->data['page'] = 'upload';
		$this->data['media_type'] = $type;
		$this->data['allowed'] = $allowed;
		$this->render('upload');
	}

	public function browse() {
		$this->data['page'] = 'browse';
		$this->data['title'] = 'Broswe - Visual Media Service';
		$this->data['description'] = 'Ariadne visual media service job monitor';
		$this->data['media'] = $this->media->search(array("publish = 1", "processed = 1"));
		$this->data['browsertable'] = $this->load->view('browsertable', $this->data, TRUE);
		$this->render('browse');
	}

	public function  search() {
		$criteria = array("publish = 1", "processed = 1");

		$query  = pg_escape_string($_POST['search']);
		$query = explode(' ', $query);
		$query = implode(' | ', $query);
		if($query != '')           $criteria[] = "words @@ to_tsquery('$query')";

		if($_POST['3d'] == '0')    $criteria[] = "media_type != '3d'";
		if($_POST['rti'] == '0')   $criteria[] = "media_type != 'rti'";
		if($_POST['album'] == '0') $criteria[] = "media_type != 'album'";
		if($_POST['img'] == '0')   $criteria[] = "media_type != 'img'";

		$this->data['media'] = $this->media->search($criteria);
		$this->load->view('browsertable', $this->data);
	}

	public function info($type) {
		$title = array(
			'3d'    => '3D Models',
			'rti'   => 'RTI Images',
			'img'   => 'High-resolution Images',
			'album' => 'Set of images');
		$this->data['title'] = $title[$type].' - Ariadne';
		$this->data['description'] = 'Ariadne visual media service, 3D models info';

		$this->render('help/'.$type);
	}

	public function help() {
		$this->data['page'] = 'help';
		$this->data['title'] = 'Help - Visual Media Service';
		$this->data['description'] = 'Ariadne visual media service help';
		$this->render('help');
	}

	public function about() {
		$this->data['page'] = 'about';
		$this->data['title'] = 'About - Visual Media Service';
		$this->data['description'] = 'Ariadne visual media service about page';
		$this->render('about');
	}

	public function terms() {
		$this->data['title'] = 'Terms - Visual Media Service';
		$this->render('terms');
	}
}
