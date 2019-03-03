	<input type="hidden" id="sliderid" value="<?php echo $sliderID?>"></input>
	
	<div class="wrap settings_wrap">
		<div class="clear_both"></div> 
		
			<div class="title_line">
				<div id="icon-options-general" class="icon32"></div>
				<h2><?php _e("Edit Slider",REVSLIDER_TEXTDOMAIN)?></h2>
				
				<a href="<?php echo GlobalsRevSlider::LINK_HELP_SLIDER?>" class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php _e("Help",REVSLIDER_TEXTDOMAIN)?></a>			
				
			</div>
		
			<div class="settings_panel">
			
				<div class="settings_panel_left">
					
					<div id="main_dlier_settings_wrapper" class="postbox unite-postbox ">
					  <h3 class="box-closed"><span><?php _e("Main Slider Settings",REVSLIDER_TEXTDOMAIN) ?></span></h3>
					  <div class="p10">

		
							<?php $settingsSliderMain->draw("form_slider_main",true)?>
							
							<div class="divide20"></div>
							
							<div id="slider_update_button_wrapper" class="slider_update_button_wrapper" style="width:120px">
								<a class='orangebutton' href='javascript:void(0)' id="button_save_slider" ><?php _e("Update Slider",REVSLIDER_TEXTDOMAIN)?></a>
								<div id="loader_update" class="loader_round" style="display:none;"><?php _e("updating...",REVSLIDER_TEXTDOMAIN)?> </div>
								<div id="update_slider_success" class="success_message" class="display:none;"></div>
							</div>
							
							<a id="button_delete_slider" class='button-primary' href='javascript:void(0)' id="button_delete_slider" ><?php _e("Delete Slider",REVSLIDER_TEXTDOMAIN)?></a>
							
							<a id="button_close_slider_edit" class='button-primary' href='<?php echo self::getViewUrl("sliders") ?>' ><?php _e("Close",REVSLIDER_TEXTDOMAIN)?></a>
							
							<a href="<?php echo $linksEditSlides?>" class="greenbutton" id="link_edit_slides"><?php _e("Edit Slides",REVSLIDER_TEXTDOMAIN)?> </a>
												
							<a href="javascript:void(0)" class="button-secondary prpos" id="button_preview_slider" title="Preview Slider"><?php _e("Preview Slider",REVSLIDER_TEXTDOMAIN)?></a>
							
							<div class="clear"></div>
							<div class="divide20"></div>
					  </div>
					</div>
					 
					<?php require self::getPathTemplate("slider_toolbox"); ?>
					<?php require self::getPathTemplate("slider_api"); ?>
					
				</div>
				<div class="settings_panel_right">
					<?php $settingsSliderParams->draw("form_slider_params",true); ?>
				</div>
				
				<div class="clear"></div>
				
			</div>

	</div>

	<?php require self::getPathTemplate("dialog_preview_slider");?>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			
			RevSliderAdmin.initEditSliderView();
		});
	</script>
	
