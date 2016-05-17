<?php 

class CountriesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->autoRender = false;
		$this->response->type('json');
	}

	
	public function index() {
		/*$countries =  $this->Country->find('list', 
			array('fields' => array('Country.id', 'Country.country_name'))
		);
		*/
		$countries =  $this->Country->find('all', array(
			'recursive' => 0
			));
		$this->response->body(json_encode($countries));
	}
}