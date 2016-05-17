<?php


App::uses('Model', 'Model');

class Role extends AppModel {
	var $name = 'Role';

	var $hasMany = array('User');
}