<?php


App::uses('Model', 'Model');

class Objetive_type extends AppModel {
	var $name = 'Objetive_type';
	public $useTable = 'objetive_types';
  
	public $hasMany = array("Exercise");
}