<?php

	class UniteFunctionsWPRev{

		const THUMB_SMALL = "thumbnail";
		const THUMB_MEDIUM = "medium";
		const THUMB_LARGE = "large";
		const THUMB_FULL = "full";
		
		
		/**
		 * get blog id
		 */
		public static function getBlogID(){
			global $blog_id;
			return($blog_id);
		}
		
		
		/**
		 * 
		 * get blog id
		 */
		public static function isMultisite(){
			$isMultisite = is_multisite();
			return($isMultisite);
		}
		
		
		/**
		 * 
		 * check if some db table exists
		 */
		public static function isDBTableExists($tableName){
			global $wpdb;
			
			if(empty($tableName))
				UniteFunctionsRev::throwError("Empty table name!!!");
			
			$sql = "show tables like '$tableName'";
			
			$table = $wpdb->get_var($sql);
			
			if($table == $tableName)
				return(true);
				
			return(false);
		}
		
		
		/**
		 * 
		 * get wordpress base path
		 */
		public static function getPathBase(){
			return ABSPATH;
		}
		
		/**
		 * 
		 * get wp-content path
		 */
		public static function getPathContent(){		
			if(self::isMultisite()){
				if(!defined("BLOGUPLOADDIR")){
					$pathBase = self::getPathBase();
					$pathContent = $pathBase."wp-content/";
				}else
				  $pathContent = BLOGUPLOADDIR;
			}else{
				$pathContent = WP_CONTENT_DIR;
				if(!empty($pathContent)){
					$pathContent .= "/";
				}
				else{
					$pathBase = self::getPathBase();
					$pathContent = $pathBase."wp-content/";
				}
			}
			
			return($pathContent);
		}
		
		/**
		 * 
		 * get content url
		 */
		public static function getUrlContent(){
		
			if(self::isMultisite() == false){	//without multisite
				$baseUrl = content_url()."/";
			}
			else{	//for multisite
				$arrUploadData = wp_upload_dir();
				$baseUrl = $arrUploadData["baseurl"]."/";
			}
			
			return($baseUrl);
			
		}
		
		/**
		 * 
		 * register widget (must be class)
		 */
		public static function registerWidget($widgetName){
			add_action('widgets_init', create_function('', 'return register_widget("'.$widgetName.'");'));
		}

		/**
		 * get image relative path from image url (from upload)
		 */
		public static function getImagePathFromURL($urlImage){
			
			$baseUrl = self::getUrlContent();
			$pathImage = str_replace($baseUrl, "", $urlImage);
			
			return($pathImage);
		}
		
		/**
		 * get image real path phisical on disk from url
		 */
		public static function getImageRealPathFromUrl($urlImage){
			$filepath = self::getImagePathFromURL($urlImage);
			$realPath = UniteFunctionsWPRev::getPathContent().$filepath;
			return($realPath);
		}
		
		
		/**
		 * 
		 * get image url from image path.
		 */
		public static function getImageUrlFromPath($pathImage){
			//protect from absolute url
			$pathLower = strtolower($pathImage);
			if(strpos($pathLower, "http://") !== false || strpos($pathLower, "www.") === 0)
				return($pathImage);
			
			$urlImage = self::getUrlContent().$pathImage;
			return($urlImage); 
		}

		/**
		 * 
		 * write settings language file for wp automatic scanning
		 */
		public static function writeSettingLanguageFile($filepath){
			$info = pathinfo($filepath);
			$path = UniteFunctionsRev::getVal($info, "dirname")."/";
			$filename = UniteFunctionsRev::getVal($info, "filename");
			$ext =  UniteFunctionsRev::getVal($info, "extension");
			$filenameOutput = "{$filename}_{$ext}_lang.php";
			$filepathOutput = $path.$filenameOutput;
			
			//load settings
			$settings = new UniteSettingsAdvancedRev();	
			$settings->loadXMLFile($filepath);
			$arrText = $settings->getArrTextFromAllSettings();
			
			$str = "";
			$str .= "<?php \n";
			foreach($arrText as $text){
				$text = str_replace('"', '\\"', $text);
				$str .= "_e(\"$text\",\"".REVSLIDER_TEXTDOMAIN."\"); \n";				
			}
			$str .= "?>";
			
			UniteFunctionsRev::writeFile($str, $filepathOutput);
		}

		
		/**
		 * 
		 * check the current post for the existence of a short code
		 */  
		public static function hasShortcode($shortcode = '') {  
		      
		    $post = get_post(get_the_ID());  
		      
		    if (empty($shortcode))   
		        return $found;
		        		        
		    $found = false; 
		        
		    if (stripos($post->post_content, '[' . $shortcode) !== false )    
		        $found = true;  
		       
		    return $found;  
		}  		
		
		
		/**
		 * 
		 * get attachment image url
		 */
		public static function getUrlAttachmentImage($thumbID,$size = self::THUMB_FULL){
			$arrImage = wp_get_attachment_image_src($thumbID,$size);
			if(empty($arrImage))
				return(false);
			$url = UniteFunctionsRev::getVal($arrImage, 0);
			return($url);
		}
		
		/**
		 * 
		 * get attachment image array by id and size
		 */
		public static function getAttachmentImage($thumbID,$size = self::THUMB_FULL){
			
			$arrImage = wp_get_attachment_image_src($thumbID,$size);
			if(empty($arrImage))
				return(false);
			
			$output = array();
			$output["url"] = UniteFunctionsRev::getVal($arrImage, 0);
			$output["width"] = UniteFunctionsRev::getVal($arrImage, 1);
			$output["height"] = UniteFunctionsRev::getVal($arrImage, 2);
			
			return($output);
		}
		
		
		/**
		 * 
		 * get post thumb id from post id
		 */
		public static function getPostThumbID($postID){
			$thumbID = get_post_thumbnail_id( $postID );
			return($thumbID);
		}

		
		/**
		 * 
		 * get url of post thumbnail
		 */
		public static function getUrlPostImage($postID,$size = self::THUMB_FULL){
			
			$post_thumbnail_id = get_post_thumbnail_id( $postID );
			if(empty($post_thumbnail_id))
				return("");
			
			$arrImage = wp_get_attachment_image_src($post_thumbnail_id,$size);
			if(empty($arrImage))
				return("");
			
			$urlImage = $arrImage[0];
			return($urlImage);
		}
		
		/**
		 * 
		 * get current language code
		 */
		public static function getCurrentLangCode(){
			$langTag = get_bloginfo("language");
			$data = explode("-", $langTag);
			$code = $data[0];
			return($code);
		}
		
	}
	
?>