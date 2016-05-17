<?php 

class ClientsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');

		$excludeBeforeFilter = array('login', 'signup','recoveryPassword','newPassword');
		
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
		
			if(!$data){
				$data = json_decode($this->request->data, true);
			}
		}

		if($data['User']){
			return $this->checkUser($data);
		}else{
			return false;
		}
		
	}
	
	private function checkUser($data){
		

		if($data['User']['role'] == 'admin'){
			$ip =  $this->request->clientIp();
			App::uses('UsersController', 'Controller');
			$controller = new UsersController();
			return $controller->checkToken($data['User'], $ip);
		}

		if($data['User']['role'] == 'Client'){
			return $this->checkToken($data['User']);
		}
	}


	public function index(){
		$response = array(
			'content' => [],
			'success' => false
		);

		$response['content'] = $this->Client->find('all', array(
			'fields' => array('Client.id', 'Client.first_name', 'Client.last_name', 'Client.photo' , 'Client.email', 'Client.phone', 'Client.phone_code', 'Client.birth_date', 'Client.sex', 'Client.metric', 'Client.country_id', 'Client.city'),
			'contain' => array(
				'Country' => array(
					'fields' => array('Country.id', 'Country.name')
				)
			)
			
		));
		$response['success'] = true;
		
		$this->response->body(json_encode($response));
	}

	public function clients_list(){
		$response = array(
			'content' => [],
			'success' => false
		);

		$response['content'] = $this->Client->find('all');
		$response['success'] = true;
		
		$this->response->body(json_encode($response));
	}

	public function view_complete($id) {
		$response = array(
			'content' => [],
			'success' => false
		);
		
		$this->Client->id = $id;
		$response['content'] = $this->Client->read();
		$response['success'] = true;

		$this->response->body(json_encode($response));
	}


	public function view($id) {
		$response = array(
			'content' => [],
			'success' => false
		);
		
		$this->Client->id = $id;
		$response['content'] = $this->Client->find('first',array(
				'fields' => array('Client.id', 'Client.first_name', 'Client.last_name', 'Client.email', 'Client.phone_code','Client.phone', 'Client.photo', 'Client.city', 'Client.sex','Client.metric', 'Client.birth_date'),
				'contain' => array(
					'Country' => array(
						'fields' => array('Country.id', 'Country.name')
					)
				),
				'conditions' => array('Client.id' => $id)
			)
		);
		$response['success'] = true;

		$this->response->body(json_encode($response));
	}



	public function add() {
		$response = array(
			'success' => false,
			'message' => 'Failed request save client.',
			'errors' => []
			);
		
		$data = json_decode($this->request->data, true);
		//$data = $this->request->input ('json_decode', true);
		
		
		if(!empty($_FILES)){
		   	$file = $_FILES['file'];
			   	if(!empty($file) && !empty($file['name']))
	            {
		            $name = $file['name'];
	                $ary_ext=array('jpg','jpeg','gif','png'); 
	                $ext = substr(strtolower(strrchr($file['name'], '.')), 1); 
	                if(in_array($ext, $ary_ext))
	                {
	                	$new_name = time().$file['name'];
	                	$path = Configure::read('path_img');
	                	
	                	if (!file_exists($path)) {
						    mkdir($path, 0755, true);
						}
	                    move_uploaded_file($file['tmp_name'], $path . $new_name);
	                    $data['Client']['photo'] = $new_name;
	                }
	            }
	    }

	   
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);

		$data['Client']['password'] = sha1($randomString);

	    $data['Client']['pass_2'] = $data['Client']['password'];

 		/*
		if(isset($data['Client']['password'])){
			$data['Client']['password'] = sha1($data['Client']['password']); 
			$data['Client']['pass_2'] = sha1($data['Client']['pass_2']); 
		}*/
			//$message = $data;
		if($client = $this->Client->save($data)){
			$response['success'] = true;
			$response['message'] = 'New client record has been saved.';
			$response['content'] = array('id' =>  $client['Client']['id']);

			//mail($data['Client']['email'], "Fitness For Polo - Recovery your password", $msg);
			$msg = "<h3 style='color:blue'>Hello, ".$data['Client']['first_name']."! Welcome to Fitness for polo!</h3>";
			$msg .= "<p>You can signin with this password: <strong>" .$randomString ."</strong></p>";
			$msg .= "Please change your password immediately after logging in with your new password.";
			$this->sendMail($data['Client']['email'], "FitnessForPolo@m.com", "Fitness For Polo - Signup", $msg);

		}else{
			$response['errors'] = $this->Client->invalidFields();
		}

		$this->response->body(json_encode($response));
	}


	
	public function signup() {
		$response = array(
			'success' => false,
			'message' => 'Failed request save client.',
			'errors' => []
			);
		
		$data = json_decode($this->request->data, true);
		//$data = $this->request->input ('json_decode', true);
	
		
		if(!empty($_FILES)){
		   	$file = $_FILES['file'];
			   	if(!empty($file) && !empty($file['name']))
	            {
		            $name = $file['name'];
	                $ary_ext=array('jpg','jpeg','gif','png'); 
	                $ext = substr(strtolower(strrchr($file['name'], '.')), 1); 
	                if(in_array($ext, $ary_ext))
	                {
	                	$new_name = time().$file['name'];
	                	$path = Configure::read('path_img');
	                	
	                	if (!file_exists($path)) {
						    mkdir($path, 0755, true);
						}
	                    move_uploaded_file($file['tmp_name'], $path . $new_name);
	                    $data['Client']['photo'] = $new_name;
	                }
	            }
	    }
		
		if(isset($data['Client']['password'])){
			$data['Client']['password'] = sha1($data['Client']['password']); 
			$data['Client']['pass_2'] = sha1($data['Client']['pass_2']); 
		}
			//$message = $data;
		

		if($client = $this->Client->save($data)){
			$ip = $this->request->clientIp();
			$token = $this->generateToken($ip);

			$this->Client = $client['Client'];

			$client['Client']['deviceID'] 	= $data['User']['deviceID'];
			$client['Client']['deviceOS'] 	= $data['User']['deviceOS'];
			$client['Client']['token'] 		= $token;
			$client['Client']['last_use'] 	= date('Y-m-d H:i:s');

			$this->Client->save($client);

			$response['success'] = true;
			$response['message'] = 'New client record has been saved.';
			$response['content'] = array(
				'id' 	=>  $client['Client']['id'],
				'token' =>  $token 
			);

		}else{
			$response['errors'] = $this->Client->invalidFields();
		}

		$this->response->body(json_encode($response));
	}


	public function edit($id = null) {
		$response = array(
			'success' => false,
			'message' => 'Unable to update your client.',
			'errors' => []
			);
		
	    if (!$id) {
	       $response['message'] .= ' - Invalid user id';
	    }
	    $user = $this->Client->findById($id);
	    
	    if (!$user) {
	       $response['message'] .= ' - Invalid user';
	    }
	    else{
	    	$data = $this->request->input('json_decode', true);
	    	if(!$data){
	    		$data = json_decode($this->request->data, true);
	    	}

    		if(!empty($_FILES)){
		   	$file = $_FILES['file'];
			   	if(!empty($file) && !empty($file['name']))
	            {

	            	if ($_FILES['file']['size'] > 1000000) {
						$response['message'] = 'File was not saved. Is over of 10Mb.';
	            	}else{
			            $name = $file['name'];
		                $ary_ext=array('jpg','jpeg','gif','png'); 
		                $ext = substr(strtolower(strrchr($file['name'], '.')), 1); 
		                if(in_array($ext, $ary_ext))
		                {
		                	$new_name = time().$file['name'];
		                	$path = Configure::read('path_img');
		                	if (!file_exists($path)) {
							    mkdir($path, 0755, true);
							}
		                    move_uploaded_file($file['tmp_name'], $path . $new_name);
		                    $data['Client']['photo'] = $new_name;
		                }
		            }

	            }
	        }


		    $this->Client->id = $id;	    

	        if ($this->Client->save($data)) {
	        	$response['success'] = true;
				$response['message'] = 'Your client has been updated.';
	        }else {

		    $response['errors'] = $this->Client->invalidFields();
			}

		}    
	    $this->response->body(json_encode($response));
	}


	public function delete($id) {
		$response = array(
			'success' => false,
			'message' => 'Failed delete a client'
		);

		$param = $this->params['url'];
		if($param){
			$array = (array)json_decode($param['User']);
			$data = array(
				'User' => $array
			);

			if($data['User']['role'] == "admin"){
				$client = $this->Client->findById($id);
			    if ($this->Client->delete($id)) {
			        $response['message'] = 'The record of client ' . $client['Client']['first_name']. ' '. $client['Client']['last_name']. ' has been deleted.';
			        $response['success'] = true;
			    }
			}else{
				 $response['message'] = "You have no permission to delete a client";
			}
		}
	   

	    $this->response->body(json_encode($response));
	}


	public function login(){
		$response = array(
			'success' => false,
			'message' => 'Username or password invalid'
			);
		
		$data = $this->request->input ('json_decode', true);
		
		$password = sha1($data['Client']['password']);

		$client =  $this->Client->find('first',array(
			'conditions' => array(
				'Client.email' => $data['Client']['email'],
				'Client.password' => $password
			)
		));
		
		if($client){

			$ip = $this->request->clientIp();
			$token = $this->generateToken($ip);

			$client['Client']['deviceID'] 	= $data['User']['deviceID'];
			$client['Client']['deviceOS'] 	= $data['User']['deviceOS'];
			$client['Client']['token'] 		= $token;
			$client['Client']['last_use'] 	= date('Y-m-d H:i:s');

			if(!$this->Client->save($client)){
				$response['errors'] =  $this->Client->invalidFields();
			}
			
			
			$client['Client']['token'] = $token;
			
			$response['success'] = true;
			$response['content'] = array('Client' => array('id' => $client['Client']['id'], 'token' => $client['Client']['token']));
			$response['message'] = "Login success";
		}

		header("Content-type: application/json");
		$this->response->body(json_encode($response));
	}


	public function checkToken($user){
		$this->Client->id = $user['id'];
		$record = $this->Client->read();

		if($record['Client']['token'] == $user['token']){
			if(($record['Client']['deviceID'] == $user['deviceID']) && ($record['Client']['deviceOS'] == $user['deviceOS'])){
				
				$now = new DateTime(date('Y-m-d H:i:s'));
				$last = new DateTime($record['Client']['last_use']);

				$interval = $now->diff($last);

				$minutes = $interval->days * 24 * 60;
				$minutes += $interval->h * 60;
				$minutes += $interval->i;

				if($minutes < 20){
					
					$record['Client']['last_use'] 	= date('Y-m-d H:i:s');
					$this->Client->save($record);
					return true;
				}else{

					$record['Client']['deviceID'] 	= NULL;
					$record['Client']['deviceOS'] 	= NULL;
					$record['Client']['token'] 		= NULL;
					$record['Client']['last_use'] 	= NULL;
					$this->Client->save($record);
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

	
	public function changePassword($id){
		$response = array(
			'success' => false,
			'message' => 'Failed change the password'
			);
		
		$data = $this->request->input('json_decode', true);
		
		if($data['Client']['new_pass'] != $data['Client']['new_pass_2']){
			$response['message'] = 'Password & Confirm Password must be match.';
		}else{
			$password = sha1($data['Client']['password']);

			$client =  $this->Client->find('first',array(
				'conditions' => array(
					'Client.id' => $id,
					'Client.password' => $password
				)
			));
			
			if($client){
				$client['Client']['password'] 		= sha1($data['Client']['new_pass']);
				$client['Client']['last_use'] 		= date('Y-m-d H:i:s');
				$this->Client->save($client);
				$response['success'] = true;
				$response['message'] = "Password changed";
			}
		}
		$this->response->body(json_encode($response));
	}


	public function recoveryPassword(){
		$response = array(
			'success' => false,
			'message' => 'Failed recovery the password'
			);
		
		$data = $this->request->input('json_decode', true);

		$client =  $this->Client->find('first',array(
				'conditions' => array(
					'Client.email' => $data['Client']['email']
				)
		));

		if($client){
			$response['message'] = "Email was send successfully";
			$response['success'] = true;

			$email = $client['Client']['email'];
			$key = substr(str_shuffle(md5(time())),0,30);
			$client['Client']['pass_reset_key'] = $key;
			$date = new DateTime('+2 hours');
		    $client['Client']['pass_reset_expire'] = $date->format('Y-m-d H:i:s');
		    $this->Client->save($client);


			$link = '<a href="'.Configure::read('path_front').'/clients/newPassword?email='.$email.'&key='.$key.'">Change password</a>';
			//mail($data['Client']['email'], "Fitness For Polo - Recovery your password", $msg);
			$msg = "<p>Click the link for change the password</p>";
			$msg .= $link;
			$this->sendMail($email, "FitnessForPolo@m.com", "Fitness For Polo - Recovery your password", $msg);
		}else{
			$response['message'] = "Email address was not found";
		}

		$this->response->body(json_encode($response));
	}


	//POST
	//{"Client" : {"email" : "mail@mail.com", "pass_reset_key" : "0347be77f2bbc5bf", "new_pass" :"12", "new_pass_2" : "12"}}
	public function newPassword(){
		$response = array(
			'success' => false,
			'message' => 'Failed change the password'
			);
		
		$data = $this->request->input('json_decode', true);
		
		if(isset($data['Client']['new_pass']) && isset($data['Client']['new_pass_2']) && isset($data['Client']['email']) && isset($data['Client']['pass_reset_key'])){
			if($data['Client']['new_pass'] != $data['Client']['new_pass_2']){
			$response['message'] = 'Password & Confirm Password must be match.';
			}else{

				$password = sha1($data['Client']['new_pass']);

				$client =  $this->Client->find('first',array(
					'conditions' => array(
						'Client.email' => $data['Client']['email'],
						'Client.pass_reset_key' => $data['Client']['pass_reset_key']
					)
				));
				
				if($client){
					if($client['Client']['pass_reset_expire'] > date('Y-m-d H:i:s')){
						$client['Client']['password'] 		= sha1($data['Client']['new_pass']);
						$client['Client']['last_use'] 		= date('Y-m-d H:i:s');
						$this->Client->save($client);
						$response['success'] = true;
						$response['message'] = "Password changed";
					}else{

						$response['success'] = true;
						$response['message'] = "Link has expired. Recovery your password again.";
					}

				}
			}
		}else{
			$response['message'] = "Invalid paremeters.";
		}
		
		$this->response->body(json_encode($response));
	}


	private function sendMail($to,$from, $subject, $content){

		$headers = "From: " . $from . "\r\n";
		$headers .= "Reply-To: ". $from . "\r\n";
		$headers .= "CC: susan@example.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$message = '<html><body>';
		$message .= '<h1>Fitness For Polo</h1>';
		$message .= $content;
		$message .= '</body></html>';

		mail($to, $subject, $message, $headers);
	}
}