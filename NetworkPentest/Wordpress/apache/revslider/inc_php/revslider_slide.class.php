<?php

	class RevSlide extends UniteElementsBaseRev{
		
		private $id;
		private $sliderID;
		private $slideOrder;
		
		private $imageUrl;
		private $imageID;		
		private $imageThumb;		
		private $imageFilepath;
		private $imageFilename;
		
		private $params;
		private $arrLayers;
		private $arrChildren = null;
		
		public function __construct(){
			parent::__construct();
		}
		
		/**
		 * 
		 * init slide by db record
		 */
		public function initByData($record){
			
			$this->id = $record["id"];
			$this->sliderID = $record["slider_id"];
			$this->slideOrder = $record["slide_order"];
			
			$params = $record["params"];
			$params = (array)json_decode($params);
			
			$layers = $record["layers"];
			$layers = (array)json_decode($layers);
			$layers = UniteFunctionsRev::convertStdClassToArray($layers);

			$imageID = UniteFunctionsRev::getVal($params, "image_id");
			
			//get image url and thumb url
			if(!empty($imageID)){
				$this->imageID = $imageID;
				
				$imageUrl = UniteFunctionsWPRev::getUrlAttachmentImage($imageID);
				if(empty($imageUrl))
					$imageUrl = UniteFunctionsRev::getVal($params, "image");
				
				$this->imageThumb = UniteFunctionsWPRev::getUrlAttachmentImage($imageID,UniteFunctionsWPRev::THUMB_MEDIUM);
				
			}else{
				$imageUrl = UniteFunctionsRev::getVal($params, "image");
			}
			
			//set image path, file and url
			$this->imageUrl = $imageUrl;
			
			$this->imageFilepath = UniteFunctionsWPRev::getImagePathFromURL($this->imageUrl);
		    $realPath = UniteFunctionsWPRev::getPathContent().$this->imageFilepath;
		    
		    if(file_exists($realPath) == false || is_file($realPath) == false)
		    	$this->imageFilepath = "";
		    
			$this->imageFilename = basename($this->imageUrl);
			
			$this->params = $params;
			$this->arrLayers = $layers;	
		}
		
		
		/**
		 * 
		 * init the slider by id
		 */
		public function initByID($slideid){
			UniteFunctionsRev::validateNumeric($slideid,"Slide ID");
			$slideid = $this->db->escape($slideid);
			$record = $this->db->fetchSingle(GlobalsRevSlider::$table_slides,"id=$slideid");
			
			$this->initByData($record);
		}
		
		
		/**
		 * 
		 * set children array
		 */
		public function setArrChildren($arrChildren){
			$this->arrChildren = $arrChildren;
		}
		
		
		/**
		 * 
		 * get children array
		 */
		public function getArrChildren(){
			
			$this->validateInited();
			
			if($this->arrChildren === null){
				$slider = new RevSlider();
				$slider->initByID($this->sliderID);
				$this->arrChildren = $slider->getArrSlideChildren($this->id);
			}
			
			return($this->arrChildren);				
		}
		
		/**
		 * 
		 * return if the slide is parent slide
		 */
		public function isParent(){
			$parentID = $this->getParam("parentid","");
			return(!empty($parentID));
		}
		
		
		/**
		 * 
		 * get slide language
		 */
		public function getLang(){
			$lang = $this->getParam("lang","all");
			return($lang);
		}
		
		/**
		 * 
		 * return parent slide. If the slide is parent, return this slide.
		 */
		public function getParentSlide(){
			$parentID = $this->getParam("parentid","");
			if(empty($parentID))
				return($this);
				
			$parentSlide = new RevSlide();
			$parentSlide->initByID($parentID);
			return($parentSlide);
		}
		
		/**
		 * 
		 * get array of children id's
		 */
		public function getArrChildrenIDs(){
			$arrChildren = $this->getArrChildren();
			$arrChildrenIDs = array();
			foreach($arrChildren as $child){
				$childID = $child->getID();
				$arrChildrenIDs[] = $childID;
			}
			
			return($arrChildrenIDs);
		}
		
		
		/**
		 * 
		 * get array of children array and languages, the first is current language.
		 */
		public function getArrChildrenLangs($includeParent = true){			
			$this->validateInited();
			$slideID = $this->id;
			
			if($includeParent == true){
				$lang = $this->getParam("lang","all");
				$arrOutput = array();
				$arrOutput[] = array("slideid"=>$slideID,"lang"=>$lang,"isparent"=>true);
			}
			
			$arrChildren = $this->getArrChildren();
			
			foreach($arrChildren as $child){
				$childID = $child->getID();
				$childLang = $child->getParam("lang","all");
				$arrOutput[] = array("slideid"=>$childID,"lang"=>$childLang,"isparent"=>false);
			}
			
			return($arrOutput);
		}
		
		/**
		 * 
		 * get children language codes (including current slide lang code)
		 */
		public function getArrChildLangCodes($includeParent = true){
			$arrLangsWithSlideID = $this->getArrChildrenLangs($includeParent);
			$arrLangCodes = array();
			foreach($arrLangsWithSlideID as $item){
				$lang = $item["lang"];
				$arrLangCodes[$lang] = $lang;
			}
			
			return($arrLangCodes);
		}
		
		
		/**
		 * 
		 * get slide ID
		 */
		public function getID(){
			return($this->id);
		}
		
		
		/**
		 * 
		 * get slide order
		 */
		public function getOrder(){
			$this->validateInited();
			return($this->slideOrder);
		}
		
		
		/**
		 * 
		 * get layers in json format
		 */
		public function getLayers(){
			$this->validateInited();
			return($this->arrLayers);
		}
		
		/**
		 * 
		 * modify layer links for export
		 */
		public function getLayersForExport(){
			$this->validateInited();
			$arrLayersNew = array();
			foreach($this->arrLayers as $key=>$layer){
				$imageUrl = UniteFunctionsRev::getVal($layer, "image_url");
				if(!empty($imageUrl))
					$layer["image_url"] = UniteFunctionsWPRev::getImagePathFromURL($layer["image_url"]);
					
				$arrLayersNew[] = $layer;
			}
			
			return($arrLayersNew);
		}
		
		/**
		 * 
		 * get params for export
		 */
		public function getParamsForExport(){
			$arrParams = $this->getParams();
			$urlImage = UniteFunctionsRev::getVal($arrParams, "image");
			if(!empty($urlImage))
				$arrParams["image"] = UniteFunctionsWPRev::getImagePathFromURL($urlImage);
			
			return($arrParams);
		}
		
		
		/**
		 * normalize layers text, and get layers
		 * 
		 */
		public function getLayersNormalizeText(){
			$arrLayersNew = array();
			foreach ($this->arrLayers as $key=>$layer){
				$text = $layer["text"];
				$text = addslashes($text);
				$layer["text"] = $text;
				$arrLayersNew[] = $layer;
			}
			
			return($arrLayersNew);
		}
		

		/**
		 * 
		 * get slide params
		 */
		public function getParams(){
			$this->validateInited();
			return($this->params);
		}

		
		/**
		 * 
		 * get parameter from params array. if no default, then the param is a must!
		 */
		function getParam($name,$default=null){
			
			if($default === null){
				if(!array_key_exists($name, $this->params))
					UniteFunctionsRev::throwError("The param <b>$name</b> not found in slide params.");
				$default = "";
			}
				
			return UniteFunctionsRev::getVal($this->params, $name,$default);
		}
		
		
		/**
		 * 
		 * get image filename
		 */
		public function getImageFilename(){
			return($this->imageFilename);
		}
		
		
		/**
		 * 
		 * get image filepath
		 */
		public function getImageFilepath(){
			return($this->imageFilepath);
		}
		
		
		/**
		 * 
		 * get image url
		 */
		public function getImageUrl(){
			return($this->imageUrl);
		}
		
		
		/**
		 * 
		 * get image id
		 */
		public function getImageID(){
			return($this->imageID);
		}
		
		/**
		 * 
		 * get thumb url
		 */
		public function getThumbUrl(){
			$thumbUrl = $this->imageUrl;
			if(!empty($this->imageThumb))
				$thumbUrl = $this->imageThumb;
				
			return($thumbUrl);
		}
		
		
		/**
		 * 
		 * get the slider id
		 */
		public function getSliderID(){
			return($this->sliderID);
		}
		
		/**
		 * 
		 * validate that the slider exists
		 */
		private function validateSliderExists($sliderID){
			$slider = new RevSlider();
			$slider->initByID($sliderID);
		}
		
		/**
		 * 
		 * validate that the slide is inited and the id exists.
		 */
		private function validateInited(){
			if(empty($this->id))
				UniteFunctionsRev::throwError("The slide is not inited!!!");
		}
		
		
		/**
		 * 
		 * create the slide (from image)
		 */
		public function createSlide($sliderID,$obj=""){
			
			$imageID = null;
			
			if(is_array($obj)){
				$urlImage = UniteFunctionsRev::getVal($obj, "url");
				$imageID = UniteFunctionsRev::getVal($obj, "id");
			}else{
				$urlImage = $obj;
			}
			
			//get max order
			$slider = new RevSlider();
			$slider->initByID($sliderID);
			$maxOrder = $slider->getMaxOrder();
			$order = $maxOrder+1;
			
			$params = array();
			if(!empty($urlImage)){
				$params["background_type"] = "image";
				$params["image"] = $urlImage;
				if(!empty($imageID))
					$params["image_id"] = $imageID;
					
			}else{	//create transparent slide
				
				$params["background_type"] = "trans";
			}
				
			$jsonParams = json_encode($params);
			
			$arrInsert = array("params"=>$jsonParams,
			           		   "slider_id"=>$sliderID,
								"slide_order"=>$order,
								"layers"=>""
						);
			
			$slideID = $this->db->insert(GlobalsRevSlider::$table_slides, $arrInsert);
			
			return($slideID);
		}
		
		/**
		 * 
		 * update slide image from data
		 */
		public function updateSlideImageFromData($data){
			
			$slideID = UniteFunctionsRev::getVal($data, "slide_id");			
			$this->initByID($slideID);
			
			$urlImage = UniteFunctionsRev::getVal($data, "url_image");
			UniteFunctionsRev::validateNotEmpty($urlImage);
			$imageID = UniteFunctionsRev::getVal($data, "image_id");
			
			$arrUpdate = array();
			$arrUpdate["image"] = $urlImage;			
			$arrUpdate["image_id"] = $imageID;
			
			$this->updateParamsInDB($arrUpdate);
			
			return($urlImage);
		}
		
		/**
		 * 
		 * update slide parameters in db
		 */
		private function updateParamsInDB($arrUpdate){
			$this->validateInited();
			$this->params = array_merge($this->params,$arrUpdate);
			$jsonParams = json_encode($this->params);
			
			$arrDBUpdate = array("params"=>$jsonParams);
			
			$this->db->update(GlobalsRevSlider::$table_slides,$arrDBUpdate,array("id"=>$this->id));
		}
		
		
		/**
		 * 
		 * update parent slideID 
		 */
		public function updateParentSlideID($parentID){
			$arrUpdate = array();
			$arrUpdate["parentid"] = $parentID;
			$this->updateParamsInDB($arrUpdate);
		}
		
		
		/**
		 * 
		 * sort layers by order
		 */
		private function sortLayersByOrder($layer1,$layer2){
			$layer1 = (array)$layer1;
			$layer2 = (array)$layer2;
			
			$order1 = UniteFunctionsRev::getVal($layer1, "order",1);
			$order2 = UniteFunctionsRev::getVal($layer2, "order",2);
			if($order1 == $order2)
				return(0);
			
			return($order1 > $order2);
		}
		
		
		/**
		 * 
		 * go through the layers and fix small bugs if exists
		 */
		private function normalizeLayers($arrLayers){
			
			usort($arrLayers,array($this,"sortLayersByOrder"));
			
			$arrLayersNew = array();
			foreach ($arrLayers as $key=>$layer){
				
				$layer = (array)$layer;
				
				//set type
				$type = UniteFunctionsRev::getVal($layer, "type","text");
				$layer["type"] = $type;
				
				//normalize position:
				$layer["left"] = round($layer["left"]);
				$layer["top"] = round($layer["top"]);
				
				//unset order
				unset($layer["order"]);
				
				//modify text
				$layer["text"] = stripcslashes($layer["text"]);
				
				$arrLayersNew[] = $layer;
			}
			
			return($arrLayersNew);
		}  
		
		
		
		/**
		 * 
		 * normalize params
		 */
		private function normalizeParams($params){
			
			$urlImage = UniteFunctionsRev::getVal($params, "image_url");
			
			//init the id if absent
			$params["image_id"] = UniteFunctionsRev::getVal($params, "image_id");
			
			$params["image"] = $urlImage;
			unset($params["image_url"]);
			
			if(isset($params["video_description"]))
				$params["video_description"] = UniteFunctionsRev::normalizeTextareaContent($params["video_description"]);
			
			return($params);
		}
		
		
		/**
		 * 
		 * update slide from data
		 * @param $data
		 */
		public function updateSlideFromData($data){
			
			$slideID = UniteFunctionsRev::getVal($data, "slideid");
			$this->initByID($slideID);						
			
			//treat params
			$params = UniteFunctionsRev::getVal($data, "params");
			$params = $this->normalizeParams($params);
			
			//preserve old data that not included in the given data
			$params = array_merge($this->params,$params);
			
			//treat layers
			$layers = UniteFunctionsRev::getVal($data, "layers");
			
			if(gettype($layers) == "string"){
				$layersStrip = stripslashes($layers);
				$layersDecoded = json_decode($layersStrip);
				if(empty($layersDecoded))
					$layersDecoded = json_decode($layers);
				
				$layers = UniteFunctionsRev::convertStdClassToArray($layersDecoded);
			}
			
			if(empty($layers) || gettype($layers) != "array")
				$layers = array();
			
			$layers = $this->normalizeLayers($layers);
			
			$arrUpdate = array();
			$arrUpdate["layers"] = json_encode($layers);
			$arrUpdate["params"] = json_encode($params);
			
			$this->db->update(GlobalsRevSlider::$table_slides,$arrUpdate,array("id"=>$this->id));
		}
		
		/**
		 * 
		 * delete slide by slideid
		 */
		public function deleteSlide(){
			$this->validateInited();
			
			$this->db->delete(GlobalsRevSlider::$table_slides,"id='{$this->id}'");
		}
		
		
		/**
		 * 
		 * delete slide children
		 */
		public function deleteChildren(){
			$this->validateInited();
			$arrChildren = $this->getArrChildren();
			foreach($arrChildren as $child)
				$child->deleteSlide();
		}
		
		
		/**
		 * 
		 * delete slide from data
		 */
		public function deleteSlideFromData($data){
			$slideID = UniteFunctionsRev::getVal($data, "slideID");
			$this->initByID($slideID);
			
			$this->deleteChildren();
			$this->deleteSlide();
		}
		
		
		/**
		 * 
		 * set params from client
		 */
		public function setParams($params){
			$params = $this->normalizeParams($params);
			$this->params = $params;
		}
		
		/**
		 * 
		 * set layers from client
		 */
		public function setLayers($layers){
			$layers = $this->normalizeLayers($layers);
			$this->arrLayers = $layers;
		}
		
		
		/**
		/* toggle slide state from data
		 */
		public function toggleSlideStatFromData($data){
			
			$slideID = UniteFunctionsRev::getVal($data, "slide_id");
			$this->initByID($slideID);
			
			$state = $this->getParam("state","published");
			$newState = ($state == "published")?"unpublished":"published";
			
			$arrUpdate = array();
			$arrUpdate["state"] = $newState;
			
			$this->updateParamsInDB($arrUpdate);
			
			return($newState);
		}
		
		
		/**
		 * 
		 * updatye slide language from data
		 */
		private function updateLangFromData($data){
						
			$slideID = UniteFunctionsRev::getVal($data, "slideid");
			$this->initByID($slideID);
			
			$lang = UniteFunctionsRev::getVal($data, "lang");
			
			$arrUpdate = array();
			$arrUpdate["lang"] = $lang;
			$this->updateParamsInDB($arrUpdate);
			
			$response = array();
			$response["url_icon"] = UniteWpmlRev::getFlagUrl($lang);
			$response["title"] = UniteWpmlRev::getLangTitle($lang);
			$response["operation"] = "update";
			
			return($response);
		}
		
		
		/**
		 * 
		 * add language (add slide that connected to current slide) from data
		 */
		private function addLangFromData($data){
			$sliderID = UniteFunctionsRev::getVal($data, "sliderid");
			$slideID = UniteFunctionsRev::getVal($data, "slideid");
			$lang = UniteFunctionsRev::getVal($data, "lang");
			
			//duplicate slide
			$slider = new RevSlider();
			$slider->initByID($sliderID);
			$newSlideID = $slider->duplicateSlide($slideID);
					
			//update new slide
			$this->initByID($newSlideID);
			
			$arrUpdate = array();
			$arrUpdate["lang"] = $lang;
			$arrUpdate["parentid"] = $slideID;
			$this->updateParamsInDB($arrUpdate);
						
			$urlIcon = UniteWpmlRev::getFlagUrl($lang);
			$title = UniteWpmlRev::getLangTitle($lang);
			
			$newSlide = new RevSlide();
			$newSlide->initByID($slideID);
			$arrLangCodes = $newSlide->getArrChildLangCodes();
			$isAll = UniteWpmlRev::isAllLangsInArray($arrLangCodes);
			
			$html = "<li>
								<img id=\"icon_lang_{$newSlideID}\" class=\"icon_slide_lang\" src=\"{$urlIcon}\" title=\"{$title}\" data-slideid=\"{$newSlideID}\" data-lang=\"{$lang}\">
								<div class=\"icon_lang_loader loader_round\" style=\"display:none\"></div>								
							</li>";
			
			$response = array();
			$response["operation"] = "add";
			$response["isAll"] = $isAll;
			$response["html"] = $html;
			
			return($response);
		}
		
		
		/**
		 * 
		 * delete slide from language menu data
		 */
		private function deleteSlideFromLangData($data){
			
			$slideID = UniteFunctionsRev::getVal($data, "slideid");
			$this->initByID($slideID);
			$this->deleteSlide();
			
			$response = array();
			$response["operation"] = "delete";
			return($response);
		}
		
		
		/**
		 * 
		 * add or update language from data
		 */
		public function doSlideLangOperation($data){
			
			$operation = UniteFunctionsRev::getVal($data, "operation");
			switch($operation){
				case "add":
					$response = $this->addLangFromData($data);	
				break;
				case "delete":
					$response = $this->deleteSlideFromLangData($data);
				break;
				case "update":
				default:
					$response = $this->updateLangFromData($data);
				break;
			}
			
			return($response);
		}
		
		
	}
	
?>