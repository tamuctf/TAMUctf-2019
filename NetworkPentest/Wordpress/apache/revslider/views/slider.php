<?php

	$settingsMain = self::getSettings("slider_main");
	$settingsParams = self::getSettings("slider_params");

	$settingsSliderMain = new RevSliderSettingsProduct();
	$settingsSliderParams = new UniteSettingsProductSidebarRev();
	
	//check existing slider data:
	$sliderID = self::getGetVar("id");
	
	if(!empty($sliderID)){
		$slider = new RevSlider();
		$slider->initByID($sliderID);
		
		//get setting fields
		$settingsFields = $slider->getSettingsFields();
		$arrFieldsMain = $settingsFields["main"];
		$arrFieldsParams = $settingsFields["params"];		

		//modify arrows type for backword compatability
		$arrowsType = UniteFunctionsRev::getVal($arrFieldsParams, "navigation_arrows");
		switch($arrowsType){
			case "verticalcentered":
				$arrFieldsParams["navigation_arrows"] = "solo";
			break;
		}
		
		
		//set setting values from the slider
		$settingsMain->setStoredValues($arrFieldsParams);
		
		//set custom type params values:
		$settingsMain = RevSliderSettingsProduct::setSettingsCustomValues($settingsMain, $arrFieldsParams);		
		
		$settingsParams->setStoredValues($arrFieldsParams);
		
		//update short code setting
		$shortcode = $slider->getShortcode();
		$settingsMain->updateSettingValue("shortcode",$shortcode);
		
		$linksEditSlides = self::getViewUrl(RevSliderAdmin::VIEW_SLIDES,"id=$sliderID");
		
		$settingsSliderParams->init($settingsParams);	
		$settingsSliderMain->init($settingsMain);
		
		$settingsSliderParams->isAccordion(true);
		
		require self::getPathTemplate("slider_edit");
	}
	
	else{
		
		$settingsSliderParams->init($settingsParams);	
		$settingsSliderMain->init($settingsMain);
		
		$settingsSliderParams->isAccordion(true);
		
		require self::getPathTemplate("slider_new");		
	}
	
?>


	