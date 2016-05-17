<?php
/**
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

class User extends AppModel {
	var $name = 'User';
  	
  	var $belongsTo = array("Role");

	
  	public $validate = array(
        'login' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Please enter a login. ',
            'on' => 'create'
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
        'role_id' => array(
            'Not empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Please choose a role for user. ',
                'on' => 'create'
            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Not valid number. ',
                'on' => 'create'
            )
        )
    );


    function checkpasswords()     // to check pasword and confirm password
    {  
        if(strcmp($this->data['User']['password'],$this->data['User']['pass_2']) == 0 ) 
        {
            return true;
        }
        
        return false;
    }

    
    /*public function beforeSave($options = array()) {
        if (!$this->User->id){
            $this->data['User']['password'] = sha1($this->data['User']['password']);
        }
    }*/
}
