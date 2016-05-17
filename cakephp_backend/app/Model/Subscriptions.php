<?php 

class SubscriptionTypesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
		$subcriptions =  $this->Subscription->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($subcriptions));
	}
}