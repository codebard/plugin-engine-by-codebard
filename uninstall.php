<?php



	// if uninstall.php is not called by WordPress, die
		if (!defined('WP_UNINSTALL_PLUGIN')) {
			die;
		}
	

		global $wpdb;
	


		// Create dud object for loading options and internal vars:
		
		class cb_dud_object {
			
			public $internal = array(
	
			// Holds internal and generated vars. Never saved.

			);
			public $opt = array(
	
			// Holds internal and generated vars. Never saved.

			);
			public $hardcoded = array(
	
			// Holds hardcoded vars. Never saved.

			);
			
					
			public function __construct() 
			{
			
				require_once('core/includes/default_internal_vars.php');
				require_once('plugin/includes/default_internal_vars.php');
				require_once('plugin/includes/hardcoded_vars.php');
					
			
			}
		}	
		$PLUGINPREFIX = new cb_dud_object;
		
		// Include internal vars from file:
		
		
		// Get options 
		
		$PLUGINPREFIX->opt=get_option($PLUGINPREFIX->internal['prefix'].'options');		

		if($PLUGINPREFIX->opt['delete_options_on_uninstall']=='yes')
		{
			$wpdb->query( "DELETE FROM ".$wpdb->options." WHERE option_name LIKE '".$PLUGINPREFIX->internal['id']."_%';");
		
		}
	
		if($PLUGINPREFIX->opt['delete_data_on_uninstall']=='yes')
		{
			
			foreach($PLUGINPREFIX->internal['tables'] as $key => $value)
			{
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix.$PLUGINPREFIX->internal['id']."_".$key.";");
				
			}
			foreach($PLUGINPREFIX->internal['meta_tables'] as $key => $value)
			{
				
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix.$PLUGINPREFIX->internal['id']."_".$key.";");
				
			}
			
			// Remove wordpress posts
			
			// Get posts first:
	
			$results = $wpdb->get_results( "SELECT ID FROM ".$wpdb->posts." WHERE post_type = '".$PLUGINPREFIX->internal['id']."_ticket';",ARRAY_A);
			
			foreach($results as $key => $value)
			{
				$post_id = $results[$key]['ID'];
				
				// Delete post meta
				
				$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE post_id = '".$post_id."';");	
				
				// Delete post 
				
				$wpdb->query( "DELETE FROM ".$wpdb->posts." WHERE ID = '".$post_id."';");	
				
				
			}
			
			// Delete custom taxonomy
						
			// Delete terms
			$wpdb->query( "
				DELETE FROM
				".$wpdb->terms."
				WHERE term_id IN
				( SELECT * FROM (
					SELECT ".$wpdb->terms.".term_id
					FROM ".$wpdb->terms."
					JOIN ".$wpdb->term_taxonomy."
					ON ".$wpdb->term_taxonomy.".term_id = ".$wpdb->terms.".term_id
					WHERE taxonomy = '".$PLUGINPREFIX->internal['id']."_support'
				) as T
				);
			" );

			// Delete taxonomies
			$wpdb->query( "DELETE FROM ".$wpdb->term_taxonomy." WHERE taxonomy = '".$PLUGINPREFIX->internal['id']."_support'" );

			
		}
		

?>