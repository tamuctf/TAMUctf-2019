<?php

	class UniteCssParserRev{
		
		private $cssContent;
		
		public function __construct(){
			
		}
		
		/**
		 * 
		 * init the parser, set css content
		 */
		public function initContent($cssContent){
			$this->cssContent = $cssContent;
		}		
		
		
		/**
		 * 
		 * get array of slide classes, between two sections.
		 */
		public function getArrClasses($startText = "",$endText=""){
			
			$content = $this->cssContent;
			
			//trim from top
			if(!empty($startText)){
				$posStart = strpos($content, $startText);
				if($posStart !== false)
					$content = substr($content, $posStart,strlen($content)-$posStart);
			}
			
			//trim from bottom
			if(!empty($endText)){
				$posEnd = strpos($content, $endText);
				if($posEnd !== false)
					$content = substr($content,0,$posEnd);
			}
			
			//get styles
			$lines = explode("\n",$content);
			$arrClasses = array();
			foreach($lines as $key=>$line){
				$line = trim($line);
				
				if(strpos($line, "{") === false)
					continue;

				//skip unnessasary links
				if(strpos($line, ".caption a") !== false)
					continue;
					
				if(strpos($line, ".tp-caption a") !== false)
					continue;
					
				//get style out of the line
				$class = str_replace("{", "", $line);
				$class = trim($class);
				
				//skip captions like this: .tp-caption.imageclass img
				if(strpos($class," ") !== false)
					continue;
				
				$class = str_replace(".caption.", ".", $class);
				$class = str_replace(".tp-caption.", ".", $class);
				
				$class = str_replace(".", "", $class);
				$class = trim($class);
				$arrWords = explode(" ", $class);
				$class = $arrWords[count($arrWords)-1];
				$class = trim($class);
				
				$arrClasses[] = $class;	
			}
			
			return($arrClasses);
		}
		
	}

?>