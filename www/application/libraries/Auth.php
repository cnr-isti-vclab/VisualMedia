<?php

class Auth {

	var $db;

function __construct() { 
	$this->db = &get_instance()->db;
	$this->session = &get_instance()->session;
}

public function hash($salt) {
	date_default_timezone_set('UTC');
	$now = date('Y-m-d H:i:s');
	return md5($now.$salt);
}

public function addUser($user, $identity) {
	date_default_timezone_set('UTC');
	$user['created'] =  date('Y-m-d H:i:s');
	$user['validate'] = $this->hash("validate");
	$this->db->insert('users', $user);
	$userid = $this->db->insert_id();

	$identity['userid'] = $userid;
	$identity['token'] = $this->hash("token");
	$this->db->insert('identities', $identity);
}

public function addIdentity($identity) {
	$identity['token'] = $this->hash($identity['userid']."token");
	$this->db->insert('identities', $identity);
}

/* PASSWORDLESS */

public function createPasswordless($email) {

	$user = $this->db->query("SELECT u.*, i.userid from users u LEFT JOIN identities i on i.userid = u.id and provider='passwordless' WHERE email = ?", $email)->row();
	date_default_timezone_set('UTC');

	if(!$user)
		$this->addUser(array('email'=>$email), array('provider'=>'passwordless', 'uid'=>$this->hash($email)));

	//userid is used only to test if at least a passwordless identity is associated with the user
	else if(!$user->userid)
		$this->addidentity(array('userid'=>$user->id, 'provider'=>'passwordless', 'uid'=>$this->hash($email)));


	$user = $this->db->query("SELECT * from users u JOIN identities i on i.userid = u.id WHERE provider='passwordless' AND email = ?", $email)->row();

	$user->{'validate'} = $this->hash("validate1");
	$this->db->query("UPDATE users SET validate = ? WHERE id = ?", array($user->validate, $user->id));

	return $user;
}

public function authenticatePasswordless($token) {
	return $this->db->query("SELECT * from users u JOIN identities i on i.userid = u.id WHERE provider='passwordless' AND validate = ?", $token)->row();
}

/* GOOGLE */

public function initGoogle() {
	$client_id = '257893829634-3l1e27frsg2vp8d014sumd93bgtrtg7g.apps.googleusercontent.com';
	$client_secret = 'UGWx0XQMT-OPXaSAFftj-oyO';
#		$redirect_uri = 'https://ariadne.isti.cnr.it/login'; //http://visual.ariadne-infrastructure.eu/login

	$host = $_SERVER['HTTP_HOST'];
	if($host == '146.48.85.249')
		$host = 'visual.ariadne-infrastructure.eu';

	$redirect_uri = "https://$host/login"; 
	$application_name = 'Ariadne';
	$api_key  = '';
	$scopes  = array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile');

	require APPPATH .'third_party/Google/vendor/autoload.php';

	$client = new Google_Client();
	$client->setApplicationName($application_name);
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->setDeveloperKey($api_key);
	$client->setScopes($scopes);

//		use this for refresh token
	$client->setAccessType('offline');
	$client->setApprovalPrompt('force'); 
//		$client->setAccessType('online');
//		$client->setApprovalPrompt('auto');

	$client->setState('google');
	return $client;
}

function genericAuthenticate($provider, $token, $user_info) {
	//Token is and array with access_token, refresh_token, uid
	//info contains email, name
	//returns a user object


	$email = $user_info['email'];
	$name  = $user_info['name'];
	$access = $token['access_token'];
	$refresh = $token['refresh_token'];
	$uid = $user_info['uid'];

	$identity = array('access_token'=>$access, 'refresh_token'=> $refresh, 'uid'=>$uid);

	//check wether this identity exists.
	$user = $this->db->query("SELECT * FROM users u LEFT JOIN identities i on i.userid = u.id AND i.provider=? WHERE email=?", [$provider, $email] )->row();

	//var_dump($user);
	if(!$user) {                       //create user and identity
		$identity['provider'] = $provider;
		$this->addUser(array('email'=>$email, 'name'=>$name), $identity);

	} else if(!$user->provider) {      //create identity
		$identity['userid'] = $user->id;
		$identity['provider'] = $provider;
		$this->db->insert('identities', $identity);

	} else {                           //update identity
		$this->db->where('provider', $provider);
		$this->db->where('userid', $user->id);
		$this->db->update('identities', $identity);
	}


	return $this->db->query("SELECT * from users u JOIN identities i on i.userid = u.id WHERE provider=? AND email=?", [$provider, $email] )->row();
}


public function authenticateGoogle($code) {
	$client = $this->initGoogle();
	$oauth2 = new Google_Service_Oauth2($client);

	//authenticate user
	$response = $client->authenticate($code);
	if(isset($response['error']))
		show_error($response['error'].': '.$response['error_description'], 403);

	//get user info from google
	$info = $oauth2->userinfo->get();
	$access = $client->getAccessToken();
		
	$email = $info['email'];
	$name  = $info['given_name'].' '.$info['family_name'];

	return $this->genericAuthenticate('google', $access, array('email'=>$email, 'name'=>$name, 'uid'=>$info['id']));
}
	
public function authenticateH2IOSC($code) {
	$token = http_build_query([
	    'grant_type' => 'authorization_code',
	    'code' => $code,
	    'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/login',
	    'client_id' => H2IOSC_CLIENT,
	    'client_secret' => H2IOSC_SECRET,
		'response_type' => 'code'
	]);

	$ch = curl_init(H2IOSC_URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
	$response = curl_exec($ch);
	$data = json_decode($response, true);

	if(isset($data['error'])) {
		show_error($data['error'].': '.$data['error_description'], 403);
	}
	
	// ID token contains user info in JWT
	$id_token = $data['id_token'];
	$access_token = $data['access_token'];

	// Decode the JWT (basic, not verified here)
	list($header, $payload, $signature) = explode('.', $id_token);
	$userinfo = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	$userinfo['name'] = $userinfo['preferred_username'];
	$userinfo['uid'] = $userinfo['sub'];
	
	list($header, $payload, $signature) = explode('.', $access_token);
	$token = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	$token['access_token'] = $token['refresh_token'] = $token['sub'];
	
	return $this->genericAuthenticate('h2iosc', $token, $userinfo);
}

public function authenticateOrcid($code) {

		$headers = array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded');
		$params = array(
				'url'           =>'https://orcid.org/oauth/token',
				'grant_type'    =>'authorization_code',
				'code'          =>$code,
				'redirect_uri'  => 'https://'.$_SERVER['HTTP_HOST'].'/login',
				'client_id'     => 'APP-INSSY2VKBR9EV73P',
				'client_secret' =>'f38ad7dc-0bd3-44ac-acef-44963f2c62ec');
	
		$res = $this->postRequest('https://orcid.org/oauth/token', $params, $headers);
		$info = json_decode($res);
		if(isset($info->error)) {
			echo($info->error. ' ' .$info->error_description);
			exit(0);
		}

		$name = $info->name;
		$uid  = $info->orcid;

		$identity = 	array(
			'access_token'  => $info->access_token, 
			'refresh_token' => $info->refresh_token, 
			'uid'           => $uid);

		//check wether this identity exists.
		$user = $this->db->query("SELECT * FROM users u LEFT JOIN identities i on i.userid = u.id AND i.provider='orcid' WHERE uid = ?", $uid)->row();

		if(!$user) {                       //create user and identity
			$identity['provider'] = 'orcid';
			$this->addUser(array('name'=>$name, 'username'=>$uid), $identity);
		} else {                           //update identity
			$this->db->where('provider', 'orcid');
			$this->db->where('userid', $user->id);
			$this->db->update('identities', $identity);
		}


		$user = $this->db->query("SELECT * FROM users u LEFT JOIN identities i on i.userid = u.id AND i.provider='orcid' WHERE uid = ?", $uid)->row();

		return $user;
	}


}

?>
