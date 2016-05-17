<?php 

class MuscleGroupsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
		$muscles =  $this->MuscleGroup->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($muscles));
	}
}