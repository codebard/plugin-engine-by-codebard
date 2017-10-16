<?php

$this->internal = array_replace_recursive(
	
	
	$this->internal,
	
	array(
		
		'id' => 'PLUGINPREFIX',
		'plugin_id' => 'plugin-engine-by-codebard',
		'prefix' => 'PLUGINPREFIX_',
		'version' => '1.0.7',
		'plugin_name' => 'Plugin Engine by CodeBard',
		
		'callable_from_request' => array(
			
			'save_settings' => 1,
			
			'reset_languages' => 1,
			'save_language' => 1,
			'choose_language' => 1,
			
			'save_license' => 1,
		
			'ignore the ones after this line they were allowed for development!'=>1,
			
		),
			
		
		'do_log' => false,
		
		'calllimits' => array(
		
			'add_admin_menu'=>1,
		),		
		
		'callcount' => array(
		
		),		
		
		'tables'=> array(


		),	
		'data'=> array(


		),	
		

		'meta_tables'=> array(

						
		),	
		
		
		'admin_tabs' => array(
		
			'dashboard'=>array(
				
			),
			'quickstart'=>array(
				
				
			),
			'example_tab'=>array(
				
				
			),
			'languages'=>array(
				
				
			),
			'addons'=>array(
				
				
				
			),
			'extras'=>array(
				
			
				
			),
			'support'=>array(
				
				
			),
		
		
		
		),
		
		'addons' => array(
		
			'woocommerce_integration' => array(
			
				'title' => '',
				'icon' => 'woocommerce_integration.jpg',		
				'link' => 'https://codebard.com/codebard-help-desk-woocommerce-integration',		
				'slug' => 'codebard-help-desk-woocommerce-integration/index.php',		
				'class_name' => 'PLUGINPREFIX_a1',		
			
			),
		
		
		
		
		),
		'template_parts' => array(
			'content' => '',
		),
	
	)
	
);


?>