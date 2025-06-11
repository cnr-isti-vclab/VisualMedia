<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 



class Media extends CI_Model {
	var $allowed_types = array(
		'img'   => array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff'),
		'album' => array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff'),
		'3d'    => array('ply', 'obj', 'mtl', 'nxs', 'nxz', 'jpg', 'jpeg', 'png'),
		'rti'   => array('rti', 'ptm', 'json', 'lp', 'jpg')
	);

	var $tools = array('Measure'=>1, 'Picking'=>2, 'Sections'=>4, 'Color on/off'=>8);

	public function ownsById($id, $user) {
		return $this->owns('id = ?', $id, $user);
	}

	public function owns($condition, $param, $user) {
		$admincheck = $user->role == 'admin' ? 'TRUE' : 'FALSE';
		$media = $this->db->query("SELECT * FROM media WHERE ($condition AND (userid = ? OR $admincheck))",
			array($param, $user->id))->row();
		return $media;
	}

	//TODO: clean up this mess and addFiles should be done where needed.

	public function ownsMedia($label, $user) {
		if(!$user) return null;
		$label = urldecode($label);
		$admincheck = $user->role == 'admin' ? 'TRUE' : 'FALSE';
		$media = $this->db->query("SELECT * FROM media WHERE (label = ? AND (userid = ? OR $admincheck))", array($label, $user->id))->row();
		//$this->addFiles($media);
		return $media;
	}

	public function ownsModel($id, $user) {
		if(!$user) return null;

		$admincheck = $user->role == 'admin' ? 'TRUE' : 'FALSE';
		$model = $this->db->query("SELECT mo.* FROM model mo JOIN media m ON mo.media = m.id WHERE (mo.id = ? AND (m.userid = ? OR $admincheck))", array($label, $user->id))->row();
		return $model;
	}

	public function ownsFile($id, $user) {
		$file = $this->db->query("SELECT f.*, m.path as mediapath, m.status as status FROM files f JOIN media m on m.id = f.media WHERE f.id = ? AND m.userid = ?", 
			array($id, $user->id))->row();
		return $file;
	}

	public function byLabel($label) {
		$media = $this->db->query("SELECT * FROM media WHERE label = ? AND publish = 1", $label)->row();
		//$this->addFiles($media);
		return $media;
	}

	public function bySecret($secret) {
		$media = $this->db->query("SELECT * FROM media WHERE secret = ?", $secret)->row();
		//$this->addFiles($media);
		return $media;
	}

	public function addCollections($media) {
		$media->collections = $this->db->query("SELECT c.* from collections c 
			JOIN collections_media cm on cm.media = ? and cm.collection = c.id", $media->id)->result();
	}

	public function addModels($media) {
		$media->{'models'} = $this->db->query("SELECT mo.* FROM models WHERE media = ? order by label", $media->id)->result();
	}

	public function addFiles($media) {
		if($media)
			$media->{'files'} = $this->db->query("SELECT f.*, m.path as mediapath FROM files f JOIN media m on m.id = f.media WHERE media = ? order by label", $media->id)->result();
//		$this->addCollections($media);
	}

	public function setModelStatus($model, $status) {
		$this->db->update('models', array('status'=>$status), array('id'=>$model->id));
	}

	public function setMediaStatus($media, $status) {
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
			if($count > 10)
				$this->jsonError('Too many object with the same title.');
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


/*
	public function upload($data) {


		//TODO VALIDATE!
		date_default_timezone_set('UTC');
		$now = date('Y-m-d H:i:s');
		$secret = md5($now."salt?");
		$media = array(
			'email' => $data['email'],
			'name' => $data['name'],
			'institution' => $data['institution'],

			'label' =>$data['label'], 
			'title' => $data['title'], 
			'description' => htmlentities($data['description']), 
			'owner' => $data['owner'],
			'collection' => $data['collection'], 
			'url' => $data['url'],

			'background' => $data['background'],
			'skin' => $data['skin'],
			'tools' => $data['tools'],
			'trackball'=> $data['trackball'] , 

			'creation'=>$now, 
			'secret'=>$secret,
			'ip'=>$data['ip'],
			'private' => $data['private'],
			'filename'=>$data['filename'], 
			'size'=>$data['size'], 
			'status'=>'waiting');

		if($data['userid'])
			$media['userid'] = $data['userid'];

		$insert_string = $this->db->insert_string('media', $media);	  
		$insert_string .= ' RETURNING id AS last_id';
		$query = $this->db->query($insert_string);
		$query = $this->db->query("SELECT CURRVAL('media_id_seq') as last_id");
		$id = $query->row();
		$id = $id->last_id;

		//insert tags.
		$tags = array();
		foreach ($data['tags'] as $t) 
			$tags[] = array('media' => $id, 'tag'=>$t);
		
		$this->db->insert_batch('tags', $tags);

		return $id;
	} */

	public function create($media) {
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
		$id = $this->db->insert_id();
		return ['label'=> $label, 'id' => $id];
	}

	public function createModel($mediaid, $files) {

		$preferred3D = ['obj', 'ply', 'nxs', 'nxz'];
		$imageTypes = ['jpg', 'jpeg', 'png'];
		
		$label = null;
		
		// Normalize to lowercase extensions and check priority
		foreach ($filenames as $f) {
			$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
		
			if (in_array($ext, $preferred3D)) {
				$label = pathinfo($f, PATHINFO_FILENAME);
				break;
			}
		}
		
		if (!$label) {
			foreach ($filenames as $f) {
				$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
		
				if (in_array($ext, $imageTypes)) {
					$label = pathinfo($f, PATHINFO_FILENAME);
					break;
				}
			}
		}
		
		if (!$label && count($filenames) > 0)
			$label = pathinfo($filenames[0], PATHINFO_FILENAME);

		$model = [
			'media' => $mediaid, 
			'label' => $label
		];
		$this->db->insert('models', $model);

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
		

		//rmdir
//		rmdir($
		
		/*
		
		$label = str_replace('.', '', $row->label); //. not allowed in labels but better be safe.
		if($row->status == 'success') {
			switch($row->media_type) {
				case 'img': $this->delTree(DATA_DIR.'img/'.$label); break;
				case 'rti': $this->delTree(DATA_DIR.'img/'.$label); break;
				case 'nexus': unlink(DATA_DIR.'3d/'.$label.'.nxs'); break;
				default: return "Unknown media type: ".$row->media_type;
			}			
		}

		if($row->status == 'waiting' || $row->status == 'failed') {
			$path = UPLOAD_DIR.$row->filename;
			if (file_exists($path))
				unlink($path); 
		}

		$this->db->delete('media', array('secret' => $secret)); */
		return NULL;
	}

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
	}


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
	}



/* UNUSED! 

	public function tags($id) {
		$query = $this->db->query("SELECT tag FROM tags WHERE media = ?", array($id));
		$result = 
	
		$tags = array();
		foreach($query->result() as $t)
				$tags[] = $t->tag;  
		return $tags;
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
