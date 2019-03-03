<?php

	$generalSettings = new UniteSettingsRev();
	
	$generalSettings->addSelect("role", 
								array(UniteBaseAdminClassRev::ROLE_ADMIN => __("To Admin",REVSLIDER_TEXTDOMAIN),
									  UniteBaseAdminClassRev::ROLE_EDITOR =>__("To Editor, Admin",REVSLIDER_TEXTDOMAIN),
									  UniteBaseAdminClassRev::ROLE_AUTHOR =>__("Author, Editor, Admin",REVSLIDER_TEXTDOMAIN)),									  
									  __("View Plugin Permission",REVSLIDER_TEXTDOMAIN), 
									  UniteBaseAdminClassRev::ROLE_ADMIN, 
									  array("description"=>"<br>".__("The role of user that can view and edit the plugin",REVSLIDER_TEXTDOMAIN)));

	$generalSettings->addRadio("includes_globally", 
							   array("on"=>__("On",REVSLIDER_TEXTDOMAIN),"off"=>__("Off",REVSLIDER_TEXTDOMAIN)),
							   __("Include RevSlider libraries globally",REVSLIDER_TEXTDOMAIN),
							   "on",
							   array("description"=>"<br>".__("Add css and js includes only on all pages. Id turned to off they will added to pages where the rev_slider shortcode exists only. This will work only when the slider added by a shortcode.",REVSLIDER_TEXTDOMAIN)));
	
	$generalSettings->addTextBox("pages_for_includes", "",__("Pages to include RevSlider libraries",REVSLIDER_TEXTDOMAIN),
								  array("description"=>"<br>".__("Specify the page id's that the front end includes will be included in. Example: 2,3,5 also: homepage,3,4",REVSLIDER_TEXTDOMAIN)));
									  
	//--------------------------
	
	//get stored values
	$operations = new RevOperations();
	$arrValues = $operations->getGeneralSettingsValues();
	$generalSettings->setStoredValues($arrValues);
	
	self::storeSettings("general", $generalSettings);

?>