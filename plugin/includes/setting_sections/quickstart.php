<?php

$tab=$_REQUEST[$this->internal['prefix'].'tab'];


echo $this->do_admin_settings_form_header($tab);

		if(isset($_REQUEST[$this->internal['prefix'].'tab']))
		{
			
			$tab=$_REQUEST[$this->internal['prefix'].'tab'];
		}


		if(isset($this->opt[$tab]['open_new_window']) AND $this->opt[$tab]['open_new_window']=='yes')
		{
		
			$open_new_window_checked_yes=" CHECKED";
		
		}
		else
		{
			$open_new_window_checked_no=" CHECKED";	
		}	

		
		
?>
			<h3>This is the option that plugin asked during setup</h3>
			This is a random setting explanation</b>.<br><br>
			<input type="text" style="width : 500px" name="opt[<?php echo $tab; ?>][site_account]" value="<?php echo $this->opt[$tab]['site_account']; ?>">
			
			
			
			
			<h3>Open pages in new window?</h3>
			This is another random option example. All of these text can be made multi lingual. Just put the string in relevant language file in languages folder and echo the var here.
			
			<br><br>
			Yes <input type="radio" name="opt[<?php echo $tab; ?>][open_new_window]" value="yes"<?php echo $open_new_window_checked_yes; ?>>
			No <input type="radio" name="opt[<?php echo $tab; ?>][open_new_window]" value="no"<?php echo $open_new_window_checked_no; ?>>
			<br><br>		
			
		

<?php


$this->do_setting_section_additional_settings($tab);

echo $this->do_admin_settings_form_footer($tab);

?>