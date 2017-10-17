<?php


class PLUGINPREFIX_plugin extends PLUGINPREFIX_core
{
	public function plugin_construct()
	{

		add_action('init', array(&$this, 'init'));
		
		add_action('upgrader_process_complete', array(&$this, 'upgrade'));
		
		register_activation_hook( __FILE__, array(&$this,'activate' ));
		
		register_deactivation_hook(__FILE__, array(&$this,'deactivate'));
		
		if(is_admin())
		{
			add_action('init', array(&$this, 'admin_init'));
		}
		else
		{
			add_action('init', array(&$this, 'frontend_init'),99);
		}
		
		add_action('activated_plugin',array(&$this,'check_redirect_to_setup_wizard'),99);		
		
		
	}
	public function add_admin_menus_p()
	{
		
		add_menu_page( 'CB Engine', 'CB Engine', 'administrator', 'settings_'.$this->internal['id'], array(&$this,'do_settings_pages'), $this->internal['plugin_url'].'images/admin_menu_icon.png', 86 );
		
	}
	public function admin_init_p()
	{
		
		// Updates are important - Add update nag if update exist
		add_filter( 'pre_set_site_transient_update_plugins', array(&$this, 'check_for_update' ),99 );
		
		
		// Do setup wizard if it was not done
		if(!isset($this->opt['setup_done']))
		{
			add_action($this->internal['prefix'].'action_before_do_settings_pages',array(&$this,'do_setup_wizard'),99,1);
		}		
	}
	public function frontend_init_p()
	{
		
	}
	public function init_p()
	{
		
		// Below function checks the request in any way necessary, and queues any action/filter depending on request. This way, we avoid filtering content or putting any actions in pages or operations not relevant to plugin
				
		add_action( 'wp', array(&$this, 'route_request'));
		
		add_action( 'template_redirect', array(&$this, 'template_redirections'));
		
		$upload_dir = wp_upload_dir();
		
		$this->internal['attachments_dir'] = $upload_dir['basedir'] . '/'.$this->internal['prefix'].'_attachments/';
	
		$this->internal['attachment_url'] =  $upload_dir['baseurl'] . '/'.$this->internal['prefix'].'attachments/';
		
		// Get relative attachment dir/url :
		
		$this->internal['attachment_relative_url']=substr(wp_make_link_relative($upload_dir['baseurl']),1).'/'.$this->internal['prefix'].'attachments/';
		
		$this->internal['plugin_slug'] =  plugin_basename( __FILE__ );
		
		$this->internal['plugin_update_url'] =  wp_nonce_url(get_admin_url().'update.php?action=upgrade-plugin&plugin='.$this->internal['plugin_id'].'/index.php','upgrade-plugin_'.$this->internal['plugin_id'].'/index.php');
		

		add_action( 'show_user_profile', array(&$this, 'add_custom_user_field') );
		add_action( 'edit_user_profile', array(&$this, 'add_custom_user_field') );

		add_action( 'personal_options_update', array(&$this, 'save_custom_user_field') );
		add_action( 'edit_user_profile_update', array(&$this, 'save_custom_user_field') );			
		
		
	}
	public function load_options_p()
	{
		// Initialize and modify plugin related variables
		

		return $this->internal['core_return'];
		
	}

