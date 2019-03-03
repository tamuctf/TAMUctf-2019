<?php
	class UniteSettingsProductSidebarRev extends UniteSettingsOutputRev{
		
		private $addClass = "";		//add class to the main div
		private $arrButtons = array();
		private $isAccordion = true;
		
		/**
		 * 
		 * add buggon
		 */
		public function addButton($title,$id,$class = "button-secondary"){
			
			$button = array(
				"title"=>$title,
				"id"=>$id,
				"class"=>$class
			);
			
			$this->arrButtons[] = $button;			
		}
		
		
		/**
		 * 
		 * set add class for the main div
		 */
		public function setAddClass($addClass){
			$this->addClass = $addClass;
		}
		
		
		//-----------------------------------------------------------------------------------------------
		//draw text as input
		protected function drawTextInput($setting) {
			$disabled = "";
			$style="";
			if(isset($setting["style"])) 
				$style = "style='".$setting["style"]."'";
			if(isset($setting["disabled"])) 
				$disabled = 'disabled="disabled"';

			$class = UniteFunctionsRev::getVal($setting, "class","text-sidebar");
			
			//modify class:
			switch($class){
				case "normal":
				case "regular":
					$class = "text-sidebar-normal";
				break;				
			}
			
			if(!empty($class))
				$class = "class='$class'";
			
			$attribs = UniteFunctionsRev::getVal($setting, "attribs");
			
			?>
				<input type="text" <?php echo $attribs?> <?php echo $class?> <?php echo $style?> <?php echo $disabled?> id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" value="<?php echo $setting["value"]?>" />
			<?php
		}
		
		//-----------------------------------------------------------------------------------------------
		//draw a color picker
		protected function drawColorPickerInput($setting){			
			$bgcolor = $setting["value"];
			$bgcolor = str_replace("0x","#",$bgcolor);			
			// set the forent color (by black and white value)
			$rgb = UniteFunctionsRev::html2rgb($bgcolor);
			$bw = UniteFunctionsRev::yiq($rgb[0],$rgb[1],$rgb[2]);
			$color = "#000000";
			if($bw<128) $color = "#ffffff";
			
			
			$disabled = "";
			if(isset($setting["disabled"])){
				$color = "";
				$disabled = 'disabled="disabled"';
			}
			
			$style="style='background-color:$bgcolor;color:$color'";
			
			?>
				<input type="text" class="inputColorPicker" id="<?php echo $setting["id"]?>" <?php echo $style?> name="<?php echo $setting["name"]?>" value="<?php echo $bgcolor?>" <?php echo $disabled?>></input>
			<?php
		}
		
		//-----------------------------------------------------------------------------------------------
		// draw setting input by type
		protected function drawInputs($setting){
			switch($setting["type"]){
				case UniteSettingsRev::TYPE_TEXT:
					$this->drawTextInput($setting);
				break;
				case UniteSettingsRev::TYPE_COLOR:
					$this->drawColorPickerInput($setting);
				break;
				case UniteSettingsRev::TYPE_SELECT:
					$this->drawSelectInput($setting);
				break;
				case UniteSettingsRev::TYPE_CHECKBOX:
					$this->drawCheckboxInput($setting);
				break;
				case UniteSettingsRev::TYPE_RADIO:
					$this->drawRadioInput($setting);
				break;
				case UniteSettingsRev::TYPE_TEXTAREA:
					$this->drawTextAreaInput($setting);
				break;
				case UniteSettingsRev::TYPE_CUSTOM:
					$this->drawCustom($setting);
				break;
				case UniteSettingsRev::TYPE_BUTTON:
					$this->drawButtonSetting($setting);
				break;				
				default:
					throw new Exception("wrong setting type - ".$setting["type"]);
				break;
			}			
		}		
		
		//-----------------------------------------------------------------------------------------------
		//draw advanced order box
		protected function drawOrderbox_advanced($setting){
			
			$items = $setting["items"];
			if(!is_array($items))
				$this->throwError("Orderbox error - the items option must be array (items)");
				
			//get arrItems modify items by saved value			
			
			if(!empty($setting["value"]) && 
				getType($setting["value"]) == "array" &&
				count($setting["value"]) == count($items)):
				
				$savedItems = $setting["value"];
				
				//make assoc array by id:
				$arrAssoc = array();
				foreach($items as $item)
					$arrAssoc[$item[0]] = $item[1];
				
				foreach($savedItems as $item){
					$value = $item["id"];
					$text = $value;
					if(isset($arrAssoc[$value]))
						$text = $arrAssoc[$value];
					$arrItems[] = array($value,$text,$item["enabled"]);
				}
			else: 
				$arrItems = $items;
			endif;
			
			?>	
			<ul class="orderbox_advanced" id="<?php echo $setting["id"]?>">
			<?php 
			foreach($arrItems as $arrItem){
				switch(getType($arrItem)){
					case "string":
						$value = $arrItem;
						$text = $arrItem;
						$enabled = true;
					break;
					case "array":
						$value = $arrItem[0];
						$text = (count($arrItem)>1)?$arrItem[1]:$arrItem[0];
						$enabled = (count($arrItem)>2)?$arrItem[2]:true;
					break;
					default:
						$this->throwError("Error in setting:".$setting.". unknown item type.");
					break;
				}
				$checkboxClass = $enabled ? "div_checkbox_on" : "div_checkbox_off";
				
					?>
						<li>
							<div class="div_value"><?php echo $value?></div>
							<div class="div_checkbox <?php echo $checkboxClass?>"></div>
							<div class="div_text"><?php echo $text?></div>
							<div class="div_handle"></div>
						</li>
					<?php 
			}
			
			?>
			</ul>
			<?php 			
		}
		
		//-----------------------------------------------------------------------------------------------
		//draw order box
		protected function drawOrderbox($setting){
						
			$items = $setting["items"];
			
			//get arrItems by saved value
			$arrItems = array();
					
			if(!empty($setting["value"]) && 
				getType($setting["value"]) == "array" &&
				count($setting["value"]) == count($items)){
				
				$savedItems = $setting["value"];
								
				foreach($savedItems as $value){
					$text = $value;
					if(isset($items[$value]))
						$text = $items[$value];
					$arrItems[] = array("value"=>$value,"text"=>$text);	
				}
			}		//get arrItems only from original items
			else{
				foreach($items as $value=>$text)
					$arrItems[] = array("value"=>$value,"text"=>$text);
			}
			
			
			?>
			<ul class="orderbox" id="<?php echo $setting["id"]?>">
			<?php 
				foreach($arrItems as $item){
					$itemKey = $item["value"];
					$itemText = $item["text"];
					
					$value = (getType($itemKey) == "string")?$itemKey:$itemText;
					?>
						<li>
							<div class="div_value"><?php echo $value?></div>
							<div class="div_text"><?php echo $itemText?></div>
						</li>
					<?php 
				} 
			?>
			</ul>
			<?php 
		}
		
		/**
		 * 
		 * draw button
		 */
		function drawButtonSetting($setting){
			//set class
			$class = UniteFunctionsRev::getVal($setting, "class");
			if(!empty($class))
				$class = "class='$class'";
			
			?>
				<input type="button" id="<?php echo $setting["id"]?>" value="<?php echo $setting["value"]?>" <?php echo $class?>>
			<?php 
		}
		
		
		//-----------------------------------------------------------------------------------------------
		// draw text area input
		protected function drawTextAreaInput($setting){
			$disabled = "";
			if (isset($setting["disabled"])) $disabled = 'disabled="disabled"';
			
			//set style
			$style = UniteFunctionsRev::getVal($setting, "style");	
			if(!empty($style)) 
				$style = "style='".$style."'";

			//set class
			$class = UniteFunctionsRev::getVal($setting, "class");
			if(!empty($class))
				$class = "class='$class'";
			
			?>
				<textarea id="<?php echo $setting["id"]?>" <?php echo $class?> name="<?php echo $setting["name"]?>" <?php echo $style?> <?php echo $disabled?>><?php echo $setting["value"]?></textarea>				
			<?php
		}		
		
		//-----------------------------------------------------------------------------------------------
		// draw radio input
		protected function drawRadioInput($setting){
			$items = $setting["items"];
			$counter = 0;
			$id = $setting["id"];
			$isDisabled = UniteFunctionsRev::getVal($setting, "disabled",false); 
			
			?>
			<span id="<?php echo $id?>" class="radio_wrapper">
			<?php 
			foreach($items as $value=>$text):
				$counter++;
				$radioID = $id."_".$counter;
				$checked = "";
				if($value == $setting["value"]) $checked = " checked";

				$disabled = "";
				if($isDisabled == true)
					$disabled = 'disabled="disabled"';
				
				?>
					<input type="radio" id="<?php echo $radioID?>" value="<?php echo $value?>" name="<?php echo $setting["name"]?>" <?php echo $disabled?> <?php echo $checked?>/>
					<label for="<?php echo $radioID?>" style="cursor:pointer;"><?php _e($text)?></label>
					&nbsp; &nbsp;
				<?php				
			endforeach;
			?>
			</span>
			<?php 
		}
		
		
		//-----------------------------------------------------------------------------------------------
		// draw checkbox
		protected function drawCheckboxInput($setting){
			$checked = "";
			if($setting["value"] == true) $checked = 'checked="checked"';
			?>
				<input type="checkbox" id="<?php echo $setting["id"]?>" class="inputCheckbox" name="<?php echo $setting["name"]?>" <?php echo $checked?>/>
			<?php
		}		
		
		//-----------------------------------------------------------------------------------------------
		//draw select input
		protected function drawSelectInput($setting){
			
			$className = "";
			if(isset($this->arrControls[$setting["name"]])) $className = "control";
			$class = "";
			if($className != "") $class = "class='".$className."'";
			
			$disabled = "";
			if(isset($setting["disabled"])) 
				$disabled = 'disabled="disabled"';
			
			?>
			<select id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" <?php echo $disabled?> <?php echo $class?>>
			<?php
			foreach($setting["items"] as $value=>$text):
				$text = __($text,REVSLIDER_TEXTDOMAIN);
				$selected = "";
				if($value == $setting["value"]) $selected = 'selected="selected"';
				?>
					<option value="<?php echo $value?>" <?php echo $selected?>><?php echo $text?></option>
				<?php
			endforeach
			?>
			</select>
			<?php
		}

		/**
		 * 
		 * draw custom setting
		 */
		protected function drawCustom($setting){
			dmp($setting);
			exit();
		}
		
		//-----------------------------------------------------------------------------------------------
		//draw hr row
		protected function drawTextRow($setting){
			
			//set cell style
			$cellStyle = "";
			if(isset($setting["padding"])) 
				$cellStyle .= "padding-left:".$setting["padding"].";";
				
			if(!empty($cellStyle))
				$cellStyle="style='$cellStyle'";
				
			//set style
			$rowStyle = "";					
			if(isset($setting["hidden"]) && $setting["hidden"] == true) 
				$rowStyle .= "display:none;";
				
			if(!empty($rowStyle))
				$rowStyle = "style='$rowStyle'";
			
			?>
				<span class="spanSettingsStaticText"><?php echo __($setting["text"],REVSLIDER_TEXTDOMAIN)?></span>
			<?php 
		}
		
		//-----------------------------------------------------------------------------------------------
		//draw hr row
		protected function drawHrRow($setting){
			//set hidden
			$rowStyle = "";
			if(isset($setting["hidden"]) && $setting["hidden"] == true) $rowStyle = "style='display:none;'";
			
			?>
				<li id="<?php echo $setting["id"]?>_row">
					<hr />
				</li>
			<?php 
		}
		
		
		//-----------------------------------------------------------------------------------------------
		//draw settings row
		protected function drawSettingRow($setting){
		
			//set cellstyle:
			$cellStyle = "";
			if(isset($setting[UniteSettingsRev::PARAM_CELLSTYLE])){
				$cellStyle .= $setting[UniteSettingsRev::PARAM_CELLSTYLE];
			}
			
			//set text style:
			$textStyle = $cellStyle;
			if(isset($setting[UniteSettingsRev::PARAM_TEXTSTYLE])){
				$textStyle .= $setting[UniteSettingsRev::PARAM_TEXTSTYLE];
			}
			
			if($textStyle != "") $textStyle = "style='".$textStyle."'";
			if($cellStyle != "") $cellStyle = "style='".$cellStyle."'";
			
			//set hidden
			$rowStyle = "";
			if(isset($setting["hidden"]) && $setting["hidden"] == true) $rowStyle = "display:none;";
			if(!empty($rowStyle)) $rowStyle = "style='$rowStyle'";
			
			//set row class:
			$rowClass = "";
			if(isset($setting["disabled"])) $rowClass = "class='disabled'";
			
			//modify text:
			$text = UniteFunctionsRev::getVal($setting,"text","");
			$text = __($text,REVSLIDER_TEXTDOMAIN);
			
			// prevent line break (convert spaces to nbsp)
			$text = str_replace(" ","&nbsp;",$text);
			
			if($setting["type"] == UniteSettingsRev::TYPE_CHECKBOX)
				$text = "<label for='{$setting["id"]}'>{$text}</label>";
			
			//set settings text width:
			$textWidth = "";
			if(isset($setting["textWidth"])) $textWidth = 'width="'.$setting["textWidth"].'"';
			
			$description = UniteFunctionsRev::getVal($setting, "description");
			$description = __($description,REVSLIDER_TEXTDOMAIN);
			
			$unit = UniteFunctionsRev::getVal($setting, "unit");
			$unit = __($unit,REVSLIDER_TEXTDOMAIN);
			
			$required = UniteFunctionsRev::getVal($setting, "required");
			
			$addHtml = UniteFunctionsRev::getVal($setting, UniteSettingsRev::PARAM_ADDTEXT);			
			$addHtmlBefore = UniteFunctionsRev::getVal($setting, UniteSettingsRev::PARAM_ADDTEXT_BEFORE_ELEMENT);			
			
			
			//set if draw text or not.
			$toDrawText = true;
			if($setting["type"] == UniteSettingsRev::TYPE_BUTTON)
				$toDrawText = false;
				
			$settingID = $setting["id"];
			
			$attribsText = UniteFunctionsRev::getVal($setting, "attrib_text");
			
			$info = ($toDrawText == true && $description !== '') ? ' <span class="setting_info">i</span>' : '';
			
			?>
				<li id="<?php echo $settingID?>_row" <?php echo $rowStyle." ".$rowClass?>>
					
					<?php if($toDrawText == true):?>
						<span id="<?php echo $settingID?>_text" class='setting_text' title="<?php echo $description?>" <?php echo $attribsText?>><?php echo $text.$info ?></span>
					<?php endif?>
					
					<?php if(!empty($addHtmlBefore)):?>
						<span class="settings_addhtmlbefore"><?php echo $addHtmlBefore?></span>
					<?php endif?>
					
					<span class='setting_input'>
						<?php $this->drawInputs($setting);?>
					</span>
					<?php if(!empty($unit)):?>
						<span class='setting_unit'><?php echo $unit?></span>
					<?php endif?>
					<?php if(!empty($required)):?>
						<span class='setting_required'>*</span>
					<?php endif?>
					<?php if(!empty($addHtml)):?>
						<span class="settings_addhtml"><?php echo $addHtml?></span>
					<?php endif?>
					<div class="clear"></div>
				</li>
			<?php 
		}
		
		/**
		 * 
		 * insert settings into saps array
		 */
		private function groupSettingsIntoSaps(){
			$arrSections = $this->settings->getArrSections();
			$arrSaps = $arrSections[0]["arrSaps"];
			$arrSettings = $this->settings->getArrSettings(); 
			
			//group settings by saps
			foreach($arrSettings as $key=>$setting){
				
				$sapID = $setting["sap"];
				
				if(isset($arrSaps[$sapID]["settings"]))
					$arrSaps[$sapID]["settings"][] = $setting;
				else 
					$arrSaps[$sapID]["settings"] = array($setting);
			}
			return($arrSaps);
		}
		
		/**
		 * 
		 * draw buttons that defined earlier
		 */
		private function drawButtons(){
			foreach($this->arrButtons as $key=>$button){
				if($key>0)
				echo "<span class='hor_sap'></span>";
				echo UniteFunctionsRev::getHtmlLink("#", $button["title"],$button["id"],$button["class"]);
			}
		}
		
		/**
		 * 
		 * draw some setting, can be setting array or name
		 */
		public function drawSetting($setting,$state = null){
			if(gettype($setting) == "string")
				$setting = $this->settings->getSettingByName($setting);
			
			switch($state){
				case "hidden":
					$setting["hidden"] = true;
				break;
			}
				
			switch($setting["type"]){
				case UniteSettingsRev::TYPE_HR:
					$this->drawHrRow($setting);
				break;
				case UniteSettingsRev::TYPE_STATIC_TEXT:
					$this->drawTextRow($setting);
				break;
				default:
					$this->drawSettingRow($setting);
				break;
			}
		}
		
		/**
		 * 
		 * draw setting by bulk names
		 */
		public function drawSettingsByNames($arrSettingNames,$state=null){
			if(gettype($arrSettingNames) == "string")
				$arrSettingNames = explode(",",$arrSettingNames);
				
			foreach($arrSettingNames as $name)
				$this->drawSetting($name,$state);
		}
		
		
		/**
		 * 
		 * draw all settings
		 */
		public function drawSettings(){
			$this->prepareToDraw();
			$this->drawHeaderIncludes();
			
			
			$arrSaps = $this->groupSettingsIntoSaps();			
			
			$class = "postbox unite-postbox";
			if(!empty($this->addClass))
				$class .= " ".$this->addClass;
			
			//draw wrapper
			echo "<div class='settings_wrapper'>";
				
			//draw settings - advanced - with sections
			foreach($arrSaps as $key=>$sap):

				//set accordion closed
				$style = "";
				if($this->isAccordion == false){
					$h3Class = "class='no-accordion'";
				}else{
					$h3Class = "";
					if($key>0){
						$style = "style='display:none;'";
						$h3Class = "class='box_closed'";
					}
				}
					
				$text = $sap["text"];
				$text = __($text,REVSLIDER_TEXTDOMAIN);
				
				?>
					<div class="<?php echo $class?>">
						<h3 <?php echo $h3Class?>>
						
						<?php if($this->isAccordion == true):?>
							<div class="postbox-arrow"></div>
						<?php endif?>
						
							<span><?php echo $text ?></span>
						</h3>			
												
						<div class="inside" <?php echo $style?> >
							<ul class="list_settings">
						<?php
						
							foreach($sap["settings"] as $setting){
								$this->drawSetting($setting);
							}
							
							?>
							</ul>
							
							<?php 
							if(!empty($this->arrButtons)){
								?>
								<div class="clear"></div>
								<div class="settings_buttons">
								<?php 
									$this->drawButtons();
								?>
								</div>	
								<div class="clear"></div>
								<?php 								
							}								
						?>
						
							<div class="clear"></div>
						</div>
					</div>
				<?php 			
														
			endforeach;
			
			echo "</div>";	//wrapper close
		}
		
		
		//-----------------------------------------------------------------------------------------------
		// draw sections menu
		public function drawSections($activeSection=0){
			if(!empty($this->arrSections)):
				echo "<ul class='listSections' >";
				for($i=0;$i<count($this->arrSections);$i++):
					$class = "";
					if($activeSection == $i) $class="class='selected'";
					$text = $this->arrSections[$i]["text"];
					echo '<li '.$class.'><a onfocus="this.blur()" href="#'.($i+1).'"><div>'.$text.'</div></a></li>';
				endfor;
				echo "</ul>";
			endif;
				
			//call custom draw function:
			if($this->customFunction_afterSections) call_user_func($this->customFunction_afterSections);
		}
		
		/**
		 * 
		 * init accordion
		 */
		private function putAccordionInit(){
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					UniteSettingsRev.initAccordion("<?php echo $this->formID?>");
				});				
			</script>
			<?php 
		}
		
		/**
		 * 
		 * activate the accordion
		 */
		public function isAccordion($activate){
			$this->isAccordion = $activate;
		}
		
		
		/**
		 * 
		 * draw settings function
		 */
		public function draw($formID=null){
			if(empty($formID))
				UniteFunctionsRev::throwError("You must provide formID to side settings.");
			
			$this->formID = $formID;
			
			if(!empty($formID)){
				?>
				<form name="<?php echo $formID?>" id="<?php echo $formID?>">
					<?php $this->drawSettings() ?>
				</form>
				<?php 
			}else
				$this->drawSettings();
			
			if($this->isAccordion == true)
				$this->putAccordionInit();
			
		}
		
		
	}
?>