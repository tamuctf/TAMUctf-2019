<?php 

$generalSettings = self::getSettings("general");
$settingsOutput = new UniteSettingsRevProductRev();
$settingsOutput->init($generalSettings);

?>


<div id="dialog_general_settings" title="<?php _e("General Settings",REVSLIDER_TEXTDOMAIN)?>" style="display:none;">
	
	<?php
		$settingsOutput->draw("form_general_settings",true);	
	?>
	<br>
	
	<a id="button_save_general_settings" class="button-primary"><?php _e("Update",REVSLIDER_TEXTDOMAIN)?></a>
	<span id="loader_general_settings" class="loader_round mleft_10"></span>
	
	<!-- 
		&nbsp;
		<a class="button-primary"><?php _e("Close",REVSLIDER_TEXTDOMAIN)?></a>
	 -->
	 
</div>
