			<?php
				
				//set "slider_main" settings
				$sliderMainSettings = new UniteSettingsAdvancedRev();
				
				
				$sliderMainSettings->addTextBox("title", "",__("Slider Title",REVSLIDER_TEXTDOMAIN),array("description"=>__("The title of the slider. Example: Slider1",REVSLIDER_TEXTDOMAIN),"required"=>"true"));	
				$sliderMainSettings->addTextBox("alias", "",__("Slider Alias",REVSLIDER_TEXTDOMAIN),array("description"=>__("The alias that will be used for embedding the slider. Example: slider1",REVSLIDER_TEXTDOMAIN),"required"=>"true"));
				$sliderMainSettings->addTextBox("shortcode", "",__("Slider Shortcode",REVSLIDER_TEXTDOMAIN), array("readonly"=>true,"class"=>"code"));
				$sliderMainSettings->addHr();
				
				
				//set slider type / texts
				$sliderMainSettings->addRadio("slider_type", array("fixed"=>__("Fixed",REVSLIDER_TEXTDOMAIN),
					"responsitive"=>__("Custom",REVSLIDER_TEXTDOMAIN),
					"fullwidth"=>__("Auto Responsive",REVSLIDER_TEXTDOMAIN),
					"fullscreen"=>__("Full Screen",REVSLIDER_TEXTDOMAIN)
					),__("Slider Layout",REVSLIDER_TEXTDOMAIN),		
					"fixed");
			
				$arrParams = array("class"=>"medium","description"=>__("Example: #header | The height of fullscreen slider will be decreased with the height of the #header to fit perfect in the screen",REVSLIDER_TEXTDOMAIN));
				$sliderMainSettings->addTextBox("fullscreen_offset_container", "",__("Fullscreen Offset Container",REVSLIDER_TEXTDOMAIN), $arrParams);
			
				$sliderMainSettings->addControl("slider_type", "fullscreen_offset_container", UniteSettingsRev::CONTROL_TYPE_SHOW, "fullscreen");
				
				$paramsSize = array("width"=>960,"height"=>350);	
				$sliderMainSettings->addCustom("slider_size", "slider_size","",__("Grid Settings",REVSLIDER_TEXTDOMAIN),$paramsSize);
				
				
			
			
				$paramsResponsitive = array("w1"=>940,"sw1"=>770,"w2"=>780,"sw2"=>500,"w3"=>510,"sw3"=>310);
				$sliderMainSettings->addCustom("responsitive_settings", "responsitive","",__("Custom Responsive Sizes"),$paramsResponsitive);
				$sliderMainSettings->addHr();
				
				self::storeSettings("slider_main",$sliderMainSettings);
				
				//set "slider_params" settings. 
				$sliderParamsSettings = new UniteSettingsAdvancedRev();	
				$sliderParamsSettings->loadXMLFile(self::$path_settings."/slider_settings.xml");
				
				//update transition type setting.
				$settingFirstType = $sliderParamsSettings->getSettingByName("first_transition_type");
				$operations = new RevOperations();
				$arrTransitions = $operations->getArrTransition();
				$settingFirstType["items"] = $arrTransitions;
				$sliderParamsSettings->updateArrSettingByName("first_transition_type", $settingFirstType);
					
				
				//store params
				self::storeSettings("slider_params",$sliderParamsSettings); 
				?>
				

