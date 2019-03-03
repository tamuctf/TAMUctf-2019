	
	<div class="edit_slide_wrapper">
		
		<div class="editor_buttons_wrapper  postbox unite-postbox mw960">
			<h3 class="box-closed tp-accordion"><span class="postbox-arrow2">-</span><span><?php _e("Slide Image and Layers",REVSLIDER_TEXTDOMAIN) ?></span></h3>
			<div class="toggled-content">
					<div class="inner_wrapper p10 pb0 pt0 boxsized">
						<div class="editor_buttons_wrapper_top">
							<input type="radio" name="radio_bgtype" data-bgtype="image" id="radio_back_image" <?php if($bgType == "image") echo 'checked="checked"'?> >
							<label for="radio_back_image"><?php _e("Image BG",REVSLIDER_TEXTDOMAIN)?></label>
							<a href="javascript:void(0)" id="button_change_image" class="button-primary margin_right10 <?php if($bgType != "image") echo "button-disabled" ?>" style="margin-bottom:5px">Change Image</a>
							
							<span class="hor_sap"></span>
							
							<input type="radio" name="radio_bgtype" data-bgtype="trans" id="radio_back_trans" <?php if($bgType == "trans") echo 'checked="checked"'?>>
							<label for="radio_back_trans"><?php _e("Transparent BG",REVSLIDER_TEXTDOMAIN)?></label>
							
							<span class="hor_sap"></span>
							
							<input type="radio" name="radio_bgtype" data-bgtype="solid" id="radio_back_solid" <?php if($bgType == "solid") echo 'checked="solid"'?>>
							<label for="radio_back_solid"><?php _e("Solid BG",REVSLIDER_TEXTDOMAIN)?></label>
							<input type="text" name="bg_color" id="slide_bg_color" <?php echo $bgSolidPickerProps?> value="<?php echo $slideBGColor?>">
							
							
							
						</div>
						<div class="clear"></div>
						
						<div class="editor_buttons_wrapper_bottom">
							<a href="javascript:void(0)" id="button_add_layer"       class="button-secondary margin_top2 ml0 mr10 mb10"><?php _e("Add Layer",REVSLIDER_TEXTDOMAIN)?></a>
							<a href="javascript:void(0)" id="button_add_layer_image" class="button-secondary margin_top2 ml0 mr10 mb10"><?php _e("Add Layer: Image",REVSLIDER_TEXTDOMAIN)?> </a>
							<a href="javascript:void(0)" id="button_add_layer_video" class="button-secondary margin_top2 ml0 mr10 mb10"><?php _e("Add Layer: Video",REVSLIDER_TEXTDOMAIN)?> </a>
													
							<a href="javascript:void(0)" id="button_delete_layer"    class="button-secondary margin_top2 ml0 mr10 mb10 button-disabled"><?php _e("Delete Layer",REVSLIDER_TEXTDOMAIN)?></a>
							<a href="javascript:void(0)" id="button_delete_all"      class="button-secondary margin_top2 ml0 mr10 mb10 button-disabled"><?php _e("Delete All Layers",REVSLIDER_TEXTDOMAIN)?> </a>
													
							<a href="javascript:void(0)" id="button_duplicate_layer" class="button-secondary margin_top2 ml0 mr10 mb10 button-disabled"><?php _e("Duplicate Layer")?></a>
							
							<a href="javascript:void(0)" id="button_preview_slide"   class="button-secondary ml0 mr10 mb10" title="Preview Slide">Preview Slide</a>
						</div>
					</div>
					
					<div class="clear"></div>
			</div>
			
		</div>
		
		<div class="clear"></div>
		
		<div class="divide10"></div>
				
		<div id="divLayers" class="<?php echo $divLayersClass?>" style="<?php echo $style?>"></div>
		<div class="clear"></div>
		<div class="vert_sap"></div>
		
		
		
		<div class="layer_props_wrapper">
		
		<!-----  Left Layers Form ------>
		
			<div class="edit_layers_left">
		
				<form name="form_layers" id="form_layers">
					<script type='text/javascript'>
						g_settingsObj['form_layers'] = {}
					</script>
					
					<!-- THE GENERAL LAYER PARAMETERS -->
					<div class='settings_wrapper'>					
						<div class="postbox unite-postbox">
							<h3 class='no-accordion tp-accordion'><span class="postbox-arrow2">-</span>
								<span><?php _e("Layer General Parameters",REVSLIDER_TEXTDOMAIN)?> </span>
							</h3>
							<div class="toggled-content tp-closeifotheropen">
								<ul class="list_settings">
									<?php
										$s = $settingsLayerOutput;
										$s->drawSettingsByNames("layer_caption,layer_text,button_edit_video,button_change_image_source"); 
									?>
									<li style="clear:both">
										<span class="setting_text_2 text-disabled" original-title=""><?php _e("Align & Position",REVSLIDER_TEXTDOMAIN)?></span>										
										<hr>
									</li>
									<li class="align_table_wrapper">
										<table id="align_table" class="align_table table_disabled">
											<tr>
												<td><a href="javascript:void(0)" id="linkalign_left_top" data-hor="left" data-vert="top"></a></td>
												<td><a href="javascript:void(0)" id="linkalign_center_top" data-hor="center" data-vert="top"></a></td>
												<td><a href="javascript:void(0)" id="linkalign_right_top" data-hor="right" data-vert="top"></a></td>
											</tr>
											<tr>
												<td><a href="javascript:void(0)" id="linkalign_left_middle" data-hor="left" data-vert="middle"></a></td>
												<td><a href="javascript:void(0)" id="linkalign_center_middle" data-hor="center" data-vert="middle"></a></td>
												<td><a href="javascript:void(0)" id="linkalign_right_middle" data-hor="right" data-vert="middle"></a></td>
											</tr>
											<tr>
												<td><a href="javascript:void(0)" id="linkalign_left_bottom" data-hor="left" data-vert="bottom"></a></td>
												<td><a href="javascript:void(0)" id="linkalign_center_bottom" data-hor="center" data-vert="bottom"></a></td>
												<td><a href="javascript:void(0)" id="linkalign_right_bottom" data-hor="right" data-vert="bottom"></a></td>
											</tr>
										</table>
									</li>
									
									<?php 
								    	$s->drawSettingsByNames("layer_left,layer_top");
								    	$s->drawSettingsByNames("layer_align_hor,layer_align_vert");
								    ?>									
									
															
								    
								</ul>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					
					<!-- THE ANIMATION PARAMETERS -->
					<div class='settings_wrapper'>					
						<div class="postbox unite-postbox">
							<h3 class='no-accordion tp-accordion tpa-closed'><span class="postbox-arrow2">+</span>
								<span><?php _e("Layer Animation",REVSLIDER_TEXTDOMAIN)?> </span>
							</h3>
							<div class="toggled-content tp-closedatstart tp-closeifotheropen">
								<ul class="list_settings">
									
									<!--LAYER START ANIMATION -->									
									<li id="end_layer_sap" class="attribute_title" style="">
										<span class="setting_text_2 text-disabled" original-title=""><?php _e("Start Transition",REVSLIDER_TEXTDOMAIN)?></span>										
										<hr>										
									</li>									
									<?php 
								    	$s->drawSettingsByNames("layer_animation,layer_easing,layer_speed"); 
								    ?>							

									<!--LAYER END ANIMATION -->									
									<li id="end_layer_sap" class="attribute_title" style="">
										<span class="setting_text_2 text-disabled" original-title=""><?php _e("End Transition (optional)",REVSLIDER_TEXTDOMAIN)?></span>										
										<hr>										
									</li>
									<?php 
								    	$s->drawSettingsByNames("layer_endanimation,layer_endeasing,layer_endspeed,layer_endtime");
								    ?>
									
								    
								</ul>
								<div class="clear"></div>
							</div>
						</div>
					</div><!-- END OF ANIMATION PARAMETERS -->
					
					
					<!-- THE ADVANCED LAYER PARAMETERS -->
					<div class='settings_wrapper'>					
						<div class="postbox unite-postbox">
							<h3 class='no-accordion tp-accordion tpa-closed'><span class="postbox-arrow2">+</span>
								<span><?php _e("Layer Links & Advanced Params",REVSLIDER_TEXTDOMAIN)?> </span>
							</h3>
							<div class="toggled-content tp-closedatstart tp-closeifotheropen">
								
								<ul class="list_settings">
									<?php
										$s = $settingsLayerOutput;
										$s->drawSettingsByNames("layer_image_link,layer_link_open_in,layer_slide_link,layer_scrolloffset,layer_cornerleft,layer_cornerright,layer_resizeme,layer_hidden"); 
									?>
									
								</ul>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					
				</form>	
			</div>
			
		<!----- End Left Layers Form ------>
			
			<div class="edit_layers_right">
				<div class="postbox unite-postbox layer_sortbox">
					<h3 class="no-accordion">
						<span><?php _e("Layers Timing & Sorting",REVSLIDER_TEXTDOMAIN)?></span>
						<div id="button_sort_visibility" title="Hide All Layers"></div>
						<div id="button_sort_time" class="ui-state-active ui-corner-all button_sorttype"><span><?php _e("By Time",REVSLIDER_TEXTDOMAIN) ?></span></div>
						<div id="button_sort_depth" class="ui-state-hover ui-corner-all button_sorttype"><span><?php _e("By Depth",REVSLIDER_TEXTDOMAIN)?><span></div>
					</h3>			
					
					<div id="global_timeline" class="timeline">
						<div id="timeline_handle" class="timerdot"></div>
						<div id="layer_timeline" class="layertime"></div>
						<div class="mintime">0 ms</div>
						<div class="maxtime"><?php echo $slideDelay?> ms</div>
					</div>
					
					
					<div class="inside">
						<ul id="sortlist" class='sortlist'></ul>
					</div>
				</div>
			</div>
			
			<div class="clear"></div>
			
		</div>
	</div>
	
	<div id="dialog_edit_css" class="dialog_edit_file" title="Edit captions.css file" style="display:none;">
		<p>
			<textarea id="textarea_edit" rows="20" cols="100"></textarea>
		</p>
		<div class='unite_error_message' id="dialog_error_message" style="display:none;"></div>
		<div class='unite_success_message' id="dialog_success_message" style="display:none;"></div>
	</div> 
	
	<div id="dialog_insert_button" class="dialog_insert_button" title="Insert Button" style="display:none;">
		<p>
			<ul class="list-buttons">
			<?php foreach($arrButtonClasses as $class=>$text): ?>
					<li>
						<a href="javascript:UniteLayersRev.insertButton('<?php echo $class?>','<?php echo $text?>')" class="tp-button <?php echo $class?> small"><?php echo $text?></a>
					</li>
			<?php endforeach;?> 
			</ul>
		</p>
	</div>
	
	<script type="text/javascript">
		
		jQuery(document).ready(function() {
			<?php if(!empty($jsonLayers)):?>
				//set init layers object
				UniteLayersRev.setInitLayersJson(<?php echo $jsonLayers?>);
			<?php endif?>

			<?php if(!empty($jsonCaptions)):?>
			UniteLayersRev.setInitCaptionClasses(<?php echo $jsonCaptions?>);
			<?php endif?>
			
			UniteLayersRev.setCssCaptionsUrl('<?php echo $urlCaptionsCSS?>'); 
			UniteLayersRev.init("<?php echo $slideDelay?>");
			
		});
	
	</script>
