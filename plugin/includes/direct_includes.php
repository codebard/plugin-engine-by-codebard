<?php

/* This include is for functions and directives that need to be loaded outside class structure */


/* Runs when plugin is activated */
register_activation_hook($PLUGINPREFIX->internal['plugin_path'].'index.php',array(&$PLUGINPREFIX,'activate')); 

/* Runs on plugin deactivation*/
register_deactivation_hook( $PLUGINPREFIX->internal['plugin_path'].'index.php', array(&$PLUGINPREFIX,'deactivate'));




?>