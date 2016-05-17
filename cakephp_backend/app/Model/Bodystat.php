<?php


App::uses('Model', 'Model');

class Bodystat extends AppModel {
	var $name = 'Bodystat';
  	
  	var $belongsTo = array("Client");

  	public $actsAs = array('Containable');

  	public $validate = array(
        'height' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a height'

            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid number. '
            )
        ), 
        'weight' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a weight'

            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid number. '
            )
        ), 
        'body_fat' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a body fat'

            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid number. '
            )
        ), 
        'muscle_mass' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a muscle mass'

            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid number. '
            )
        ), 
    );
}