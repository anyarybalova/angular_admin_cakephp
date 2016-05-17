<?php 

class UsersController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');

		$excludeBeforeFilter = array('add','signin', 'signup');
		
		if (!in_array($this->action,$excludeBeforeFilter)){
			if(!$this->checkCredentials()){
				$response = array(
					'message' => "Access is denied due to invalid credentials.",
					'success' => false,		
					'NotAccess'  => true
				);
					header("Content-type: application/json");
					echo json_encode($response);
					//echo json_encode($response));
					exit();
			}
		}
	}


	private function checkCredentials(){
		$data = null;

		if($this->request->is('get')){
			$param = $this->params['url'];
			if($param){
				$array = (array)json_decode($param['User']);
				$data = array(
					'User' => $array
				);
			}
		}else{
			$data = $this->request->input ('json_decode', true);
		}
		if($data['User']){
			$ip =  $this->request->clientIp();
			return $this->checkToken($data['User'], $ip);

		}else{
			return false;
		}
		
	}
	

	
	public function index() {
		$response = array(
			'content' => [],
			'success' => false
		);

		$data = $this->params['url'];
		$array = (array)json_decode($data['User']);
		$data = array(
			'User' => $array
		);

		$ip = $this->request->clientIp();
		$user = $this->checkToken($data['User'], $ip);
		if($user){ 
			$response['content'] = $this->User->find('all');
			$response['success'] = true;
		}
		$this->response->body(json_encode($response));
	}


	public function view($id) {
		$response = array(
			'content' => [],
			'success' => false
		);

		$data = $this->params['url'];
		$array = (array)json_decode($data['User']);
		$data = array(
			'User' => $array
		);

		$ip = $this->request->clientIp();
		$ok = $this->checkToken($data['User'], $ip);
		if($ok){ 
			$this->User->id = $id;
			$response['content'] = $this->User->read();
			$response['success'] = true;
		}

		$this->response->body(json_encode($response));
	}
	
	public function add() {
		$response = array(
			'success' => false,
			'message' => 'Failed request save user.',
			'errors' => []
			);
		
		$this->data = $this->request->input ('json_decode', true);

		if($data['User']['password']){
			$data['User']['password'] = sha1($data['User']['password']); 
			//$message = $data;
			if ($user = $this->User->save($this->data)) {
				$response['success'] = true;
				$response['message'] = 'New user record has been saved.';
				$response['content'] = $user['User']['id'];
			}else {

			    $response['errors'] = $this->User->invalidFields();
			}
		}

		$this->response->body(json_encode($response));
	}


	public function edit($id = null) {
		$response = array(
			'success' => false,
			'message' => 'Unable to update your user.',
			'errors' => []
			);
		
	    if (!$id) {
	       $response['message'] .= ' - Invalid user id';
	    }
	    $user = $this->User->findById($id);
	    
	    if (!$user) {
	       $response['message'] .= ' - Invalid user';
	    }
	    else{
	    	$data = json_decode($this->request->data, true);

		    $this->User->id = $id;	    

	        if ($this->User->save($data)) {
	        	$response['success'] = true;
				$response['message'] = 'Your user has been updated.';
	        }else {

		    $response['errors'] = $this->User->invalidFields();
			}

		}    
	    $this->response->body(json_encode($response));
	}

	public function delete($id) {
		$response = array(
			'success' => false
		);

		$data = $this->params['url'];
		$array = (array)json_decode($data['User']);
		$data = array(
			'User' => $array
		);

		$ip = $this->request->clientIp();
		$ok = $this->checkToken($data['User'], $ip);
		
		if($ok){ 
		    $user = $this->User->findById($id);

		    if ($this->User->delete($id)) {
		        //$response['message'] = 'The record of user ' . $user['User']['login']. ' has been deleted.';
		        $response['success'] = true;
		    }
		}

	    $this->response->body(json_encode($response));
	}


	public function signin(){
		$response = array(
			'success' => false,
			'message' => 'Failed signin.',
			'errors' => []
			);
		
		$data = $this->request->input ('json_decode', true);
		$response['message'] = $data;
		$password = sha1($data['User']['password']);

		$user =  $this->User->find('first',array(
			'conditions' => array(
				'User.login' => $data['User']['login'],
				'User.password' => $password
			),
			'fields' => array('id')
		));
		
		if($user){	
			$ip = $this->request->clientIp();
			$token = $this->generateToken($ip);

			$this->User->id = $user['User']['id'];
			$this->User->set('token',$token);
			$this->User->set('ip',$ip);
			$this->User->set('last_use', date('Y-m-d H:i:s'));
			$this->User->save();
			
			$response['errors'] =  $this->User->invalidFields();
			$user['User']['token'] = $token;
			
			$response['success'] = true;
			$response['content'] = $user;
			//$response['message'] = "Sign in";
		}

		header("Content-type: application/json");
		$this->response->body(json_encode($response));
	}


	public function checkToken($user,$ip){
		$this->User->id = $user['id'];
		$record = $this->User->read();

		if($record['User']['token'] == $user['token']){
			if($record['User']['ip'] == $ip){
				$now = new DateTime(date('Y-m-d H:i:s'));
				$last = new DateTime($record['User']['last_use']);

				$interval = $now->diff($last);

				$minutes = $interval->days * 24 * 60;
				$minutes += $interval->h * 60;
				$minutes += $interval->i;

				if($minutes < 20){
					//$token = $this->generateToken();

					//$this->User->set('token', $token);
					$this->User->set('last_use', date('Y-m-d H:i:s'));
					$this->User->save();
					return true;
				}else{
					$this->User->set('token', NULL);
					$this->User->set('last_use', NULL);
					$this->User->set('ip', NULL);
					$this->User->save();
					return false;
				}
			}
		}
		return false;
	}


	public function generateToken($ip){
		
		$time = time();

		$token = md5($time+$ip);
		return $token;
	}

	
}