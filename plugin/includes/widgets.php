<?php


class PLUGINPREFIX_sidebar_user_widget extends WP_Widget {
 
 
    /** constructor -- name this the same as the class above */
    function PLUGINPREFIX_sidebar_user_widget() {
		global $PLUGINPREFIX;
		
		// Load language from db
		$PLUGINPREFIX->lang = $PLUGINPREFIX->load_language();
		
        parent::__construct(false, $name = $PLUGINPREFIX->lang['sidebar_user_widget_name']);	
		
		
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {	
	
		
		global $PLUGINPREFIX;
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
		
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
							
								<?php 
									
								
								?>
									<div class="PLUGINPREFIX_sidebar_user_widget_content">
									
									
										<button onclick="window.location.href='<?php echo get_permalink($PLUGINPREFIX->opt['pages']['support_desk_page']) ?>';"><?php echo $PLUGINPREFIX->lang['sidebar_user_widget_help_desk_button_label'] ?></button>
										

										
									
									</div>
								
								<?php 
									
								?>
							
     
						
              <?php echo $after_widget; ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
		global $PLUGINPREFIX;
		$instance = wp_parse_args( (array) $instance, array( 'title' => $PLUGINPREFIX->lang['sidebar_user_widget_title'] ) );
        $title 		= esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>	
		
        <?php 
    }
	


}



function PLUGINPREFIX_register_widgets()
{

	register_widget( 'PLUGINPREFIX_sidebar_user_widget' );


}

add_action('widgets_init', 'PLUGINPREFIX_register_widgets');


?>