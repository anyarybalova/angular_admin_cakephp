<?php


App::uses('Model', 'Model');

class Exercise extends AppModel {
	var $name = 'Exercise';
  
	var $belongsTo = array("Subscription_type", "Objetive_type");

	public $hasMany = array(
        'ExerciseEquipment' => array(
            'className' => 'ExerciseEquipment'
        ),
        'ExerciseMuscleGroup' => array(
            'className' => 'ExerciseMuscleGroup'
        )
    );

  	public $actsAs = array('Containable');

  	public $validate = array(
        'name' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a name. '
        ),
        'description' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a description. '
        ),
        'subscription_type_id' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a Subscription type'

            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid Subscription type. '
            )
        )
    );
}