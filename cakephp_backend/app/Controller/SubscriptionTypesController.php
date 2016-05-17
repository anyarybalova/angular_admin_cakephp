<?php 

class SubscriptionTypesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
	
		$subscriptions =  $this->SubscriptionType->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($subscriptions));
	}
}