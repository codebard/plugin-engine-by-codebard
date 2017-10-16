<?php


?>

<div class="<?php echo $this->internal['prefix'];?>settings">

	<div style="font-size:175%;font-weight:bold;margin-top:30px;display:inline-table;width:100%;">Plugin Engine by <a href="http://codebard.com" target="_blank"><img src="<?php echo $this->internal['plugin_url']; ?>images/codebard_very_small.png"></a> - This is a welcome screen after setup - customize this!</div>

	
	<div style="font-size:150%;font-weight:bold;margin-top:30px;margin-bottom:15px;display:inline-table;width:100%;">This plugin engine enables you with:</div>
	<b>
	<ul>
		<li>Fully OOP plugin engine that separates core plugin functions and plugin functions</li>
		<li>All actions/functions are hookable with actions and filters for input and output variables</li>
		<li>Many default needs like WordPress admin page setup, init hooks etc already handled</li>
		<li>Built in framework for publishing premium addons for your free plugins</li>
		<li>Multi Language</li>
		<li>Languages editable from Admin</li>
		<li>Setup Wizard that guides users through setup</li>
		<li>Ready made settings/options system</li>
		<li>Optional Customizable custom tables for plugin data</li>
		<li>Optional Customizable custom meta tables for plugin data - indexed and differentiated meta values - int, decimal, longtext</li>
		<li>Built in Stylesheets, javascript queueing</li>
		<li>Built in update checking and nagging</li>
		<li>Built in logging system that tracks everything that happens inside the plugin</li>
		<li>In-built Templating System</li>
		<li>Routing system for modifying content</li>
		<li>Built in ready Widgets</li>
		<li>Frontend and Admin Notification system</li>
		<li>In built security system that checks requested actions/functions and imposes limits</li>
		<li>Full fledged uninstall system</li>
	<ul>
		</b>
	<h2>Below is some setting or initial info that you can require your users to enter upon setup</h2>
	<form method="post" action="<?php echo $this->internal['admin_url'].'admin.php?page=settings_'.$this->internal['id']; ?>">
	
		<input type="text" style="width : 500px;font-size:150%;" name="opt[quickstart][site_account]" value="Enter Value">
		<input type="submit" style="font-size:150%;" value="	Save!	">


	<input type="hidden" name="<?php echo $this->internal['id'];?>_action" value="setup_wizard">
	<input type="hidden" name="setup_stage" value="1">
	</form>

	<div style="font-size:125%;font-weight:bold;margin-top:30px;margin-bottom:15px;display:inline-table;width:100%;">You can link a guide to on a web page to help users do something or give some info or lead them to a manual, <a href="http://codebard.com/plugin-engine" target="_blank">click here to read the guide</a> - its easy!</div>

		
	
<?php


?>