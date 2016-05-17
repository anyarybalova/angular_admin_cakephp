<?php


App::uses('Model', 'Model');

class ExerciseMuscleGroup extends AppModel {

	public $useTable = 'exercise_muscle_groups';
	var $name = 'ExerciseMuscleGroup';
  	
  	public $actsAs = array('Containable');
  	
	public $belongsTo = array("Exercise", "MuscleGroup");

}