	public function title_filters_p($title)
	{
		global $post;

		
		return $title;
	}
	public function content_filters_p($wordpress_content)
	{
		global $post;
	
		// Do stuff here	
	
		return $wordpress_content;
	}
	public function template_redirections_p($link)
	{
		global $post;

		return $link;
	}
	public function setup_languages_p()
	{
		// Here we do plugin specific language procedures. 
		
		// Set up the custom post type and its taxonomy slug into options:
		
		$current_lang=get_option($this->internal['prefix'].'lang_'.$this->opt['lang']);
		
		// Get current options
		
		$current_options=get_option($this->internal['prefix'].'options');
		
		$current_options['ticket_post_type_slug']=$current_lang['ticket_post_type_slug'];
		$current_options['ticket_category_slug']=$current_lang['ticket_post_type_category_slug'];
		
		// Update options :
		
		update_option($this->internal['prefix'].'options',$current_options);
		
		// Set current options the same as well :
		
		$this->opt=$current_options;
		
	}
	public function activate_p()
	{
	
		
	}
	public function check_redirect_to_setup_wizard_p($v1)
	{
		$activated_plugin =  $v1;

		if($activated_plugin!=$this->internal['plugin_slug'])
		{
			return;
			
		}		
		// If setup was not done, redirect to wizard
		if(!$this->opt['setup_done'])
		{
	
			wp_redirect($this->internal['admin_url'].'admin.php?page=settings_'.$this->internal['id']);
			exit;	
		}		
		
	}
	public function enqueue_frontend_styles_p()
	{
		
		wp_enqueue_style( $this->internal['id'].'-css-main', $this->internal['template_url'].'/'.$this->opt['template'].'/style.css' );
	}
	public function enqueue_admin_styles_p()
	{
	
		$current_screen=get_current_screen();

		if($current_screen->base=='toplevel_page_settings_'.$this->internal['id'])
		{
			wp_enqueue_style( $this->internal['id'].'-css-admin', $this->internal['plugin_url'].'plugin/includes/css/admin.css' );
			
		}
	}
	public function enqueue_frontend_scripts_p()
	{
	
	
	
		
	}	
	public function enqueue_admin_scripts_p()
	{
	
		// This will enqueue the Media Uploader script
		wp_enqueue_media();	
		wp_enqueue_script( $this->internal['id'].'-js-admin', $this->internal['plugin_url'].'plugin/includes/scripts/admin.js' );	
		
		
	}	
	public function route_request_p()
	{
		global $post;
		
		$current_term = get_queried_object();
		$current_user = wp_get_current_user();
		
		// Placeholder queuer
		
		// Support desk main page. Queue content filter or any necessary function
		
		$this->queue_content_filters();

		
		
	}
	public function queue_title_filters_p()
	{
		// This function is a wrapper for queueing content filter
		
		if(!isset($this->internal['title_filter_queued']))
		{
			$this->internal['title_filter_queued']=true;
			add_filter('the_title', array(&$this, 'title_filters'));		
		}
	}
	public function queue_content_filters_p()
	{
		// This function is a wrapper for queueing content filter
		
		if(!isset($this->internal['content_filter_queued']))
		{
			$this->internal['content_filter_queued']=true;
			add_filter('the_content', array(&$this, 'content_filters'));		
		}
	}
	public function choose_language_p($v1)
	{
		
		// Check if language was successfully changed and hook to create pages if necessary:
		if($this->internal['core_return'])
		{
			add_action( 'admin_init', array(&$this, 'check_create_pages'));			
		}
	}
	public function check_for_update($checked_data) 
	{
			global $wp_version, $plugin_version, $plugin_base;
		
			if ( empty( $checked_data->checked ) ) {
				return $checked_data;
			}

			if(isset($checked_data->response[$this->internal['plugin_id'].'/index.php']) AND version_compare( $this->internal['version'], $checked_data->response[$this->internal['plugin_id'].'/index.php']->new_version, '<' ))
			{
				// place update link into update lang string :
				
				$update_link = $this->process_vars_to_template(array('plugin_update_url'=>$this->internal['plugin_update_url']),$this->lang['update_available']);

				$this->queue_notice($update_link,'info','update_available','perma',true);		
			}
			return $checked_data;
		
	}	
	public function upgrade_p($v1,$v2)
	{
		
		$upgrader_object = $v1;
		$options = $v2;
		
		if($upgrader_object->result['destination_name']!=$this->internal['plugin_id'])
		{
			return;			
		}
		
		if(!current_user_can('manage_options'))
		{
			$this->queue_notice($this->lang['error_operation_failed_no_permission'],'error','error_operation_failed_no_permission','admin');
			return false;
		}
		
		// Check if woocommerce is installed to give our message
		$this->check_woocommerce_exists();
		
		
		if($this->internal['woocommerce_installed'] AND $this->check_addon_exists('woocommerce_integration')=='notinstalled')
		{
			$this->queue_notice($this->lang['woocommerce_addon_available'],'info','update_available','perma',true);		
		}		
	
		$this->dismiss_admin_notice(array('notice_id'=>'update_available','notice_type'=>'info'));
		
	}
	public function do_setup_wizard_p()
	{
		// Here we do and process setup wizard if it is not done:
		 
		$this->internal['setup_is_being_done']=true;
		
		// Check if this is an pre-new engine install which has options, and if so only show them the welcome screen

		
		if((isset($_REQUEST['setup_stage']) AND $_REQUEST['setup_stage']=='') OR !isset($_REQUEST['setup_stage']))
		{
			
			require($this->internal['plugin_path'].'plugin/includes/setup_1.php');
			
		}
		else
		{
			if(isset($_REQUEST['setup_stage'])=='1')
			{
				require($this->internal['plugin_path'].'plugin/includes/setup_2.php');
			
				$this->opt['setup_done'] = true;
				update_option($this->internal['prefix'].'options',$this->opt);	
			}
			
		}

	}
	public function display_addons_p()
	{
		// This function displays addons from internal vars
		echo '<div class="cb_addons_list">';
		foreach($this->internal['addons'] as $key => $value)
		{
			echo $this->display_addon($key);
			
		}
		echo '</div>';
		
	}
	public function display_addon_p($v1)
	{
		$addon_key=$v1;
		
		$addon=$this->internal['addons'][$addon_key];
		
		// This function displays a particular addon
	
		echo '<div class="cb_addon_listing">';	
		echo '<div class="cb_addon_icon"><a href="'.$this->internal['addons'][$addon_key]['link'].'" target="_blank"><img src="'.$this->internal['plugin_url'].'images/'.$addon['icon'].'" /></a></div>';echo '<div class="cb_addon_title"><a href="'.$this->internal['addons'][$addon_key]['link'].'" target="_blank">'.$this->lang['addon_'.$addon_key.'_title'].'</a></div>';		
		echo '<div class="cb_addon_status">'.$this->check_addon_status($addon_key).'</div>';
		echo '</div>';			
		
	}
	public function wrapper_check_addon_license_p($v1)
	{
		// Wrapper solely for the purpose of letting addons check their licenses
		return;
	}
	public function check_addon_status_p($v1)
	{
		// Checks addon status, license, and provides links if inecessary
		
		$addon_key = $v1;
		
		// Check if addon is active:
		
		if ( is_plugin_active( $this->internal['addons'][$addon_key]['slug'] ) ) 
		{
			//plugin is active
			
			echo $this->wrapper_check_addon_license($addon_key);
			
		}
		else
		{
			// Check if plugin exists:
			
			if(file_exists(WP_PLUGIN_DIR.'/'.$this->internal['addons'][$addon_key1]['slug']))
			{
				
				return $this->lang['inactive']; 
				
			}
			else			
			{
				// Not installed. 
				return '<a href="'.$this->internal['addons'][$addon_key]['link'].'" class="cb_get_addon_link" target="_blank">'.$this->lang['get_this_addon'].'</a>';
				
			}
			
		}
		
		
	}
	public function check_addon_exists_p($v1)
	{
		// Checks addon status, license, and provides links if inecessary
		
		$addon_key = $v1;
		
		// Check if addon is active:
		
		if ( is_plugin_active( $this->internal['addons'][$addon_key]['slug'] ) ) 
		{
			//plugin is active
			
			return 'active';
			
		}
		else
		{
			// Check if plugin exists:
			
			if(file_exists(WP_PLUGIN_DIR.'/'.$this->internal['addons'][$addon_key1]['slug']))
			{
				
				return 'notinstalled';
				
			}
			else			
			{
				// Not installed. 
				return 'notinstalled';
				
			}
			
		}
		
		
	}

}


$PLUGINPREFIX = PLUGINPREFIX_plugin::get_instance();

function PLUGINPREFIX_get()
{

	// This function allows any plugin to easily retieve this plugin object
	return PLUGINPREFIX_plugin::get_instance();

}

?>