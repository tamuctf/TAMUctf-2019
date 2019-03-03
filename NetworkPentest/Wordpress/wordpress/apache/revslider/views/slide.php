<?php

	//get input
	$slideID = UniteFunctionsRev::getGetVar("id");
	
	//init slide object
	$slide = new RevSlide();
	$slide->initByID($slideID);
	$slideParams = $slide->getParams();
	
	//dmp($slideParams);exit();
	
	$operations = new RevOperations();
	
	//init slider object
	$sliderID = $slide->getSliderID();
	$slider = new RevSlider();
	$slider->initByID($sliderID);
	$sliderParams = $slider->getParams();
	
	$arrSlideNames = $slider->getArrSlideNames();
	
	//set slide delay
	$sliderDelay = $slider->getParam("delay","9000");
	$slideDelay = $slide->getParam("delay","");
	if(empty($slideDelay))
		$slideDelay = $sliderDelay;
	
	require self::getSettingsFilePath("slide_settings");
	require self::getSettingsFilePath("layer_settings");
	
	$settingsLayerOutput = new UniteSettingsProductSidebarRev();
	$settingsSlideOutput = new UniteSettingsRevProductRev();
		
	$arrLayers = $slide->getLayers();
	
	//get settings objects
	$settingsLayer = self::getSettings("layer_settings");	
	$settingsSlide = self::getSettings("slide_settings");
	
	$cssContent = self::getSettings("css_captions_content");
	$arrCaptionClasses = $operations->getArrCaptionClasses($cssContent);
	
	$arrButtonClasses = $operations->getButtonClasses();
	
	//set layer caption as first caption class
	$firstCaption = !empty($arrCaptionClasses)?$arrCaptionClasses[0]:"";
	$settingsLayer->updateSettingValue("layer_caption",$firstCaption);
	
	//set stored values from "slide params"
	$settingsSlide->setStoredValues($slideParams);
		
	//init the settings output object
	$settingsLayerOutput->init($settingsLayer);
	$settingsSlideOutput->init($settingsSlide);
	
	//set various parameters needed for the page
	$width = $sliderParams["width"];
	$height = $sliderParams["height"];
	$imageUrl = $slide->getImageUrl();
	$imageID = $slide->getImageID();
	
	$imageFilename = $slide->getImageFilename();
	$urlCaptionsCSS = GlobalsRevSlider::$urlCaptionsCSS;
	
	$style = "width:{$width}px;height:{$height}px;";
	
	//set iframe parameters
	$iframeWidth = $width+60;
	$iframeHeight = $height+50;
	
	$iframeStyle = "width:{$iframeWidth}px;height:{$iframeHeight}px;";
	
	$closeUrl = self::getViewUrl(RevSliderAdmin::VIEW_SLIDES,"id=".$sliderID);
	
	$jsonLayers = UniteFunctionsRev::jsonEncodeForClientSide($arrLayers);
	$jsonCaptions = UniteFunctionsRev::jsonEncodeForClientSide($arrCaptionClasses);
	
	$loadGoogleFont = $slider->getParam("load_googlefont","false");
	
	//bg type params
	$bgType = UniteFunctionsRev::getVal($slideParams, "background_type","image");
	$slideBGColor = UniteFunctionsRev::getVal($slideParams, "slide_bg_color","#E7E7E7");
	$divLayersClass = "slide_layers";
	$bgSolidPickerProps = 'class="inputColorPicker slide_bg_color disabled" disabled="disabled"';

	
	switch($bgType){
		case "trans":
			$divLayersClass = "slide_layers trans_bg";
		break;
		case "solid":
			$style .= "background-color:{$slideBGColor};";
			$bgSolidPickerProps = 'class="inputColorPicker slide_bg_color" style="background-color:'.$slideBGColor.'"';
		break;
		case "image":
			$style .= "background-image:url('{$imageUrl}');";
		break;
	}
	
	$slideTitle = $slide->getParam("title","Slide");
	$slideOrder = $slide->getOrder();

	//treat multilanguage
	$isWpmlExists = UniteWpmlRev::isWpmlExists();	
	$useWpml = $slider->getParam("use_wpml","off");
	$wpmlActive = false;
	if($isWpmlExists && $useWpml == "on"){
		$wpmlActive = true;
		$parentSlide = $slide->getParentSlide();
		$arrChildLangs = $parentSlide->getArrChildrenLangs();
	}
	
	require self::getPathTemplate("slide");
?>
	
