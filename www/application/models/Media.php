<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 



class Media extends CI_Model {
	var $allowed_types = array(
		'img'   => array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff'),
		'album' => array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff'),
		'3d'    => array('ply', 'obj', 'mtl', 'nxs', 'nxz', 'jpg', 'jpeg', 'png'),
		'rti'   => array('rti', 'ptm', 'json', 'lp', 'jpg')
	);

	var $tools = array('Measure'=>1, 'Picking'=>2, 'Sections'=>4, 'Color on/off'=>8);

	public function owns($condition, $param, $user) {
		$admincheck = $user->role == 'admin' ? 'TRUE' : 'FALSE';
		$media = $this->db->query("SELECT * FROM media WHERE ($condition AND (userid = ? OR $admincheck))",
			array($param, $user->id))->row();
		return $media;
	}

	public function ownsById($id, $user) {
		return $this->owns('id = ?', $id, $user);
	}

	//TODO: clean up this mess and addFiles should be done where needed.

	public function ownsByLabel($label, $user) {
		if(!$user) return null;
		$label = urldecode($label);
		return $this->owns('label = ?', $label, $user);
	}

	public function byLabel($label) {
		$media = $this->db->query("SELECT * FROM media WHERE label = ? AND publish = 1", $label)->row();
		return $media;
	}

	public function bySecret($secret) {
		$media = $this->db->query("SELECT * FROM media WHERE secret = ?", $secret)->row();
		return $media;
	}


	public function addModels($media) {
		$media->{'models'} = $this->db->query("SELECT * FROM models WHERE media = ? order by label", $media->id)->result();
	}

	public function addCollections($media) {
		$media->collections = $this->db->query("SELECT c.* from collections c 
			JOIN collections_media cm on cm.media = ? and cm.collection = c.id", $media->id)->result();
	}


/*
	public function addFiles($media) {
		foreach($models as $m) {
			$path = $this->model->currentPath($media, $m);
			//list files in the model path
			$m->files = array();
			if(is_dir(DATA_DIR.$path)) {
				$files = scandir(DATA_DIR.$path);
				foreach($files as $f) {
					if($f == '.' || $f == '..') continue;
					$file = new stdClass();
					$file->label = $f;
					$file->mediapath = $path.'/'.$f;
					$file->filename = $f;
					$file->ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
					$file->format = 'unknown';
					if(preg_match('/\.(jpg|jpeg|png|gif|tif|tiff)$/', $file->ext)) {
						$file->format = 'img';
					} elseif(preg_match('/\.(ply|obj|nxs|nxz)$/', $file->ext)) {
						$file->format = '3d';
					} elseif(preg_match('/\.(rti|ptm|json)$/', $file->ext)) {
						$file->format = 'rti';
					}
				}
			}
		}
	} */

	public function setStatus($media, $status) {
		//uploading, on queue, processing, ready, failed
		$this->db->update('media', array('status'=>$status), array('id'=>$media->id));
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
		}
		return $label;
	}

	public function allowed($type) {
		if(!is_string($type))
			$type = $type->media_type;
		return $this->allowed_types[$type];
	}

	public function link($media) {
		$type;
		switch($media->media_type) {
		case '3d'   : $type = '3d'; break;
		case 'rti'  : $type = 'rti'; break;
		case 'img'  : $type = 'img'; break;
		case 'album': $type = 'album'; break;
		default: $type = 'unknown'; break;
		}
		if(!$type) return null;
		$label = $media->label;
		return "/$type/$label";
	}

	public function secretlink($media) {
		$type;
		switch($media->media_type) {
		case '3d'   : $type = '3d'; break;
		case 'rti'  : $type = 'rti'; break;
		case 'img'  : $type = 'img'; break;
		case 'album': $type = 'img'; break;
		default: $type = 'unknown'; break;
		}
		if(!$type) return null;
		$label = $media->secret;
		return "/$type/$label";
	}

