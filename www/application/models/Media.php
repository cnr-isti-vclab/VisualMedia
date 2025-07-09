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

	public function ownsByLabel($label, $user) {
		if(!$user) return null;
		$label = urldecode($label);
		return $this->owns('label = ?', $label, $user);
	}

	public function ownsFile($id, $user) {
		$file = $this->db->query("SELECT f.*, m.path as mediapath, m.status as status FROM files f JOIN media m on m.id = f.media WHERE f.id = ? AND m.userid = ?", 
			array($id, $user->id))->row();
		return $file;
	}

	public function byLabel($label) {
		$media = $this->db->query("SELECT * FROM media WHERE label = ? AND publish = 1", $label)->row();
		return $media;
	}

	public function bySecret($secret) {
		$media = $this->db->query("SELECT * FROM media WHERE secret = ?", $secret)->row();
		return $media;
	}

	public function addCollections($media) {
		$media->collections = $this->db->query("SELECT c.* from collections c 
			JOIN collections_media cm on cm.media = ? and cm.collection = c.id", $media->id)->result();
	}

	public function getFiles($media) {
		return $this->db->query("SELECT f.*, m.path as mediapath FROM files f JOIN media m on m.id = f.media WHERE media = ? order by label", $media->id)->result();
	}

	public function uploadPath($media) {
		return UPLOAD_DIR.$media->path;
	}

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
		if($media_type == '3d') {
			$media['variants'] = '[{"version": 0, "parent": -1, "label": "Original", "creation": "'.$now.'"}]';
		}

		$path = $media['path'];
		$upload_path = UPLOAD_DIR.$path;
		$data_path = DATA_DIR.$path;

		if (file_exists($upload_path) || file_exists($data_path))
			return ['error'=> "Oooopps a directory ($path) is in the way. Please contact us (and just change the label)"];

		try {
			umask(0);
			if(!mkdir($upload_path, 0777, true))    //be sure to have a suid to federico in ariadne1
				return ['error'=> 'Failed to create upload directory'];
		} catch(Exception $e) {
			return ['error'=> 'Error while creating dir.'];
		}

		$this->db->insert('media', $media);
		$id = $media['id'] = $this->db->insert_id();
		foreach($files as $f) {
			
			$result = $this->createFile((object)$media, $f);
			if(isset($result['error'])) {
				return $result; //return error
			}
		}
		return ['label'=> $label, 'id' => $id];
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


	public function fileUploaded($media, $filename) {
		$file = $this->db->query("SELECT * FROM files WHERE media = ? AND filename = ?", array($media->id, $filename))->row();
		if(!$file) {
			return;
		}
		//update file status
		$this->db->update('files', array('status'=>'ready'), array('id'=>$file->id));

		//check if all files are uploaded
		$files = $this->db->query("SELECT * FROM files WHERE media = ? AND status != 'ready'", array($media->id))->result();
		if(count($files) == 0) {
			$this->db->update('media', array('status'=>'on queue'), array('id'=>$media->id));
		}
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
	public function createFile($media, $filename) {

		$exists = $this->db->query("SELECT id FROM files WHERE filename = ? AND media = ?", array($filename, $media->id))->row();
		if($exists)
			return ['error' => 'File exists!'];

		$ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if(!in_array($ext, $this->media->allowed($media)))
			return ['error' => "Unknown extension: ".$ext];
	

		$prelabel = filenamer(pathinfo($filename, PATHINFO_FILENAME));
		$label = $prelabel;
		$count = 0;
		while(1) {
			if($this->db->query("SELECT id FROM files WHERE label = ? AND media = ?", array($label, $media->id))->row() == NULL)
				break;
			$label = "$prelabel-$count";
			$count++;
		}

		$path = $this->uploadPath($media).$filename;

		$formats = array('jpg'=>'img', 'jpeg'=>'img', 'png'=>'img', 'gif'=>'img', 'tif'=>'img', 'tiff'=>'img', 
			'ply'=>'3d', 'obj'=>'3d', 'nxs' => '3d', 'nxz' => '3d',
			'rti'=>'rti', 'ptm'=>'rti', 'json'=>'rti');
		$format  = isset($formats[$ext])? $formats[$ext] : 'unknown';


		$files = array(
			'media'    => $media->id,
			'format'   => $format,
			'ext'      => $ext,
			'filename' => $filename,
			'label'    => $label,
			'status'   => 'uploading',
		);

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

	public function modify($media, $data) {
		//convert data to json
		$json = json_encode($data);
		if($json === false) {
			return ['error' => 'Invalid JSON data: '.json_last_error_msg()];
		}
		//change status to processing and todo to json
		$this->db->update('media', array('status'=>'modify', 'todo'=>$json), array('id'=>$media->id));
		return [];
	}


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
