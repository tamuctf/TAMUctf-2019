<?php
	$exampleID = '"slider1"';
	if(!empty($arrSliders))
		$exampleID = '"'.$arrSliders[0]->getAlias().'"';
?>

	<div class='wrap'>
		<div class="clear_both"></div> 

	<div class="title_line">
		<h2>
			<?php _e("Revolution Sliders",REVSLIDER_TEXTDOMAIN)?>
		</h2>
		
		<a href="<?php echo GlobalsRevSlider::LINK_HELP_SLIDERS?>" class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php _e("Help",REVSLIDER_TEXTDOMAIN)?></a>			
		
		<a id="button_general_settings" class="button-secondary float_right mtop_10"><?php _e("Global Settings",REVSLIDER_TEXTDOMAIN)?></a>
		
	</div>

	<?php if(empty($arrSliders)): ?>
		<?php _e("No Sliders Found",REVSLIDER_TEXTDOMAIN)?>
		<br>
	<?php else:
		 require self::getPathTemplate("sliders_list");	 		
	endif?>
	
	
	<br>
	<p>			
		<a class='button-primary' href='<?php echo $addNewLink?>'><?php _e("Create New Slider",REVSLIDER_TEXTDOMAIN)?> </a>
		
		<a id="button_import_slider" class='button-secondary float_right' href='javascript:void(0)'><?php _e("Import Slider",REVSLIDER_TEXTDOMAIN)?> </a>		
	</p>
	 
	 <br>
	 
	<div>		
		<h3> <?php _e("How To Use",REVSLIDER_TEXTDOMAIN)?>:</h3>
		
		<ul>
			<li>
				<?php _e("* From the")?> <b><?php _e("theme html",REVSLIDER_TEXTDOMAIN)?></b> <?php _e("use",REVSLIDER_TEXTDOMAIN)?>: <code>&lt?php putRevSlider( "alias" ) ?&gt</code> <?php _e("example",REVSLIDER_TEXTDOMAIN)?>: <code>&lt?php putRevSlider(<?echo $exampleID?>) ?&gt</code>
				<br>
				&nbsp;&nbsp; <?php _e("For show only on homepage use",REVSLIDER_TEXTDOMAIN)?>: <code>&lt?php putRevSlider(<?echo $exampleID?>,"homepage") ?&gt</code>
				<br>&nbsp;&nbsp; <?php _e("For show on certain pages use")?>: <code>&lt?php putRevSlider(<?echo $exampleID?>,"2,10") ?&gt</code> 
			</li>
			<li><?php _e("* From the",REVSLIDER_TEXTDOMAIN)?> <b><?php _e("widgets panel",REVSLIDER_TEXTDOMAIN)?></b> <?php _e("drag the \"Revolution Slider\" widget to the desired sidebar",REVSLIDER_TEXTDOMAIN)?></li>
			<li><?php _e("* From the",REVSLIDER_TEXTDOMAIN)?> <b><?php _e("post editor",REVSLIDER_TEXTDOMAIN)?></b> <?php _e("insert the shortcode from the sliders table",REVSLIDER_TEXTDOMAIN)?></li>
		</ul>
		---------
		<p>
			<?php _e("If you have some support issue, don't hesitate to",REVSLIDER_TEXTDOMAIN)?>
			 <a href="http://themepunch.ticksy.com" target="_blank"><?php _e("write here",REVSLIDER_TEXTDOMAIN)?>.</a>
		 	<br><?php _e("The ThemePunch team will be happy to support you on any issue",REVSLIDER_TEXTDOMAIN)?>.
		</p> 
	</div>
	
	<p></p>
	
	</div>
	
	<!-- Import slider dialog -->
	<div id="dialog_import_slider" title="<?php _e("Import Slider",REVSLIDER_TEXTDOMAIN)?>" class="dialog_import_slider" style="display:none">
		<br><br><br>
		<form action="<?php echo UniteBaseClassRev::$url_ajax?>" enctype="multipart/form-data" method="post">
		    
		    <input type="hidden" name="action" value="revslider_ajax_action">
		    <input type="hidden" name="client_action" value="import_slider_slidersview">
		    
		    <?php _e("Choose the import file",REVSLIDER_TEXTDOMAIN)?>:   
		    <br>
			<input type="file" size="60" name="import_file" class="input_import_slider">
			<br><br>
			<input type="submit" class='button-primary' value="Import Slider">
		</form>		
		
	</div>
	
	<script type="text/javascript">
		jQuery(document).ready(function(){
			RevSliderAdmin.initSlidersListView();
		});
	</script>
	