	public function search($options = array(), $start=null, $size=null) {
		$q = "SELECT * FROM media WHERE ".implode(" AND ", $options)." order by creation desc";
		$query = $this->db->query($q);  
		$media = $query->result();
		foreach($media as &$m) 
			$m->link = $this->link($m);
		return $media;
	}

	public function userMediaById($userid) {
		$query = $this->db->query("SELECT media.*, c.id as collectionid, c.label as collectionlabel, c.title as collectiontitle ".
			"FROM media ".
			"LEFT JOIN collections_media cm ON media.id = cm.media ".
			"LEFT JOIN collections c ON cm.collection = c.id ".
			"WHERE media.userid = ?", $userid);
		return $query->result();
	}




	public function create($media, $files) {
		if(!preg_match('/^[\w\(\)\,\.\!\?\:\;`\'"\\s]+$/', $media['title']))
			return ['error' => "Invalid characters in title, keep it to just text and punctuation."];
		
		$label = $this->uniquelabel($media['title']);
		
		$media_type = $media['media_type'];
		if(!in_array($media_type, array('3d', 'rti', 'img', 'album')))
			return ['error' => "Unknown media type: $media_type"];

		$now = date('Y-m-d H:i:s');

		$path = $media['media_type'].'/'.$label.'/';
		$media['label']    = $label;
		$media['creation'] = $now;
		$media['secret']   = md5($now."salt?");
		$media['path']     = $path;
		$media['status']   = 'uploading';

		$path = $media['path'];
		$upload_path = UPLOAD_DIR.$path;
		$data_path = DATA_DIR.$path;

		if (file_exists($upload_path) || file_exists($data_path))
			return ['error'=> "Oooopps a directory ($path) is in the way. Please contact us (and just change the label)"];

		try {
			umask(0);
			if(!mkdir($upload_path, 0777, true))    //be sure to have a suid to federico in ariadne1
				return ['error'=> 'Failed to create upload directory'];

/*			if(!mkdir($data_path, 0770, true)) //be sure to have a suid to federico in ariadne1
				$this->jsonError("Failed to create data directory $data_path");
			chmod($data_path, 02770); */

		} catch(Exception $e) {
			return ['error'=> 'Error while creating dir.'];
		}

		$this->db->insert('media', $media);
		$id = $media['id'] = $this->db->insert_id();
		$model_id = $this->model->create((object)$media, $files);
		return ['label'=> $label, 'id' => $id, 'model_id' => $model_id];
	}

	public function addModel($media, $files) {
		$model_id = $this->model->create($media, $files);
		return ['label'=> $model->label, 'id' => $model_id];
	}

	public function update($data) {
		$media = array(
			'email'	   => $data['email'], 
			'name'		=> $data['name'],
			'institution' => $data['institution'], 

			'background' => $data['background'],
			'skin'	   => $data['skin'],
			'tools'	  => $data['tools'],
			'trackball'  => $data['trackball'],

			'label' =>$data['label'], 
			'title' => $data['title'], 
			'description' => htmlentities($data['description']), 
			'owner' => $data['owner'], 
			'collection' => $data['collection'], 
			'url' => $data['url'],
			'private' => $data['private']);

		$this->db->update('media', $media, array('secret'=>$data['secret']));
		$this->db->delete('tags', array('media'=> $data['id']));
		$tags = explode(',', $data['tags']);
		$insert = array();
		foreach($tags as $t)
			$insert[] = array('media'=>$data['id'], 'tag'=>$t);
	
		$this->db->insert_batch('tags', $insert);
		return true;
	}

//THIS IS TOO DANGEROUS!
	public function delTree($dir) { 
		if(!is_dir($dir))
			return true;
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file") && !is_link($dir)) ? $this->delTree("$dir/$file") : unlink("$dir/$file"); 
		} 
		rmdir($dir);
		return !is_dir($dir);
	}

	#it's noe the .py in charge of deleting.
	public function delete_deprecated($media) {
		if($media->status == 'processing') 
			return "Cannot remove a media while it is processing. Just wait a bit.";

		//remove processed files

		//remove all files
		foreach($media->files as $file)
			$this->removeFile($file);

		$removed = $this->delTree(UPLOAD_DIR.$media->path);
		if(!$removed)
			return "Could not remove upload dir: ".$media->path;

		$removed = $this->delTree(DATA_DIR.$media->path);
		if(!$removed)
			return "Could not remove data dir: ".$media->path;


		$this->db->query("DELETE FROM files WHERE media = ?", $media->id);
		$this->db->query("DELETE FROM media WHERE id = ?", $media->id);
		
		return NULL;
	}


