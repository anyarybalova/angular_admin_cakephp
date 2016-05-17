<?php 

class RolesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
		$roles =  $this->Role->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($roles));
	}
}