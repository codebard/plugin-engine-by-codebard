<?php


// Save settings we received from stage 1

$this->save_settings_during_setup($_REQUEST);

?>

<div class="<?php echo $this->internal['prefix'];?>settings">



	<div style="font-size:175%;font-weight:bold;margin-top:30px;display:inline-table;width:100%;">Great! This is the second stage of the setup wizard! You can add more stages. Your Plugin is now ready! Drop a link to your webpage here. <a href="http://codebard.com" target="_blank"><img src="<?php echo $this->internal['plugin_url']; ?>images/codebard_very_small.png"></a></div>

	
	<div style="font-size:150%;font-weight:bold;margin-top:30px;margin-bottom:15px;display:inline-table;width:100%;">Now if you wish, you can: 
<br><br> <a href="<?php echo $this->internal['admin_url']; ?>widgets.php" target="_blank">Guide users to necessary page A</a>
<br><br> <a href="<?php echo $this->internal['admin_url'].'admin.php?page=settings_'.$this->internal['id']; ?>" target="_blank">Guide User to settings</a>
<br><br> <a href="<?php echo $this->internal['admin_url'].'admin.php?page=settings_'.$this->internal['id']; ?>&<?php echo $this->internal['prefix'];?>tab=extras" target="_blank">Guide user to check out your extras</a></div>

		
	
<?php


?>