/*
	public function uploadFile($media, $file, $overwrite) {

		$exists = $this->db->query("SELECT id FROM files WHERE original = ? AND media = ?", array($file['name'], $media->id))->row();
		if($exists && !$overwrite)
			return ['error' => 'File exists!'];

		$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(!in_array($ext, $this->media->allowed($media)))
			return ['error' => "Unknown extension: ".$ext];
	
		$debug = $file["tmp_name"];

		$prelabel = filenamer(pathinfo($file['name'], PATHINFO_FILENAME));
		$label = $prelabel;
		$count = 0;
		while(1) {
			if($this->db->query("SELECT id FROM files WHERE label = ? AND media = ?", array($label, $media->id))->row() == NULL)
				break;
			$label = "$prelabel-$count";
			$count++;
			if($count > 10)
				return ['error' => 'Too many object with the same title.'];
		}

		$filename = $file['name'];


//			$types[] = $file['type']; What to do with this?

		$path = UPLOAD_DIR.$media->path.$filename;

		try {
			rename($file['tmp_name'], $path);
			chmod($path, 0664);
		} catch(Exception $e) {
			return ['error' => "Could not upload file."];
		}

		if($exists)
			return ['id' => $exists->id];

		//new files

		$formats = array('jpg'=>'img', 'jpeg'=>'img', 'png'=>'img', 'gif'=>'img', 'tif'=>'img', 'tiff'=>'img', 
			'ply'=>'3d', 'obj'=>'3d', 'nxs' => '3d', 'nxz' => '3d',
			'rti'=>'rti', 'ptm'=>'rti', 'json'=>'rti');
		$format  = isset($formats[$ext])? $formats[$ext] : 'unknown';


		$files = array(
			'media'    => $media->id,
			'format'   => $format,
			'ext'      => $ext,
			'size'     => $file['size'],
			'original' => $file['name'],
			'filename' => $filename,
			'label'    => $label
		);

		$this->media->setStatus($media, 'uploading');
		$this->db->insert('files', $files);
		$id = $this->db->insert_id();
		return ['id' => $id];
	} */

/*
	public function deleteFile($file) {
		$removed = $this->removeFile($file);
		if($removed)
			return $removed;

		$this->db->query("DELETE FROM files WHERE id = ?", $file->id);
		return NULL;
	}

	public function removeFile($file) {
		//remove uploaded file
		$path = UPLOAD_DIR.$file->mediapath.$file->filename;
		if(file_exists($path))
			$removed = unlink($path);

		//remove processed file
		$media = $this->db->query("SELECT * FROM media WHERE id = ?", $file->media)->row();
		$path = DATA_DIR.$media->path.$file->label;
		if(file_exists($path))
			$this->delTree($path);
		if(file_exists($path.'.jpg'))
			unlink($path.'.jpg');
		if(file_exists($path.'.dzi'))
			unlink($path.'.dzi');
		if(file_exists($path.'_files')) 
			$this->delTree($path.'_files');

		return NULL;
	} */

	public function jobs($all) {
	  $where = array();
	  if($all)
		$where[] = '1=1';
	  else
		$where[] = "processing != 'processed'";
		
	  $query = $this->db->query("SELECT * FROM media WHERE ".implode(' AND ', $where)." ORDER BY creation DESC");
	  return $query->result();
	}

}
?>
