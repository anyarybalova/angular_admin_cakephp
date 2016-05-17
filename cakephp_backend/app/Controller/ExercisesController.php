<?php 

class ExercisesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');

		
		$excludeBeforeFilter = array('edit', 'add');
		
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
			$ip =  $this->request->clientIp();
			App::uses('ClientsController', 'Controller');
			$controller = new ClientsController();
			return $controller->checkToken($data['User'], $ip);
		}
	}


	public function index(){
		$this->Exercise->recursive = 2;
		$exercises = $this->Exercise->find('all');
		$this->response->body(json_encode($exercises));
	}


	public function view($id){
		$this->Exercise->id = $id;
		$exercise = $this->Exercise->read();
		$this->response->body(json_encode($exercise));
	}


	public function add(){
		$response = array(
			'success' => false,
			'message' => 'Failed request save exercise.',
			'errors' => []
			);
		
		//$data = json_decode($this->request->data, true);
		$data = $this->request->input ('json_decode', true);		
		
		if(isset($data['Exercise'])){
			if($exercise = $this->Exercise->save($data)){
				
				//add  equipments of exercise
				$eq = $data['Exercise']['equipments']; 
				foreach ($eq as $equip) {
					$this->Exercise->ExerciseEquipment->create();
					$this->Exercise->ExerciseEquipment->save(array(
						'ExerciseEquipment' => array(
							'exercise_id' 	=> $exercise['Exercise']['id'],
							'equipment_id' 	=> $equip
					)));
				}

				//add  muscle groups of exercise
				$mg = $data['Exercise']['muscle_groups']; 
				foreach ($mg as $mus_gr) {
					$this->Exercise->ExerciseMuscleGroup->create();
					$this->Exercise->ExerciseMuscleGroup->save(array(
						'ExerciseMuscleGroup' => array(
							'exercise_id' 		=> $exercise['Exercise']['id'],
							'muscle_group_id' 	=> $mus_gr
					)));
				}

				//add data files of exercise
				if(!empty($_FILES)){
				   	$files = $_FILES['file'];

			   		foreach ($files as $file) {
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
			                    
			                    $this->Exercise->ExerciseFile->create();
								$this->Exercise->ExerciseFile->save(array(
									'ExerciseFile' => array(
										'exercise_id' 		=> $exercise['Exercise']['id'],
										'file' 				=> $$new_name,
										'type'				=> 'image'
								)));
			                }
			            }
			        }
			    } 


				$response['success'] = true;
				$response['message'] = 'New exercise record has been saved.';
			}else{
				$response['errors'] = $this->Exercise->invalidFields();
			}	
		}else{
			$response['message'] = 'Invalid parameter.';
		}

		$this->response->body(json_encode($response));
	}


	public function edit(){
		$response = array(
			'success' => false,
			'message' => 'Failed request edit exercise.',
			'errors' => []
			);
		
		//$data = json_decode($this->request->data, true);
		$data = $this->request->input ('json_decode', true);		
		
		if(isset($data['Exercise'])){
			if($exercise = $this->Exercise->save($data)){
				$response['success'] = true;
				$response['message'] = 'Changes for exercise saved.';
			}else{
				$response['errors'] = $this->Exercise->invalidFields();
			}	
		}else{
			$response['message'] = 'Invalid parameter.';
		}

		$this->response->body(json_encode($response));
	}

	public function delete($id){
		$response = array(
			'success' => false,
			'message' => 'Failed delete exercise.'
		);

		$this->Exercise->id = $id;
		if($this->Exercise->delete()){
			$response['success'] = true;
			$response['message'] = 'Exercise deleted.';
		}

		$this->response->body(json_encode($response));
	}


	public function addEquipment(){
		$response = array(
			'success' => false,
			'message' => 'Failed add equipment to exercise.'
		);

		$exercise_id 	= $this->params['exercise_id'];
		$equipment_id 	= $this->params['equipment_id'];

		$this->Exercise->ExerciseEquipment->create();
		if($this->Exercise->ExerciseEquipment->save(array(
			'ExerciseEquipment' => array(
				'exercise_id' => $exercise_id,
				'equipment_id' => $equipment_id
			))
		)){
			$response['success'] = true;
			$response['message'] = 'Equipment added to exercise.';
		}

		$this->response->body(json_encode($response));
	}


	public function removeEquipment(){
		$response = array(
			'success' => false,
			'message' => 'Failed remove equipment from exercise.'
		);

		$exercise_id 	= $this->params['exercise_id'];
		$equipment_id 	= $this->params['equipment_id'];

		

		$rec = $this->Exercise->ExerciseEquipment->find('first',array(
			'conditions' => array(
				'exercise_id' => $exercise_id,
				'equipment_id' => $equipment_id
		)));
		
		if($this->Exercise->ExerciseEquipment->delete($rec['ExerciseEquipment']['id'])){
			$response['success'] = true;
			$response['message'] = 'Equipment removed from exercise.';
		}

		$this->response->body(json_encode($response));
	}


	public function addMuscleGroup(){
		$response = array(
			'success' => false,
			'message' => 'Failed add muscle_group to exercise.'
		);

		$exercise_id 		= $this->params['exercise_id'];
		$muscle_group_id 	= $this->params['muscle_group_id'];

		$this->Exercise->ExerciseMuscleGroup->create();
		if($this->Exercise->ExerciseMuscleGroup->save(array(
			'ExerciseMuscleGroup' => array(
				'exercise_id' => $exercise_id,
				'muscle_group_id' => $muscle_group_id
			))
		)){
			$response['success'] = true;
			$response['message'] = 'Muscle group added to exercise.';
		}

		$this->response->body(json_encode($response));
	}


	public function removeMuscleGroup(){
		$response = array(
			'success' => false,
			'message' => 'Failed remove muscle_group from exercise.'
		);

		$exercise_id 		= $this->params['exercise_id'];
		$muscle_group_id 	= $this->params['muscle_group_id'];

		$rec = $this->Exercise->ExerciseMuscleGroup->find('first',array(
			'conditions' => array(
				'exercise_id' => $exercise_id,
				'muscle_group_id' => $muscle_group_id
		)));
		
		if($this->Exercise->ExerciseMuscleGroup->delete($rec['ExerciseMuscleGroup']['id'])){
			$response['success'] = true;
			$response['message'] = 'Muscle group removed from exercise.';
		}

		$this->response->body(json_encode($response));
	}
}