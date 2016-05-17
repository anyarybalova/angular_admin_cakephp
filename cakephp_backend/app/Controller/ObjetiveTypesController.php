<?php 

class ObjetiveTypesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
		$objetive_types =  $this->ObjetiveType->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($objetive_types));

	}


}