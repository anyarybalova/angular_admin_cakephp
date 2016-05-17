<?php


App::uses('Model', 'Model');

class Muscle_group extends AppModel {
	var $name = 'Muscle_group';
  
	public $hasMany = array(
        'ExerciseMuscleGroup' => array(
            'className' => 'ExerciseMuscleGroup'
        )
    );
	
  	public $actsAs = array('Containable');
}