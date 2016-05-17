<?php


App::uses('Model', 'Model');

class Client extends AppModel {
	var $name = 'Client';
  	
  	var $belongsTo = array("Country");

  	public $actsAs = array('Containable');

  	public $validate = array(
        'first_name' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a first name. '
        ),
        'last_name' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a last name. '
        ),
        'password' => array(
            'rule' => array('minLength', '8'),
            'required' => true,
            'message' => 'Minimum 8 characters long',
            'on' => 'create'
        ),
        'pass_2' => array(
            array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please Enter Confirm password',
            'on' => 'create'
            ),
            array(
                'rule' => 'checkpasswords',
                'required' => true,
                'message' => 'Password & Confirm Password must be match.',
                'on' => 'create'
            )
        ),
        'email' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Your email is required'

            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Your email is invalid'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'That email has already been taken',
                'on' => 'create'
            )
            
        ),
        'phone' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a phone number. '
        ),
        'country_id' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a country'

            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid number. '
            )
        ), 
        'city' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a city. '
        ),
        'birth_date' => array(
           'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please enter a date of birth'
            )
        ),
        'sex' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please enter a sex'
            ),
            'sex' => array(
                'rule' => array('inList', array('M', 'F')),
                'required' => true,
                'message' => 'Not valid genre'
            )
            
        ),
    );

    function checkpasswords()     // to check pasword and confirm password
    {  
        if(strcmp($this->data['Client']['password'],$this->data['Client']['pass_2']) == 0 ) 
        {
            return true;
        }
        
        return false;
    }
 }
