<?php 

class BodystatsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');

		$excludeBeforeFilter = array('');
		
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
		$ip =  $this->request->clientIp();

		if($data['User']['role'] == 'admin'){
			App::uses('UsersController', 'Controller');
			$controller = new UsersController();
			return $controller->checkToken($data['User'], $ip);
		}

		if($data['User']['role'] == 'Client'){
			App::uses('ClientsController', 'Controller');
			$controller = new ClientsController();
			return $controller->checkToken($data['User'], $ip);
		}
	}


	public function index(){
		$this->Bodystat->recursive = -1;
		$stats = $this->Bodystat->find('all');
		$this->response->body(json_encode($stats));
	} 


	public function add(){
		$response = array(
			'success' => false,
			'message' => 'Failed request save body stat.',
			'errors' => []
			);
		
		//$data = json_decode($this->request->data, true);
		$data = $this->request->input ('json_decode', true);		
		
		if(isset($data['Bodystat']) && isset($data['Bodystat']['client_id'])){
			$client = $this->Bodystat->Client->find('first',array(
				'conditions' => array('Client.id' => $data['Bodystat']['client_id']),
				'fields' => array('metric')
			));
			
			if($client['Client']['metric'] != 'M'){
				$data = $this->convertToMetric($data);
			}

			if($stat = $this->Bodystat->save($data)){
				$response['success'] = true;
				$response['message'] = 'New body stat record has been saved.';
			}else{
				$response['errors'] = $this->Bodystat->invalidFields();
			}	
		}else{
			$response['message'] = 'Invalid parameter.';
		}

		$this->response->body(json_encode($response));
	}


	


 	public function view($client_id){
 		$response = array(
			'success' => false,
			'content' => []
		);

		if($client_id){
			$client = $this->Bodystat->Client->find('first',array(
				'conditions' => array('Client.id' => $client_id),
				'fields' => array('metric')
			));
			
			$stat = $this->Bodystat->find('first', array(
				'conditions' => array('Bodystat.client_id' => $client_id),
				'order' => 'Bodystat.date DESC',
				'recursive' => -1
			));

			if($stat){
				if($client['Client']['metric'] != 'M'){
					$stat = $this->convertToImperial($stat);
				}
				$response['success'] = true;
				$response['content'] = $stat;
			}

			
		}

		$this->response->body(json_encode($response));
 	}


 	//GET
 	//http://oficina.vnstudios.com/fitness4polo/fitness4polo_back/bodystats/viewAll/1 
 	public function viewAll($client_id){
 		$response = array(
			'success' => false,
			'content' => []
		);

		if($client_id){
			$client = $this->Bodystat->Client->find('first',array(
				'conditions' => array('Client.id' => $client_id),
				'fields' => array('metric')
			));
			
			$this->Bodystat->recursive = -1;
			$stats = $this->Bodystat->find('all', array(
				'conditions' => array('Bodystat.client_id' => $client_id)
			));

			if($stats){
				if($client['Client']['metric'] != 'M'){
					$new_stats = array(); 

					foreach ($stats as $stat){
						$stat = $this->convertToImperial($stat);
						array_push($new_stats, $stat);
					}
					$stats = $new_stats;
				}

				$response['success'] = true;
				$response['content'] = $stats;

			}
		}

		$this->response->body(json_encode($response));
 	}

 	public function edit($id){
 		$response = array(
			'success' => false,
			'message' => 'Unable to edit.',
			'errors' => []
			);
		
	    if (!$id) {
	       $response['message'] .= ' - Invalid body stat id';
	    }
	    $stat = $this->Bodystat->findById($id);
	    
	    if (!$stat) {
	       $response['message'] .= ' - Invalid body stat';
	    }
	    else{
	    	$data = $this->request->input ('json_decode', true);

		    $this->Bodystat->id = $id;	    
		    

		    if($stat['Client']['metric'] != 'M'){
		    	$data = $this->convertToMetric($data);
		    }
	        
	        if ($this->Bodystat->save($data)) {
	        	$response['success'] = true;
				$response['message'] = 'Update success.';
	        }else {

		    $response['errors'] = $this->Bodystat->invalidFields();
			}

		}    
	    $this->response->body(json_encode($response));
 	}

 	public function delete($id){
 		$response = array(
			'success' => false,
			'message' => 'Failed delete body stats'
		);
 		if ($this->Bodystat->delete($id)) {
			$response['message'] = 'The record of body stat deleted.';
			$response['success'] = true;
		}
 		$this->response->body(json_encode($response));
 	}

 	public function convertToMetric($data){
		$data['Bodystat']['height'] = $data['Bodystat']['height']*0.9144;  //pass m to yardes
		$data['Bodystat']['weight'] = $data['Bodystat']['weight']*0.4536;	//pass kg to  libras
		return $data;
 	}

 	public function convertToImperial($data){
		$data['Bodystat']['height'] = round($data['Bodystat']['height']*1.0936, 2);  //pass yardes to metr
		$data['Bodystat']['weight'] = round($data['Bodystat']['weight']*2.2046, 2);	//pass libras to  kg
		return $data;
 	}
}