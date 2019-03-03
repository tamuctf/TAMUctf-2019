
	<div id="toolbox_wrapper" class="toolbox_wrapper postbox unite-postbox">
		<h3 class="box_closed tp-accordion tpa-closed"><div class="postbox-arrow"></div><span><?php _e("Import / Export",REVSLIDER_TEXTDOMAIN) ?></span></h3>
		<div class="toggled-content tp-closedatstart p20">
			
			<div class="api-caption"><?php _e("Import Slider",REVSLIDER_TEXTDOMAIN)?>:</div>
			<div class="divide20"></div>
			
			<form action="<?php echo UniteBaseClassRev::$url_ajax?>" enctype="multipart/form-data" method="post">			    
			    <input type="hidden" name="action" value="revslider_ajax_action">
			    <input type="hidden" name="client_action" value="import_slider">
			    <input type="hidden" name="sliderid" value="<?php echo $sliderID?>">						
				<input type="file" name="import_file" class="input_import_slider" style="width:100%">
				<div class="divide20"></div>				
				<input type="submit" class='button-secondary' value="Import Slider">
			</form>	
			<div class="divide20"></div>
			<div class="api-desc"><?php _e("Note, that when you importing slider, it delete all the current slider settings and slides, then replace it with the new ones",REVSLIDER_TEXTDOMAIN)?>.</div>


			<hr>
			<div class="divide20"></div>
			
			
			<div class="api-caption"><?php _e("Export Slider",REVSLIDER_TEXTDOMAIN)?>:</div>
			<div class="divide20"></div>
						
			<a id="button_export_slider" class='button-secondary' href='javascript:void(0)' ><?php _e("Export Slider",REVSLIDER_TEXTDOMAIN)?></a>

		</div>	
	</div>
	

