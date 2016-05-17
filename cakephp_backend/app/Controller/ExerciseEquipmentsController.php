<?php 

class ExerciseEquipmentsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
		$equipments =  $this->ExerciseEquipment->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($equipments));

	}


}