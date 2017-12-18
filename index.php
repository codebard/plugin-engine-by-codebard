<?php
/*
	Plugin Name: Plugin Engine by CodeBard
	Plugin URI: https://wordpress.org/plugins/patron-button-and-widgets-by-codebard/
	Description: Powerful OOP plugin template for WordPress
	Version: 1.0.7
	Author: CodeBard
	Author URI: http://codebard.com
	Text Domain: PLUGINPREFIX
	Domain Path: /lang
*/




class PLUGINPREFIX_core {

	protected static $instance = null;

	public $info = array(	
	
		// Holds plugin info. Overridden by info from db by merging db info into this
	
	);
	
	public $internal = array(
	
		// Holds internal and generated vars. Never saved.

	);
	
	public $opt=array(
	
		// Holds plugin info. Overridden by options from db by merging db options into this	
	
	);

	private function __construct() 
	{
		// We need a few internal vars initialized here 
		
		$this->internal['plugin_path']=plugin_dir_path( __FILE__ );	
		$this->internal['template_path']=$this->internal['plugin_path'].'plugin/templates';	
		$this->internal['start']=microtime(true);
		$this->internal['plugin_url'] = trailingslashit(plugin_dir_url(__FILE__));
		$this->internal['template_url']=$this->internal['plugin_url'].'plugin/templates';	
		$this->internal['plugin_slug'] = basename(dirname(__FILE__)).'/index.php';
		$this->internal['plugin_dir_name'] = basename(dirname(__FILE__));
		$this->internal['admin_url'] = trailingslashit(get_admin_url());
		$this->internal['site_url'] = get_site_url();	
		$this->internal['admin_page_url'] = get_admin_url();	
		
		require_once($this->internal['plugin_path'].'core/includes/default_internal_vars.php');
		require_once($this->internal['plugin_path'].'plugin/includes/default_internal_vars.php');
		require_once($this->internal['plugin_path'].'plugin/includes/hardcoded_vars.php');
		
		if(isset($_REQUEST[$this->internal['prefix'].'action']))
		{
			$this->internal['requested_action'] = $_REQUEST[$this->internal['prefix'].'action'];
		}
		else
		{
			$this->internal['requested_action']=false;
		}
		
		$this->plugin_construct();
		
		
	}
	public static function get_instance()
    {
        if (!isset(self::$instance)) {
			
			$obj = get_called_class(); 
            self::$instance = new $obj;
        }
        
        return static::$instance;
    }
	private function __clone()	{    }
    private function __wakeup()	{    }
	public function __call($action,$vars)
	{
		

		// Runner Function - checks, processes and runs any action requested and distributes into core and plugin singleton objects in order. This function acts as a front controller. https://en.wikipedia.org/wiki/Front_controller
		
		// $this->run('action') method does not work here because you cant pass vars to WordPress' add_action hence you cant pass the action name. So this Runner function has to be magic call.
		
		// Normalize the action name
		$action=str_replace($this->internal['prefix'],'',$action);	

		// Map the vars

		for($varcount=1;$varcount<=11;$varcount++)
		{
			$var_name = 'v'.$varcount;
			
			if(isset($vars[$varcount-1]))
			{

				$$var_name = $vars[$varcount-1];
					
			}
			else
			{
				$$var_name = false;				
			}
			
		}

		
		// Check if action exits in methods and return dud if it isnt
		if(!method_exists($this,$action.'_c') AND !method_exists($this,$action.'_p'))
		{
			

			if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
			{				
				$this->do_log(array('action'=>$action, 'status'=>'Action END due to NO ACTION FOUND. false returned without processing action.', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
			}
			return false;
		}

		// Check if the action is coming from $_REQUEST and if so check if its in uncallable actions from $_REQUEST list
		if(isset($this->internal['requested_action']))
		{
			if($this->internal['requested_action']==$action AND (!array_key_exists($this->internal['requested_action'], $this->internal['callable_from_request']) OR array_key_exists($this->internal['requested_action'], $this->internal['ignore_at_call'])))
			{
				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
			
					$this->do_log(array('action'=>$action, 'status'=>'Action END due to UNCALLABLE FROM REQUEST or IGNORE AT CALL. false returned without processing action.', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
				return false;
			}
		}
			
		// Now check if function has any call limit. For functions like init, frontend_init and admin_init, for example, they should only be called once
		
		// Set call limits to array if its too early in launch (to allow init_internal_vars and init not to trigger error)
		if(!is_array($this->internal['calllimits']))
		{
			$this->internal['calllimits']=array();
		}

		
		if(array_key_exists($action, $this->internal['calllimits']))
		{
			if(!isset($this->internal['callcount'][$action]))
			{
				$this->internal['callcount'][$action]=0;				
			}
	
			
			if($this->internal['calllimits'][$action] > $this->internal['callcount'][$action])
			{
				$this->internal['callcount'][$action]++;
			}
			else
			{
				if(array_key_exists($action, $this->internal['calllimits']))
				{


				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{					
						$this->do_log(array('action'=>$action, 'status'=>'Action END due to CALL LIMIT. Action has '.$this->internal['calllimits'][$action].' call limit whereas it was called '.$this->internal['callcount'][$action].' times. returned false without processing action.', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
					}
					return false;		
				}
			}
		
		}
		
		// Checks passed. Start processing the action request
			
		// This is the action which runs before the actual action runs
		
		if(has_action($this->internal['prefix'].'action_before_'.$action))
		{
			// Check if the action/function is set to dont execute hooks for itself:
			
			if(array_key_exists($action, $this->hardcoded['dont_execute_hooks']))
			{

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
					$this->do_log(array('action'=>$action, 'status'=>'Action Hooks - BEFORE - Action '.$action.' is set to not execute hooks. Not running hooked functions', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
			}
			else
			{

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
					$this->do_log(array('action'=>$action, 'status'=>'Action Hooks - BEFORE - START', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
							
				do_action($this->internal['prefix'].'action_before_'.$action,$v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);
				

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
					$this->do_log(array('action'=>$action, 'status'=>'Action Hooks - BEFORE - END', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
				
				
			}
			
		}
	
		// This filter is used to filter variables before they are sent to actions
		if(has_filter($this->internal['prefix'].'filter_vars_before_'.$action))
		{

			// Check if the action/function is set to do not filter
			if(array_key_exists($action, $this->hardcoded['dont_filter']))
			{			

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
					$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - Action '.$action.' is set to do not filter. Not filtering', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
			}
			else
			{
				// It can be filtered - log and apply filters

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
					$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - BEFORE - START', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}	
				
				// In WP only var1 is filtered, so only var1 changes - all rest are context-providing variables in apply_filters - they stay the same
				
				$v1 = apply_filters($this->internal['prefix'].'filter_vars_before_'.$action,$v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);
				

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
					$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - BEFORE - END', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}		
				
			}
			
	
		}
		
		if(!isset($this->internal['nobuffer']))
		{	
			//ob_start();
		}	

		if(method_exists($this,$action.'_c'))
		{


			if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
			{	
				$this->do_log(array('action'=>$action, 'status'=>' CORE START - calling action from core method', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
			}
	
			////////// Here magic happens //////////
			$return_c = $this->{$action.'_c'}($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);
			////////// Magic happened //////////
		

			if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
			{	
				$this->do_log(array('action'=>$action, 'status'=>' CORE END - calling action from core method if exists', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
			}				
		}
		
		if(method_exists($this,$action.'_p'))
		{


			if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
			{	
				$this->do_log(array('action'=>$action, 'status'=>' PLUGIN START - calling action from plugin method', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
			}
		
			// If there is any return for core function, assign it to an internal var so plugin function can see and ue it:
			if(isset($return_c))
			{
				$this->internal['core_return']=$return_c;
			}
			else
			{
				$this->internal['core_return']=false;
			}
			
			////////// Here magic happens //////////
			$return_p=$this->{$action.'_p'}($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);
			////////// Here magic happened //////////
			

			if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
			{	
				$this->do_log(array('action'=>$action, 'status'=>' PLUGIN END - calling action from core method if exists', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
			}
		}
		// Run post action actions and filters
		if(!isset($this->internal['nobuffer']))
		{
			//$output=ob_get_clean();
		}
		
		if(!isset($output))
		{
			$output=false;			
		}

		if(strlen($output)>0 AND !$this->internal['nobuffer'])
		{
			// Filter that runs after the action has been run and direct output is printed in case output buffering is not turned off before running the function
			// Still checks for if nobuffer is turned on because some action may set $output variable inside their action code.
	
			if(has_filter($this->internal['prefix'].'filter_output_after_'.$action))
			{	
				// Check if the action/function is set to do not filter
				if(array_key_exists($action, $this->hardcoded['dont_filter']))
				{			

				if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
				{	
						$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - Action '.$action.' is set to do not filter. Not filtering', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
					}
				}	
				else
				{


					if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
					{	
						$this->do_log(array('action'=>$action, 'status'=>'Output Filter - START', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
					}						
					
					$output=apply_filters($this->internal['prefix'].'filter_output_after_'.$action,$output,$v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);
					

					if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $action!=='do_log')
					{	
						$this->do_log(array('action'=>$action, 'status'=>'Output Filter - END', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
					}	
					
				}
			}
			
			echo $output;
		}
		
		// Set internal nobuffer to false before return in case it was given			
		$this->internal['nobuffer']=false;
			
		// This is the action hook which runs after the actual action completed
		if(has_action($this->internal['prefix'].'action_after_'.$action))
		{	
	

			if(array_key_exists($action, $this->hardcoded['dont_execute_hooks']))
			{
				if(isset($this->internal['do_log']) AND $action!=='do_log')
				{
					$this->do_log(array('action'=>$action, 'status'=>'Action Hooks - AFTER - Action '.$action.' is set to not execute hooks. Not running hooked functions', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
			}
			else
			{	

				if(isset($this->internal['do_log']) AND $action!=='do_log')
				{
					$this->do_log(array('action'=>$action, 'status'=>'Action Hooks - AFTER - START', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}	
				
				do_action($this->internal['prefix'].'action_after_'.$action,$v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);	

				if(isset($this->internal['do_log']) AND $action!=='do_log')
				{
					$this->do_log(array('action'=>$action, 'status'=>'Action Hooks - AFTER - END', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
			}
		}
		
		// If there is return from plugin methods, send that to return. If there is no return from plugin methods but there is from core methods, set that one:
		if(isset($return_p))
		{
			$return=$return_p;
		}
		else
		{
			if(isset($return_c))
			{
				$return=$return_c;
			}
			else
			{
				$return=false;
			}						
		
		}
	
		// Filter that runs after the action has been run and a return value is produced
		if(has_filter($this->internal['prefix'].'filter_vars_after_'.$action))
		{
			// Check if the action/function is set to do not filter
			if(array_key_exists($action, $this->hardcoded['dont_filter']))
			{
				if(isset($this->internal['do_log']) AND $action!=='do_log')
				{
					$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - Action '.$action.' is set to do not filter. Not filtering', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
			}
			else
			{
				
				if(isset($this->internal['do_log']) AND $action!=='do_log')
				{
					$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - AFTER - START', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}		
				
				$return = apply_filters($this->internal['prefix'].'filter_vars_after_'.$action,$return,$v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10);
					
				if(isset($this->internal['do_log']) AND $action!=='do_log')
				{
					$this->do_log(array('action'=>$action, 'status'=>'Variable FILTERS - AFTER - END', 'time' => microtime(true), 'vars' => array($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10)));
				}
			}
		}
		
		unset($action);
		return $return;
		
		
	}
	public function do_log_c($args)
    {
	
		$passed=$args['time']-$this->internal['start'];
		
		if(isset($this->internal['write_log']))
		{
			file_put_contents('log', $this->internal['id'].' - Action '.$args['action'].' '.$args['status'].' at '.$passed.' msec with vars v1 '.serialize($args['vars'][0]).
			' v2 '.serialize($args['vars'][1]).
			' v3 '.serialize($args['vars'][2]).
			' v4 '.serialize($args['vars'][3]).
			' v5 '.serialize($args['vars'][4]).
			' v6 '.serialize($args['vars'][5]).
			' v7 '.serialize($args['vars'][6]).
			' v8 '.serialize($args['vars'][7]).
			' v9 '.serialize($args['vars'][8]).
			' v10 '.serialize($args['vars'][9])
			.PHP_EOL
			
			 , FILE_APPEND);
			
			
		}
		else
		{
			$this->internal['log'][]=$this->internal['id'].' - Action '.$args['action'].' '.$args['status'].' at '.$passed.' msec';
			
		}
    }
	//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	//|||||||||||||||||||		Here start the Core Methods		|||||||||||||||||||||||
	//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	
	public function activate_c()
	{
		
		$this->require_admin_page();
		
		if(!current_user_can('activate_plugins'))
		{
			return false;			
		}
		// Get options just in case it is not loaded during activate or install - this will carry over to all other functions:
		
		$this->opt=get_option($this->internal['prefix'].'options');
				
		$this->create_tables();		
		
		$this->reset_languages();
		
		$this->setup_languages();
		
		
		
	}
	public function add_options_c($v1)
	{

		$option_array_to_add=$v1;

		$this->opt=array_replace_recursive(

			$this->opt,
			$option_array_to_add	
			
		);
	
	}
	public function admin_init_c()
	{
		if(!is_admin())
		{
			return;
		}
			
		add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_admin_styles'));
		add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_admin_scripts'));		

		add_action('admin_menu', array(&$this,'add_admin_menus'));
		
		add_action( 'admin_notices', array(&$this,'admin_notices'));
		
		add_action( 'wp_ajax_'.$this->internal['prefix'].'dismiss_admin_notice', array( &$this, 'dismiss_admin_notice' ),10,1 );
		
		add_filter( 'pre_set_site_transient_update_plugins', array(&$this, 'check_for_update' ) );
		
		add_filter( 'plugins_api', array( &$this, 'injectInfo' ), 90, 3 );
				
		
		if($this->internal['requested_action']!='')
		{
			$this->{$this->internal['requested_action']}($_REQUEST);
		}		
	
	}
	public function create_tables_c()
	{
		$this->require_admin_page();

		global $wpdb;
		global $wp_roles;
		global $current_user;

		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		foreach($this->internal['tables'] as $key => $value)
		{
			// If a table structure is not given, use default sql:
			
			if(!isset($this->internal['tables'][$key]['structure']) OR $this->internal['tables'][$key]['structure']=='')
			{
				$table_structure=$this->internal['default_table_structure'];
			
			}
			else
			{
				$table_structure=$this->internal['tables'][$key]['structure'];
			
			}
			$prefix_structure = explode("\n", $table_structure);
			
			$prefixed_table_structure='';
			
			foreach($prefix_structure as $row_key => $row_value)
			{
				$prefix_structure[$row_key]=trim($prefix_structure[$row_key]);
				
				if(substr($prefix_structure[$row_key],0,4)=='KEY ')
				{
					$key_it = str_replace('KEY ',"\n".'KEY '.$key.'_',$prefix_structure[$row_key]);
					$key_it = str_replace(' (',' ('.$key.'_',$key_it);
					$prefixed_table_structure .= $key_it;
				}
				else
				{
					$prefixed_table_structure .= $key.'_'.$prefix_structure[$row_key];					
				}
				
			}
$sql="CREATE TABLE ".$wpdb->prefix.$this->internal['id']."_".$key." (
".$prefixed_table_structure."
PRIMARY KEY  (".$key."_id)
) ".$charset_collate;
		
			
			dbDelta( $sql );
		}
		
		// Create meta tables if they arent here
		
		foreach($this->internal['meta_tables'] as $key => $value)
		{
			// If a table structure is not given, use default sql:
			
			if($this->internal['meta_tables'][$key]['structure']=='')
			{
				$table_structure=$this->internal['default_meta_structure'];
			
			}
			else
			{
				$table_structure=$this->internal['meta_tables'][$key]['structure'];
			
			}
			$type_length='';
			if($this->internal['meta_tables'][$key]['length']!='')
			{
				$type_length='('.$this->internal['meta_tables'][$key]['length'].')';				
				
			}
			$type_default='';
			if(isset($this->internal['meta_tables'][$key]['default']) AND $this->internal['meta_tables'][$key]['default']!='')
			{
				$type_default=' DEFAULT '.$this->internal['meta_tables'][$key]['default'];			
				
			}
			if($this->internal['meta_tables'][$key]['type']=='longtext')
			{
				$table_structure=str_replace('{REPLACEVALUE}','value '.$this->internal['meta_tables'][$key]['type']." NOT NULL",$table_structure);				
			}
			else			
			{
				$table_structure=str_replace('{REPLACEVALUE}','value '.$this->internal['meta_tables'][$key]['type'].$type_length.$type_default,$table_structure);					
			}			
			
			$prefix_structure = explode("\n", $table_structure);
			
			$prefixed_table_structure='';
			
	
			
			foreach($prefix_structure as $row_key => $row_value)
			{
				$prefix_structure[$row_key]=trim($prefix_structure[$row_key]);
				if(substr($prefix_structure[$row_key],0,4)=='KEY ')
				{
					$key_it='';
					if($this->internal['meta_tables'][$key]['type']!='longtext')
					{
						$key_it = str_replace('KEY ',"\n".'KEY '.$key.'_',$prefix_structure[$row_key]);
						$key_it = str_replace(' (',' ('.$key.'_',$key_it)."";
					}	
					$prefixed_table_structure .= $key_it;
				}
				else
				{
					$prefixed_table_structure .= $key.'_'.$prefix_structure[$row_key];					
				}
				
			}
			

		
						
$sql="CREATE TABLE ".$wpdb->prefix.$this->internal['id']."_".$key." (
".$prefixed_table_structure."
PRIMARY KEY  (".$key."_id)
) ".$charset_collate;
			
				
			dbDelta( $sql );
		}
		
		
	}
	public function deactivate_c($v1)
	{

		$this->require_admin_page();

		if(!current_user_can('activate_plugins'))
		{
			return false;			
		}

		flush_rewrite_rules(true);
		
	}
	public function do_admin_page_tabs_c()
	{

		$this->require_admin_page();
		
		unset($tab);
	
		if(isset($_REQUEST[$this->internal['prefix'].'tab']))
		{
			$tab=$_REQUEST[$this->internal['prefix'].'tab'];
		}

		if(!isset($tab))
		{
			$tab='dashboard';
		}
		
		// Now lets explode the tab string to find what tab hierarchy is wanted:
		
		$tab_hierarchy=explode('-',$tab);
		
		
		// Top tab is whatever is on the top of the array :
		
		$top_tab=array_shift(array_values($tab_hierarchy));
		
	
		$tabs = $this->internal['admin_tabs'];

		echo '<div id="icon-themes" class="icon32"><br></div>';

		echo '<h2 class="nav-tab-wrapper">';

		foreach( $tabs as $key => $value )
		{
			
			$class = ( $key == $top_tab ) ? ' nav-tab-active' : '';
			
			echo '<a class="nav-tab'.$class.'" href="?page=settings_'.$this->internal['id'].'&'.$this->internal['prefix'].'tab='.$key.'">'.$this->lang['admin_tab_'.$key].'</a>';

		}

		echo '</h2>';
		
		// Now do sub-tabs for the active tab:
		
		$keys = array_keys($tabs[$top_tab]);
		
		// Fetch last key
		
		$last = array_pop($keys);
		
		if(count($tabs[$top_tab])>0)
		{
			foreach( $tabs[$top_tab] as $key => $value )
			{
					
				echo '<a href="?page=settings_'.$this->internal['id'].'&'.$this->internal['prefix'].'tab='.$top_tab.'-'.$key.'">'.$this->lang['admin_tab_'.$top_tab.'_'.$key].'</a>';
				
				if($key!=$last)
				{
					echo ' | ';				
				}

			}	
		}
		
		// Level 3 for the active subtab if level 2 is selected and we have level 3
		
		
		if(count($tab_hierarchy)>1)
		{
			$second_level = $tab_hierarchy[1];
			
			
			$keys = array_keys($tabs[$top_tab][$second_level]);
	
			$last = array_pop($keys);
			echo '<hr>';
			
			foreach( $tabs[$top_tab][$second_level] as $key => $value )
			{
				echo '<a href="?page=settings_'.$this->internal['id'].'&'.$this->internal['prefix'].'tab='.$top_tab.'-'.$second_level.'-'.$key.'">'.$this->lang['admin_tab_'.$top_tab.'_'.$second_level.'_'.$key].'</a>';
				
				if($key!=$last)
				{
					echo ' | ';				
				}

			}
			echo '<hr>';
		}
		
		
	}
	public function do_setting_section_c($v1)
	{
		$tab=$v1;
	
		$this->internal['current_tab']=$tab;
			

		if(file_exists($this->internal['plugin_path'].'plugin/includes/setting_sections/'.$tab.'.php'))
		{
			require_once($this->internal['plugin_path'].'plugin/includes/setting_sections/'.$tab.'.php');
			
		}
	
	
	}
	public function do_setting_section_additional_settings_c($v1)
	{
		
		// This function is a wrapper to allow addons to be able to add their own setting entries to a section. Its included in sections.
	}
	public function do_settings_pages_c($v1)
	{
		$tab=$v1;
	
		if(isset($this->internal['setup_is_being_done']))
		{
			return;
		}
	
		if(isset($_REQUEST[$this->internal['prefix'].'tab']))
		{
			$tab=$_REQUEST[$this->internal['prefix'].'tab'];
		}
		
		if($tab=='' OR !$tab)
		{
			$tab='dashboard';
		
		}
		
		$template_vars=array('tab'=>$tab,'referer'=>$_SERVER['HTTP_REFERER']);

		// We don't want admin settings page headers and footers to be filtered by anyone. Therefore we don't use load_template here
		
		$admin_settings_page_header = file_get_contents($this->internal['plugin_path'].'plugin/includes/setting_sections/admin_settings_page_header.php');
	
		$admin_settings_page_header = $this->process_vars_to_template($this->internal, $admin_settings_page_header, array('id','prefix','plugin_url','admin_page_url','plugin_name'));
		
		$admin_settings_page_header = $this->process_vars_to_template($template_vars, $admin_settings_page_header);
		
		$admin_settings_page_header = $this->process_lang($admin_settings_page_header);	
		
		// We don't want admin settings page headers and footers to be filtered by anyone. Therefore we don't use load_template here
		
		$admin_settings_page_footer = file_get_contents($this->internal['plugin_path'].'plugin/includes/setting_sections/admin_settings_page_footer.php');
		
		$admin_settings_page_footer = $this->process_vars_to_template($this->internal, $admin_settings_page_footer, array('id','prefix','plugin_url','admin_page_url','plugin_name'));
		
		$admin_settings_page_footer = $this->process_vars_to_template($template_vars, $admin_settings_page_footer);
		
		$admin_settings_page_footer = $this->process_lang($admin_settings_page_footer);	

		echo $admin_settings_page_header;
		
		echo $this->do_admin_page_tabs();
		
				
		$this->do_setting_section($tab);
		
		echo $admin_settings_page_footer;
		
	}
	public function do_admin_settings_form_header_c()
	{
		
		$admin_settings_form_header = file_get_contents($this->internal['plugin_path'].'plugin/includes/setting_sections/admin_settings_form_header.php');
				
		$admin_settings_form_header = $this->process_vars_to_template($this->internal, $admin_settings_form_header, array('id','prefix','plugin_url','admin_page_url','plugin_name'));
		
		if(isset($_REQUEST['tab']))
		{
			$tab=$_REQUEST['tab'];
			
		}
		else
		{
			$tab='';
		}
	
		$template_vars=array('tab'=>$tab,'referer'=> isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");		

		$admin_settings_form_header = $this->process_vars_to_template($template_vars, $admin_settings_form_header);
		
		$admin_settings_form_header = $this->process_lang($admin_settings_form_header);	
		
		return $admin_settings_form_header;
		
	}
	public function do_admin_settings_form_footer_c()
	{

		$admin_settings_form_footer = file_get_contents($this->internal['plugin_path'].'plugin/includes/setting_sections/admin_settings_form_footer.php');
				
		$admin_settings_form_footer = $this->process_vars_to_template($this->internal, $admin_settings_form_footer, array('id','prefix','plugin_url','admin_page_url','plugin_name'));
		
		
		if(isset($_REQUEST['tab']))
		{
			$tab=$_REQUEST['tab'];
			
		}
		else
		{
			$tab='';
		}
				
		
		$template_vars=array('tab'=>$tab,'referer'=>$_SERVER['HTTP_REFERER']);		

		$admin_settings_form_footer = $this->process_vars_to_template($template_vars, $admin_settings_form_footer);
		
		$admin_settings_form_footer = $this->process_lang($admin_settings_form_footer);	
		
		return $admin_settings_form_footer;
				
	}
	public function frontend_init_c()
	{
		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue_frontend_styles'));
		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue_frontend_scripts'));
		
		// Sanitize $this->internal['requested_action'] before this!!! 

		if($this->internal['requested_action']!='')
		{
			$this->{$this->internal['requested_action']}($_REQUEST);
		}	

	}
	public function init_c()
	{

		// Initialize variables 

		$this->internal = $this->init_internal_vars();
		
		$this->info = $this->init_info();
		
		$this->opt = $this->init_options();

		// Lets get the option tabs into an internal var so they wont be bloated by dormant option keys from old addons in db
		
		$this->internal['tabs'] = array_flip(array_keys($this->opt));

		// Load info from db
		$this->info = $this->load_info();
		
		// Load options from db
		$this->opt = $this->load_options();
		
		// Load language from db
		$this->lang = $this->load_language();
		
		// Hooks debug info to bottom of WP page if logging is true and the user is admin

		if(isset($this->internal['do_log']) AND $this->internal['do_log'] AND $this->internal['requested_action']!=='do_log')
		{	
			add_action('wp_footer',  array(&$this, 'output_log'));
			add_action('admin_footer',  array(&$this, 'output_log'));
		}		
	}
	public function init_internal_vars_c()
	{
		// This function is mainly here so that addons can filter internal vars at init
		return $this->internal;

	}
	public function init_info_c()
	{
		require_once($this->internal['plugin_path'].'core/includes/default_info.php');
		require_once($this->internal['plugin_path'].'plugin/includes/default_info.php');
		
		return $this->info;
	}
	public function init_options_c()
	{
		require_once($this->internal['plugin_path'].'core/includes/default_options.php');
		require_once($this->internal['plugin_path'].'plugin/includes/default_options.php');
	
		return $this->opt;
	}
	public function load_info_c()
	{
		
		if($this->internal['requested_action']=='reset_info')
		{
			$this->reset_info();
			
			return $this->info;	
		}
			
		$saved_info = get_option($this->internal['prefix'].'info');
		
		if(!is_array($saved_info) OR count($saved_info)==0)
		{
			// No info saved. Save default info:
			
			//$this->reset_info();
			
			return $this->info;		
		}
		// If info is saved, then override/merge default info with saved one
		return array_replace_recursive
		(
			$this->info, 			
			$saved_info
		);		
	}
	public function load_options_c()
	{
			
		if($this->internal['requested_action']=='reset_options')
		{
			$this->reset_options();
			
			return $this->opt;	
		}
		
		$saved_options = get_option($this->internal['prefix'].'options');
		
		if(!is_array($saved_options) OR count($saved_options)==0)
		{
			// No info saved. Save default info:
			
			$this->reset_options();
			
			return $this->opt;		
		}
		// If options is saved, then override/merge default options with saved one
		$options = array_replace_recursive
		(
			$this->opt, 			
			get_option($this->internal['prefix'].'options')
		);	
		// Assign values to options var so load_plugins_p will be able to use and modify options
		$this->opt = $options;
		// return it so it can be filtered
		return $this->opt;

	}
	public function output_log_c()
	{

		// This action outputs log info at the end of the WP footer
		echo '<br>======================== LOG OF '.$this->internal['plugin_name'].'  ========================<br>';

		foreach($this->internal['log'] as $key => $value)
		{

			echo $this->internal['log'][$key];
			echo '<br>';

		}

	}
	public function require_admin_page_c()
	{
		if(!(is_admin() AND current_user_can( 'manage_options' )))
		{
		
			
			wp_die(__('Need admin privileges for this page',$this->internal['id'])); 
		}

	}
	public function reset_info_c()
	{
		$this->require_admin_page();
		
		delete_option($this->internal['prefix'].'info');	
		
		// Reset info using the default info which were initialized up until this point:
		
		update_option($this->internal['prefix'].'info' ,$this->info);
		
		return $this->info;

	}
	public function reset_options_c()
	{
		$this->require_admin_page();
		
		delete_option($this->internal['prefix'].'options');
		
		// Reset options using the default options which were initialized up until this point:
		
		update_option($this->internal['prefix'].'options', $this->opt);
		
		return $this->opt;
		
	}
	public function save_settings_c($v1)
	{
	
		$this->require_admin_page();
		
		$new_options=$v1['opt'];
		
		
		
		
		$this->opt = array_replace_recursive(
			
			$this->opt,
				
			$new_options
			
		);
	
		update_option($this->internal['prefix'].'options' ,$this->opt);

		// Load options from db
		$this->opt=$this->load_options();
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();		
		
	}
	public function save_settings_during_setup_c($v1)
	{

		$this->require_admin_page();

		$new_options=$v1['opt'];

		$this->opt = array_replace_recursive(
			
			$this->opt,
				
			$new_options
			
		);
	
		update_option($this->internal['prefix'].'options' ,$this->opt);

		// Load options from db
		$this->opt=$this->load_options();
			
		
	}
	public function message_c($v1)
	{
		$message=$v1;
		return $this->lang[$message];
		
	}
	public function upgrade_c($v1,$v2)
	{
		
		if(!current_user_can('manage_options'))
		{
			$this->queue_notice($this->lang['error_operation_failed_no_permission'],'error','error_operation_failed_no_permission','admin');
			return false;
		}		
		
		
		
		$upgrader_object = $v1;
		$options = $v2;
		
		if($upgrader_object->result['destination_name']!=$this->internal['id'])
		{
			return;			
		}

		
		$this->require_admin_page();
		
		
	

	}
	public function get_single_c($v1,$v2)
	{

		global $wpdb;
		
		$type = $v1;
		$id = $v2;
		
		$tables = $this->internal['data'][$type]['tables'];

		
		if(count($tables)>1)
		{
			foreach($tables as $key => $value)
			{
				$results[$key] = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$key." WHERE ".$key."_id = '".$id."'", ARRAY_A );
			}
			
			return $results;
		}
		else
		{
			reset($tables);
			$key=key($tables);
			
			$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$key." WHERE ".$key."_id = '".$id."'", ARRAY_A );

			
			return $results[0];				
			
		}
	}
	public function get_many_by_post_id_c($v1, $v2, $v3=false, $v4=false)
	{
		
		global $wpdb;
		
		$type = $v1;
		$post_id = $v2;
		
		if(!isset($order_by_clause))
		{
			$order_by_clause=''	;		
		}
			
		if(!isset($limit_clause))
		{
			$limit_clause='';	
		}
			
		
		$result =  $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$type." WHERE ".$type."_post = '".$post_id."' ".$order_by_clause." ".$limit_clause, ARRAY_A );
	
		
		if($wpdb->last_error!='')
		{

			$this->queue_notice($this->lang['sql_query_error'],'error','sql_query_error');
			$this->queue_notice($wpdb->last_error,'error','sql_query_error_message');			
		}
		
		return $result;
			
	}
	public function query_c($v1,$v2=false,$v3=false)
	{
		
		
	}
	public function make_button_c($v1,$v2,$v3=false)
	{
		$label=$v1;
		$url=$v2;
		$args=$v3;
		
	
		
		$button = $this->load_template('button');
		
		$button = $this->process_lang($button);
	
		$button = $this->process_vars_to_template($this->internal, $button, array('prefix','id'));

		$button = $this->process_vars_to_template(
		array(
			'button_label' => $label,
			'button_url' => $url,
		
		)		
		
		, $button);			
		
		// If any args given, process them too:
		
		if(is_array($args) AND count($args)>0)
		{
			$button = $this->process_vars_to_template($args, $button);	
			
		}
		
		return $button;
		
	}
	public function insert_wp_post_c($v1)
	{
		$args=$v1;
		
		// Insert the post into the database
		$post_id = wp_insert_post( $args ,true );		
		

		return $post_id;
				
	}
	public function get_user_avatar_c($v1,$v2=false,$v3=false,$v4=false,$v5=false)
	{
		$user_id=$v1;
		$size=$v2;
		$default=$v3;
		$alt=$v4;
		$args=$v5;
		
		if($size=='')
		{
			$size=48;
		}
		if($default=='')
		{
			$default='';
		}
		if($alt=='')
		{
			$alt=false;
		}
		if(!isset($args) OR $args=='')
		{
			$args=null;
		}
		
		return  get_avatar( $user_id, $size, $default, $alt, $args );		

	}
	public function insert_single_c($v1,$v2)
	{
		global $wpdb;
		$type=$v1;
		$args=$v2;
		
		$wpdb->query(
		
			"INSERT INTO 
			".$wpdb->prefix.$this->internal['id']."_".$type."
			(
			".$type."_post,
			".$type."_parent,
			".$type."_slug,
			".$type."_status,
			".$type."_sort,
			".$type."_created,
			".$type."_modified,
			".$type."_title,
			".$type."_content,
			".$type."_user,
			".$type."_group
			)
			VALUES
			(
				'".@$args[$type.'_post']."',
				'".@$args[$type.'_parent']."',
				'".@$args[$type.'_slug']."',
				'".@$args[$type.'_status']."',
				'".@$args[$type.'_sort']."',
				'".@$args[$type.'_created']."',
				'".@$args[$type.'_modified']."',
				'".esc_sql(@$args[$type.'_title'])."',
				'".esc_sql(@$args[$type.'_content'])."',
				'".esc_sql(@$args[$type.'_user'])."',
				'".@$args[$type.'_group']."'
			
			)"
		);

		return $wpdb->insert_id;
		
	}
	public function load_template_c($v1,$v2=false,$v3=false)
	{
		
		$template_file=$v1;
		$template=$v2;
		$template_path=$v3;
		
		if(!$template)
		{
			if($this->opt['template']!='')
			{
				$template=$this->opt['template'];			
			}
			else
			{
				$template='default';	
				
			}
		
			
		}
		
		if(!$template_path)
		{
			$template_path=$this->internal['template_path'];			
			
		}
		
	
		return file_get_contents($template_path.'/'.$template.'/'.$template_file.'.tpl');
		
	}
	public function process_lang_c($v1)
	{
		$str=$v1;

 
		foreach($this->lang as $key => $value)
		{
			
			$str=str_replace('{%%%'.$key.'%%%}',$this->lang[$key],$str);
			
		}
			
		return $str;
		
	}
	public function process_vars_to_template_c($v1,$v2,$v3=false)
	{
		$vars=$v1;
		$str=$v2;
		$keys=$v3;
	

		// If keys is set, then use only those array keys and their values
		if(is_array($keys) AND count($keys)>0)
		{
			foreach($keys as $key => $value)
			{
				$str=str_replace('{***'.$keys[$key].'***}',$vars[$keys[$key]],$str);
				
			}
		}
		else
		{
	
			// If no keys array is given, then it is wanted that we use the whole array to process. Iterate entire array :
			foreach($vars as $key => $value)
			{
				
				$str=str_replace('{***'.$key.'***}',$vars[$key],$str);
				
			}
		}
		
		
			
		return $str;
		
	}
	public function make_select_c($v1,$v2,$v3=false)
	{
		$array=$v1;
		$name=$v2;
		$selected=$v3;
		$select='<select name="'.$name.'">';
		
		foreach($array as $key => $value)
		{
			if($selected==$array[$key])
			{
				$make_selected = ' selected';
			}
			else
			{
				$make_selected = '';			
			}
			$select.='<option value="'.$key.'"'.$make_selected.'>'.$array[$key].'</option>';
			
		
		
		}
		$select.='</select>';
		
		
		return $select;
	
	
	}
	public function get_post_id_by_slug_c($v1,$v2)
	{
		global $wpdb;
		
		$type=$v1;
		$slug=$v2;
	
		
		return  $wpdb->get_var("SELECT * FROM ".$wpdb->posts." WHERE post_name = '".$slug."' AND post_type = '".$type."'");
	}
	public function setup_languages_c()
	{
		// Sets up languages into database from language files
		
		$lang_dir=$this->internal['plugin_path'].'plugin/includes/languages';
	

		if ($dir = opendir($lang_dir)) {
			while (($file = readdir($dir)) !== false) {
				if($file=='.' OR $file=='..')
				{
					
					continue;
				}
				unset($lang);
				include($lang_dir.'/'.$file);
				
				// Remove slashes in preparation for wp db:
				
				$lang=array_map('stripslashes',$lang);
				
				$lang_code=str_replace('.php','',$file);
				
				$current_entry=get_option($this->internal['prefix'].'lang_'.$lang_code);
				
				
				if(!is_array($current_entry))
				{
					// If no option exists lets init this var as an array so we can merge. In case of existing option in db, this will allow us to just add the new language entries to existing ones.
					
					$current_entry=array();
					
				}
				
				$current_entry = array_replace_recursive(
				
									$lang,
									$current_entry
					
				);	

				// Save the 
				update_option($this->internal['prefix'].'lang_'.$lang_code,$current_entry);
					
			}
			closedir($dir);
		}
		
		// Update language option in db
				
		$this->opt['lang']=get_bloginfo('language');
		
		update_option($this->internal['prefix'].'options' ,$this->opt);
	
	}
	public function reset_languages_c()
	{
		
		if(!current_user_can('manage_options'))
		{
			$this->queue_notice($this->lang['error_operation_failed_no_permission'],'error','error_operation_failed_no_permission','admin');
			return false;
		}			
		// Resets languages from language files
		
		$lang_dir=$this->internal['plugin_path'].'plugin/includes/languages';
	
		$false_once=false;

		if ($dir = opendir($lang_dir)) {
			while (($file = readdir($dir)) !== false) {
				if($file=='.' OR $file=='..')
				{
					
					continue;
				}
				unset($lang);
				include($lang_dir.'/'.$file);
				

				// Remove slashes in preparation for wp db:
				
				$lang=array_map('stripslashes',$lang);				
				
				$lang_code=str_replace('.php','',$file);
				
				// The difference is that this time we delete the existing language in db - modified language in db goes away, default one in file is saved to db
				
				delete_option($this->internal['prefix'].'lang_'.$lang_code);
				
				$current_entry=get_option($this->internal['prefix'].'lang_'.$lang_code);
				
				if(!is_array($current_entry))
				{
					// If no option exists lets init this var as an array so we can merge. In case of existing option in db, this will allow us to just add the new language entries to existing ones.
					
					$current_entry=array();
					
				}
				
				$current_entry = array_replace_recursive(
				
									$lang,
									$current_entry
					
				);	

			
				
				// Save the 
				$update = update_option($this->internal['prefix'].'lang_'.$lang_code,$current_entry);
				
				if(!$update)
				{
					$false_once = true;
					
				}
					
			}
			closedir($dir);
		}
		
	
		if($false_once)
		{
			$this->queue_notice($this->lang['error_updating_one_of_the_languages_failed'],'error','error_updating_one_of_the_languages_failed','admin');
			
			return false;
		}
		else
		{	
			// Reload active language
			$this->lang = $this->load_language();
			
			$this->queue_notice($this->lang['success_language_operation_successful'],'success','success_language_operation_successful','admin');
			
			return true;
		}			
		
		// Update language option in db
				
		$this->opt['lang']=get_bloginfo('language');
		
		update_option($this->internal['prefix'].'options' ,$this->opt);
	
	}
	public function load_language_c($v1)
	{
		// Loads language from db.
		
		if(isset($this->opt['lang']))
		{
			$lang = $this->opt['lang'];
		}
		else
		{
			$lang = 'en-US';			
		}		
		
		$lang_file = $this->internal['plugin_path'].'plugin/includes/languages/'.$lang.'.php';
		
		if(!file_exists($lang_file))
		{
			$lang='en-US';
			$this->opt['lang']='en-US';
			$this->update_opt();
			$lang_file = $this->internal['plugin_path'].'plugin/includes/languages/'.$lang.'.php';
			
		}
		
		 
		// Get saved values in db:
		
		$language_values = get_option($this->internal['prefix'].'lang_'.$lang);
		
		// If values dont exist, reset languages so we have them:
		
		if(!is_array($language_values))
		{
			$this->reset_languages();
			$language_values = get_option($this->internal['prefix'].'lang_'.$lang);
			
		}
		
		// Get the language from language file:

		include($lang_file);
		
		$language_values = array_replace_recursive(
		
							$lang,
							$language_values
		);
	
		return array_map('stripslashes', $language_values);
			
	}
	public function enqueue_frontend_styles_c($v1)
	{
		
	
		
	}
	public function enqueue_frontend_scripts_c($v1)
	{
	
	
	
		
	}
	public function enqueue_admin_styles_c($v1)
	{
	
		
	}
	public function enqueue_admin_scripts_c($v1)
	{
	
	
	
		
	}
	public function prepare_notices_c($v1,$v2=false)
	{
		$side=$v1;
		$template_type = $v2;
		
		if(!isset($side)) 
		{
			$side='front';
		}
		
		if($template_type=='' OR !$template_type)
		{
			$template_type='front';
			
		}
		
		// Merge any permanent notices if they exist:
		
		foreach($this->internal['notice_types'] as $key => $value)
		{
			// First sort the relevant notice array by numeric key:
			if(isset($this->internal['content'][$side.'_notices'][$key]))
			{
				if(is_array($this->internal['content'][$side.'_notices'][$key]) AND count($this->internal['content'][$side.'_notices'][$key])>0)
				{
					ksort($this->internal['content'][$side.'_notices'][$key],SORT_NUMERIC);
				
				}
			}
			$notice_templates[$side][$key] = $this->load_template($template_type.'_notices_'.$key);
			
			$notice_templates[$side][$key] = $this->process_vars_to_template($this->internal, $notice_templates[$side][$key],array('prefix','id'));
			
			
			if(isset($this->internal['content'][$side.'_notices'][$key]) AND count($this->internal['content'][$side.'_notices'][$key])>0 AND is_array($this->internal['content'][$side.'_notices'][$key]))
			{
				$message='';
				foreach($this->internal['content'][$side.'_notices'][$key] as $key_2 => $value_2)
				{
					
						
					$vars=array(
						
						'notice_content' => $this->add_to_notice($key_2,$key,$side),				
						'notice_id' => $key_2,				
						'notice_type' => $key,				
					);
				
				
					$message .= $this->process_vars_to_template($vars, $notice_templates[$side][$key]);
			
				}
			}			
			
		}
		if(isset($message))
		{
			return $message;
		}
		
		
	}
	public function dismiss_admin_notice_c($v1)
	{
		$request=$_POST;
		
		$direct=$v1;
		
		//wp_send_json($request);
		if(!current_user_can('manage_options'))
		{
			return false;
		}
		//wp_send_json($this->opt['content']);
		
		if(isset($direct) AND is_array($direct))
		{
			$request=$direct;
			
		}
		
		unset($this->opt['content']['perma_notices'][$request['notice_type']][$request['notice_id']]);
		
		$this->update_opt();
	}
	public function admin_notices_c($v1)
	{
		// If there are any perma admin notices, add them to admin notices:

		// Load perma notices to content :
		
		if(!isset($this->internal['content']['perma_notices']))
		{
			$this->internal['content']['perma_notices']='';
		}
		
		
		if(isset($this->opt['content']['perma_notices']))
		{
			$this->internal['content']['perma_notices']=$this->opt['content']['perma_notices'];
		}
		 
		echo $this->prepare_notices('perma','admin');
		echo $this->prepare_notices('admin','admin');
	
	}
	public function queue_content_c($v1,$v2,$v3,$v4)
	{
		// Queues content to content array per arguments
		
		$content=$v1;
		$key=$v2;
		$order=$v3;
		$side=$v4;
		
		// Set defaults :
		
		if(!isset($order)) $order = 50;
		
		if(!isset($side)) $side = 'front';
		
		
		if(!isset($this->internal['content'][$side]) OR !is_array($this->internal['content'][$side]))
		{
			// Init array if its not 
			$this->internal['content'][$side]=array();			
		}
		
		// Put into in place in existing array :
		
		$content = array(
			$key => $content		
		);

		// Check if the key for the order in array exists:
	
		if(isset($order) AND $order)
		{
			$exists=array_key_exists($order,$this->internal['content'][$side]);
		}
		else
		{
			$exists=false;
		}
		if(!$exists)
		{
			// Key is free. Great stuff. Just assign new content to array:
			
			$this->internal['content'][$side][$order]=$content;
			
			
		}
		else
		{
			// Key exists. Use function to get a free key (next available)
			
			$free_key = $this->place_into_array($this->internal['content'][$side],$order);
		
			$this->internal['content'][$side][$free_key]=$content;
			
		}
		
		// Ordering is done simply by using numeric keys, and the string key for content identifier allows for filtering content by addons
	
	}
	public function queue_notice_c($v1,$v2,$v3,$v4,$v5)
	{
		// Queues notices to content array per arguments
		
		$content=$v1;
		$type=$v2;
		$key=$v3;
		$side=$v4;
		$perma=$v5;
		
		// Set defaults :
		
		if(!isset($side)) $side = 'front';
		
		if(!isset($type)) $type = 'success';
		
		$side.='_notices';
		
		if(!isset($this->internal['content'][$side][$type]))
		{
			// Init array if its not 
			$this->internal['content'][$side][$type]=array();			
		}
	
		$this->internal['content'][$side][$type][$key]=$content;
		
		// Check if the key for the order in array exists:

		if($perma)
		{
			// Its a perma notice. Add it to relevant section in options:
	
			$this->opt['content'][$side][$type][$key]=$content;
			
			$this->update_opt();
			
		}
		
		// Ordering is done simply by using numeric keys, and the string key for content identifier allows for filtering content by addons
	
	}
	public function update_opt_p()
	{
		// Does nothing but wrap update_options for options:
		
		return update_option($this->internal['prefix'].'options',$this->opt);		

		
	}	
	public function place_into_array($v1,$v2)
	{
		$array=$v1;
		$key=$v2;
		
		// This function finds a free numeric array key next to the one specified, recursively
		
		// Increment the given key since it was sent here because it was not available:
		
		$key++;
		
		// Check if the array key is available, if so, return it. If not, check the next. If not available recurse. If available, return
		
		if(array_key_exists($key,$array))
		{
			$key = $this->place_into_array($array,$key);
		}
		
		return $key;			
		
		
	}
	public function prepare_content_c($v1)
	{
		$side=$v1;
		
		if(!isset($side)) $side = 'front';
		
		if(is_array($this->internal['content'][$side]) AND count($this->internal['content'][$side])>0)
		{
			// Sort the content array numerically
			ksort($this->internal['content'][$side],SORT_NUMERIC);
			
		}
			
		
		if(is_array($this->internal['content'][$side]) AND count($this->internal['content'][$side])>0)
		{
			foreach($this->internal['content'][$side] as $key => $value)
			{
				foreach($this->internal['content'][$side][$key] as $key_2 => $value_2)
				{
					$content.= $this->add_to_content($key_2,$key,$side);
					
					
				}
			}
			
		}
		
		
		// Do and process any extra variables if any was assigned:

		if(is_array($this->internal['template_vars']) AND count($this->internal['template_vars'])>0)
		{
			$content = $this->process_vars_to_template($this->internal['template_vars'],$content);
			
			
		}
		
		return $content;
	
	}	
	public function prepare_queued_content_c($v1)
	{
		$side=$v1;
		
		if(!isset($side)) $side = 'front';
		
		if(is_array($this->internal['content'][$side]) AND count($this->internal['content'][$side])>0)
		{
			// Sort the content array numerically
			ksort($this->internal['content'][$side],SORT_NUMERIC);
			
		}
			
		
		if(is_array($this->internal['content'][$side]) AND count($this->internal['content'][$side])>0)
		{
			foreach($this->internal['content'][$side] as $key => $value)
			{
				foreach($this->internal['content'][$side][$key] as $key_2 => $value_2)
				{
					$content.= $this->add_to_content($key_2,$key,$side);
					
					
				}
			}
			
		}
		
		
		// Do and process any extra variables if any was assigned:

		if(is_array($this->internal['template_vars']) AND count($this->internal['template_vars'])>0)
		{
			$content = $this->process_vars_to_template($this->internal['template_vars'],$content);
			
			
		}
		
		return $content;
	
	}	
	public function set_template_var_c($v1,$v2,$v3=false)
	{
		$key = $v1;
		$value = $v2;
		$template = $v3;
		
		if($template=='' OR !$template)
		{
			$template = 'content';			
		}
		
		$this->internal['template_vars'][$template][$key]=$value;		
		
	}
	public function append_template_var_c($v1,$v2,$v3=false)
	{
		$key = $v1;
		$value = $v2;
		$template = $v3;
		
		if($template=='' OR !$template)
		{
			$template = 'content';			
		}
		if(!isset($this->internal['template_vars'][$template][$key]))
		{
			$this->internal['template_vars'][$template][$key]='';
			
		}
		
		$this->internal['template_vars'][$template][$key].=$value;		
		
	}
	public function load_append_template_part_c($v1,$v2=false)
	{
		$template_part = $v1;
		$template = $v2;
		
		if($template=='' OR !$template)
		{
			$template = 'content';			
		}
		
		$this->internal['template_parts'][$template].=$this->load_template($template_part);		
		
	}
	public function load_set_template_part_c($v1,$v2=false)
	{
		$template_part = $v1;
		$template = $v2;
		
		if($template=='' OR !$template)
		{
			$template = 'content';			
		}
		
		$this->internal['template_parts'][$template]=$this->load_template($template_part);		
		
	}
	public function process_template_c($v1)
	{
		$template = $v1;
		
		$processed_template = $this->process_lang($this->internal['template_parts'][$template]);
		
		$processed_template =  $this->process_vars_to_template($this->internal, $processed_template,array('prefix','id'));
		
		$processed_template = $this->process_vars_to_template($this->internal['template_vars'][$template],$processed_template);
		
		if(isset($processed_template))
		{
			return $processed_template;
		}
		else
		{
			return false;
		}
	}
	public function add_to_content_c($v1,$v2,$v3)
	{
		$key_2=$v1;
		$key=$v2;
		$side=$v3;
		
		
		// This function is only a wrapper to allow easy filtering of content processing - all it does is to return relevant piece from content array
		
		return $this->internal['content'][$side][$key][$key_2];
	
	}
	public function add_to_notice_c($v1,$v2,$v3)
	{
		$key_2=$v1;
		$type=$v2;
		$side=$v3;
		
		if(!isset($type)) $type = 'success';
		
		if(!isset($side)) $side = 'front';
		

		// This function is only a wrapper to allow easy filtering of notice processing - all it does is to return relevant piece from content array
		
		
		return $this->internal['content'][$side.'_notices'][$type][$key_2];
	
	}
	public function do_admin_language_selector_c($v1)
	{
		$selected_lang=$v1;
		
		global $wpdb;
		// Read all existing languages in db 
		
		$languages=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "options WHERE option_name LIKE '".$this->internal['prefix']."lang%'",ARRAY_A);
		
		foreach($languages as $key => $value)
		{
			$iso_code = str_replace($this->internal['prefix'].'lang_','',$languages[$key]['option_name']);
			$lang_array[$iso_code]=$iso_code;
			
		}
		
		// Make the select : 
		
		$language_select = $this->make_select($lang_array,$this->internal['prefix'].'language',$this->opt['lang']);
		
		return $language_select;
	}
	public function save_language_c($v1)
	{
		if(!current_user_can('manage_options'))
		{
			$this->queue_notice($this->lang['error_operation_failed_no_permission'],'error','error_operation_failed_no_permission','admin');
			return false;
		}	
		
		$request=$v1;
		
		// We will save this modified version of the language as a different language in db for convenience
		
		$updated_lang = $request[$this->internal['prefix'].'lang_strings'];
		
		$updated_lang= array_map('stripslashes',$updated_lang);

		if($updated_lang==$this->lang)
		{		
			$this->queue_notice($this->lang['info_language_is_already_same_with_saved'],'info','info_language_is_already_same_with_saved','admin');			
			return false;		
			
		}
		
		// Remove _custom from sent language id if it exists:
		
		$request[$this->internal['prefix'].'lang']=str_replace('_custom','',$request[$this->internal['prefix'].'lang']);
		
		$updated = update_option($this->internal['prefix'].'lang_'.$request[$this->internal['prefix'].'lang'].'_custom' ,$updated_lang);
		
		if($updated)
		{
			$this->queue_notice($this->lang['success_language_translation_saved'],'success','success_language_translation_saved','admin');
			
			// Set new language to modified one:
			
			$this->opt['lang']=$request[$this->internal['prefix'].'lang'].'_custom';
			
			$updated = update_option($this->internal['prefix'].'options' ,$this->opt);
			
			if($updated)
			{
				// Reload language
				$this->lang = $this->load_language();
				
				$this->queue_notice($this->lang['info_active_language_updated'],'info','info_active_language_updated','admin');
					
			}
			else
			{		
				$this->queue_notice($this->lang['error_language_operation_failed'],'error','error_language_operation_failed','admin');
				
				return false;
			}		
			
			// Reload language
			
			$this->lang = $this->load_language();
			
			
			return true;
		}
		else
		{		
			$this->queue_notice($this->lang['error_language_translation_no_save'],'error','error_language_translation_no_save','admin');
			
			return false;
		}			
		

	
	}
	public function choose_language_c($v1)
	{
		if(!current_user_can('manage_options'))
		{
			$this->queue_notice($this->lang['error_operation_failed_no_permission'],'error','error_operation_failed_no_permission','admin');
			return false;
		}

		$language=$v1[$this->internal['prefix'].'language'];		
		
		if($this->opt['lang']==$language)
		{
			$this->queue_notice($this->lang['info_language_is_already_active'],'info','info_language_is_already_active','admin');			
			return false;
		}		
	
		$this->opt['lang']=$language;
		
		
		$updated = update_option($this->internal['prefix'].'options' ,$this->opt);
		
		if($updated)
		{
			// Reload language
			$this->lang = $this->load_language();
			
			$this->queue_notice($this->lang['success_language_operation_successful'],'success','success_language_operation_successful','admin');
			
			// Check and create pages for current language:
			
			
			return true;
		}
		else
		{		
			$this->queue_notice($this->lang['error_language_operation_failed'],'error','error_language_operation_failed','admin');
			
			return false;
		}		
	
	}
	public function get_item_meta_c($v1,$v2,$v3=false,$v4=false,$v5=false)
	{
		global $wpdb;
		
		$item_id=$v1;
		$meta_name=$v2;
		$meta_type=$v3;
		$data_type=$v4;
		$single=$v5;
		

		if($meta_type=='')
		{
			$meta_type='longtext';
		}
	
		if($data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
		
		
		// First prepare the sql to check if meta exists
		
		
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type." WHERE ";
		
		$where_statement = $meta_type."_parent = %d
		AND 
		".$meta_type."_data_type = '%s'
		AND 
		".$meta_type."_name = '%s'
		";
		// Decide which kind of replacer to use for value
		if(isset($value_type) AND $value_type=='int')
		{
			$value_replacer = "%d";
		}
		elseif(isset($value_type) AND $value_type=='float')
		{
			$value_replacer = "%f";				
		}
		else
		{
			// Treat it as string :
			$value_replacer = "'%s'";	
			
		}		
		// We do not add set into query, since it is there for easy retrieval of data groups.
				
		// Now, if the value was supplied, we are going to add existence of that value as well:
		
		if(isset($old_meta_value))
		{
			
			// Decide what kind of value it will be treated as:
			
			$where_statement .= " 
			AND 
			".$meta_type."_value = ".$value_replacer."
			";

		}
	
		// Sql ready, now prepare with the values array:
	
		$sql.=$where_statement;
		
		$values_array=array(
		
			$item_id,
			$data_type,
			$meta_name
		
		);		
		
	
		
		if(isset($old_meta_value))
		{
			array_push($values_array,$old_meta_value);
		
		}
		
		$prepared_sql = $wpdb->prepare(
		
						$sql,
						
						$values_array
			  
		);
		// Now get the result : 
			
		
		$results = $wpdb->get_results($prepared_sql,ARRAY_A);

		
		if(count($results)>0)
		{	

			if($single)
			{
				
				
				// They want only one single result. Pull the first value and give it out
				return $results[0][$meta_type.'_value'];
				
			}
			// Else return entire result array
			return $results;		
		}
		
		
	}
	public function get_all_item_meta_c($v1)
	{
		global $wpdb;
		
		$item_id=$v1;
		

		// Delete all metas from all tables by item id:
		
		foreach($this->internal['meta_tables'] as $key => $value)
		{
			
			$meta_type=$key;
			
			$sql="SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type;

			$where_statement = " WHERE ";

			$where_statement.=$meta_type."_parent = %d";
			
			$sql.= $where_statement;

			$values_array=array(

				$item_id
			);

			
			$prepared_sql = $wpdb->prepare(

							$sql,
							
							$values_array
				  
			);
		
			$result = $wpdb->get_results($prepared_sql,ARRAY_A);	

			if(count($result)>0)
			{
				foreach($result as $key => $value)
				{
					$metas[$meta_type][]=$result[$key];
				}
			}

		}
		if(count($metas)>0)
		{
			return $metas;			
		}
		else
		{
			return false;			
		}
		
	}
	public function get_items_by_meta_multi_c($v1,$v2,$v3=false,$v4=false)
	{
		
		
		
		
		
	}
	public function get_items_by_meta_c($v1,$v2=false,$v3=false,$v4=false,$v5=false)
	{
		global $wpdb;
		
		$meta_name = $v1;
		$meta_values = $v2;
		$meta_type = $v3;
		$data_type = $v4;
		
		$args = $v5;
		
		
		if(isset($args['order_by']))
		{
			$order_by = $args['order_by'];
		}
		else
		{
			$order_by = false;
		}
		if(isset($args['limit']))
		{
			$limit = $args['limit'];
		}
		else
		{
			$limit = false;
		}
		if(isset($args['start']))
		{
			$start = $args['start'];
		}
		else
		{
			$start = false;
		}
		if(isset($args['end']))
		{
			$end = $args['end'];
		}
		else
		{
			$end = false;
		}
		if(isset($args['sort']))
		{
			$sort = $args['sort'];
		}
		else
		{
			$sort = false;
		}

		if(!isset($meta_type) OR $meta_type=='')
		{
			$meta_type='longtext';
		}
	
		if($data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
		
		$meta_value_type = $this->internal['meta_tables'][$meta_type]['type'];
		
		// Meta's value type needs to be decided. Wordpress recognizes int, float, string, we are going to treat datetime as string as well.
		if(	
			$meta_value_type == 'int' OR 
			$meta_value_type == 'bigint' OR
			$meta_value_type == 'tinyint' OR
			$meta_value_type == 'smallint'
		)
		{
			$value_type='int';	
			
		}
		elseif(	$meta_value_type == 'float')
		{
			$value_type='float';	
			
		}
		else
		{
			$value_type='string';			
		}
		// First prepare the sql to check if meta exists
				
		// Left Join data table rows if requested:
		
		if($args['get_data_rows'])
		{
			$sql = "SELECT m.".$meta_type."_parent,d.* FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type." m ";
						
			if($data_type=='post')
			{
				// WordPress posts to be left joined:
				$sql.=" LEFT JOIN ".$wpdb->posts." d ON d.ID = m.".$meta_type."_parent ";
			}
			else
			{
				// Our own data type from our own table
				
				$sql.=" LEFT JOIN ".$wpdb->prefix.$this->internal['id']."_".$data_type." d ON d.".$data_type."_id = m.".$meta_type."_parent ";
				
				
			}
			
		}
		else
		{
			// Only get meta values :
			
			$sql = "SELECT m.".$meta_type."_parent FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type." m ";
			
		}
		
		$where_statement = " WHERE ";		
		$where_statement .=  " m.".$meta_type."_data_type = '%s'";
		$where_statement .=  " AND ";
		$where_statement .=   " m.".$meta_type."_name = '%s' ";
		
		$values_array=array(
		
			$data_type,
			$meta_name,
		
		);				
		
		
		// Decide which kind of replacer to use for value
		if($value_type=='int')
		{
			$value_replacer = "%d";
		}
		elseif($value_type=='float')
		{
			$value_replacer = "%f";				
		}
		else
		{
			// Treat it as string :
			$value_replacer = "'%s'";	
			
		}		

		// Check if multiple criteria is given - meaning we need to get items by OR operator - run through the array to add as many conditions:
		
		if(is_array($meta_values) AND count($meta_values)>0)
		{
			$where_statement .=" AND ( ";
			
			$value_keys = array_keys($meta_values);
						
			$last_key = array_pop($value_keys);
			
			foreach($meta_values as $key => $value)
			{
				
				$where_statement .= " m.".$meta_type."_value = ".$value_replacer;
				
				if($key!=$last_key)
				{
					$where_statement.=" OR ";					
				}
				
				 
				// push the value into value array:
				
				array_push($values_array,$meta_values[$key]);
				
			}
			$where_statement .=" ) ";
			
		}
		else
		{
			// Single value was given. add only once:
			
			$where_statement .= " AND m.". $meta_type."_value = ".$value_replacer;	
			
			array_push($values_array,$meta_values);		
			
		}
		
		// Now we check the args and process:
		
		if($order_by!='' AND $order_by!=false)
		{
			// Add order by:
			// Check if the value is in list of allowed column suffixes so we wont have to sanitize and add directly:
			
			if(in_array($order_by,array('id','data_type','set','parent','name','value')))
			{
			
				$where_statement .= " ORDER BY m.".$meta_type.'_'.$order_by." ";

			}
			
		}
		if($sort!='' AND $sort!=false)
		{
			// Add order by:
			// Just check and add directly so we wont have to sanitize
			if($sort=='ASC' OR $sort=='DESC')
			{
			
				$where_statement .= $sort." ";
			}
			
		}
		
		// If a start is given, but not limit, they want all rows starting from that row - do the maximum number of items trick mysql manual explains: http://dev.mysql.com/doc/refman/5.1/en/select.html#id4442276
		
		if(($limit=='' OR $limit==false) AND $start != '' AND $start != false)
		{
			
			$where_statement .= " LIMIT %d, 18446744073709551615";
			
			array_push($values_array,$start);		
			
			
		}
		
		// If both start and end is given, use them accordingly:
		

		if($limit!='' AND $limit!=false AND $start != '' AND $start != false)
		{
			
			$where_statement .= " LIMIT %d, %d";
			
			array_push($values_array,$start);	
			array_push($values_array,$limit);	
			
		}		
		
		// If only limit is given they only want first x rows
		

		if($limit!='' AND $limit!=false AND ($start == '' OR $start == false))
		{
			
			$where_statement .= " LIMIT %d ";
			
			array_push($values_array,$limit);
			
		}		
		
			
		// Sql ready, now prepare with the values array:
	
		$sql.=$where_statement;
		
		
		
		$prepared_sql = $wpdb->prepare(
		
						$sql,
						
						$values_array
			  
		);
		
				
		// Now get the result : 
		
		$results = $wpdb->get_results($prepared_sql,ARRAY_A);
		
		if(count($results)>0)
		{
			foreach($results as $key => $value)
			{
				$items[]=$results[$key];
			}
			
		}

		if(count($items)>0)
		{
			return $items;			
		}
		else
		{
			return false;			
		}		
		
		
		if(is_array($meta_value))
		{
			// meta value is array. We will get items which fulfill multiple value criteria
			
			
			
			
		}
		else
		{
			
			
		}
		
		
		
		
	}
	public function get_item_meta_by_item_id_and_set_c($v1,$v2,$v3=false,$v4=false)
	{
		global $wpdb;
		
		$item_id=$v1;
		$set=$v2;
		$meta_type=$v4;
		$data_type=$v5;
		
		if($meta_type=='')
		{
			$meta_type='longtext';
		}
	
		if($data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		
		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
		
		// Prepare the sql		
		
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type." WHERE ";
		
		$where_statement = $meta_type."_parent = %d
		AND 
		".$meta_type."_data_type = '%s'
		";

		$where_statement.=" AND ";
			
		$where_statement.=$meta_type."_set = '%s' ";

		// Sql ready, now prepare with the values array:
	
		$sql.=$where_statement;
		
		$values_array=array(
		
			$item_id,
			$data_type,
			$set
		
		);		
		
		
		
		$prepared_sql = $wpdb->prepare(
		
						$sql,
						
						$values_array
			  
		);
		
		
		// Now get the result : 
		
		$results = $wpdb->get_results($prepared_sql,ARRAY_A);
		

		if(count($results)>0)
		{
			foreach($results as $key => $value)
			{
				$metas[$meta_type][]=$results[$key];
			}
			
		}

		if(count($metas)>0)
		{
			return $metas;			
		}
		else
		{
			return false;			
		}
		
	}
	public function get_all_item_meta_by_set_c($v1,$v2)
	{
		global $wpdb;
		
		$item_id=$v1;
		$set=$v2;
		

		// Select all metas from all tables by item id and set:
		
		foreach($this->internal['meta_tables'] as $key => $value)
		{
			
			$meta_type=$key;
			
			$sql="SELECT * FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type;

			$where_statement = " WHERE ";

			$where_statement.=$meta_type."_parent = %d ";
			
			$where_statement.=" AND ";
			
			$where_statement.=$meta_type."_set = '%s' ";
			
			$sql.= $where_statement;

			$values_array=array(

				$item_id,
				$set,
			);

			
			$prepared_sql = $wpdb->prepare(

							$sql,
							
							$values_array
				  
			);
			
		
			$result = $wpdb->get_results($prepared_sql,ARRAY_A);	

			if(count($result)>0)
			{
				foreach($result as $key => $value)
				{
					$metas[$meta_type][]=$result[$key];
				}
			}

		}
		
		if(count($metas)>0)
		{
			return $metas;			
		}
		else
		{
			return false;			
		}
		
	}
	public function delete_item_c($v1,$v2)
	{
		global $wpdb;
		
		$item_id =$v1;
		$data_type =$v2;
		
		
		
		$sql="DELETE FROM ".$wpdb->prefix.$this->internal['id']."_".$data_type;

		$where_statement = " WHERE ";

		$where_statement.=$data_type."_id = %d";
		
		$sql.= $where_statement;

		$values_array=array(

			$item_id,
		);
		$prepared_sql = $wpdb->prepare(

						$sql,
						
						$values_array
			  
		);
		
		$result = $wpdb->query($prepared_sql);	

		if($result!== false)
		{
			return $result;					
		}
		else
		{
			return false;
		}
		
	}
	public function delete_all_item_meta_c($v1,$v2=false)
	{
		global $wpdb;
		
		$item_id =$v1;
		$post_type =$v2;
		
		if($post_type=='' OR !$post_type)
		{
			$post_type = 'post';			
			
		}
		
		// Select all metas from all tables by item id:
		
		foreach($this->internal['meta_tables'] as $key => $value)
		{
			
			$meta_type=$key;
			
			$sql="DELETE FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type;

			$where_statement = " WHERE ";

			$where_statement.=$meta_type."_parent = %d";
			
			$where_statement.=" AND ";
			
			$where_statement.=$meta_type."_data_type = %s";
			
			$sql.= $where_statement;

			$values_array=array(

				$item_id,
				$post_type,
			);

			
			$prepared_sql = $wpdb->prepare(

							$sql,
							
							$values_array
				  
			);
			
			
			$result = $wpdb->query($prepared_sql);	

			if(!$result)
			{
				$failed_once=true;				
			}

		}		
		
		// Return fail even if one single sql query failed once:
		if($failed_once)
		{
			return false;			
		}
		else
		{
			return true;
		}
		
	}
	public function delete_item_meta_by_set_c($v1,$v2)
	{
		global $wpdb;
		
		$item_id=$v1;
		$set=$v2;
		
		
		// Delete all metas from all tables by set:
		
		foreach($this->internal['meta_tables'] as $key => $value)
		{
			
			$meta_type=$key;
			
			$sql="DELETE FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type;

			$where_statement = " WHERE ";

			$where_statement.=$meta_type."_parent = %d";
			$where_statement .= " AND ";
			$where_statement.=$meta_type."_set = '%s'";
			
			$sql.=$where_statement;

			$values_array=array(

				$item_id,
				$set
			);

			
			$prepared_sql = $wpdb->prepare(

							$sql,
							
							$values_array
				  
			);
		
			$result = $wpdb->query($prepared_sql);	

			if(!$result)
			{
				$failed_once=true;				
			}

		}		
		
		// Return fail even if one single sql query failed once:
		if($failed_once)
		{
			return false;			
		}
		else
		{
			return true;
		}
			
	
	}
	public function delete_meta_by_meta_id_c($v1,$v2)
	{
		global $wpdb;
		
		$meta_id=$v1;
		$meta_type=$v2;

		if($meta_type=='')
		{
			$meta_type='longtext';
		}		
				
		$sql="DELETE FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type;

		$where_statement .= " WHERE ";
		
		$where_statement.=$meta_type."_id = %d";
		
		$sql.=$where_statement;

		$values_array=array(

			$meta_id
		);

		
		$prepared_sql = $wpdb->prepare(

						$sql,
						
						$values_array
			  
		);
	
		
		$result = $wpdb->query($prepared_sql);		

		if($result!== false)
		{
			return $result;					
		}
		else
		{
			return false;
		}			
		
	}
	public function delete_meta_by_item_id_c($v1,$v2,$v3,$v4=false,$v5=false)
	{
		global $wpdb;
		
		$item_id=$v1;
		$meta_name=$v2;
		$data_type=$v3;
		$meta_type=$v4;
		$meta_value=$v5;
		
		if($meta_type=='')
		{
			$meta_type='longtext';
		}
		
		//+
		
		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
	
		if($data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		$meta_value_type = $this->internal['meta_tables'][$meta_type]['type'];

		// Meta's value type needs to be decided. Wordpress recognizes int, float, string, we are going to treat datetime as string as well.
		if(	
			$meta_value_type == 'int' OR 
			$meta_value_type == 'bigint' OR
			$meta_value_type == 'tinyint' OR
			$meta_value_type == 'smallint'
					
		)
		{
			$value_type='int';	
			
		}
		elseif(	$meta_value_type == 'float')
		{
			$value_type='float';	
			
		}
		else
		{
			$value_type='string';			
		}		
			
		// Decide which kind of replacer to use for value
		if($value_type=='int')
		{
			$value_replacer = "%d";
		}
		elseif($value_type=='float')
		{
			$value_replacer = "%f";				
		}
		else
		{
			// Treat it as string :
			$value_replacer = "'%s'";	
			
		}			

		$sql="DELETE FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type." WHERE ";

		$where_statement.=$meta_type."_data_type = '%s'";
		$where_statement.=" AND ";
		
		$where_statement.=$meta_type."_parent = %d";
		$where_statement.= " AND ";		

		$where_statement.=$meta_type."_name = '%s'";

		if($meta_value!='')
		{

			$where_statement.=" AND ";				
			$where_statement.=$meta_type."_value = ".$value_replacer;		
				
		}

		$sql.=$where_statement;


		$values_array=array(

			$data_type,
			$item_id,
			$meta_name,
		);

		if($meta_value!='')
		{
			
			array_push($values_array,$meta_value);
				
		}	
		
		$prepared_sql = $wpdb->prepare(

						$sql,
						
						$values_array
			  
		);

		
		$result = $wpdb->query($prepared_sql);
		
		

		if($result!== false)
		{
			return $result;					
		}
		else
		{
			return false;
		}		
			
	}
	public function update_meta_by_meta_id_c($v1,$v2,$v3=false)
	{
		global $wpdb;
		
		$meta_id=$v1;
		$meta_value=$v2;
		$meta_type=$v3;
		
		if($meta_type=='')
		{
			$meta_type='longtext';
		}
		

		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
	
		if(!isset($data_type) OR $data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		$meta_value_type = $this->internal['meta_tables'][$meta_type]['type'];


		// Meta's value type needs to be decided. Wordpress recognizes int, float, string, we are going to treat datetime as string as well.
		if(
			$meta_value_type == 'int' OR 
			$meta_value_type == 'bigint' OR
			$meta_value_type == 'tinyint' OR
			$meta_value_type == 'smallint'
			
		)
		{
			$value_type='int';	
			
		}
		elseif(	$meta_value_type == 'float')
		{
			$value_type='float';	
			
		}
		else
		{
			$value_type='string';			
		}		
			
		// Decide which kind of replacer to use for value
		if($value_type=='int')
		{
			$value_replacer = "%d";
		}
		elseif($value_type=='float')
		{
			$value_replacer = "%f";				
		}
		else
		{
			// Treat it as string :
			$value_replacer = "'%s'";	
			
		}			

		$sql="UPDATE ".$wpdb->prefix.$this->internal['id']."_".$meta_type." SET ".$meta_type."_value = ".$value_replacer." ";

		$where_statement=" WHERE ".$meta_type."_id = %d";

		$sql.=$where_statement;


		$values_array=array(

			$meta_value,
			$meta_id

		);	


		$prepared_sql = $wpdb->prepare(

						$sql,
						
						$values_array
			  
		);


		$result = $wpdb->query($prepared_sql);


		if($result!== false)
		{
			return $result;					
		}
		else
		{
			return false;
		}		
		
	}
	public function add_meta_c($v1,$v2,$v3,$v4=false,$v5=false,$v6=false,$v7=false)
	{

		global $wpdb;
		
		$item_id=$v1;
		$meta_name=$v2;
		$meta_value=$v3;
		$meta_type=$v4;
		$data_type=$v5;
		$old_meta_value=$v6;
		$set=$v7;
		

		
		if($meta_type=='')
		{
			$meta_type='longtext';
		}
		
		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
	
		if($data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		$meta_value_type = $this->internal['meta_tables'][$meta_type]['type'];
		
		// Meta's value type needs to be decided. Wordpress recognizes int, float, string, we are going to treat datetime as string as well.
		if(	
			$meta_value_type == 'int' OR 
			$meta_value_type == 'bigint' OR
			$meta_value_type == 'tinyint' OR
			$meta_value_type == 'smallint'
			
			)
		{
			$value_type='int';	
			
		}
		elseif(	$meta_value_type == 'float')
		{
			$value_type='float';	
			
		}
		else
		{
			$value_type='string';			
		}		
			
		// Decide which kind of replacer to use for value
		if($value_type=='int')
		{
			$value_replacer = "%d";
		}
		elseif($value_type=='float')
		{
			$value_replacer = "%f";				
		}
		else
		{
			// Treat it as string :
			$value_replacer = "'%s'";	
			
		}	
		
		$sql="INSERT INTO ".$wpdb->prefix.$this->internal['id']."_".$meta_type."
		(";

		// Start adding columns 

		$sql.= $meta_type."_id,";
		$sql.= $meta_type."_data_type,";
		$sql.= $meta_type."_set,";
		$sql.= $meta_type."_parent,";
		$sql.= $meta_type."_name,";
		$sql.= $meta_type."_value";


		$values_array=array(
			'',
			$data_type,
			$set,
			$item_id,
			$meta_name,
			$meta_value


		);


		$sql.=") VALUES (

			%d,
			'%s',
			'%s',
			%d,
			'%s',
			".$value_replacer."
			
		)";


		$prepared_sql = $wpdb->prepare(

						$sql,
						
						$values_array
			  
		);
	
		$result = $wpdb->query($prepared_sql);

		if($result!== false)
		{
			return $result;					
		}
		else
		{
			return false;
		}					
		
		
	}
	public function send_email_c($args)
	{

		
		// Add from filters to prevent overriding of our mail from name and email if they are given:
		
		if(isset($args['from_name']))
		{
			add_filter('wp_mail_from_name',array(&$this,'filter_email_from_name'));
		}
		if(isset($args['from_email']))
		{
			add_filter('wp_mail_from',array(&$this,'filter_email_from_address'));
		}
		
		$result = wp_mail(
				$args['to'],
				$args['subject'],
				$args['message'],
				$args['headers'],
				$args['attachments']
		);
		
		// Now remove filters if necessary:
		
	
		if(isset($args['from_name']))
		{
			remove_filter('wp_mail_from','custom_wp_mail_from');
		}
		if(isset($args['from_email']))
		{
			remove_filter('wp_mail_from_name','custom_wp_mail_from_name');
		}		
		
		return $result;
		
	}
	public function filter_email_from_address($v1) 
	{
		return $this->opt['from_email'];
	}
	public function filter_email_from_name($v1) 
	{
		return $this->opt['org_name'];
	}			
	public function update_meta_by_item_id_c($v1,$v2,$v3,$v4=false,$v5=false,$v6=false,$v7=false)
	{
		global $wpdb;
		
		$item_id=$v1;
		$meta_name=$v2;
		$meta_value=$v3;
		$meta_type=$v4;
		$data_type=$v5;
		$old_meta_value=$v6;
		$set=$v7;
		
		
		if($meta_type=='')
		{
			$meta_type='longtext';
		}
		
		// Now, check meta_type from meta tables internal array so that no kind of non sanitized string will work but only the meta type strings from the internal vars :
		
	
		if(array_key_exists($meta_type,$this->internal['meta_tables']))
		{
			// All good, do nothing. This means we dont have to sanitize this field	
			
		}
		else
		{
			// Meta key wasnt found in internal list of meta tables. Return false:
			
			return false;			
		}
	
		if($data_type=='' OR $data_type==false)
		{
			// No post type (data type) is given, external or internal. We are going to assume it is external (wordpress) and it is 'post'
			$data_type='post';
		}
		
		$meta_value_type = $this->internal['meta_tables'][$meta_type]['type'];
		
		// Meta's value type needs to be decided. Wordpress recognizes int, float, string, we are going to treat datetime as string as well.
		if(	
			$meta_value_type == 'int' OR 
			$meta_value_type == 'bigint' OR
			$meta_value_type == 'tinyint' OR
			$meta_value_type == 'smallint'
		)
		{
			$value_type='int';	
			
		}
		elseif(	$meta_value_type == 'float')
		{
			$value_type='float';	
			
		}
		else
		{
			$value_type='string';			
		}
		// First prepare the sql to check if meta exists
		
		
		$sql = "SELECT ".$meta_type."_id FROM ".$wpdb->prefix.$this->internal['id']."_".$meta_type." WHERE ";
		
		$where_statement = $meta_type."_parent = %d
		AND 
		".$meta_type."_data_type = '%s'
		AND 
		".$meta_type."_name = '%s'
		";
		// Decide which kind of replacer to use for value
		if($value_type=='int')
		{
			$value_replacer = "%d";
		}
		elseif($value_type=='float')
		{
			$value_replacer = "%f";				
		}
		else
		{
			// Treat it as string :
			$value_replacer = "'%s'";	
			
		}		
		// We do not add set into query, since it is there for easy retrieval of data groups.
				
		// Now, if the value was supplied, we are going to add existence of that value as well:
		
		if($old_meta_value!='')
		{
			
			// Decide what kind of value it will be treated as:
			
			$where_statement .= " 
			AND 
			".$meta_type."_value = ".$value_replacer."
			";

		}
	
		// Sql ready, now prepare with the values array:
	
		$sql.=$where_statement;
		
		$values_array=array(
		
			$item_id,
			$data_type,
			$meta_name
		
		);		
		
		
		if($old_meta_value!='')
		{
			array_push($values_array,$old_meta_value);
		
		}
		
		$prepared_sql = $wpdb->prepare(
		
						$sql,
						
						$values_array
			  
		);

		// Now get the result : 
		
		$results = $wpdb->get_results($prepared_sql,ARRAY_A);
		
		if(count($results)>0)
		{
			// Meta exists. Update.
		
			foreach($results as $key => $value)
			{
				$meta_id = $results[$key][$meta_type.'_id'];
				
				$result = $this->update_meta_by_meta_id($meta_id,$meta_value,$meta_type);
							
			}
			return $result;
		}
		else
		{
			// Meta doesnt exist. add.
		

			return $this->add_meta($item_id, $meta_name, $meta_value,$meta_type,$data_type, $old_meta_value, $set);	
			
		}
				
	}
	public function date_compare_c($v1,$v2,$v3=false)
	{
		$a = $v1;
		$b = $v2;
		$var_name = $v3;
		if($var_name == '' OR !$var_name)
		{
			$var_name = 'post_modified';
		}
		
		$t1 = strtotime($a[$var_name]);
		$t2 = strtotime($b[$var_name]);
		
		return $t2 - $t1;		

	}	
	public function save_user_last_login_c( $v1, $v2 ) 
	{
		$user = $v2;
		
		update_user_meta( $user->ID, $this->internal['prefix'].'last_login', time() );

	}
	public function save_user_last_seen_c() 
	{
		$user = wp_get_current_user();

		update_user_meta( $user->ID, $this->internal['prefix'].'user_last_seen', time() );

	}
	public function dud_c($v1)
	{

		
	}
	public function is_user_wp_admin_c($v1)
	{
		$user_id = $v1;

		
		return is_super_admin($user_id);
		
	}
	public function update_settings_c($v1)
	{
		
		$new_options=$v1;
		
		$this->opt = array_replace_recursive(
			
			$this->opt,
				
			$new_options
			
		);
		
		update_option($this->internal['prefix'].'options' ,$this->opt);

		// Load options from db
		$this->opt=$this->load_options();
		
	}	
	

}

require('plugin/plugin.php');

require('plugin/includes/direct_includes.php');

require('plugin/includes/widgets.php');


?>