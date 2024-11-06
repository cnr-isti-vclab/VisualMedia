<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Collectioncontroller extends MY_Controller {


	function __construct() {
		parent::__construct();
		$this->load->library('auth');
	}

	function ensureLogged() {
		if(!$this->isLogged())
			$this->error("</p>It looks like you are not logged in. Maybe the session expired or the cookies were deleted.</p>".
				"<p>Please login and try again...</p>");
	}

	function notFound() {
		$error = "<p>The link does not seems to be valid:</p>\n".
			"<ul>\n".
			"<li>the collection might have been removed or\n".
			"<li>the media is not currently published or\n".
			"<li>the link is wrong\n".
			"</ul>\n".
			"<p>Contact us for help: <a href='mailto:ponchio@isti.cnr.it'>ponchio@isti.cnr.it</a></p>\n";
		$this->error($error);
	}

	function show($label) {
		$collection = $this->collection->ownsByLabel($label, $this->user());

		if(!$collection)
			$collection = $this->collection->byLabel($label);

		if(!$collection) {
			$this->error("<p>The link does not seems to be valid:</p>\n".
				"<ul>\n".
				"<li>some problem occourred moving to the version or\n".
				"<li>the collection might have been removed or\n".
				"<li>the collection is not currently published or\n".
				"<li>the link is wrong\n".
				"</ul>\n".
				"<p>Contact us for help: <a href='mailto:ponchio@isti.cnr.it'>ponchio@isti.cnr.it</a></p>\n"
			);
		}
		$this->collection->addMedia($collection);

		$this->data['media'] = $collection->media;
		$this->data['browsertable'] = $this->load->view('browsertable', $this->data, TRUE);

		$this->data['collection'] = $collection;
		$this->render('collection/show');
	}

	//not actually creating, just managing
	function create() {
		return $this->manage();
	}

	function manage($label = NULL) {
		$this->ensureLogged();
		$user = $this->user();

		$collection = $this->collection->ownsByLabel($label, $user);
		$objects = $this->media->userMediaById($user->id);
		if($label != '' && !$collection) 
			return $this->notFound();

		$empty = (object)[
			'id' => NULL,
			'title' => '',
			'userid' => $user->id,
			'label' => '',
			'description' => '',
			'body' => '',
			'publish' => FALSE,
			'category' => '',
			'url' => ''
		];
		$this->data['objects'] = $objects;
		$this->data['collection'] = $collection? $collection : $empty;
		$this->render('collection/manage');
	}

	function update() {
		//ensure you are logged.
		$this->ensureLogged();

		//look for existing 
		$collection = $_POST;

		//if we are creating set the user as owner
		if(!isset($collection['userid']))
			$collection['userid'] = $this->user()->id;

		if($collection['id'] == '')
			$this->collection->create($collection);

		else {
			//ensure the collection exists
			$existing =  $this->collection->ownsById($collection['id'], $this->user());
			if(!$existing) {
				return $this->jsonError("Collection not found.");
			}

			$this->collection->update($collection, $existing);
		}

		return $this->jsonOk();
	}

	function delete($id) {
		$this->ensureLogged();
		$collection = $this->collection->ownsById($id, $this->user());
		if(!$collection)
			$this->jsonError("Not found.");

		$this->collection->delete($collection->id);
		$this->jsonOk();
	}
	public function publish() {
		$id = $this->input->post("collection");
		$collection = $this->collection->ownsById($id, $this->user());
		if(!$collection)
			$this->jsonError("Unknown collection or not owner");

		$this->db->query("UPDATE collections SET publish = 1 WHERE id = ?", $collection->id);
		$this->jsonOk();
	}

	public function unpublish() {
		$id = $this->input->post("collection");
		$collection = $this->collection->ownsById($id, $this->user());
		if(!$collection)
			$this->jsonError("Unknown collection or not owner");

		$this->db->query("UPDATE collections SET publish = 1 WHERE id = ?", $collection->id);
		$this->render(array(), 'json');
	}

	function addMedia($collectionid, $mediaid) {
		$collection = $this->collection->ownsById($collectionid, $this->user());
		$media = $this->media->ownsById($mediaid, $this->user());
		if(!$collection)
			$this->jsonError("Collection not found.");
		if(!$media)
			$this->jsonError("Media not found.");
		$this->db->insert('collections_media', ['media'=>$mediaid, 'collection'=>$collectionid]);
		$this->jsonOk();
	}

	function removeMedia($collectionid, $mediaid) {
		$collection = $this->collection->ownsById($collectionid, $this->user());
		$media = $this->media->ownsById($mediaid, $this->user());
		if(!$collection)
			$this->jsonError("Collection not found.");
		if(!$media)
			$this->jsonError("Media not found.");
		$this->db->query('DELETE FROM collections_media where media=? and collection = ?', ['media'=>$mediaid, 'collection'=>$collectionid]);
		$this->jsonOk();
	}

	function invalid() {
		$error = "<p>The link does not seems to be valid:</p>\n".
				"<ul>\n".
				"<li>the collection doesn't exists\n".
				"<li>you are not the owner of the collection\n".
				"</ul>\n".
				"<p>Contact us for help: <a href='mailto:ponchio@isti.cnr.it'>ponchio@isti.cnr.it</a></p>\n";
		$this->error($error);
	}


	function batch($label) {
		if(!$this->isAdmin())
			$this->error("Require admin privilege");
		$collection = $this->collection->ownsByLabel($label, $this->user());
		if(!$collection)
			$this->invalid();

		$this->data['collection'] = $collection;
		$this->render('collection/batch');
	}

	function status($label) {
		$collection = $this->collection->ownsByLabel($label, $this->user());
		if(!$collection)
			$this->jsonError("Unknown collection or not owner");
		//collect all files and media
		$collection->files = $this->db->query(
			"SELECT f.filename, m.id as mediaid ".
			"from files f ".
			"join media m on f.media = m.id ".
			"join collections_media cm on cm.media = m.id and cm.collection = ?",
			array($collection->id))->result();
		$this->render($collection, 'json');
	}

	function uploadFile() {
		$collectionid = $this->input->post('collection');
		$collection = $this->collection->ownsById($collectionid, $this->user());
		if(!$collection) 
			$this->jsonError("Unknown collection or not owner");

		$file = $_FILES['file'];

		//look for file in collection with same name
		$record = $this->db->query("SELECT m.id from media m ".
			"join files f on f.media = m.id ".
			"join collections_media cm on cm.media = m.id and cm.collection = $collectionid ".
			"where f.filename = ?", array($file['name']))->row();


		if($record) {
			$mediaid = $record->id;

		} else {
			$media = [];
			$media['userid']      = $this->user()->id;
			$media['title']       = $this->input->post('title');
			$media['description'] = $this->input->post('description');
			$media['url']         = $this->input->post('url');
			$media['collection']  = $this->input->post('collection');
			$media['owner']       = $this->input->post('copyright');
			$media['media_type']  = $this->input->post('media_type');

			$result = $this->media->create($media);

			if($result['error'])
				$this->jsonError($result['error']);
			$mediaid = $result['id'];

			$this->db->insert('collections_media', array('media'=>$mediaid, 'collection' => $collectionid));
		}

		$media = $this->media->ownsById($mediaid, $this->user());


		$result = $this->media->uploadFile($media, $file, TRUE);
		if(isset($result['error']))
			$this->jsonError($result['error']);

		$this->db->query("update media set status = 'on queue' where id = ?", array($media->id));

		$this->render(array(
			'mediaid'=>$mediaid,
			'fileid'=>$result['id']), 'json');
	}

	function copyConfig($collectionid, $mediaid) {
		$this->collection->copyConfig($collectionid, $mediaid);
		echo('Done');
	}

}

