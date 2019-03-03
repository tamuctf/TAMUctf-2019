<?php

	class UniteFunctionsWPBiz{
		
		public static $urlSite;
		public static $urlAdmin;
		
		const SORTBY_NONE = "none";
		const SORTBY_ID = "ID";
		const SORTBY_AUTHOR = "author";
		const SORTBY_TITLE = "title";
		const SORTBY_SLUG = "name";
		const SORTBY_DATE = "date";
		const SORTBY_LAST_MODIFIED = "modified";
		const SORTBY_RAND = "rand";
		const SORTBY_COMMENT_COUNT = "comment_count";
		const SORTBY_MENU_ORDER = "menu_order";
		
		const ORDER_DIRECTION_ASC = "ASC";
		const ORDER_DIRECTION_DESC = "DESC";
		
		const THUMB_SMALL = "thumbnail";
		const THUMB_MEDIUM = "medium";
		const THUMB_LARGE = "large";
		const THUMB_FULL = "full";
		
		const STATE_PUBLISHED = "publish";
		const STATE_DRAFT = "draft";
		
		
		/**
		 * 
		 * init the static variables
		 */
		public static function initStaticVars(){
			//UniteFunctionsBiz::printDefinedConstants();
			
			self::$urlSite = site_url();
			
			if(substr(self::$urlSite, -1) != "/")
				self::$urlSite .= "/";
			
			self::$urlAdmin = admin_url();			
			if(substr(self::$urlAdmin, -1) != "/")
				self::$urlAdmin .= "/";
				
			
		}
		
		
		/**
		 * 
		 * get sort by with the names
		 */
		public static function getArrSortBy(){
			$arr = array();
			$arr[self::SORTBY_ID] = "Post ID"; 
			$arr[self::SORTBY_DATE] = "Date";
			$arr[self::SORTBY_TITLE] = "Title"; 
			$arr[self::SORTBY_SLUG] = "Slug"; 
			$arr[self::SORTBY_AUTHOR] = "Author";
			$arr[self::SORTBY_LAST_MODIFIED] = "Last Modified"; 
			$arr[self::SORTBY_COMMENT_COUNT] = "Number Of Comments";
			$arr[self::SORTBY_RAND] = "Random";
			$arr[self::SORTBY_NONE] = "Unsorted";
			$arr[self::SORTBY_MENU_ORDER] = "Custom Order";
			return($arr);
		}
		
		
		/**
		 * 
		 * get array of sort direction
		 */
		public static function getArrSortDirection(){
			$arr = array();
			$arr[self::ORDER_DIRECTION_DESC] = "Descending";
			$arr[self::ORDER_DIRECTION_ASC] = "Ascending";
			return($arr);
		}
		
		
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
				UniteFunctionsBiz::throwError("Empty table name!!!");
			
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
		public static function getPathUploads(){		
			if(self::isMultisite()){
				if(!defined("BLOGUPLOADDIR")){
					$pathBase = self::getPathBase();
					$pathContent = $pathBase."wp-content/uploads/";
				}else
				  $pathContent = BLOGUPLOADDIR;
			}else{
				$pathContent = WP_CONTENT_DIR;
				if(!empty($pathContent)){
					$pathContent .= "/";
				}
				else{
					$pathBase = self::getPathBase();
					$pathContent = $pathBase."wp-content/uploads/";
				}
			}
			
			return($pathContent);
		}
		
		/**
		 * 
		 * get content url
		 */
		public static function getUrlUploads(){
		
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
			
			$baseUrl = self::getUrlUploads();
			$pathImage = str_replace($baseUrl, "", $urlImage);
			
			return($pathImage);
		}
		
		/**
		 * get image real path phisical on disk from url
		 */
		public static function getImageRealPathFromUrl($urlImage){
			$filepath = self::getImagePathFromURL($urlImage);
			$realPath = UniteFunctionsWPBiz::getPathUploads().$filepath;
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
			
			$urlImage = self::getUrlUploads().$pathImage;
			return($urlImage); 
		}
		
		
		/**	
		 * 
		 * get post categories list assoc - id / title
		 */
		public static function getCategoriesAssoc($taxonomy = "category"){
			
			if(strpos($taxonomy,",") !== false){
				$arrTax = explode(",", $taxonomy);
				$arrCats = array();
				foreach($arrTax as $tax){
					$cats = self::getCategoriesAssoc($tax);
					$arrCats = array_merge($arrCats,$cats);
				}
				
				return($arrCats);
			}	
			
			//$cats = get_terms("category");
			$args = array("taxonomy"=>$taxonomy);
			$cats = get_categories($args);
			
			$arrCats = array();
			foreach($cats as $cat){
				$numItems = $cat->count;
				$itemsName = "items";
				if($numItems == 1)
					$itemsName = "item";
					
				$title = $cat->name . " ($numItems $itemsName)";
				
				$id = $cat->cat_ID;
				$arrCats[$id] = $title;
			}
			return($arrCats);
		}
		
		
		/**
		 * 
		 * return post type title from the post type
		 */
		public static function getPostTypeTitle($postType){
			
			$objType = get_post_type_object($postType);
						
			if(empty($objType))
				return($postType);

			$title = $objType->labels->singular_name;
			
			return($title);
		}
		
		
		/**
		 * 
		 * get post type taxomonies
		 */
		public static function getPostTypeTaxomonies($postType){
			$arrTaxonomies = get_object_taxonomies(array( 'post_type' => $postType ), 'objects');
			
			$arrNames = array();
			foreach($arrTaxonomies as $key=>$objTax){			
				$arrNames[$objTax->name] = $objTax->labels->name;
			}
			
			return($arrNames);
		}
		
		
		/**
		 * 
		 * get all the post types including custom ones
		 */
		public static function getPostTypesAssoc(){
			 $arrBuiltIn = array(
			 	"post"=>"post",
			 	"page"=>"page",
			 );
			 
			 $arrCustomTypes = get_post_types(array('_builtin' => false));
			 
			 $arrPostTypes = array_merge($arrBuiltIn,$arrCustomTypes);
			 
			 //update label
			 foreach($arrPostTypes as $key=>$type){
				$arrPostTypes[$key] = self::getPostTypeTitle($type);			 		
			 }
			 
			 return($arrPostTypes);
		}
		
		
		/**
		 * 
		 * get the category data
		 */
		public static function getCategoryData($catID){
			$catData = get_category($catID);
			if(empty($catData))
				return($catData);
				
			$catData = (array)$catData;			
			return($catData);
		}
		
		
		/**
		 * 
		 * get posts by coma saparated posts
		 */
		public static function getPostsByIDs($strIDs){
			
			if(is_string($strIDs)){
				$arr = explode(",",$strIDs);
			}			
			
			$query = array(
				'post_type'=>"any",
				'post__in' => $arr
			);		
			
			$objQuery = new WP_Query($query);
			
			$arrPosts = $objQuery->posts;						
			
			//dmp($query);dmp("num posts: ".count($arrPosts));exit();
			
			foreach($arrPosts as $key=>$post){
					
				if(method_exists($post, "to_array"))
					$arrPosts[$key] = $post->to_array();
				else
					$arrPosts[$key] = (array)$post;
			}
			
			return($arrPosts);
		}
		
		
		/**
		 * 
		 * get posts by some category
		 * could be multiple
		 */
		public static function getPostsByCategory($catID,$sortBy = self::SORTBY_ID,$direction = self::ORDER_DIRECTION_DESC,$numPosts=-1,$postTypes="any",$taxonomies="category",$arrAddition = array()){
			
			//get post types
			if(strpos($postTypes,",") !== false){
				$postTypes = explode(",", $postTypes);
				if(array_search("any", $postTypes) !== false)
					$postTypes = "any";		
			}
			
			if(empty($postTypes))
				$postTypes = "any";
			
			if(strpos($catID,",") !== false)
				$catID = explode(",",$catID);
			else
				$catID = array($catID);
			
			$query = array(
				'order'=>$direction,
				'posts_per_page'=>$numPosts,
				'showposts'=>$numPosts,
				'post_type'=>$postTypes
			);		

			//add sort by (could be by meta)
			if(strpos($sortBy, "meta_num_") === 0){
				$metaKey = str_replace("meta_num_", "", $sortBy);
				$query["orderby"] = "meta_value_num";
				$query["meta_key"] = $metaKey;
			}else
			if(strpos($sortBy, "meta_") === 0){
				$metaKey = str_replace("meta_", "", $sortBy);
				$query["orderby"] = "meta_value";
				$query["meta_key"] = $metaKey;
			}else
				$query["orderby"] = $sortBy;
				
				
			if(!empty($taxonomies)){
			
				$taxQuery = array();
			
				//add taxomonies to the query
				if(strpos($taxonomies,",") !== false){	//multiple taxomonies
					$taxonomies = explode(",",$taxonomies);
					foreach($taxonomies as $taxomony){
						$taxArray = array(
							'taxonomy' => $taxomony,
							'field' => 'id',
							'terms' => $catID
						);			
						$taxQuery[] = $taxArray;
					}
				}else{		//single taxomony
					$taxArray = array(
						'taxonomy' => $taxonomies,
						'field' => 'id',
						'terms' => $catID
					);			
					$taxQuery[] = $taxArray;				
				}
							
				$taxQuery['relation'] = 'OR';
				
				$query['tax_query'] = $taxQuery;
			} //if exists taxanomies

						
			if(!empty($arrAddition))
				$query = array_merge($query, $arrAddition);
			
			$objQuery = new WP_Query($query);
			
			$arrPosts = $objQuery->posts;						
			
			//dmp($query);dmp("num posts: ".count($arrPosts));exit();
			
			foreach($arrPosts as $key=>$post){
					
				if(method_exists($post, "to_array"))
					$arrPosts[$key] = $post->to_array();
				else
					$arrPosts[$key] = (array)$post;
			}
			
			return($arrPosts);
		}
		
		
		/**
		 * 
		 * get single post
		 */
		public static function getPost($postID){
			$post = get_post($postID);
			if(empty($post))
				UniteFunctionsBiz::throwError("Post with id: $postID not found");
				
			$arrPost = $post->to_array();
			return($arrPost);
		}

		
		/**
		 * 
		 * update post state
		 */
		public static function updatePostState($postID,$state){
			$arrUpdate = array();
			$arrUpdate["ID"] = $postID;
			$arrUpdate["post_status"] = $state;
			
			wp_update_post($arrUpdate);
		}
		
		/**
		 * 
		 * update post menu order
		 */
		public static function updatePostOrder($postID,$order){
			$arrUpdate = array();
			$arrUpdate["ID"] = $postID;
			$arrUpdate["menu_order"] = $order;
			
			wp_update_post($arrUpdate);
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
		 * get post thumb id from post id
		 */
		public static function getPostThumbID($postID){
			$thumbID = get_post_thumbnail_id( $postID );
			return($thumbID);
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
			$output["url"] = UniteFunctionsBiz::getVal($arrImage, 0);
			$output["width"] = UniteFunctionsBiz::getVal($arrImage, 1);
			$output["height"] = UniteFunctionsBiz::getVal($arrImage, 2);
			
			return($output);
		}
		
		
		/**
		 * 
		 * get attachment image url
		 */
		public static function getUrlAttachmentImage($thumbID,$size = self::THUMB_FULL){
			$arrImage = wp_get_attachment_image_src($thumbID,$size);
			if(empty($arrImage))
				return(false);
			$url = UniteFunctionsBiz::getVal($arrImage, 0);
			return($url);
		}
		
		
		/**
		 * 
		 * get link of edit slides by category id
		 */
		public static function getUrlSlidesEditByCatID($catID){
			
			$url = self::$urlAdmin;
			$url .= "edit.php?s&post_status=all&post_type=post&action=-1&m=0&cat={$catID}&paged=1&mode=list&action2=-1";
			
			return($url);
		}
		
		/**
		 * 
		 * get edit post url
		 */
		public static function getUrlEditPost($postID){
			$url = self::$urlAdmin;
			$url .= "post.php?post={$postID}&action=edit";
			
			return($url);
		}
		
		
		/**
		 * 
		 * get new post url
		 */
		public static function getUrlNewPost(){
			$url = self::$urlAdmin;
			$url .= "post-new.php";
			return($url);
		}
		
		
		/**
		 * 
		 * delete post
		 */
		public static function deletePost($postID){
			$success = wp_delete_post($postID,true);
			if($success == false)
				UniteFunctionsBiz::throwError("Could not delete post: $postID");
		}
		
		/**
		 * 
		 * update post thumbnail
		 */
		public static function updatePostThumbnail($postID,$thumbID){
			set_post_thumbnail($postID, $thumbID);
		}
		
		
		/**
		 * 
		 * get intro from content
		 */
		public static function getIntroFromContent($text){
			$intro = "";
			if(!empty($text)){
				$arrExtended = get_extended($text);
				$intro = UniteFunctionsBiz::getVal($arrExtended, "main");
				
				/*
				if(strlen($text) != strlen($intro))
					$intro .= "...";
				*/
			}
			
			return($intro);
		}

		
		/**
		 * 
		 * get excerpt from post id
		 */
		public static function getExcerptById($postID, $limit=55){
			
			 $post = get_post($postID);	
			 
			 $excerpt = $post->post_excerpt;
			 $excerpt = trim($excerpt);
			 
			 $excerpt = trim($excerpt);
			 if(empty($excerpt))
				$excerpt = $post->post_content;			 
			 
			 $excerpt = strip_tags($excerpt,"<b><br><br/><i><strong><small>");
			 
			 $excerpt = UniteFunctionsBiz::getTextIntro($excerpt, $limit);
			 
			 return $excerpt;
		}		
				
		
		/**
		 * 
		 * get user display name from user id
		 */
		public static function getUserDisplayName($userID){
			
			$displayName =  get_the_author_meta('display_name', $userID);
			
			return($displayName);
		}
		
		
		/**
		 * 
		 * get categories by id's
		 */
		public static function getCategoriesByIDs($arrIDs){			
			
			if(empty($arrIDs))
				return(array());
				
			$strIDs = implode(",", $arrIDs);
			
			$args = "include=$strIDs";
		
			$arrCats = get_categories( $args );
			return($arrCats);
		}
		
		
		/**
		 * get categories list, copy the code from default wp functions
		 */
		public static function getCategoriesHtmlList($catIDs){
			global $wp_rewrite;
			
			//$catList = get_the_category_list( ",", "", $postID );
			
			$categories = self::getCategoriesByIDs($catIDs);
			
			$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
			
			$separator = ',';
			$parents='';
			
			$thelist = '';
			
				$i = 0;
				foreach ( $categories as $category ) {
					if ( 0 < $i )
						$thelist .= $separator;
					switch ( strtolower( $parents ) ) {
						case 'multiple':
							if ( $category->parent )
								$thelist .= get_category_parents( $category->parent, true, $separator );
							$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a>';
							break;
						case 'single':
							$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>';
							if ( $category->parent )
								$thelist .= get_category_parents( $category->parent, false, $separator );
							$thelist .= "$category->name</a>";
							break;
						case '':
						default:
							$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a>';
					}
					++$i;
				}
			
			
			return $thelist;
		}
		
		
		/**
		 * 
		 * get post tags html list
		 */
		public static function getTagsHtmlList($postID){
			$tagList = get_the_tag_list("",",","",$postID);
			return($tagList);
		}
		
		/**
		 * 
		 * convert date to the date format that the user chose.
		 */
		public static function convertPostDate($date){
			if(empty($date))
				return($date);
			$date = date_i18n(get_option('date_format'), strtotime($date));
			return($date);
		}
		
		/**
		 * 
		 * get assoc list of the taxonomies
		 */
		public static function getTaxonomiesAssoc(){
			$arr = get_taxonomies();
			unset($arr["post_tag"]);
			unset($arr["nav_menu"]);
			unset($arr["link_category"]);
			unset($arr["post_format"]);
			
			return($arr);
		}
		
		
		/**
		 * 
		 * get post types array with taxomonies
		 */
		public static function getPostTypesWithTaxomonies(){
			$arrPostTypes = self::getPostTypesAssoc();
			
			foreach($arrPostTypes as $postType=>$title){
				$arrTaxomonies = self::getPostTypeTaxomonies($postType);
				$arrPostTypes[$postType] = $arrTaxomonies;
			}
			
			return($arrPostTypes);
		}
		
		
		/**
		 * 
		 * get array of post types with categories (the taxonomies is between).
		 * get only those taxomonies that have some categories in it.
		 */
		public static function getPostTypesWithCats(){
			$arrPostTypes = self::getPostTypesWithTaxomonies();
			
			$arrPostTypesOutput = array();
			foreach($arrPostTypes as $name=>$arrTax){

				$arrTaxOutput = array();
				foreach($arrTax as $taxName=>$taxTitle){
					$cats = self::getCategoriesAssoc($taxName);
					if(!empty($cats))
						$arrTaxOutput[] = array(
								 "name"=>$taxName,
								 "title"=>$taxTitle,
								 "cats"=>$cats);
				}
								
				$arrPostTypesOutput[$name] = $arrTaxOutput;
				
			}
			
			return($arrPostTypesOutput);
		}
		
		
		/**
		 * 
		 * get array of all taxonomies with categories.
		 */
		public static function getTaxonomiesWithCats(){
						
			$arrTax = self::getTaxonomiesAssoc();
			$arrTaxNew = array();
			foreach($arrTax as $key=>$value){
				$arrItem = array();
				$arrItem["name"] = $key;
				$arrItem["title"] = $value;
				$arrItem["cats"] = self::getCategoriesAssoc($key);
				$arrTaxNew[$key] = $arrItem;
			}
			
			return($arrTaxNew);
		}
		
				
		
	}	//end of the class
	
	
	//init the static vars
	UniteFunctionsWPBiz::initStaticVars();
	
?>