
<!-- //Youtube dialog: -->
<div id="dialog_video" class="dialog-video" title="<?php _e("Add Youtube Layout",REVSLIDER_TEXTDOMAIN)?>" style="display:none">
	
	<div class="video_left">
		
		<!-- Type chooser -->
		
		<div id="video_type_chooser" class="video-type-chooser">
			<div class="choose-video-type">
				<?php _e("Choose video type",REVSLIDER_TEXTDOMAIN)?>
			</div>
			
			<label for="video_radio_youtube"><?php _e("Youtube",REVSLIDER_TEXTDOMAIN)?></label>
			<input type="radio" checked id="video_radio_youtube" name="video_select">
			
			<label for="video_radio_vimeo"><?php _e("Vimeo",REVSLIDER_TEXTDOMAIN)?></label>
			<input type="radio" id="video_radio_vimeo" name="video_select">
			
			<label for="video_radio_html5"><?php _e("HTML5",REVSLIDER_TEXTDOMAIN)?></label>
			<input type="radio" id="video_radio_html5" name="video_select">
			
		</div>
		
		<!-- Vimeo block -->
		
		<div id="video_block_vimeo" class="video-select-block" style="display:none;" >
			<div class="video-title" >
				<?php _e("Enter Vimeo ID or URL",REVSLIDER_TEXTDOMAIN)?>
			</div>
			
			<input type="text" id="vimeo_id" value=""></input>
			&nbsp;
			<input type="button" id="button_vimeo_search" class="button-regular" value="search">
			
			<img id="vimeo_loader" src="<?php echo self::$url_plugin?>/images/loader.gif" style="display:none">
			
			<div class="video_example">
				<?php _e("example:  30300114",REVSLIDER_TEXTDOMAIN)?>
			</div>
		
		</div>
		
		<!-- Youtube block -->
		
		<div id="video_block_youtube" class="video-select-block">
		
			<div class="video-title">
				<?php _e("Enter Youtube ID or URL",REVSLIDER_TEXTDOMAIN)?>:
			</div>
			
			<input type="text" id="youtube_id" value=""></input>
			&nbsp;
			<input type="button" id="button_youtube_search" class="button-regular" value="search">
			
			<img id="youtube_loader" src="<?php echo self::$url_plugin?>/images/loader.gif" style="display:none">
			
			<div class="video_example">
				<?php _e("example",REVSLIDER_TEXTDOMAIN)?>:  <?php echo GlobalsRevSlider::YOUTUBE_EXAMPLE_ID?>
			</div>
			
		</div>
		
		<!-- Html 5 block -->
		
		<div id="video_block_html5" class="video-select-block" style="display:none;">
			
			<ul>
				<li>
					<div class="video_title2">
					<?php _e("Poster Image Url")?>:
					</div>
					<input type="text" id="html5_url_poster" value=""></input>
					<span class="video_example"><?php _e("Example",REVSLIDER_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.png</span>
				</li>
				<li>
					<div class="video_title2">				
					<?php _e("Video MP4 Url")?>:
					</div>
					<input type="text" id="html5_url_mp4" value=""></input>
					<span class="video_example"><?php _e("Example",REVSLIDER_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.mp4</span>
				</li>
				<li>
					<div class="video_title2">								
					<?php _e("Video WEBM Url")?>:
					</div>
					<input type="text" id="html5_url_webm" value=""></input>
					<span class="video_example"><?php _e("Example",REVSLIDER_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.webm</span>					
				</li>
				<li>
					<div class="video_title2">
					<?php _e("Video OGV Url")?>:
					</div>			
					<input type="text" id="html5_url_ogv" value=""></input>
					<span class="video_example"><?php _e("Example",REVSLIDER_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.ogv</span>	
				</li>
				
			</ul>
			
		</div>
		
		
		<!-- Video controls -->
		
		<div id="video_hidden_controls" style="display:none;">
		
			<div id="video_size_wrapper" class="youtube-inputs-wrapper">
				<?php _e("Width",REVSLIDER_TEXTDOMAIN)?>:
				<input type="text" id="input_video_width" class="video-input-small" value="320">
				&nbsp;&nbsp;&nbsp;
				<?php _e("Height",REVSLIDER_TEXTDOMAIN)?>:
				<input type="text" id="input_video_height" class="video-input-small" value="240">
				
			</div>
			
			<div class="mtop_20">
				<label for="input_video_fullwidth" class="video-title float_left">
					<?php _e("Full Width:",REVSLIDER_TEXTDOMAIN)?>
				</label>
				
				<input type="checkbox" class="checkbox_video_dialog float_left" id="input_video_fullwidth" ></input>
			
			</div>
			
			<div class="clear"></div>
			
			<div class="video-title mtop_20">
				<?php _e("Arguments:",REVSLIDER_TEXTDOMAIN)?>
			</div>
					
			<input type="text" id="input_video_arguments" style="width:245px;" value="" data-youtube="<?php echo GlobalsRevSlider::DEFAULT_YOUTUBE_ARGUMENTS?>" data-vimeo="<?php echo GlobalsRevSlider::DEFAULT_VIMEO_ARGUMENTS?>" ></input>
			
			<div class="mtop_20">
				<label for="input_video_autoplay" class="video-title float_left">
					<?php _e("Autplay:",REVSLIDER_TEXTDOMAIN)?>
				</label>
				
				<input type="checkbox" class="checkbox_video_dialog float_left" id="input_video_autoplay" ></input>
				
				<label for="input_video_nextslide" class="video-title float_left mleft_20">
					<?php _e("Next Slide On End:",REVSLIDER_TEXTDOMAIN)?>
				</label>
				
				<input type="checkbox" class="checkbox_video_dialog float_left" id="input_video_nextslide" ></input>
				
			</div>
			
			<div class="clear"></div>	
							
			<div class="add-button-wrapper">
				<a href="javascript:void(0)" class="button-primary" id="button-video-add" data-textadd="<?php _e("Add This Video",REVSLIDER_TEXTDOMAIN)?>" data-textupdate="<?php _e("Update Video",REVSLIDER_TEXTDOMAIN)?>" ><?php _e("Add This Video",REVSLIDER_TEXTDOMAIN)?></a>
			</div>
			
		</div>
		
	</div>
	
	<div id="video_content" class="video_right"></div>		
	
</div>
