<?php


App::uses('Model', 'Model');

class Subscription_type extends AppModel {
	var $name = 'Subscription_type';
  
	public $hasMany = array("Exercise");
	
  	public $actsAs = array('Containable');

}