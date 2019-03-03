<?php

	class RevSliderFront extends UniteBaseFrontClassRev{
		
		/**
		 * 
		 * the constructor
		 */
		public function __construct($mainFilepath){
			
			parent::__construct($mainFilepath,$this);
			
			//set table names
			GlobalsRevSlider::$table_sliders = self::$table_prefix.GlobalsRevSlider::TABLE_SLIDERS_NAME;
			GlobalsRevSlider::$table_slides = self::$table_prefix.GlobalsRevSlider::TABLE_SLIDES_NAME;
			GlobalsRevSlider::$table_settings = self::$table_prefix.GlobalsRevSlider::TABLE_SETTINGS_NAME;
		}
		
		
		/**
		 * 
		 * a must function. you can not use it, but the function must stay there!.
		 *   
		 */		
		public static function onAddScripts(){
			
			$operations = new RevOperations();
			$arrValues = $operations->getGeneralSettingsValues();
			
			$includesGlobally = UniteFunctionsRev::getVal($arrValues, "includes_globally","on");
			$strPutIn = UniteFunctionsRev::getVal($arrValues, "pages_for_includes");
			$isPutIn = RevSliderOutput::isPutIn($strPutIn,true);
			
			//put the includes only on pages with active widget or shortcode
			// if the put in match, then include them always (ignore this if)			
			if($isPutIn == false && $includesGlobally == "off"){
				$isWidgetActive = is_active_widget( false, false, "rev-slider-widget", true );
				$hasShortcode = UniteFunctionsWPRev::hasShortcode("rev_slider");
				
				if($isWidgetActive == false && $hasShortcode == false)
					return(false);
			}
			
			self::addStyle("settings","rs-settings","rs-plugin/css");
			self::addStyle("captions","rs-captions","rs-plugin/css");

			$url_jquery = "http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js?app=revolution";
			self::addScriptAbsoluteUrl($url_jquery, "jquery");
			
			self::addScript("jquery.themepunch.revolution.min","rs-plugin/js");
			
		}
		
	}
	

?>