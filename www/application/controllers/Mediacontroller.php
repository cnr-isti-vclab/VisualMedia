<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mediacontroller extends MY_Controller {


	function __construct() {
		parent::__construct();
		$this->load->library('auth');
	}


	public function regenerateSecretKeys($min, $max) {
		if(!$this->user()) return;
		if($this->user()->role != 'admin') return;
		for($id = $min; $id < $max; $id++) {
			$now = new DateTime();
			$now_label = $now->format('Y-m-d H:i:s').rand();
			$secret = md5($now_label);
			$this->db->query("update media set secret = ? where id = ?", array($secret, $id));
		}
		echo('done');
	}
	//owns media by label?
	public function owns($label, $user = NULL) {
		if(!$user) $user = $this->user();
		if(!$user)
			$this->jsonError('Please login');
		$media = $this->media->ownsByLabel($label, $user);

		if(!$media)
			$this->jsonError("Invalid, missing or unauthorized media ");
		return $media;
	}

/*
	public function ownsModel($model, $user = NULL) {
		if(!$user) $user = $this->user();
		if(!$user)
			$this->jsonError('Please login');

		$model = $this->model->owns($model, $user);

		if(!$model)
			$this->jsonError("Invalid, missing or unauthorized model");
		return $model;
	}
		*/

	//parameters: url (for the moment)
	//will create a temporary account and identity, save cookie


	public function switchboard() {

		
		$url = $this->input->get('url');
		$title = $this->input->get('title');
		$type = $this->input->get('type');

		if(!$url)
			$this->error("Missing 'url' parameter");

		if(!$title) {
			$this->error("Missing 'title' parameter");
			$title = substr(md5(uniqid()), 10);
		}

		if(!$type)
			$this->error("Missing 'type' parameter"); 

		if(!in_array($type, array('3d', 'rti', 'img', 'album')))
			$this->jsonError('Unknown media type.');

		//Load additional urls and concatenate all of it into a single string separated by ' ';

		$urls = [$url];
		$i = 1;
		for($i = 1; $i < 50; $i++) {
			$u = $this->input->get('url'.$i);
			if(!$u)
				break;
			$urls[] = str_replace(' ', '+', $u);
		}
		$url = implode($urls, ' ');

		
		$this->data['user'] = $user = $this->db->query("select * from users where username = 'switchboard'")->row();

		$this->data['media'] = $media = $this->db->query("select * from media where userid = ? and url = ?", [$user->id, $url])->row();

		
		if($media) {
			$model = $this->db->query("select * from models where media = ?", $media->id)->row();
			if(!$model) {
				//render error about url not having a model
				$this->error("The media does not have a model associated with it. Please upload a model first.");
				exit(0);
			}
			if($model->status == 'failed') { //just redownload
				$this->db->query("update model set status = 'download' where id = ?", $model->id);
				$media->status = 'download';//TODO do we need a media status?
			}

		} else {
			if($media) $media = [];
			$now = new DateTime();
			$now_label = $now->format('Y-m-d H:i:s');

			$expire = $now->add(new DateInterval('P2D'));
			$expire = $now->add(new DateInterval('PT2M'));
			$expire_label = $expire->format('Y-m-d H:i:s');

			$label = $this->uniqueLabel($title);
			$media['label']       = $label;
			$media['title']       = $title;
			$media['userid']      = $user->id;
			$media['media_type']  = $type;
			$media['url']         = $url;
			$media['creation']    = $now_label;
			$media['secret']      = md5($now_label.$label."salt?");
			$media['path']        = $type.'/'.$label.'/';
			$media['status']      = 'download';
			$media['expire']      = $expire_label;
			$this->data['media'] = $media;

			$this->db->insert('media', $media);
			$id = $this->db->insert_id();
			$media = $this->data['media'] = (object)$media;


			$model['media'] = $id;
			$model['model_type'] = $type;
			$model['status'] = 'download';
			$model['url'] = $url;
			$this->db->insert('model', $model);
			$model['id'] = $this->db->insert_id();
		}


		if($media->status == 'ready')
			$this->show($media->label);
		else
			$this->render('switchboard');		
	}

	public function uniqueLabel($title) {
		$prelabel = filenamer($title);


		$label = $prelabel;
		$count = 0;
		while(1) {
			if($this->db->query("SELECT id FROM media WHERE label = ?", $label)->row() == NULL)
				break;
			$label = "$prelabel-$count";
			$count++;
			if($count > 10)
				$this->jsonError('Too many object with the same title.');
		}
		return $label;
	}


	public function create() {
		if(!$this->isLogged())
			$this->jsonError('Please log in to upload.');

		$media = array();
		$media['userid']      = $this->input->post('userid');
		$media['media_type']  = $this->input->post('media_type');
		$media['title']       = $this->input->post('title');
		$media['description'] = $this->input->post('description');
		$media['url']         = $this->input->post('url');
		$media['collection']  = $this->input->post('collection');
		$media['owner']       = $this->input->post('owner');
		$files                = $this->input->post('files');

		$result = $this->media->create($media, $files);
		
		$this->render($result, 'json');
	}


	public function createModel() {
		if(!$this->isLogged())
			$this->jsonError('Please log in to upload.');

		$label= $this->input->post('media');
		$media = $this->owns($label);

		$files = $this->input->post('files');
		
		$result = $this->model->createModel($media->id, $files);
		$this->render($result, 'json');
	}

	function parseTusMetadata(string $raw): array {
		$out = [];
		foreach (explode(',', $raw) as $pair) {
			if (strpos($pair, ' ') !== false) {
				[$key, $val] = explode(' ', $pair, 2);
				$out[$key] = base64_decode($val);
			}
		}
		return $out;
	}

	public function uploadFile($key = NULL) {
		$server = new \TusPhp\Tus\Server('file');

		$server->setApiPath('/media/upload/file');                // path dell'endpoint

		if ($this->input->method() == 'post') {
			$rawMetadata = $this->input->get_request_header('Upload-Metadata', TRUE);

			$metadata = $rawMetadata ? $this->parseTusMetadata($rawMetadata) : [];
			$media = $this->owns($metadata['media']);
			if(!$media) {
				http_response_code(403);
				echo 'Unauthorized';
				exit;
			}
			$model = $this->model->owns($metadata['model'], $this->user());
			$path = $this->model->currentUploadPath($media, $model);
			$server->setUploadDir($path); // cartella dove salvare i file
		}

		// Listen to the complete event
		$server->event()->addListener('tus-server.upload.complete', function ($event) {
			$upload = $event->getFile();

			$filePath = $upload->getFilePath(); // full path to temp uploaded file

			$filename = $upload->getName();     // original filename
			$metadata = $upload->details()['metadata'];

			$modelid = $metadata['model'] ?? 'unknown';

			$file = $event->getFile();
			$metadata = $file->details()['metadata'];
			$model = $this->model->owns($metadata['model'], $this->user());
			$this->model->fileUploaded($model, $filename);
		});

		$response = $server->serve();
		$response->send();

		

		/*if(!isset($_FILES['file']))
			$this->jsonError('No file.');

		$label = $this->input->post('media');
		$overwrite = $this->input->post('overwrite');

		$media = $this->owns($label);
		$file = $_FILES['file'];

		$model = $this->input->post('model');

		$result = $this->media->uploadFile($media, $file, FALSE);
		if(isset($result['error']))
			$this->jsonError($result['error']);

		$this->render(array('id'=>$result['id'], 'debug'=>$debug), 'json'); */
	}


	public function oldManage($secret) {
		$this->sendMessage("Log in and your models will be available in the profile page!");
	}

	public function manage($label) {
		if(!$this->isLogged())
			$this->error("</p>It looks like you are not logged in. Maybe the session expired or the cookies were deleted.</p>".
				"<p>Please login and try again...</p>");

		$media = $this->media->ownsByLabel($label, $this->user());

		if(!$media) {
			$contact = ADMIN_EMAIL;

//			$media = $this->media->byLabel($secret, false); //return also private medias
			if(!$media)
				$error = "<p>The link does not seems to be valid:</p>\n".
					"<ul>\n".
					"<li>some problem occourred moving to the version or\n".
					"<li>the media might have been removed or\n".
					"<li>the media is not currently published or\n".
					"<li>the link is wrong\n".
					"</ul>\n".
					"<p>Contact us for help: <a href='mailto:$contact'>$contact</a></p>\n";

				$this->error($error);

//			if($this->owns
			//try looking for the name if logged in and owner
//			if(!$this->session->userdata('loggedIn') || ($this->session->userdata('user')['id'] != $media->userid && !$this->isAdmin()))
//				redirect('/login');
		}

		$media->link = $this->media->link($media);
		$this->media->addModels($media);
		$media->secretlink = $this->media->secretlink($media);

		$this->data['allowed'] = $this->media->allowed($media);
		$this->data['media'] = $media;
		$this->data['page'] = 'manage';

		$this->render('manage');
	}

	public function delete($label) {
		$media = $this->owns($label);
		if($media->status == 'processing')
			$this->sendError("The media is being processed. Try again, later.");

		$this->media->setStatus($media, 'remove');
		$this->render(array(), 'json');
	}


	public function deleteFile($id) {
		return jsonError('This method is deprecated, use /media/deleteFiles instead.');

		/*$files = explode(',', $id);
		foreach($files as $file) {
			$file = $this->ownsFile($file);
			//TODO not really race condition proof
			if($file->status == 'processing') //check media status
				$this->sendError("The file is being processed. Try again, later.");
			$this->media->deleteFile($file);
		}
		$this->render(array(), 'json'); */
	}
	
	public function update() {
		$label =  $this->input->post('label');
		$media = $this->owns($label);

		$vars['title']       = $this->input->post('title');
		$vars['description'] = $this->input->post('description');
		$vars['owner']	     = $this->input->post('owner');
		$vars['collection']  = $this->input->post('collection');
		$vars['url']         = $this->input->post('url');

		$this->db->where('label', $label);
		$this->db->update('media', $vars);

		$this->render(array(), 'json');
	}

	public function process($label) {
		$media = $this->owns($label);
		//look at the files and try to guess what it is (3d, img, collection, ptm)
		$content = array('3d' => 0, 'rti'=>0, 'img'=>0, 'unknown'=>0);

		$error = NULL;
		foreach($media->files as $file) {
			//TODO validate json is actually a relight file.
			if($file->format)
				$content[$file->format]++;
		}

		switch($media->media_type) {
		case '3d'     : if(!$content['3d'])  $error = 'A 3D model (.ply, .obj, .nxs, .nxz) file is needed.'; break;
		case 'rti'    : if(!$content['rti']) $error = 'An RTI file (.rti, .ptm, .lp, .json) is needed.'; break;
		case 'img'    : if(!$content['img']) $error = 'At least an image (.jpg, .png, .tif) is needed.'; break;
		case 'album'  : if(!$content['img']) $error = 'At least an image (.jpg, .png, .tif) is needed.'; break;
		}

		if($error) {
			$this->media->setStatus($media, 'uploading');
			$this->jsonError($error);
		}

		$this->media->setStatus($media, 'on queue');
		$this->render(array(), 'json');
	}

	public function config($label) {
		$media = $this->media->owns($label, $this->user());
		if(!$media)
			$this->error("Unauthorized.");

		if(!$media->processed)
			$this->error('<p>This media has not been processed yet. Please come back shortly.</p>');

		$data = $this->prepareShow($media);
		if($media->media_type != '3d') {
			$this->load->view('config', $data);
			return;
		}

		$this->load->view('wizard/index', $data);
	}

	public function updateConfig($label) {
//		$label = $this->input->post("media");
		$media = $this->owns($label);
//		$config = $this->input->post('config');
		$config = file_get_contents('php://input');
		if(!$config) {
			$data = $this->prepareShow($media);
			$this->render($data['options'], 'json');
		} else {
			$this->db->query("UPDATE media set options = ? WHERE id = ?", array($config, $media->id));
			$this->render(array(), 'json');
		}
	}




	public function update3dConfig($options, $url) {

		if(!isset($options->scene)) {
			$options->scene = [ (object)[
				"id" => "mesh",
				"url" => $url,
				"matrix" => null,
				"startColor" => "color",
				"solidColor" => "#aaaaaa",
				"specular" => 2
			]];
		}

		if(!isset($options->widgets)) {
			$options->widgets = (object)[
				"grid" => (object)["step"=> 0, "atStartup" => false],
				"trackSphere" => (object)["atStartup" => false ],
				"canonicalViews" => (object)["atStartup" => false],
				"compass" => (object)["atStartup" => false],
				"navCube" => (object)["atStartup" => false]
			];
		}

		if(isset($options->background->color0)) {//new format
			$options->scene[0]->url = $url;
			return $options;
		}
	
		$back = $options->background;
		$back->color0 = "#aaaaaa";
		$back->color1 = "#000000";
		$back->image = "light.jpg";

		switch($back->type) {
			case 'image': $back->image = $back->value; break;
			default:
				$back->color0 = $back->value[0]; 
				$back->color1 = $back->value[1];
				break;
		}
		unset($back->value);
		$options->background = $back;
		
		$options->tools = array_keys(array_filter(get_object_vars($options->tools), function($v, $k) { return $v; }, ARRAY_FILTER_USE_BOTH));

		$options->scene = [ (object)[
			"id" => "mesh",
			"url" => $url,
			"matrix" => null,
			"startColor" => "color",
			"solidColor" => "#aaaaaa",
			"specular" => 2
		]];


		return $options;
	}

	//used also for download
	public function prepareShow($media) {

		$data = array(
			'description' => 'Ariadne visual media service',
			'keywords' => 'Ariadne visual media home image mesh model 3d online web webgl nexus',
			'media' => $media
		);
		$this->load->library('markdown');
		$media->description = $this->markdown->parse($media->description);
		$media->description = $this->security->xss_clean($media->description);

//or { gradient: ['#fff', '#bbb'] }, radial flat (no array)

		$default = <<<'EOD'
{
	"3d": {

		"background": { "type":"image", "image": "dark.jpg" },
		"skin": "dark",
		"tools": [ "home", "zoomin", "zoomout", "lighting", "light",  "color", 
				"measure", "picking", "sections", "orthographic", "fullscreen", "info" ],
		"space": { "cameraFOV" : 60.0 },
		"mm": 0,
		"fov": 50,
		"trackball": {
			"type": "TurntablePanTrackball",
			"trackOptions": {
				"startPhi"      : 0.0,
				"startTheta"    : 0.0,
				"startDistance" : 2.5,
				"startPanX"     : 0.0,
				"startPanY"     : 0.0,
				"startPanZ"     : 0.0,
				"minMaxPhi"     : [-180, 180],
				"minMaxTheta"   : [-70.0, 70.0],
				"minMaxDist"    : [0.2, 5.0],
				"minMaxPanX"    : [-1.0, 1.0],
				"minMaxPanY"    : [-1.0, 1.0],
				"minMaxPanZ"    : [-1.0, 1.0]
			}
		}
	},
	"rti": {
		"background": { "type":"radial", "color0":"#aaaaaa", "color1":"#000000" },
		"skin": "dark",
		"tools": [ "home", "zoomin", "zoomout", "lighting", "light",  "color", "measure", "picking", "sections", "orthographic", "fullscreen", "info" ],
		"mm": 0
	},
	"img": {
		"background": { "type":"image", "image": "light.jpg" },
		"skin": "dark",
		"tools": [ "home", "zoomin", "zoomout", "lighting", "light",  "color", "measure", "picking", "sections", "orthographic", "fullscreen", "info" ],
		"mm": 0
	},
	"album": {
		"background": { "type":"image", "image": "light.jpg" },
		"skin": "dark",
		"tools": [ "home", "zoomin", "zoomout", "lighting", "light",  "color", "measure", "picking", "sections", "orthographic", "fullscreen", "info" ],
		"mm": 0
	}
}
EOD;

		$default = json_decode($default, FALSE);
		$options = json_decode($media->options, FALSE);


		if(!$options)
			$options = $default->{$media->media_type};
//		else
//			$options = array_replace_recursive($default[$media->media_type], $options);
		if(!isset($options->background->image))
			$options->background->image = 'light.jpg';

		if(!isset($options->background->color0))
			$options->background->color0 = '#aaaaaa';

		if(!isset($options->background->color1))
			$options->background->color1 = '#000000';


		if(isset($options->background->value) && is_array($options->background->value)) {
			$options->background->color0 = $options->background->value[0];
			$options->background->color1 = $options->background->value[1];
		}


 		switch($media->media_type) {
		case '3d':
			if(file_exists(DATA_DIR.$media->path.$media->label.".nxz"))
				$url = "../data/".$media->path.$media->label.".nxz";
			else
				$url = "../data/".$media->path.$media->label."Z.nxs";
			$options = $this->update3dConfig($options, $url);
			break;

		case 'rti': 
		case 'img':
		case 'album':
				if(!isset($options->mm))
					$options->{'mm'} = 0;
				$options->model = "../data/".$media->path;
		}

		$data['options'] = $options;
		return $data;
	}

	public function show($label) {
		//owner, admin, label + public or secret
		$media = $this->media->ownsByLabel($label, $this->user());

		if(!$media)
			$media = $this->media->byLabel($label);

		if(!$media)
			$media = $this->media->bySecret($label);

		if(!$media) {
			$contact = ADMIN_EMAIL;

			$this->error("<p>The link does not seems to be valid:</p>\n".
				"<ul>\n".
				"<li>some problem occourred moving to the version or\n".
				"<li>the media might have been removed or\n".
				"<li>the media is not currently published or\n".
				"<li>the link is wrong\n".
				"</ul>\n".
				"<p>Contact us for help: <a href='mailto:$contact'>$contact</a></p>\n"
			);
		}

		if(!$media->processed)
			$this->error('<p>This media has not been processed yet. Please come back shortly.</p>');

		$this->media->addCollections($media);

		if($_SERVER['QUERY_STRING'] == 'standalone') {
			$data = $this->prepareShow($media);
			$this->load->view($media->media_type, $data);
			return;
		}

		if($_SERVER['QUERY_STRING'] == 'config') {
			$data = $this->prepareShow($media);
			$this->load->view('wizard/'.$media->media_type, $data);
			return;
		}

		$this->load->library('markdown');
		$media->description = $this->markdown->parse($media->description);
		$media->description = $this->security->xss_clean($media->description);

		$media->link = $this->media->link($media);
		$media->link = $this->security->xss_clean($media->link);

		$media->secretlink = $this->media->secretlink($media);
		$this->data['media'] = $media;
		$this->render('media');
	}

	public function publish() {
		$label = $this->input->post("media");
		$media = $this->owns($label);
		
		if(!isset($_FILES['image']))
			$this->jsonError('No file.');
		
		$image = $_FILES['image'];
		$path = DATA_DIR.$media->path.$media->label.".jpg";
		try {
			rename($image['tmp_name'], $path);
			chmod($path, 0664);
		} catch(Exception $e) {
			$this->jsonError("Could not upload file.");
		}

		//generate

		$this->db->query("UPDATE media SET publish = 1, thumbnail = ? WHERE id = ?", array($media->label.".jpg", $media->id));
		$this->render(array(), 'json');
	}

	public function unpublish() {
		$label = $this->input->post("media");
		$media = $this->owns($label);
		$this->db->query("UPDATE media SET publish = 0 WHERE id = ?", $media->id);
		$this->render(array(), 'json');
	}


	public function addDir($zip, $dir, $basepath) {
		$dir = rtrim($dir, '/');
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file) {
			if($file->isDir()) continue;

//			$filePath = $file->getRealPath();
			$filePath = $file->getPathname();
			$relativePath = substr($filePath, strlen($dir) + 1);
			$zip->addFileFromPath($basepath.'/'.$relativePath, $filePath);
		}
	}

	public function download($label) {
		$media = $this->media->owns($label, $this->user());
		if(!$media)
			$this->error("Unauthorized");

		$data = $this->prepareShow($media);

		header("Content-Type: application/zip");
//		header("Content-Disposition: attachment; filename=".$media->label.".zip");


		$opt = new ZipStream\Option\Archive();
		$opt->setSendHttpHeaders(false);
		$opt->setLargeFileSize(1000000);



		$zip = new ZipStream\ZipStream($media->label.'.zip', $opt);

		//send uncompress above. Might be worth doing differently for ply!
//		$opt->setLargeFileMethod(ZipStream\ZipStream::METHOD_STORE);



		# create a file named 'hello.txt'
//		$zip->addFile('readme.txt', 'This is the contents of hello.txt');


		if($media->media_type == '3d') {
			$data['options']->model = $label.'.nxs';
			$zip->addFileFromPath("$label/$label.nxs", FCPATH."/data/".$media->path.$media->label.".nxz");
			$this->addDir($zip, FCPATH.'3d/js', $label.'/js');
			$this->addDir($zip, FCPATH.'3d/stylesheet', $label.'/stylesheet');
		} else {
			$data['options']->model = $label.'/';
			$this->addDir($zip, FCPATH.'rti/js', $label.'/js');
			$this->addDir($zip, FCPATH.'rti/css', $label.'/css');
			$this->addDir($zip, FCPATH."data/".$media->path, $label.'/'.$label);
		}
		
		$this->addDir($zip, FCPATH.'3d/skins', $label.'/skins');

		$index = $this->load->view($media->media_type, $data, true);
		$zip->addFile("$label/index.html", $index);

		$zip->finish();


/*		$this->load->library('zip');
		if($media->media_type == '3d') {
			$data['options']['model'] = $label.'.nxs';
			$this->zip->read_file(FCPATH."/data/".$media->path.$media->label.".nxz", "$label/$label.nxs");
			$this->zip->read_dir(FCPATH.'3d/js', FALSE, NULL, "$label/");
			$this->zip->read_dir(FCPATH.'3d/stylesheet', FALSE, NULL, "$label/");

		} else if($media->media_type == 'rti' || $media->media_type == 'img' || $media->media_type == 'album') {
			$this->zip->read_dir(FCPATH.'rti/js/', FALSE, NULL, "$label/");
			$this->zip->read_dir(FCPATH.'rti/css/', FALSE, NULL, "$label/");
			$this->zip->read_dir(FCPATH."data/".$media->path, FALSE, NULL, "$label/");
			$data['options']['model'] = $media->label;

		}

		$this->zip->read_dir(FCPATH.'3d/skins/', FALSE, NULL, "$label/");


		$index = $this->load->view($media->media_type, $data, true);
		$this->zip->add_data("$label/index.html", $index);
		$this->zip->download($media->label.'.zip'); */

		return;
	}

	public function status($label) {
		$media = $this->media->ownsByLabel($label, $this->user());
		if(!$media)
			$media = $this->media->bySecret($label);

		if(!$media)
			$this->error("Unauthorized");
		$this->render(['status'=>$media->status], 'json');
	}
}




