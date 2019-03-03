<?php
 
 class UniteBaseAdminClassRev extends UniteBaseClassRev{
 	
		const ACTION_ADMIN_MENU = "admin_menu";
		const ACTION_ADMIN_INIT = "admin_init";	
		const ACTION_ADD_SCRIPTS = "admin_enqueue_scripts";
		
		const ROLE_ADMIN = "admin";
		const ROLE_EDITOR = "editor";
		const ROLE_AUTHOR = "author";
		
		protected static $master_view;
		protected static $view;
		
		private static $arrSettings = array();
		private static $arrMenuPages = array();
		private static $tempVars = array();
		private static $startupError = "";
		private static $menuRole = self::ROLE_ADMIN;
		
		
		/**
		 * 
		 * main constructor		 
		 */
		public function __construct($mainFile,$t,$defaultView){
						
			parent::__construct($mainFile,$t);
			
			//set view
			self::$view = self::getGetVar("view");
			if(empty(self::$view))
				self::$view = $defaultView;
				
			//add internal hook for adding a menu in arrMenus
			self::addAction(self::ACTION_ADMIN_MENU, "addAdminMenu");
			
			//if not inside plugin don't continue
			if($this->isInsidePlugin() == true){
				self::addAction(self::ACTION_ADD_SCRIPTS, "addCommonScripts");
				self::addAction(self::ACTION_ADD_SCRIPTS, "onAddScripts");
			}
			
			//a must event for any admin. call onActivate function.
			$this->addEvent_onActivate();
			self::addActionAjax("show_image", "onShowImage");
		}		
		
		/**
		 * 
		 * set the menu role - for viewing menus
		 */
		public static function setMenuRole($menuRole){
			self::$menuRole = $menuRole;
		}
		
		/**
		 * 
		 * set startup error to be shown in master view
		 */
		public static function setStartupError($errorMessage){
			self::$startupError = $errorMessage;
		}
		
		
		/**
		 * 
		 * tells if the the current plugin opened is this plugin or not 
		 * in the admin side.
		 */
		private function isInsidePlugin(){
			$page = self::getGetVar("page");
			if($page == self::$dir_plugin)
				return(true);
			return(false);
		} 
		
		
		/**
		 * 
		 * add common used scripts
		 */
		public static function addCommonScripts(){

			//include jquery ui
			if(GlobalsRevSlider::$isNewVersion){	//load new jquery ui library
				
				$urlJqueryUI = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js";
				self::addScriptAbsoluteUrl($urlJqueryUI,"jquery-ui");
				self::addStyle("jquery-ui-1.10.3.custom.min","jui-smoothness","css/jui/new");
				
				if(function_exists("wp_enqueue_media"))
					wp_enqueue_media();
				
			}else{	//load old jquery ui library
				
				$urlJqueryUI = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js";
				self::addScriptAbsoluteUrl($urlJqueryUI,"jquery-ui");
				self::addStyle("jquery-ui-1.8.18.custom","jui-smoothness","css/jui/old");
				
			}
						
			self::addScriptCommon("settings","unite_settings");
			self::addScriptCommon("admin","unite_admin");
			self::addScriptCommon("jquery.tipsy","tipsy");
			
			//--- add styles
			
			self::addStyleCommon("admin","unite_admin");
			
			//add tipsy
			self::addStyleCommon("tipsy","tipsy");
			
			//include farbtastic
			self::addScriptCommon("my-farbtastic","my-farbtastic","js/farbtastic");
			self::addStyleCommon("farbtastic","farbtastic","js/farbtastic");
			
			//include codemirror
			self::addScriptCommon("codemirror","codemirror_js","js/codemirror");
			self::addScriptCommon("css","codemirror_js_css","js/codemirror");
			self::addStyleCommon("codemirror","codemirror_css","js/codemirror");
			
			//include dropdown checklist
			self::addScriptCommon("ui.dropdownchecklist-1.4-min","dropdownchecklist_js","js/dropdownchecklist");
			//self::addScriptCommon("ui.dropdownchecklist","dropdownchecklist_js","js/dropdownchecklist");
			//self::addStyleCommon("ui.dropdownchecklist.standalone","dropdownchecklist_css","js/dropdownchecklist");
						
		}
		
		
		
		/**
		 * 
		 * admin pages parent, includes all the admin files by default
		 */
		public static function adminPages(){
			//self::validateAdminPermissions();
		}
		
		
		/**
		 * 
		 * validate permission that the user is admin, and can manage options.
		 */
		protected static function isAdminPermissions(){
			
			if( is_admin() &&  current_user_can("manage_options") )
				return(true);
				
			return(false);
		}
		
		/**
		 * 
		 * validate admin permissions, if no pemissions - exit
		 */
		protected static function validateAdminPermissions(){
			if(!self::isAdminPermissions()){
				echo "access denied";
				return(false);
			}			
		}
		
		/**
		 * 
		 * set view that will be the master
		 */
		protected static function setMasterView($masterView){
			self::$master_view = $masterView;
		}
		
		/**
		 * 
		 * inlcude some view file
		 */
		protected static function requireView($view){
			try{
				//require master view file, and 
				if(!empty(self::$master_view) && !isset(self::$tempVars["is_masterView"]) ){
					$masterViewFilepath = self::$path_views.self::$master_view.".php";
					UniteFunctionsRev::validateFilepath($masterViewFilepath,"Master View");
					
					self::$tempVars["is_masterView"] = true;
					require $masterViewFilepath;
				}
				else{		//simple require the view file.
					$viewFilepath = self::$path_views.$view.".php";
					
					UniteFunctionsRev::validateFilepath($viewFilepath,"View");
					require $viewFilepath;
				}
				
			}catch (Exception $e){
				echo "<br><br>View ($view) Error: <b>".$e->getMessage()."</b>";
				
				if(self::$debugMode == true)
					dmp($e->getTraceAsString());
			}
		}
		
		/**
		 * require some template from "templates" folder
		 */
		protected static function getPathTemplate($templateName){
			
			$pathTemplate = self::$path_templates.$templateName.".php";
			UniteFunctionsRev::validateFilepath($pathTemplate,"Template");
			
			return($pathTemplate);
		}
		
		/**
		 * 
		 * require settings file, the filename without .php
		 */
		protected static function requireSettings($settingsFile){
			
			try{
				require self::$path_plugin."settings/$settingsFile.php";
			}catch (Exception $e){
				echo "<br><br>Settings ($settingsFile) Error: <b>".$e->getMessage()."</b>";
				dmp($e->getTraceAsString());
			}
		}
		
		/**
		 * 
		 * get path to settings file
		 * @param $settingsFile
		 */
		protected static function getSettingsFilePath($settingsFile){
			
			$filepath = self::$path_plugin."settings/$settingsFile.php";
			return($filepath);
		}
		
		
		/**
		 * 
		 * add all js and css needed for media upload
		 */
		protected static function addMediaUploadIncludes(){
			
			self::addWPScript("thickbox");
			self::addWPStyle("thickbox");
			self::addWPScript("media-upload");
			
		}
		
		
		/**
		 * add admin menus from the list.
		 */
		public static function addAdminMenu(){
			
			$role = "manage_options";
			
			switch(self::$menuRole){
				case self::ROLE_AUTHOR:
					$role = "edit_published_posts";
				break;
				case self::ROLE_EDITOR:
					$role = "edit_pages";
				break;		
				default:		
				case self::ROLE_ADMIN:
					$role = "manage_options";
				break;
			}
			
			foreach(self::$arrMenuPages as $menu){
				$title = $menu["title"];
				$pageFunctionName = $menu["pageFunction"];
				add_menu_page( $title, $title, $role, self::$dir_plugin, array(self::$t, $pageFunctionName) );
			}
			
		}
		
		
		/**
		 * 
		 * add menu page
		 */
		protected static function addMenuPage($title,$pageFunctionName){
						
			self::$arrMenuPages[] = array("title"=>$title,"pageFunction"=>$pageFunctionName);
			
		}

		/**
		 * 
		 * get url to some view.
		 */
		public static function getViewUrl($viewName,$urlParams=""){
			$params = "&view=".$viewName;
			if(!empty($urlParams))
				$params .= "&".$urlParams;
			
			$link = admin_url( "admin.php?page=".self::$dir_plugin.$params);
			return($link);
		}
		
		/**
		 * 
		 * register the "onActivate" event
		 */
		protected function addEvent_onActivate($eventFunc = "onActivate"){
			register_activation_hook( self::$mainFile, array(self::$t, $eventFunc) );
		}
		
		/**
		 * 
		 * store settings in the object
		 */
		protected static function storeSettings($key,$settings){
			self::$arrSettings[$key] = $settings;
		}
		
		/**
		 * 
		 * get settings object
		 */
		protected static function getSettings($key){
			if(!isset(self::$arrSettings[$key]))
				UniteFunctionsRev::throwError("Settings $key not found");
			$settings = self::$arrSettings[$key];
			return($settings);
		}
		
		
		/**
		 * 
		 * add ajax back end callback, on some action to some function.
		 */
		protected static function addActionAjax($ajaxAction,$eventFunction){
			self::addAction('wp_ajax_'.self::$dir_plugin."_".$ajaxAction, $eventFunction);
			self::addAction('wp_ajax_nopriv_'.self::$dir_plugin."_".$ajaxAction, $eventFunction);
		}
		
		/**
		 * 
		 * echo json ajax response
		 */
		private static function ajaxResponse($success,$message,$arrData = null){
			
			$response = array();			
			$response["success"] = $success;				
			$response["message"] = $message;
			
			if(!empty($arrData)){
				
				if(gettype($arrData) == "string")
					$arrData = array("data"=>$arrData);				
				
				$response = array_merge($response,$arrData);
			}
				
			$json = json_encode($response);
			
			echo $json;
			exit();
		}

		/**
		 * 
		 * echo json ajax response, without message, only data
		 */
		protected static function ajaxResponseData($arrData){
			if(gettype($arrData) == "string")
				$arrData = array("data"=>$arrData);
			
			self::ajaxResponse(true,"",$arrData);
		}
		
		/**
		 * 
		 * echo json ajax response
		 */
		protected static function ajaxResponseError($message,$arrData = null){
			
			self::ajaxResponse(false,$message,$arrData,true);
		}
		
		/**
		 * echo ajax success response
		 */
		protected static function ajaxResponseSuccess($message,$arrData = null){
			
			self::ajaxResponse(true,$message,$arrData,true);
			
		}
		
		/**
		 * echo ajax success response
		 */
		protected static function ajaxResponseSuccessRedirect($message,$url){
			$arrData = array("is_redirect"=>true,"redirect_url"=>$url);
			
			self::ajaxResponse(true,$message,$arrData,true);
		}
		

		/**
		 * 
		 * Enter description here ...
		 */
		protected static function updatePlugin($viewBack = false){
			$linkBack = self::getViewUrl($viewBack);
			$htmlLinkBack = UniteFunctionsRev::getHtmlLink($linkBack, "Go Back");
			
			$zip = new UniteZipRev();
						
			try{
				
				if(function_exists("unzip_file") == false){					
					if( UniteZipRev::isZipExists() == false)
						UniteFunctionsRev::throwError("The ZipArchive php extension not exists, can't extract the update file. Please turn it on in php ini.");
				}
				
				dmp("Update in progress...");
				
				$arrFiles = UniteFunctionsRev::getVal($_FILES, "update_file");
				if(empty($arrFiles))
					UniteFunctionsRev::throwError("Update file don't found.");
					
				$filename = UniteFunctionsRev::getVal($arrFiles, "name");
				
				if(empty($filename))
					UniteFunctionsRev::throwError("Update filename not found.");
				
				$fileType = UniteFunctionsRev::getVal($arrFiles, "type");
				
				/*				
				$fileType = strtolower($fileType);
				
				if($fileType != "application/zip")
					UniteFunctionsRev::throwError("The file uploaded is not zip.");
				*/
				
				$filepathTemp = UniteFunctionsRev::getVal($arrFiles, "tmp_name");
				if(file_exists($filepathTemp) == false)
					UniteFunctionsRev::throwError("Can't find the uploaded file.");	

				//crate temp folder
				UniteFunctionsRev::checkCreateDir(self::$path_temp);

				//create the update folder
				$pathUpdate = self::$path_temp."update_extract/";				
				UniteFunctionsRev::checkCreateDir($pathUpdate);
								
				//remove all files in the update folder
				if(is_dir($pathUpdate)){ 
					$arrNotDeleted = UniteFunctionsRev::deleteDir($pathUpdate,false);
					if(!empty($arrNotDeleted)){
						$strNotDeleted = print_r($arrNotDeleted,true);
						UniteFunctionsRev::throwError("Could not delete those files from the update folder: $strNotDeleted");
					}
				}
				
				//copy the zip file.
				$filepathZip = $pathUpdate.$filename;
				
				$success = move_uploaded_file($filepathTemp, $filepathZip);
				if($success == false)
					UniteFunctionsRev::throwError("Can't move the uploaded file here: {$filepathZip}.");
				
				if(function_exists("unzip_file") == true){
					WP_Filesystem();
					$response = unzip_file($filepathZip, $pathUpdate);
				}
				else					
					$zip->extract($filepathZip, $pathUpdate);
				
				//get extracted folder
				$arrFolders = UniteFunctionsRev::getFoldersList($pathUpdate);
				if(empty($arrFolders))
					UniteFunctionsRev::throwError("The update folder is not extracted");
				
				if(count($arrFolders) > 1)
					UniteFunctionsRev::throwError("Extracted folders are more then 1. Please check the update file.");
					
				//get product folder
				$productFolder = $arrFolders[0];
				if(empty($productFolder))
					UniteFunctionsRev::throwError("Wrong product folder.");
					
				if($productFolder != self::$dir_plugin)
					UniteFunctionsRev::throwError("The update folder don't match the product folder, please check the update file.");
				
				$pathUpdateProduct = $pathUpdate.$productFolder."/";				
				
				//check some file in folder to validate it's the real one:
				$checkFilepath = $pathUpdateProduct.$productFolder.".php";
				if(file_exists($checkFilepath) == false)
					UniteFunctionsRev::throwError("Wrong update extracted folder. The file: {$checkFilepath} not found.");
				
				//copy the plugin without the captions file.
				//$pathOriginalPlugin = $pathUpdate."copy/";
				$pathOriginalPlugin = self::$path_plugin;
				
				$arrBlackList = array();
				$arrBlackList[] = "rs-plugin/css/captions.css";
				
				UniteFunctionsRev::copyDir($pathUpdateProduct, $pathOriginalPlugin,"",$arrBlackList);
				
				//delete the update
				UniteFunctionsRev::deleteDir($pathUpdate);
				
				dmp("Updated Successfully, redirecting...");
				echo "<script>location.href='$linkBack'</script>";
				
			}catch(Exception $e){
				$message = $e->getMessage();
				$message .= " <br> Please update the plugin manually via the ftp";
				echo "<div style='color:#B80A0A;font-size:18px;'><b>Update Error: </b> $message</div><br>";
				echo $htmlLinkBack;
				exit();
			}
			
		}
		
 	
 }
 
 ?>