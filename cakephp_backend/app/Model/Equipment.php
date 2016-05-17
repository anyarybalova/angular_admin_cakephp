<?php


App::uses('Model', 'Model');

class Equipment extends AppModel {
	public $useTable = 'equipments';
	var $name = 'Equipment';

	var $hasMany = array("ExerciseEquipments");
	
	//var $hasMany = array('Exercise');
}