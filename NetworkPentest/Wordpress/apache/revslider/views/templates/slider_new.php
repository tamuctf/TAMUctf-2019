

	<div class="wrap settings_wrap">
		<div class="clear_both"></div> 
	
			<div class="title_line">
				<div id="icon-options-general" class="icon32"></div>
				<h2><?php _e("New Slider",REVSLIDER_TEXTDOMAIN)?></h2>
				
				<a href="<?php echo GlobalsRevSlider::LINK_HELP_SLIDER?>" class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php _e("Help",REVSLIDER_TEXTDOMAIN)?></a>			
				
			</div>
		
			<div class="settings_panel">
			
				<div class="settings_panel_left">
				
					<?php $settingsSliderMain->draw("form_slider_main",true)?>
					
					<div class="vert_sap_medium"></div>
					
					<a id="button_save_slider" class='button-primary' href='javascript:void(0)' ><?php _e("Create Slider",REVSLIDER_TEXTDOMAIN)?></a>
					
					<span class="hor_sap"></span>
					
					<a id="button_cancel_save_slider" class='button-primary' href='<?php echo self::getViewUrl("sliders") ?>' ><?php _e("Close",REVSLIDER_TEXTDOMAIN)?> </a>
					
				</div>
				<div class="settings_panel_right">
					<?php $settingsSliderParams->draw("form_slider_params",true); ?>
				</div>
				
				<div class="clear"></div>				
			</div>
			
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			
			RevSliderAdmin.initAddSliderView();
		});
	</script>
	
