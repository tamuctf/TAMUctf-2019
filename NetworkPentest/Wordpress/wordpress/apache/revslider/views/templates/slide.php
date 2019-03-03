
	<!--  load good font -->
	<?php

		if($loadGoogleFont == "true"){
			$googleFont = $slider->getParam("google_font","");
			if(!empty($googleFont))
				echo "<link href='http://fonts.googleapis.com/css?family=$googleFont' rel='stylesheet' id='google-font-css' type='text/css' media='all' />";
		}

	?>

	<div class="wrap settings_wrap">
		<div class="clear_both"></div> 
	
		<div class="title_line">
			<div id="icon-options-general" class="icon32"></div>
			<h2>
				<?php _e("Edit Slide",REVSLIDER_TEXTDOMAIN)?>  <?php echo $slideOrder?>, title: <?php echo $slideTitle?>			
			</h2>
			
			<a href="<?php echo GlobalsRevSlider::LINK_HELP_SLIDE?>" class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php _e("Help",REVSLIDER_TEXTDOMAIN)?></a>			
			
		</div>


		<div id="slide_selector" class="slide_selector">
			<ul class="list_slide_links">
			<?php
			
			foreach($arrSlideNames as $slidelistID=>$slide):
			
				$slideName = $slide["name"];
				$title = $slide["title"];
				$arrChildrenIDs = $slide["arrChildrenIDs"];
				
				$class = "tipsy_enabled_top";
				$titleclass = "";
				$urlEditSlide = self::getViewUrl(RevSliderAdmin::VIEW_SLIDE,"id=$slidelistID");
				if($slideID == $slidelistID || in_array($slideID, $arrChildrenIDs)){
					$class .= " selected";
					$titleclass = " slide_title";
					$urlEditSlide = "javascript:void(0)";
				}
				
				$addParams = "class='{$class}'";
				$slideName = str_replace("'", "", $slideName);

			?>
				 <li id="slidelist_item_<?php echo $slidelistID?>">
				 	<a href="<?php echo $urlEditSlide?>" title='<?php echo $slideName?>' <?php echo $addParams?>><span class="nowrap<?php echo $titleclass?>"><?php echo $title?></span></a>
				 </li>
			<?php endforeach;?>
				 <li>
				 	<a id="link_add_slide" href="javascript:void(0)" class="add_slide" <?php echo $addParams?>><span class="nowrap"><?php _e("Add Slide",REVSLIDER_TEXTDOMAIN)?></span></a>
				 </li>
				 <li>
				 	<div id="loader_add_slide" class="loader_round" style="display:none"></div>
				 </li>
			</ul>
		</div>
		
		<div class="clear"></div>
		<hr class="tabdivider">
		
		<?php if($wpmlActive == true && count($arrChildLangs) > 1):?>
		<div class="clear"></div>
		<div class="divide20"></div>
		<div class="slide_langs_selector">
			<span class="float_left ptop_15"> <?php _e("Choose slide language",REVSLIDER_TEXTDOMAIN)?>: </span> 
			<ul class="list_slide_view_icons float_left">
				<?php foreach($arrChildLangs as $arrLang):
						$childSlideID = $arrLang["slideid"];
						$lang = $arrLang["lang"];
						$urlFlag = UniteWpmlRev::getFlagUrl($lang);
						$langTitle = UniteWpmlRev::getLangTitle($lang);
						
						$class = "";
						$urlEditSlide = self::getViewUrl(RevSliderAdmin::VIEW_SLIDE,"id=$childSlideID");							
						
						if($childSlideID == $slideID){
							$class = "lang-selected";
							$urlEditSlide = "javascript:void(0)";
						}						
				?>
				<li>
					<a href="<?php echo $urlEditSlide?>" class="tipsy_enabled_top <?php echo $class?>" title="<?php echo $langTitle?>">
						<img class="icon_slide_lang" src="<?php echo $urlFlag?>" >
					</a>
				</li>
				<?php endforeach?>
			</ul>
			<span class="float_left ptop_15 pleft_20"> <?php _e("All the language related operations are from",REVSLIDER_TEXTDOMAIN)?> <a href="<?php echo $closeUrl?>"><?php _e("slides view",REVSLIDER_TEXTDOMAIN)?></a>. </span> 			
		</div>						
		<div class="clear"></div>
		<?php else:?>
			
			<div class="divide20"></div>
		
		<?php endif?>
		
		<div id="slide_params_holder" class="postbox unite-postbox mw960">
			<h3 class="box-closed tp-accordion"><span class="postbox-arrow2">-</span><span><?php _e("General Slide Settings",REVSLIDER_TEXTDOMAIN) ?></span></h3>
			<div class="toggled-content">
				<form name="form_slide_params" id="form_slide_params">
				<?php
					$settingsSlideOutput->draw("form_slide_params",false);
				?>
					<input type="hidden" id="image_url" name="image_url" value="<?php echo $imageUrl?>" />
					<input type="hidden" id="image_id" name="image_id" value="<?php echo $imageID?>" />
				</form>
			</div>
		</div>


		<div id="jqueryui_error_message" class="unite_error_message" style="display:none;">
				<b>Warning!!! </b>The jquery ui javascript include that is loaded by some of the plugins are custom made and not contain needed components like 'autocomplete' or 'draggable' function.
				Without those functions the editor may not work correctly. Please remove those custom jquery ui includes in order the editor will work correctly.
		 </div>

		<?php require self::getPathTemplate("edit_layers");?>
		

		
		<div class="slide_update_button_wrapper">
			<a href="javascript:void(0)" id="button_save_slide" class="orangebutton"><?php _e("Update Slide",REVSLIDER_TEXTDOMAIN)?></a>
			<div id="loader_update" class="loader_round" style="display:none;"><?php _e("updating",REVSLIDER_TEXTDOMAIN)?>...</div>
			<div id="update_slide_success" class="success_message" class="display:none;"></div>
		</div>
		<a id="button_close_slide" href="<?php echo $closeUrl?>" class="button-primary"><?php _e("Close",REVSLIDER_TEXTDOMAIN)?></a>

	</div>

	<div class="vert_sap"></div>

	<?php require self::getPathTemplate("dialog_preview_slide");?>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			
			RevSliderAdmin.initEditSlideView(<?php echo $slideID?>,<?php echo $sliderID?>);
		});
	</script>


