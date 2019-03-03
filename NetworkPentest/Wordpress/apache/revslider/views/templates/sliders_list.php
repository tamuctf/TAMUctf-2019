
	<table class='wp-list-table widefat fixed unite_table_items'>
		<thead>
			<tr>
				<th width='20'><?php _e("ID",REVSLIDER_TEXTDOMAIN)?></th>
				<th width=''><?php _e("Name",REVSLIDER_TEXTDOMAIN)?></th>
				<th width='65'><?php _e("N. Slides",REVSLIDER_TEXTDOMAIN)?></th>						
				<th width='435'><?php _e("Actions",REVSLIDER_TEXTDOMAIN)?> </th>
				<th width='15%'><?php _e("Shortcode",REVSLIDER_TEXTDOMAIN)?> </th>
				<th width='60'><?php _e("Preview",REVSLIDER_TEXTDOMAIN)?> </th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($arrSliders as $slider):
				
				$id = $slider->getID();
				$showTitle = $slider->getShowTitle();
				$title = $slider->getTitle();
				$alias = $slider->getAlias();
				$shortCode = $slider->getShortcode();
				$numSlides = $slider->getNumSlides();
				
				$editLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDER,"id=$id");
				$editSlidesLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDES,"id=$id");
				
				$showTitle = UniteFunctionsRev::getHtmlLink($editLink, $showTitle);
				
			?>
				<tr>
					<td><?php echo $id?><span id="slider_title_<?php echo $id?>" class="hidden"><?php echo $title?></span></td>								
					<td><?php echo $showTitle?></td>
					<td><?php echo $numSlides?></td>
					<td>
						<a class="greenbutton newlineheight button-edit-slider" href='<?php echo $editLink ?>'><?php _e("Edit Slider",REVSLIDER_TEXTDOMAIN)?></a>
						<div class="clearme"></div>
						<a class="greenbutton newlineheight button-edit-slides" href='<?php echo $editSlidesLink ?>'><?php _e("Edit Slides",REVSLIDER_TEXTDOMAIN)?></a>
						<div class="clearme"></div>						
						<a id="button_delete_<?php echo $id?>" href='javascript:void(0)' class="button-secondary button_delete_slider changemargin newlineheight"><?php _e("Delete",REVSLIDER_TEXTDOMAIN)?></a>
						<div class="clearme"></div>
						<a id="button_duplicate_<?php echo $id?>" href='javascript:void(0)' class="button-secondary button_duplicate_slider changemargin2 newlineheight"><?php _e("Duplicate",REVSLIDER_TEXTDOMAIN)?> </a>
					</td>
					<td><?php echo $shortCode?></td>
					<td>
						<div id="button_preview_<?php echo $id?>" class="button_slider_preview" title="<?php _e("Preview",REVSLIDER_TEXTDOMAIN)?> <?php echo $title?>"></div>
					</td>
				</tr>							
			<?php endforeach;?>
			
		</tbody>		 
	</table>

	<?php require self::getPathTemplate("dialog_preview_slider");?>


	