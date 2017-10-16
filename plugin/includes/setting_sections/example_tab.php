<?php

$tab=$_REQUEST[$this->internal['prefix'].'tab'];


echo $this->do_admin_settings_form_header($tab);

		
		
?>

<h2>Add admin tabs and relevant settings pages by adding tabs to plugin/includes/default_internal_vars.php's 'admin_tabs' array. </h2>

<h2>Add the relevant tab page to plugin/includes/setting_sections folder with tab name. Like, example_tab.php for example_tab</h2>
		

<?php


$this->do_setting_section_additional_settings($tab);

echo $this->do_admin_settings_form_footer($tab);

?>