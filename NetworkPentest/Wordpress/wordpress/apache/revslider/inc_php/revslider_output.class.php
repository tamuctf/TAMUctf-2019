<?php

	class RevSliderOutput{
		
		private static $sliderSerial = 0;
		
		private $sliderHtmlID;
		private $sliderHtmlID_wrapper;
		private $slider;
		private $oneSlideMode = false;
		private $oneSlideData;
		private $previewMode = false;	//admin preview mode
		private $slidesNumIndex;
		private $sliderLang = null;
		
		/**
		 * 
		 * check the put in string
		 * return true / false if the put in string match the current page.
		 */
		public static function isPutIn($putIn,$emptyIsFalse = false){
			
			$putIn = strtolower($putIn);
			$putIn = trim($putIn);
			
			if($emptyIsFalse && empty($putIn))
				return(false);
			
			if($putIn == "homepage"){		//filter by homepage
				if(is_front_page() == false)
					return(false);
			}				
			else		//case filter by pages	
			if(!empty($putIn)){
				$arrPutInPages = array();
				$arrPagesTemp = explode(",", $putIn);
				foreach($arrPagesTemp as $page){
					if(is_numeric($page) || $page == "homepage")
						$arrPutInPages[] = $page;
				}
				if(!empty($arrPutInPages)){
					
					//get current page id
					$currentPageID = "";
					if(is_front_page() == true)
						$currentPageID = "homepage";
					else{
						global $post;
						if(isset($post->ID))
							$currentPageID = $post->ID;
					}
						
					//do the filter by pages
					if(array_search($currentPageID, $arrPutInPages) === false) 
						return(false);
				}
			}
			
			return(true);
		}
		
		
		/**
		 * 
		 * put the rev slider slider on the html page.
		 * @param $data - mixed, can be ID ot Alias.
		 */
		public static function putSlider($sliderID,$putIn=""){
			
			$isPutIn = self::isPutIn($putIn);
			if($isPutIn == false)
				return(false);
			
			$output = new RevSliderOutput();
			$output->putSliderBase($sliderID);
			
			$slider = $output->getSlider();
			return($slider);
		}
		
		
		/**
		 * 
		 * set language
		 */
		public function setLang($lang){
			$this->sliderLang = $lang;
		}
		
		/**
		 * 
		 * set one slide mode for preview
		 */
		public function setOneSlideMode($data){
			$this->oneSlideMode = true;
			$this->oneSlideData = $data;
		}
		
		/**
		 * 
		 * set preview mode
		 */
		public function setPreviewMode(){
			$this->previewMode = true;
		}
		
		/**
		 * 
		 * get the last slider after the output
		 */
		public function getSlider(){
			return($this->slider);
		}
		
		/**
		 * 
		 * get slide full width video data
		 */
		private function getSlideFullWidthVideoData(RevSlide $slide){
			
			$response = array("found"=>false);
			
			//deal full width video:
			$enableVideo = $slide->getParam("enable_video","false");
			if($enableVideo != "true")
				return($response);
				
			$videoID = $slide->getParam("video_id","");
			$videoID = trim($videoID);
			
			if(empty($videoID))
				return($response);
				
			$response["found"] = true;
			
			$videoType = is_numeric($videoID)?"vimeo":"youtube";
			$videoAutoplay = $slide->getParam("video_autoplay");
			$videoNextslide = $slide->getParam("video_nextslide");
			
			$response["type"] = $videoType;
			$response["videoID"] = $videoID;
			$response["autoplay"] = UniteFunctionsRev::strToBool($videoAutoplay);
			$response["nextslide"] = UniteFunctionsRev::strToBool($videoNextslide);
			
			return($response);
		}
		
		/**
		 * 
		 * put full width video layer
		 */
		private function putFullWidthVideoLayer($videoData){
			if($videoData["found"] == false)
				return(false);
			
			$autoplay = UniteFunctionsRev::boolToStr($videoData["autoplay"]);
			$nextslide = UniteFunctionsRev::boolToStr($videoData["nextslide"]);
			
			$htmlParams = 'data-x="0" data-y="0" data-speed="500" data-start="10" data-easing="easeOutBack"';
			
			$videoID = $videoData["videoID"];
			
			if($videoData["type"] == "youtube"):	//youtube
				?>	
				<div class="tp-caption fade fullscreenvideo" data-nextslideatend="<?php echo $nextslide?>" data-autoplay="<?php echo $autoplay?>" <?php echo $htmlParams?>><iframe src="http://www.youtube.com/embed/<?php echo $videoID?>?hd=1&amp;wmode=opaque&amp;controls=1&amp;showinfo=0;rel=0;" width="100%" height="100%"></iframe></div>				
				<?php 
			else:									//vimeo
				?>
				<div class="tp-caption fade fullscreenvideo" data-nextslideatend="<?php echo $nextslide?>" data-autoplay="<?php echo $autoplay?>" <?php echo $htmlParams?>><iframe src="http://player.vimeo.com/video/<?php echo $videoID?>?title=0&amp;byline=0&amp;portrait=0;api=1" width="100%" height="100%"></iframe></div>
				<?php
			endif;
		}
		
		/**
		 * 
		 * filter the slides for one slide preview
		 */
		private function filterOneSlide($slides){
			
			$oneSlideID = $this->oneSlideData["slideid"];
			$oneSlideParams = UniteFunctionsRev::getVal($this->oneSlideData, "params");		 	
			$oneSlideLayers = UniteFunctionsRev::getVal($this->oneSlideData, "layers");
			
			if(gettype($oneSlideParams) == "object")
				$oneSlideParams = (array)$oneSlideParams;

			if(gettype($oneSlideLayers) == "object")
				$oneSlideLayers = (array)$oneSlideLayers;
				
			if(!empty($oneSlideLayers))
				$oneSlideLayers = UniteFunctionsRev::convertStdClassToArray($oneSlideLayers);
			
			$newSlides = array();
			foreach($slides as $slide){				
				$slideID = $slide->getID();
				
				if($slideID == $oneSlideID){
										
					if(!empty($oneSlideParams))
						$slide->setParams($oneSlideParams);
					
					if(!empty($oneSlideLayers))
						$slide->setLayers($oneSlideLayers);
					
					$newSlides[] = $slide;	//add 2 slides
					$newSlides[] = $slide;
				}
			}
			
			return($newSlides);
		}
		
		
		/**
		 * 
		 * put the slider slides
		 */
		private function putSlides(){
			
			$sliderType = $this->slider->getParam("slider_type");
			
			$publishedOnly = true;
			if($this->previewMode == true && $this->oneSlideMode == true){	
				$previewSlideID = UniteFunctionsRev::getVal($this->oneSlideData, "slideid");
				$previewSlide = new RevSlide();
				$previewSlide->initByID($previewSlideID);
				$slides = array($previewSlide);
				
			}else{
				$slides = $this->slider->getSlidesForOutput($publishedOnly,$this->sliderLang);
			}
						
			$this->slidesNumIndex = $this->slider->getSlidesNumbersByIDs(true);
			
			if(empty($slides)):
				?>
				<div class="no-slides-text">
					No slides found, please add some slides
				</div>
				<?php 
			endif;
			
			$thumbWidth = $this->slider->getParam("thumb_width",100);
			$thumbHeight = $this->slider->getParam("thumb_height",50);
			
			$slideWidth = $this->slider->getParam("width",900);
			$slideHeight = $this->slider->getParam("height",300);
			
			$navigationType = $this->slider->getParam("navigaion_type","none"); 
			$isThumbsActive = ($navigationType == "thumb")?true:false;
			
			//for one slide preview
			if($this->oneSlideMode == true)				
				$slides = $this->filterOneSlide($slides);
			
			?>
				<ul>
			<?php
						
			foreach($slides as $index => $slide){
				
				$params = $slide->getParams();
				
				//check if date is set
				$date_from = $slide->getParam("date_from","");
				$date_to = $slide->getParam("date_to","");
				
				if($date_from != ""){
					$date_from = strtotime($date_from);
					if(time() < $date_from) continue;
				}
				
				if($date_to != ""){
					$date_to = strtotime($date_to);
					if(time() > $date_to) continue;
				}
				
				$transition = $slide->getParam("slide_transition","random");
					
				$slotAmount = $slide->getParam("slot_amount","7");
								
				$urlSlideImage = $slide->getImageUrl();
				
				//get image alt
				$imageFilename = $slide->getImageFilename();
				$info = pathinfo($imageFilename);
				$alt = $info["filename"];
				
				
				//get thumb url
				$htmlThumb = "";
				if($isThumbsActive == true){
					$urlThumb = $slide->getParam("slide_thumb","");
					
					if(empty($urlThumb)){	//try to get resized thumb
						$pathThumb = $slide->getImageFilepath();
						if(!empty($pathThumb))
							$urlThumb = UniteBaseClassRev::getImageUrl($pathThumb,$thumbWidth,$thumbHeight,true);
					}
					
					//if not - put regular image:
					if(empty($urlThumb))						
						$urlThumb = $slide->getImageUrl();
					
					$htmlThumb = 'data-thumb="'.$urlThumb.'" ';
				}
				
				//get link
				$htmlLink = "";
				$enableLink = $slide->getParam("enable_link","false");
				if($enableLink == "true"){
					$linkType = $slide->getParam("link_type","regular");
					switch($linkType){
						
						//---- normal link
						
						default:		
						case "regular":
							$link = $slide->getParam("link","");
							$linkOpenIn = $slide->getParam("link_open_in","same");
							$htmlTarget = "";
							if($linkOpenIn == "new")
								$htmlTarget = ' data-target="_blank"';
							$htmlLink = "data-link=\"$link\" $htmlTarget ";	
						break;		
						
						//---- link to slide
						
						case "slide":
							$slideLink = UniteFunctionsRev::getVal($params, "slide_link");
							if(!empty($slideLink) && $slideLink != "nothing"){
								//get slide index from id
								if(is_numeric($slideLink))
									$slideLink = UniteFunctionsRev::getVal($this->slidesNumIndex, $slideLink);
								
								if(!empty($slideLink))
									$htmlLink = "data-link=\"slide\" data-linktoslide=\"$slideLink\" ";
							}
						break;
					}
					
					//set link position:
					$linkPos = UniteFunctionsRev::getVal($params, "link_pos","front");
					if($linkPos == "back")
						$htmlLink .= ' data-slideindex="back"';	
				}
				
				//set delay
				$htmlDelay = "";
				$delay = $slide->getParam("delay","");
				if(!empty($delay) && is_numeric($delay))
					$htmlDelay = "data-delay=\"$delay\" ";
				
				//get duration
				$htmlDuration = "";
				$duration = $slide->getParam("transition_duration","");
				if(!empty($duration) && is_numeric($duration))
					$htmlDuration = "data-masterspeed=\"$duration\" ";
				
				//get rotation
				$htmlRotation = "";
				$rotation = $slide->getParam("transition_rotation","");
				if(!empty($rotation)){
					$rotation = (int)$rotation;
					if($rotation != 0){
						if($rotation > 720 && $rotation != 999)
							$rotation = 720;
						if($rotation < -720)
							$rotation = -720;
					}
					$htmlRotation = "data-rotate=\"$rotation\" ";
				}
				
				$fullWidthVideoData = $this->getSlideFullWidthVideoData($slide);
				
				//set full width centering.
				$htmlImageCentering = "";
				$fullWidthCentering = $slide->getParam("fullwidth_centering","false");
				if($sliderType == "fullwidth" && $fullWidthCentering == "true")
					$htmlImageCentering = ' data-fullwidthcentering="on"';
					
				//set first slide transition
				$htmlFirstTrans = "";
				$startWithSlide = $this->slider->getStartWithSlideSetting();
				
				if($index == $startWithSlide){
					$firstTransActive = $this->slider->getParam("first_transition_active","false");
					if($firstTransActive == "true"){
						
						$firstTransition = $this->slider->getParam("first_transition_type","fade");						
						$htmlFirstTrans .= " data-fstransition=\"$firstTransition\"";
						
						$firstDuration = $this->slider->getParam("first_transition_duration","300");
						if(!empty($firstDuration) && is_numeric($firstDuration))
							$htmlFirstTrans .= " data-fsmasterspeed=\"$firstDuration\"";
							
						$firstSlotAmount = $this->slider->getParam("first_transition_slot_amount","7");
						if(!empty($firstSlotAmount) && is_numeric($firstSlotAmount))						
						$htmlFirstTrans .= " data-fsslotamount=\"$firstSlotAmount\"";
							
					}
				}//first trans
				
				$htmlParams = $htmlDuration.$htmlLink.$htmlThumb.$htmlDelay.$htmlRotation.$htmlFirstTrans;
				
				$bgType = $slide->getParam("background_type","image");
				
				$styleImage = "";
				$urlImageTransparent = UniteBaseClassRev::$url_plugin."images/transparent.png";
				
				switch($bgType){
					case "trans":
						$urlSlideImage = $urlImageTransparent;
					break;
					case "solid":
						$urlSlideImage = $urlImageTransparent;
						$slideBGColor = $slide->getParam("slide_bg_color","#d0d0d0");
						$styleImage = "style='background-color:{$slideBGColor}'";
					break;
				}
				
				//additional params
				$imageAddParams = "";
				$lazyLoad = $this->slider->getParam("lazy_load","off");
				if($lazyLoad == "on"){
					$imageAddParams .= "data-lazyload=\"$urlSlideImage\"";
					$urlSlideImage = UniteBaseClassRev::$url_plugin."images/dummy.png";
				}
				
				$imageAddParams .= $htmlImageCentering;
				
				//Html
				?>
					<li data-transition="<?php echo $transition?>" data-slotamount="<?php echo $slotAmount?>" <?php echo $htmlParams?>>
						<img src="<?php echo $urlSlideImage?>" <?php echo $styleImage?> alt="<?php echo $alt?>" <?php echo $imageAddParams?>>
						<?php	//put video:
							if($fullWidthVideoData["found"] == true)	//backward compatability
								$this->putFullWidthVideoLayer($fullWidthVideoData);
								
							$this->putCreativeLayer($slide)
						?>
					</li>
				<?php 
			}	//get foreach
			
			?>
				</ul>
			<?php
		}
		
		
		/**
		 * 
		 * get html5 layer html from data
		 */
		private function getHtml5LayerHtml($data){
			
			
			$urlPoster = UniteFunctionsRev::getVal($data, "urlPoster");
			$urlMp4 = UniteFunctionsRev::getVal($data, "urlMp4");
			$urlWebm = UniteFunctionsRev::getVal($data, "urlWebm");
			$urlOgv = UniteFunctionsRev::getVal($data, "urlOgv");
			$width = UniteFunctionsRev::getVal($data, "width");
			$height = UniteFunctionsRev::getVal($data, "height");
			
			$fullwidth = UniteFunctionsRev::getVal($data, "fullwidth");
			$fullwidth = UniteFunctionsRev::strToBool($fullwidth);
			
			if($fullwidth == true){
				$width = "100%";
				$height = "100%";
			}
			
			$htmlPoster = "";
			if(!empty($urlPoster))
				$htmlPoster = "poster='{$urlPoster}'";
				
			$htmlMp4 = "";
			if(!empty($urlMp4))
				$htmlMp4 = "<source src='{$urlMp4}' type='video/mp4' />";

			$htmlWebm = "";
			if(!empty($urlWebm))
				$htmlWebm = "<source src='{$urlWebm}' type='video/webm' />";
				
			$htmlOgv = "";
			if(!empty($urlOgv))
				$htmlOgv = "<source src='{$urlOgv}' type='video/ogg' />";
					
			$html =	"<video class=\"video-js vjs-default-skin\" controls preload=\"none\" width=\"{$width}\" height=\"{$height}\" \n";
	   		$html .=  $htmlPoster ." data-setup=\"{}\"> \n";
	        $html .=  $htmlMp4."\n";
	        $html .=  $htmlWebm."\n";
	        $html .=  $htmlOgv."\n";
			$html .=  "</video>\n";
			
			return($html);
		}
		
		
		/**
		 * 
		 * put creative layer
		 */
		private function putCreativeLayer(RevSlide $slide){
			$layers = $slide->getLayers();
						
			if(empty($layers))
				return(false);
			?>
				<?php foreach($layers as $layer):
						
					$type = UniteFunctionsRev::getVal($layer, "type","text");
					
					//set if video full screen
					$isFullWidthVideo = false;
					if($type == "video"){
						$videoData = UniteFunctionsRev::getVal($layer, "video_data");
						if(!empty($videoData)){
							$videoData = (array)$videoData;
							$isFullWidthVideo = UniteFunctionsRev::getVal($videoData, "fullwidth");
							$isFullWidthVideo = UniteFunctionsRev::strToBool($isFullWidthVideo);
						}else
							$videoData = array();
					}
					
					
					$class = UniteFunctionsRev::getVal($layer, "style");
					$animation = UniteFunctionsRev::getVal($layer, "animation","fade");
					
					//set output class:
					$outputClass = "tp-caption ". trim($class);
						$outputClass = trim($outputClass) . " ";
						
					$outputClass .= trim($animation);
					
					$left = UniteFunctionsRev::getVal($layer, "left",0);
					$top = UniteFunctionsRev::getVal($layer, "top",0);
					$speed = UniteFunctionsRev::getVal($layer, "speed",300);
					$time = UniteFunctionsRev::getVal($layer, "time",0);
					$easing = UniteFunctionsRev::getVal($layer, "easing","easeOutExpo");
					$randomRotate = UniteFunctionsRev::getVal($layer, "random_rotation","false");
					$randomRotate = UniteFunctionsRev::boolToStr($randomRotate);
					
					$text = UniteFunctionsRev::getVal($layer, "text");
					
					$htmlVideoAutoplay = "";
					$htmlVideoNextSlide = "";
					
					//set html:
					$html = "";
					switch($type){
						default:
						case "text":						
							$html = $text;
							$html = do_shortcode($html);
						break;
						case "image":
							$urlImage = UniteFunctionsRev::getVal($layer, "image_url");
							$html = '<img src="'.$urlImage.'" alt="'.$text.'">';
							$imageLink = UniteFunctionsRev::getVal($layer, "link","");
							if(!empty($imageLink)){
								$openIn = UniteFunctionsRev::getVal($layer, "link_open_in","same");

								$target = "";
								if($openIn == "new")
									$target = ' target="_blank"';
									
								$html = '<a href="'.$imageLink.'"'.$target.'>'.$html.'</a>';
							}								
						break;
						case "video":
							$videoType = trim(UniteFunctionsRev::getVal($layer, "video_type"));
							$videoID = trim(UniteFunctionsRev::getVal($layer, "video_id"));
							$videoWidth = trim(UniteFunctionsRev::getVal($layer, "video_width"));
							$videoHeight = trim(UniteFunctionsRev::getVal($layer, "video_height"));	
							$videoArgs = trim(UniteFunctionsRev::getVal($layer, "video_args"));
							
							if($isFullWidthVideo == true){
								$videoWidth = "100%";
								$videoHeight = "100%";
							}
							
							switch($videoType){
								case "youtube":
									if(empty($videoArgs))
										$videoArgs = GlobalsRevSlider::DEFAULT_YOUTUBE_ARGUMENTS;
									$html = "<iframe src='http://www.youtube.com/embed/{$videoID}?{$videoArgs}' width='{$videoWidth}' height='{$videoHeight}' style='width:{$videoWidth}px;height:{$videoHeight}px;'></iframe>";
									
								break;
								case "vimeo":
									if(empty($videoArgs))
										$videoArgs = GlobalsRevSlider::DEFAULT_VIMEO_ARGUMENTS;
									$html = "<iframe src='http://player.vimeo.com/video/{$videoID}?{$videoArgs}' width='{$videoWidth}' height='{$videoHeight}' style='width:{$videoWidth}px;height:{$videoHeight}px;'></iframe>";
								break;
								case "html5":
									$html = $this->getHtml5LayerHtml($videoData);									
								break;
								default:
									UniteFunctionsRev::throwError("wrong video type: $videoType");
								break;
							}
							
							
							//set video autoplay, with backward compatability
							if(array_key_exists("autoplay", $videoData))
								$videoAutoplay = UniteFunctionsRev::getVal($videoData, "autoplay");
							else	//backword compatability
								$videoAutoplay = UniteFunctionsRev::getVal($layer, "video_autoplay");
							
							$videoAutoplay = UniteFunctionsRev::strToBool($videoAutoplay);
							
							if($videoAutoplay == true)
								$htmlVideoAutoplay = ' data-autoplay="true"';								
							
							$videoNextSlide = UniteFunctionsRev::getVal($videoData, "nextslide");
							$videoNextSlide = UniteFunctionsRev::strToBool($videoNextSlide);
							
							if($videoNextSlide == true)
								$htmlVideoNextSlide = ' data-nextslideatend="true"';								
								
						break;
					}
					
					//handle end transitions:
					$endTime = trim(UniteFunctionsRev::getVal($layer, "endtime"));
					$htmlEnd = "";
					if(!empty($endTime)){
						$htmlEnd = "data-end=\"$endTime\"";
						$endSpeed = trim(UniteFunctionsRev::getVal($layer, "endspeed"));
						if(!empty($endSpeed))
							 $htmlEnd .= " data-endspeed=\"$endSpeed\"";
							 
						$endEasing = trim(UniteFunctionsRev::getVal($layer, "endeasing"));
						if(!empty($endSpeed) && $endEasing != "nothing")
							 $htmlEnd .= " data-endeasing=\"$endEasing\"";
						
						//add animation to class
						$endAnimation = trim(UniteFunctionsRev::getVal($layer, "endanimation"));
						if(!empty($endAnimation) && $endAnimation != "auto")
							$outputClass .= " ".$endAnimation;	
					}
					
					//slide link
					$htmlLink = "";
					$slideLink = UniteFunctionsRev::getVal($layer, "link_slide");
					if(!empty($slideLink) && $slideLink != "nothing" && $slideLink != "scroll_under"){
						//get slide index from id
						if(is_numeric($slideLink))
							$slideLink = UniteFunctionsRev::getVal($this->slidesNumIndex, $slideLink);
						
						if(!empty($slideLink))
							$htmlLink = " data-linktoslide=\"$slideLink\"";
					}
					
					//scroll under the slider
					if($slideLink == "scroll_under"){
						$outputClass .= " tp-scrollbelowslider";
						$scrollUnderOffset = UniteFunctionsRev::getVal($layer, "scrollunder_offset");
						if(!empty($scrollUnderOffset))
							$htmlLink .= " data-scrolloffset=\"{$scrollUnderOffset}\"";
					}					
					
					//hidden under resolution
					$htmlHidden = "";
					$layerHidden = UniteFunctionsRev::getVal($layer, "hiddenunder");
					if($layerHidden == "true" || $layerHidden == "1")
						$htmlHidden = ' data-captionhidden="on"';
					
					$htmlParams = $htmlEnd.$htmlLink.$htmlVideoAutoplay.$htmlVideoNextSlide.$htmlHidden;
					
					//set positioning options
					
					$alignHor = UniteFunctionsRev::getVal($layer,"align_hor","left");
					$alignVert = UniteFunctionsRev::getVal($layer, "align_vert","top");
					
					$htmlPosX = "";
					$htmlPosY = "";
					switch($alignHor){
						default:
						case "left":
							$htmlPosX = "data-x=\"{$left}\" \n";
						break;
						case "center":
							$htmlPosX = "data-x=\"center\" data-hoffset=\"{$left}\" \n";
						break;
						case "right":
							$left = (int)$left*-1;
							$htmlPosX = "data-x=\"right\" data-hoffset=\"{$left}\" \n";
						break;
					}
					
					switch($alignVert){
						default:
						case "top":
							$htmlPosY = "data-y=\"{$top}\" ";
						break;
						case "middle":
							$htmlPosY = "data-y=\"center\" data-voffset=\"{$top}\" ";
						break;
						case "bottom":
							$top = (int)$top*-1;
							$htmlPosY = "data-y=\"bottom\" data-voffset=\"{$top}\" ";
						break;						
					}
					
					//set corners
					$htmlCorners = "";
					
					if($type == "text"){
						$cornerLeft = UniteFunctionsRev::getVal($layer, "corner_left");
						$cornerRight = UniteFunctionsRev::getVal($layer, "corner_right");
						switch($cornerLeft){
							case "curved":
								$htmlCorners .= "<div class='frontcorner'></div>";
							break;
							case "reverced":
								$htmlCorners .= "<div class='frontcornertop'></div>";							
							break;
						}
						
						switch($cornerRight){
							case "curved":
								$htmlCorners .= "<div class='backcorner'></div>";
							break;
							case "reverced":
								$htmlCorners .= "<div class='backcornertop'></div>";							
							break;
						}
					
					//add resizeme class
					$resizeme = UniteFunctionsRev::getVal($layer, "resizeme");
					if($resizeme == "true" || $resizeme == "1")
						$outputClass .= ' tp-resizeme';
						
					}//end text related layer
					
					//make some modifications for the full screen video
					if($isFullWidthVideo == true){
						$htmlPosX = "data-x=\"0\""."\n";
						$htmlPosY = "data-y=\"0\""."\n";
						$outputClass .= " fullscreenvideo";
					}
					
				?>
				<div class="<?php echo $outputClass?>"
					 <?php echo $htmlPosX?>
					 <?php echo $htmlPosY?>
					 data-speed="<?php echo $speed?>" 
					 data-start="<?php echo $time?>" 
					 data-easing="<?php echo $easing?>" <?php echo $htmlParams?> ><?php echo $html?>
					 <?php echo $htmlCorners?>
					 </div>
				
				<?php 
				
			endforeach;
		}
		
		/**
		 * 
		 * put slider javascript
		 */
		private function putJS(){
			
			$params = $this->slider->getParams();
			$sliderType = $this->slider->getParam("slider_type");
			$optFullWidth = ($sliderType == "fullwidth")?"on":"off";
			
			$optFullScreen = "off";
			if($sliderType == "fullscreen"){
				$optFullWidth = "on";
				$optFullScreen = "on";
			}
			
			$noConflict = $this->slider->getParam("jquery_noconflict","on");
			
			//set thumb amount
			$numSlides = $this->slider->getNumSlides(true);
			$thumbAmount = (int)$this->slider->getParam("thumb_amount","5");
			if($thumbAmount > $numSlides)
				$thumbAmount = $numSlides;
				
			
			//get stop slider options
			 $stopSlider = $this->slider->getParam("stop_slider","off");
			 $stopAfterLoops = $this->slider->getParam("stop_after_loops","0");
			 $stopAtSlide = $this->slider->getParam("stop_at_slide","2");
			 
			 if($stopSlider == "off"){
				 $stopAfterLoops = "-1";
				 $stopAtSlide = "-1";
			 }
			
			// set hide navigation after
			$hideThumbs = $this->slider->getParam("hide_thumbs","200");
			if(is_numeric($hideThumbs) == false)
				$hideThumbs = "0";
			else{
				$hideThumbs = (int)$hideThumbs;
				if($hideThumbs < 10)
					$hideThumbs = 10;
			}
			
			$alwaysOn = $this->slider->getParam("navigaion_always_on","false");
			if($alwaysOn == "true")
				$hideThumbs = "0";
			
			$sliderID = $this->slider->getID();
			
			//treat hide slider at limit
			$hideSliderAtLimit = $this->slider->getParam("hide_slider_under","0",RevSlider::VALIDATE_NUMERIC);
			if(!empty($hideSliderAtLimit))
				$hideSliderAtLimit++;

			//this option is disabled in full width slider
			if($sliderType == "fullwidth")
				$hideSliderAtLimit = "0";
			
			$hideCaptionAtLimit = $this->slider->getParam("hide_defined_layers_under","0",RevSlider::VALIDATE_NUMERIC);;
			if(!empty($hideCaptionAtLimit))
				$hideCaptionAtLimit++;
			
			$hideAllCaptionAtLimit = $this->slider->getParam("hide_all_layers_under","0",RevSlider::VALIDATE_NUMERIC);;
			if(!empty($hideAllCaptionAtLimit))
				$hideAllCaptionAtLimit++;
			
			//start_with_slide
			$startWithSlide = $this->slider->getStartWithSlideSetting();
			
	 	  //modify navigation type (backward compatability)
			$arrowsType = $this->slider->getParam("navigation_arrows","nexttobullets");
			switch($arrowsType){
				case "verticalcentered":
					$arrowsType = "solo";
				break;
			}
			
			$videoJsPath = UniteBaseClassRev::$url_plugin."rs-plugin/videojs/";			

			?>
			
			<script type="text/javascript">

				var tpj=jQuery;
				
				<?php if($noConflict == "on"):?>
					tpj.noConflict();
				<?php endif;?>
				
				var revapi<?php echo $sliderID?>;
				
				tpj(document).ready(function() {
				
				if (tpj.fn.cssOriginal != undefined)
					tpj.fn.css = tpj.fn.cssOriginal;
				
				if(tpj('#<?php echo $this->sliderHtmlID?>').revolution == undefined)
					revslider_showDoubleJqueryError('#<?php echo $this->sliderHtmlID?>');
				else
				   revapi<?php echo $sliderID?> = tpj('#<?php echo $this->sliderHtmlID?>').show().revolution(
					{
						delay:<?php echo $this->slider->getParam("delay","9000",RevSlider::FORCE_NUMERIC)?>,
						startwidth:<?php echo $this->slider->getParam("width","900")?>,
						startheight:<?php echo $this->slider->getParam("height","300")?>,
						hideThumbs:<?php echo $hideThumbs?>,
						
						thumbWidth:<?php echo $this->slider->getParam("thumb_width","100",RevSlider::FORCE_NUMERIC)?>,
						thumbHeight:<?php echo $this->slider->getParam("thumb_height","50",RevSlider::FORCE_NUMERIC)?>,
						thumbAmount:<?php echo $thumbAmount?>,
						
						navigationType:"<?php echo $this->slider->getParam("navigaion_type","none")?>",
						navigationArrows:"<?php echo $arrowsType?>",
						navigationStyle:"<?php echo $this->slider->getParam("navigation_style","round")?>",
						
						touchenabled:"<?php echo $this->slider->getParam("touchenabled","on")?>",
						onHoverStop:"<?php echo $this->slider->getParam("stop_on_hover","on")?>",
						
						navigationHAlign:"<?php echo $this->slider->getParam("navigaion_align_hor","center")?>",
						navigationVAlign:"<?php echo $this->slider->getParam("navigaion_align_vert","bottom")?>",
						navigationHOffset:<?php echo $this->slider->getParam("navigaion_offset_hor","0",RevSlider::FORCE_NUMERIC)?>,
						navigationVOffset:<?php echo $this->slider->getParam("navigaion_offset_vert","20",RevSlider::FORCE_NUMERIC)?>,

						soloArrowLeftHalign:"<?php echo $this->slider->getParam("leftarrow_align_hor","left")?>",
						soloArrowLeftValign:"<?php echo $this->slider->getParam("leftarrow_align_vert","center")?>",
						soloArrowLeftHOffset:<?php echo $this->slider->getParam("leftarrow_offset_hor","20",RevSlider::FORCE_NUMERIC)?>,
						soloArrowLeftVOffset:<?php echo $this->slider->getParam("leftarrow_offset_vert","0",RevSlider::FORCE_NUMERIC)?>,

						soloArrowRightHalign:"<?php echo $this->slider->getParam("rightarrow_align_hor","right")?>",
						soloArrowRightValign:"<?php echo $this->slider->getParam("rightarrow_align_vert","center")?>",
						soloArrowRightHOffset:<?php echo $this->slider->getParam("rightarrow_offset_hor","20",RevSlider::FORCE_NUMERIC)?>,
						soloArrowRightVOffset:<?php echo $this->slider->getParam("rightarrow_offset_vert","0",RevSlider::FORCE_NUMERIC)?>,
								
						shadow:<?php echo $this->slider->getParam("shadow_type","2")?>,
						fullWidth:"<?php echo $optFullWidth?>",
						fullScreen:"<?php echo $optFullScreen?>",

						stopLoop:"<?php echo $stopSlider?>",
						stopAfterLoops:<?php echo $stopAfterLoops?>,
						stopAtSlide:<?php echo $stopAtSlide?>,

						shuffle:"<?php echo $this->slider->getParam("shuffle","off") ?>",
						
						hideSliderAtLimit:<?php echo $hideSliderAtLimit?>,
						hideCaptionAtLimit:<?php echo $hideCaptionAtLimit?>,
						hideAllCaptionAtLilmit:<?php echo $hideAllCaptionAtLimit?>,
						startWithSlide:<?php echo $startWithSlide?>,
						videoJsPath:"<?php echo $videoJsPath?>",
						fullScreenOffsetContainer: "<?php echo $this->slider->getParam("fullscreen_offset_container","");?>"	
					});
				
				});	//ready
				
			</script>
			
			<?php			
		}
		
		
		/**
		 * 
		 * put inline error message in a box.
		 */
		public function putErrorMessage($message){
			?>
			<div style="width:800px;height:300px;margin-bottom:10px;border:1px solid black;margin:0px auto;">
				<div style="padding-left:20px;padding-right:20px;line-height:1.5;padding-top:40px;color:red;font-size:16px;text-align:left;">
					<?php _e("Revolution Slider Error",REVSLIDER_TEXTDOMAIN)?>: <?php echo $message?> 
				</div>
			</div>
			<?php 
		}
		
		/**
		 * 
		 * fill the responsitive slider values for further output
		 */
		private function getResponsitiveValues(){
			$sliderWidth = (int)$this->slider->getParam("width");
			$sliderHeight = (int)$this->slider->getParam("height");
			
			$percent = $sliderHeight / $sliderWidth;
			
			$w1 = (int) $this->slider->getParam("responsitive_w1",0);
			$w2 = (int) $this->slider->getParam("responsitive_w2",0);
			$w3 = (int) $this->slider->getParam("responsitive_w3",0);
			$w4 = (int) $this->slider->getParam("responsitive_w4",0);
			$w5 = (int) $this->slider->getParam("responsitive_w5",0);
			$w6 = (int) $this->slider->getParam("responsitive_w6",0);
			
			$sw1 = (int) $this->slider->getParam("responsitive_sw1",0);
			$sw2 = (int) $this->slider->getParam("responsitive_sw2",0);
			$sw3 = (int) $this->slider->getParam("responsitive_sw3",0);
			$sw4 = (int) $this->slider->getParam("responsitive_sw4",0);
			$sw5 = (int) $this->slider->getParam("responsitive_sw5",0);
			$sw6 = (int) $this->slider->getParam("responsitive_sw6",0);
			
			$arrItems = array();
			
			//add main item:
			$arr = array();				
			$arr["maxWidth"] = -1;
			$arr["minWidth"] = $w1;
			$arr["sliderWidth"] = $sliderWidth;
			$arr["sliderHeight"] = $sliderHeight;
			$arrItems[] = $arr;
			
			//add item 1:
			if(empty($w1))
				return($arrItems);
				
			$arr = array();				
			$arr["maxWidth"] = $w1-1;
			$arr["minWidth"] = $w2;
			$arr["sliderWidth"] = $sw1;
			$arr["sliderHeight"] = floor($sw1 * $percent);
			$arrItems[] = $arr;
			
			//add item 2:
			if(empty($w2))
				return($arrItems);
			
			$arr["maxWidth"] = $w2-1;
			$arr["minWidth"] = $w3;
			$arr["sliderWidth"] = $sw2;
			$arr["sliderHeight"] = floor($sw2 * $percent);
			$arrItems[] = $arr;
			
			//add item 3:
			if(empty($w3))
				return($arrItems);
			
			$arr["maxWidth"] = $w3-1;
			$arr["minWidth"] = $w4;
			$arr["sliderWidth"] = $sw3;
			$arr["sliderHeight"] = floor($sw3 * $percent);
			$arrItems[] = $arr;
			
			//add item 4:
			if(empty($w4))
				return($arrItems);
			
			$arr["maxWidth"] = $w4-1;
			$arr["minWidth"] = $w5;
			$arr["sliderWidth"] = $sw4;
			$arr["sliderHeight"] = floor($sw4 * $percent);
			$arrItems[] = $arr;

			//add item 5:
			if(empty($w5))
				return($arrItems);
			
			$arr["maxWidth"] = $w5-1;
			$arr["minWidth"] = $w6;
			$arr["sliderWidth"] = $sw5;
			$arr["sliderHeight"] = floor($sw5 * $percent);
			$arrItems[] = $arr;
			
			//add item 6:
			if(empty($w6))
				return($arrItems);
			
			$arr["maxWidth"] = $w6-1;
			$arr["minWidth"] = 0;
			$arr["sliderWidth"] = $sw6;
			$arr["sliderHeight"] = floor($sw6 * $percent);
			$arrItems[] = $arr;
			
			return($arrItems);
		}
		
		
		/**
		 * 
		 * put responsitive inline styles
		 */
		private function putResponsitiveStyles(){

			$bannerWidth = $this->slider->getParam("width");
			$bannerHeight = $this->slider->getParam("height");
			
			$arrItems = $this->getResponsitiveValues();
			
			?>
			<style type='text/css'>
				#<?php echo $this->sliderHtmlID?>, #<?php echo $this->sliderHtmlID_wrapper?> { width:<?php echo $bannerWidth?>px; height:<?php echo $bannerHeight?>px;}
			<?php
			foreach($arrItems as $item):			
				$strMaxWidth = "";
				
				if($item["maxWidth"] >= 0)
					$strMaxWidth = "and (max-width: {$item["maxWidth"]}px)";
				
			?>
			
			   @media only screen and (min-width: <?php echo $item["minWidth"]?>px) <?php echo $strMaxWidth?> {
			 		  #<?php echo $this->sliderHtmlID?>, #<?php echo $this->sliderHtmlID_wrapper?> { width:<?php echo $item["sliderWidth"]?>px; height:<?php echo $item["sliderHeight"]?>px;}	
			   }
			
			<?php 
			endforeach;
			echo "</style>";
		}

		
		/**
		 * 
		 * modify slider settings for preview mode
		 */
		private function modifyPreviewModeSettings(){
			$params = $this->slider->getParams();
			$params["js_to_body"] = "false";
			
			$this->slider->setParams($params);
		}
		
		
		/**
		 * 
		 * put html slider on the html page.
		 * @param $data - mixed, can be ID ot Alias.
		 */
		
		//TODO: settings google font, position, margin, background color, alt image text
		
		public function putSliderBase($sliderID){
			
			try{
				self::$sliderSerial++;
				
				$this->slider = new RevSlider();
				$this->slider->initByMixed($sliderID);
				
				//modify settings for admin preview mode
				if($this->previewMode == true)
					$this->modifyPreviewModeSettings();
				
				//set slider language
				$isWpmlExists = UniteWpmlRev::isWpmlExists();
				$useWpml = $this->slider->getParam("use_wpml","off");
				if(	$isWpmlExists && $useWpml == "on"){					 
					if($this->previewMode == false)
						$this->sliderLang = UniteFunctionsWPRev::getCurrentLangCode();
				}
				
				//edit html before slider
				$htmlBeforeSlider = "";
				if($this->slider->getParam("load_googlefont","false") == "true"){
					$googleFont = $this->slider->getParam("google_font");
					$htmlBeforeSlider = "<link rel='stylesheet' id='rev-google-font' href='http://fonts.googleapis.com/css?family={$googleFont}' type='text/css' media='all' />";
				}
				
				//pub js to body handle
				if($this->slider->getParam("js_to_body","false") == "true"){
					$urlIncludeJS = UniteBaseClassRev::$url_plugin."rs-plugin/js/jquery.themepunch.revolution.min.js";
					$htmlBeforeSlider .= "<script type='text/javascript' src='$urlIncludeJS'></script>";
				}
				
				//the initial id can be alias
				$sliderID = $this->slider->getID();
				
				$bannerWidth = $this->slider->getParam("width",null,RevSlider::VALIDATE_NUMERIC,"Slider Width");
				$bannerHeight = $this->slider->getParam("height",null,RevSlider::VALIDATE_NUMERIC,"Slider Height");
				
				$sliderType = $this->slider->getParam("slider_type");
				
				//set wrapper height
				$wrapperHeigh = 0;
				$wrapperHeigh += $this->slider->getParam("height");
				
				//add thumb height
				if($this->slider->getParam("navigaion_type") == "thumb"){
					$wrapperHeigh += $this->slider->getParam("thumb_height");
				}

				$this->sliderHtmlID = "rev_slider_".$sliderID."_".self::$sliderSerial;
				$this->sliderHtmlID_wrapper = $this->sliderHtmlID."_wrapper";
				
				$containerStyle = "";
				
				$sliderPosition = $this->slider->getParam("position","center");
				
				//set position:
				if($sliderType != "fullscreen"){
					
					switch($sliderPosition){
						case "center":
						default:
							$containerStyle .= "margin:0px auto;";
						break;
						case "left":
							$containerStyle .= "float:left;";
						break;
						case "right":
							$containerStyle .= "float:right;";
						break;
					}
					
				}
					
				//add background color
				$backgrondColor = trim($this->slider->getParam("background_color"));
				if(!empty($backgrondColor))
					$containerStyle .= "background-color:$backgrondColor;";
				
				//set padding			
				$containerStyle .= "padding:".$this->slider->getParam("padding","0")."px;";
				
				//set margin:
				if($sliderType != "fullscreen"){
									
					if($sliderPosition != "center"){
						$containerStyle .= "margin-left:".$this->slider->getParam("margin_left","0")."px;";
						$containerStyle .= "margin-right:".$this->slider->getParam("margin_right","0")."px;";
					}
					
					$containerStyle .= "margin-top:".$this->slider->getParam("margin_top","0")."px;";
					$containerStyle .= "margin-bottom:".$this->slider->getParam("margin_bottom","0")."px;";
				}
				
				//set height and width:
				$bannerStyle = "display:none;";	
				
				//add background image (to banner style)
				$showBackgroundImage = $this->slider->getParam("show_background_image","false");
				if($showBackgroundImage == "true"){					
					$backgroundImage = $this->slider->getParam("background_image");					
					if(!empty($backgroundImage))
						$bannerStyle .= "background-image:url($backgroundImage);background-repeat:no-repeat;";
				}
				
				//set wrapper and slider class:
				$sliderWrapperClass = "rev_slider_wrapper";
				$sliderClass = "rev_slider";
				
				$putResponsiveStyles = false;
				
				switch($sliderType){
					default:
					case "fixed":
						$bannerStyle .= "height:{$bannerHeight}px;width:{$bannerWidth}px;";
						$containerStyle .= "height:{$bannerHeight}px;width:{$bannerWidth}px;";
					break;
					case "responsitive":
						$putResponsiveStyles = true;						
					break;
					case "fullwidth":
						$sliderWrapperClass .= " fullwidthbanner-container";
						$sliderClass .= " fullwidthabanner";
						$bannerStyle .= "max-height:{$bannerHeight}px;height:{$bannerHeight};";
						$containerStyle .= "max-height:{$bannerHeight}px;";						
					break;
					case "fullscreen":
						$sliderWrapperClass .= " fullscreen-container";
						$sliderClass .= " fullscreenbanner";
					break;
				}
				
				$htmlTimerBar = "";
				
				$timerBar =  $this->slider->getParam("show_timerbar","top");
				
				if($timerBar == "true")
					$timerBar = $this->slider->getParam("timebar_position","top");
										
				switch($timerBar){
					case "top":
						$htmlTimerBar = '<div class="tp-bannertimer"></div>';
					break;
					case "bottom":
						$htmlTimerBar = '<div class="tp-bannertimer tp-bottom"></div>';
					break;
				}
				
				//check inner / outer border
				$paddingType = $this->slider->getParam("padding_type","outter");
				if($paddingType == "inner")	
					$sliderWrapperClass .= " tp_inner_padding"; 
				
				global $revSliderVersion;
				
				?>
				
				<!-- START REVOLUTION SLIDER <?php echo $revSliderVersion?> <?php echo $sliderType?> mode -->
				
				<?php 
					if($putResponsiveStyles == true)
						$this->putResponsitiveStyles(); ?>
				
				<?php echo $htmlBeforeSlider?>
				<div id="<?php echo $this->sliderHtmlID_wrapper?>" class="<?php echo $sliderWrapperClass?>" style="<?php echo $containerStyle?>">
					<div id="<?php echo $this->sliderHtmlID ?>" class="<?php echo $sliderClass?>" style="<?php echo $bannerStyle?>">						
						<?php $this->putSlides()?>
						<?php echo $htmlTimerBar?>
					</div>
				</div>				
				<?php 
				
				$this->putJS();
				?>
				<!-- END REVOLUTION SLIDER -->
				<?php 
				
			}catch(Exception $e){
				$message = $e->getMessage();
				$this->putErrorMessage($message);
			}
			
		}
		
		
	}

?>