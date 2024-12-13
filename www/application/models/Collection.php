<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Collection extends CI_Model {

	public function byLabel($label) {
		$collection = $this->db->query("SELECT * FROM collections WHERE label = ? AND publish = 1", $label)->row();
		return $collection;
	}

	public function addMedia($collection, $onlypublish = true) {
		$where = ['cm.collection = ?', "processed = 1"];
		if($onlypublish)
			$where[] = "publish = 1";

		$collection->media = $this->db->query(
			"SELECT * from media m ".
			"JOIN collections_media cm on cm.media = m.id ".
			"WHERE ".implode(' AND ', $where), $collection->id)->result();
	}

	public function ownsByLabel($label, $user) {
		$label = urldecode($label);
		return $this->owns('label = ?', $label, $user);
	}

	public function ownsById($id, $user) {
		return $this->owns('id = ?', $id, $user);
	}

	public function owns($condition, $param, $user) {
		if($user == null) return null;
		$admincheck = $user->role == 'admin' ? 'TRUE' : 'FALSE';
		$collection = $this->db->query("SELECT * FROM collections WHERE ($condition AND (userid = ? OR $admincheck))",
			array($param, $user->id))->row();
		return $collection;
	}


	function create($collection) {
		unset($collection['id']);
		$collection['label'] = $this->uniqueLabel($collection['title']);
		$this->db->insert('collections', $collection);
	}

	function update($collection, $existing) {
		$id = $collection['id'];

		unset($collection['id']);
		unset($collection['label']);

		if($collection['title'] != $existing->title) {
			$collection['label'] = $this->uniqueLabel($collection['title']);
		}

		$this->db->where('id', $id);
		$this->db->update('collections', $collection);
	}

	function delete($id) {
		$this->db->query("DELETE FROM collections where id = ?", array($id));
	}

	public function uniqueLabel($title) {
		$prelabel = filenamer($title);

		$label = $prelabel;
		$count = 0;
		while(1) {
			if($this->db->query("SELECT id FROM collections WHERE label = ?", $label)->row() == NULL)
				break;
			$label = "$prelabel-$count";
			$count++;
			if($count > 10)
				$this->jsonError('Too many collections with the same title.');
		}
		return $label;
	}

	//copy config from media to all the other elements (of the same type!)
	public function copyConfig($collectionid, $mediaid) {
		$sourcemedia = $this->db->query("SELECT options FROM media WHERE id = ?", $mediaid)->row();

		$options = json_decode($sourcemedia->options);

		if(isset($options->trackball) && 
			isset($options->trackball->trackOptions) &&
			isset($options->trackball->trackOptions->startMatrix)) 
			unset($options->trackball->trackOptions->startMatrix);

		if(isset($options->scene)) {
			$scene = $options->scene[0];
			if(isset($scene->matrix)) {
				unset($scene->matrix);
			}
		}


		$allmediaconfigs = $this->db->query(
			"SELECT m.id, m.options FROM media m
			JOIN collections_media cm on m.id = cm.media 
			WHERE cm.collection = ?", $collectionid)->result();


		$newoptions = [];
		foreach($allmediaconfigs as $m) {
			$o = json_decode($m->options);
			$startMatrix = null;
			if(isset($o->trackball) && 
				isset($o->trackball->trackOptions) &&
				isset($o->trackball->trackOptions->startMatrix)) {
				$startMatrix = $o->trackball->trackOptions->startMatrix;
			}

			$matrix = null;
			if(isset($o->scene)) {
				$scene = $o->scene[0];
				if(isset($scene->matrix)) 
					$matrix = $scene->matrix;
			}

			$o = $options;
			if($startMatrix)
				$o->trackball->trackOptions->startMatrix = $startMatrix;
			if($matrix)
				$o->scene[0]->matrix = $matrix;
			$newoptions[] = ['id' => $m->id, 'options' => json_encode($o)];
		}

		//process all the options
		$this->db->update_batch('media', $newoptions, 'id');
	}
};

