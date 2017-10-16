<?php


$this->internal = array_replace_recursive(

	$this->internal, 
	
	array(
		
		'callable_from_request' => array(
			'save_settings' => 1,
			'save_settings_during_setup' => 1,
			'reset_options' => 1,
			'reset_info' => 1,
			'create_tables' => 1,
			'insert_test_replies' => 1,
			),
		'ignore_at_call' => array(
			'dud'=>1,
			),	
		'calllimits' => array(
			'init'=>1,
			'admin_init'=>1,
			'frontend_init'=>1,
			'save_settings'=>1,
			'reset_options'=>1,			
			'upgrade_options'=>1,			
			'upgrade_info'=>1,			
		),	
		'notice_types' => array(
			'success'=>1,
			'info'=>1,
			'warning'=>1,
			'error'=>1,
		),
		
		'default_table_structure' => 
		"id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		post bigint(20) unsigned NULL DEFAULT NULL,
		parent bigint(20) unsigned NULL DEFAULT NULL,
		slug varchar(255) NULL DEFAULT NULL,
		status tinyint(2) NULL DEFAULT NULL,
		sort mediumint(9) NULL DEFAULT NULL,
		created datetime NULL DEFAULT NULL,
		modified datetime NULL DEFAULT NULL,
		title varchar(255) NULL DEFAULT NULL,
		content LONGTEXT NULL DEFAULT NULL,
		user bigint(20) unsigned NULL DEFAULT NULL,
		group tinyint(4) NULL DEFAULT NULL,
		KEY user (user),
		KEY post (post),
		KEY parent (parent),
		KEY slug (slug),
		KEY title (title),",
		
		'default_meta_structure' => 
		"id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		data_type varchar(255) NULL DEFAULT NULL,
		set varchar(255) NULL DEFAULT NULL,
		parent bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL,	
		{REPLACEVALUE},
		KEY data_type (data_type),
		KEY set (set),
		KEY parent (parent),
		KEY name (name),
		KEY value (value),",

		
	)
	
);

?>