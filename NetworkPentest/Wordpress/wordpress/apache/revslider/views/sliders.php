<?php
	$slider = new RevSlider();
	$arrSliders = $slider->getArrSliders();
	
	$addNewLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDER);

	
	require self::getPathTemplate("sliders");
?>


	