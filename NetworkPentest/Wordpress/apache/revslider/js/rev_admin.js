var RevSliderAdmin = new function(){
	
		var t = this;
	
		/**
		 * init "slider" view functionality
		 */
		var initSaveSliderButton = function(ajaxAction){
			
			jQuery("#button_save_slider").click(function(){
					
					//collect data
					var data = {
							params: UniteSettingsRev.getSettingsObject("form_slider_params"),
							main: UniteSettingsRev.getSettingsObject("form_slider_main")
						};
					
					//add slider id to the data
					if(ajaxAction == "update_slider"){
						data.sliderid = jQuery("#sliderid").val();
						
						//some ajax beautifyer
						UniteAdminRev.setAjaxLoaderID("loader_update");
						UniteAdminRev.setAjaxHideButtonID("button_save_slider");
						UniteAdminRev.setSuccessMessageID("update_slider_success");
					}
					
					UniteAdminRev.ajaxRequest(ajaxAction ,data);
			});		
		}

		
		/**
		 * update shortcode from alias value.
		 */
		var updateShortcode = function(){
			var alias = jQuery("#alias").val();			
			var shortcode = "[rev_slider "+alias+"]";
			if(alias == "")
				shortcode = "-- wrong alias -- ";
			jQuery("#shortcode").val(shortcode);
		}
		
		/**
		 * change fields of the slider view
		 */
		var enableSliderViewResponsitiveFields = function(enableRes,textMode){
			
			//enable / disable responsitive fields
			if(enableRes){	
				jQuery("#responsitive_row").removeClass("disabled");
				jQuery("#responsitive_row input").prop("disabled","");
			}else{
				jQuery("#responsitive_row").addClass("disabled");
				jQuery("#responsitive_row input").prop("disabled","disabled");
			}
			
			
			var textWidth = jQuery("#cellWidth").data("text"+textMode);
			var textHeight = jQuery("#cellHeight").data("text"+textMode);
			
			jQuery("#cellWidth").html(textWidth);
			jQuery("#cellHeight").html(textHeight);
			
			
		}
		
		
		/**
		 * init slider view custom controls fields.
		 */
		var initSliderViewCustomControls = function(){
			
			//fixed
			jQuery("#slider_type_1").click(function(){
				enableSliderViewResponsitiveFields(false,"normal");
			});
			
			//responsitive
			jQuery("#slider_type_2").click(function(){
				enableSliderViewResponsitiveFields(true,"normal");
			});
			
			//full width
			jQuery("#slider_type_3").click(function(){
				enableSliderViewResponsitiveFields(false,"full");
			});
			
			//full screen
			jQuery("#slider_type_4").click(function(){
				enableSliderViewResponsitiveFields(false,"screen");
			});
			
		}
		
		
		/**
		 * init "slider->add" view.
		 */
		this.initAddSliderView = function(){
			jQuery("#title").focus();
			initSaveSliderButton("create_slider");
			initShortcode();
			initSliderViewCustomControls();
			
			enableSliderViewResponsitiveFields(false,"normal"); //show grid settings for fixed
		}
		
		
		/**
		 * init "slider->edit" view.
		 */		
		this.initEditSliderView = function(){
			
			initShortcode();
			initSliderViewCustomControls();
			
			initSaveSliderButton("update_slider");			
			
			//delete slider action
			jQuery("#button_delete_slider").click(function(){
				
				if(confirm("Do you really want to delete '"+jQuery("#title").val()+"' ?") == false)
					return(true);
				
				var data = {sliderid: jQuery("#sliderid").val()}
				
				UniteAdminRev.ajaxRequest("delete_slider" ,data);
			});
			

			//api inputs functionality:
			jQuery("#api_wrapper .api-input, #api_area").click(function(){
				jQuery(this).select().focus();
			});
			
			//api button functions:
			jQuery("#link_show_api").click(function(){
				jQuery("#api_wrapper").show();
				jQuery("#link_show_api").addClass("button-selected");
				
				jQuery("#toolbox_wrapper").hide();
				jQuery("#link_show_toolbox").removeClass("button-selected");
			});
			
			jQuery("#link_show_toolbox").click(function(){
				jQuery("#toolbox_wrapper").show();
				jQuery("#link_show_toolbox").addClass("button-selected");
				
				jQuery("#api_wrapper").hide();
				jQuery("#link_show_api").removeClass("button-selected");
			});

			
			//export slider action
			jQuery("#button_export_slider").click(function(){
				var sliderID = jQuery("#sliderid").val()
				var urlAjaxExport = ajaxurl+"?action="+g_uniteDirPlagin+"_ajax_action&client_action=export_slider";
				urlAjaxExport += "&sliderid=" + sliderID;
				location.href = urlAjaxExport;
			});
			
			//preview slider actions
			jQuery("#button_preview_slider").click(function(){
				var sliderID = jQuery("#sliderid").val()
				openPreviewSliderDialog(sliderID);
			});
		}
		
		
		/**
		 * init shortcode functionality in the slider new and slider edit views.
		 */
		var initShortcode = function(){
			
			//select shortcode text when click on it.
			jQuery("#shortcode").focus(function(){				
				this.select();
			});
			jQuery("#shortcode").click(function(){				
				this.select();
			});
			
			//update shortcode
			jQuery("#alias").change(function(){
				updateShortcode();
			});

			jQuery("#alias").keyup(function(){
				updateShortcode();
			});
		}
		
		
		/**
		 * update slides order
		 */
		var updateSlidesOrder = function(sliderID){
			var arrSlideHtmlIDs = jQuery( "#list_slides" ).sortable("toArray");
			
			//get slide id's from html (li) id's
			var arrIDs = [];
			var orderCounter = 0;
			jQuery(arrSlideHtmlIDs).each(function(index,value){
				var slideID = value.replace("slidelist_item_","");
				arrIDs.push(slideID);
				
				//update order visually
				orderCounter++;
				jQuery("#slidelist_item_"+slideID+" .order-text").text(orderCounter);
			});
			
			//save order
			var data = {arrIDs:arrIDs,sliderID:sliderID};
			
			jQuery("#saving_indicator").show();
			UniteAdminRev.ajaxRequest("update_slides_order" ,data,function(){
				jQuery("#saving_indicator").hide();
			});
			
		}
		
		/**
		 * init "sliders list" view 
		 */
		this.initSlidersListView = function(){
			
			//import slide dialog
			jQuery("#button_import_slider").click(function(){
				
				jQuery("#dialog_import_slider").dialog({
					modal:true,
					resizable:false,
					width:600,
					height:300,
					closeOnEscape:true,
					dialogClass:"tpdialogs",
					buttons:{
					"Close":function(){
						jQuery(this).dialog("close");
					}
				},					
				});	//dialog end
				
			});
			
			jQuery(".button_delete_slider").click(function(){
				
				var sliderID = this.id.replace("button_delete_","");
				var sliderTitle = jQuery("#slider_title_"+sliderID).text(); 
				if(confirm("Do you really want to delete '"+sliderTitle+"' ?") == false)
					return(false);
				
				UniteAdminRev.ajaxRequest("delete_slider" ,{sliderid:sliderID});
			});
			
			//duplicate slider action
			jQuery(".button_duplicate_slider").click(function(){
				var sliderID = this.id.replace("button_duplicate_","");
				UniteAdminRev.ajaxRequest("duplicate_slider" ,{sliderid:sliderID});
			});
			
				//preview slider action
				jQuery(".button_slider_preview").click(function(){
					
					var sliderID = this.id.replace("button_preview_","");
					openPreviewSliderDialog(sliderID);
			});
			
		}
		
		/**
		 * open preview slider dialog
		 */
		var openPreviewSliderDialog = function(sliderID){
			
			jQuery("#dialog_preview_sliders").dialog({
				modal:true,
				resizable:false,
				minWidth:1100,
				minHeight:500,
				closeOnEscape:true,
				dialogClass:"tpdialogs",
				buttons:{
					"Close":function(){
						jQuery(this).dialog("close");
					}
				},
				open:function(event,ui){
					var form1 = jQuery("#form_preview")[0];
					jQuery("#preview_sliderid").val(sliderID);
					form1.action = g_urlAjaxActions;
					form1.submit();
				},
				close:function(){
					var form1 = jQuery("#form_preview")[0];
					jQuery("#preview_sliderid").val("empty_output");
					form1.action = g_urlAjaxActions;
					form1.submit();
				}
				
			});			
		}
		
		/**
		 * get language array from the language list
		 */
		var getLangsFromLangList = function(objList){
			var arrLangs = [];
			objList.find(".icon_slide_lang").each(function(){
				var lang = jQuery(this).data("lang");
				arrLangs.push(lang);
			});
			
			return(arrLangs);
		}
		
		
		/**
		 * filter langs float menu by the list of icons
		 * show only languages in the float menu that not exists in the icons list
		 * return number of available languages
		 */
		var filterFloatMenuByListIcons = function(objList,operation){
			var arrLangs = getLangsFromLangList(objList);
			var numIcons = 0;
			
			jQuery("#langs_float_wrapper li.item_lang").each(function(){
				var objItem = jQuery(this);
				var lang = objItem.data("lang");
				var found = jQuery.inArray(lang,arrLangs);
				
				if(operation != "add")
					jQuery("#langs_float_wrapper li.operation_sap").hide();
								
				if(jQuery.inArray(lang,arrLangs) == -1){
					numIcons++;
					objItem.show();
					if(operation != "add")
						jQuery("#langs_float_wrapper li.operation_sap").show();
				}
				else
					objItem.hide();
			});
			
			return(numIcons);
		}
		
		
		/**
		 * init "slides list" view 
		 */
		this.initSlidesListView = function(sliderID){
			
			//quick lang change by lang icon
			jQuery("#list_slides").delegate(".icon_slide_lang, .icon_slide_lang_add","click",function(event){
				
				event.stopPropagation()
				var pos = UniteAdminRev.getAbsolutePos(this);
				var posLeft = pos[0] - 135;
				var posTop = pos[1] - 60;
				
				var objIcon = jQuery(this);
				
				var operation = objIcon.data("operation");
				var isParent = objIcon.data("isparent");
								
				if(operation == "add")
					jQuery("#langs_float_wrapper .item_operation").hide();
				else{
					jQuery("#langs_float_wrapper .item_operation").show();
					
					if(isParent == true)
						jQuery("#langs_float_wrapper .item_operation.operation_delete").hide();	
				}
								
				var objList = objIcon.parents(".list_slide_icons");
				filterFloatMenuByListIcons(objList,operation);
				
				jQuery("#langs_float_wrapper").show().css({left:posLeft,top:posTop});
				jQuery("#langs_float_wrapper").data("iconid",this.id);
			}); 
			
			jQuery("body").click(function(){
				jQuery("#langs_float_wrapper").hide();
			});
			
			//switch the language
			jQuery("#slides_langs_float li a").click(function(){
				var obj = jQuery(this);
				var lang = obj.data("lang");
				
				var iconID = jQuery("#langs_float_wrapper").data("iconid");
				if(!iconID)
					return(true);
				
				var objIcon = jQuery("#"+iconID);
				var objList = objIcon.parents(".list_slide_icons");
				
				//set operation
				var operation = obj.data("operation");
				
				if(operation == undefined || !operation)
					operation = objIcon.data("operation");
				
				if(operation == undefined || !operation)
					operation = "update";
				
				var currentLang = objIcon.data("lang");
				var slideID = objIcon.data("slideid");
				
				if(currentLang == lang)
					return(true);
				
				//show the loader
				if(operation != "preview"){
					objIcon.siblings(".icon_lang_loader").show();
					objIcon.hide();
				}
				
				if(operation == "edit"){
					var urlSlide = g_patternViewSlide.replace("[slideid]",slideID);
					location.href = urlSlide;
					return(true);
				}
				
				if(operation == "preview"){
					openPreviewSlideDialog(slideID,false);
					return(true);
				}
				
				var data = {sliderid:sliderID,slideid:slideID,lang:lang,operation:operation};
				UniteAdminRev.ajaxRequest("slide_lang_operation" ,data,function(response){
					
					objIcon.siblings(".icon_lang_loader").hide();					
					
					//nandle after response
					switch(response.operation){
						case "update":
							objIcon.attr("src",response.url_icon);
							objIcon.attr("title",response.title);
							objIcon.data("lang",lang);	
							objIcon.show();	
						break;
						case "add":
							objIcon.show();
							objIcon.parent().before(response.html);
							
							//hide the add icon if all langs included
							if(response.isAll == true)
								objList.find(".icon_slide_lang_add").hide();
								
						break;
						case "delete":
							objIcon.parent().remove();
							//show the add icon
							objList.find(".icon_slide_lang_add").show();
							
						break;
					}
					
				});
								
			});
			
			//set the slides sortable, init save order
			jQuery("#list_slides").sortable({
					axis:"y",
					handle:'.col-handle',
					update:function(){updateSlidesOrder(sliderID)}
			});
			
			//new slide
			jQuery("#button_new_slide, #button_new_slide_top").click(function(){
				var dialogTitle = jQuery("#button_new_slide").data("dialogtitle");
				
				UniteAdminRev.openAddImageDialog(dialogTitle, function(obj){
					var data = {sliderid:sliderID,obj:obj};
					UniteAdminRev.ajaxRequest("add_slide" ,data);
				},true);	//allow multiple selection
				
			});
			
			//new transparent slide
			jQuery("#button_new_slide_transparent, #button_new_slide_transparent_top").click(function(){
				jQuery(this).hide();
				jQuery(".new_trans_slide_loader").show();
				var data = {sliderid:sliderID};
				UniteAdminRev.ajaxRequest("add_slide" ,data);
			});
			
			//duplicate slide
			jQuery(".button_duplicate_slide").click(function(){
				var slideID = this.id.replace("button_duplicate_slide_","");
				var data = {slideID:slideID,sliderID:sliderID};
				UniteAdminRev.ajaxRequest("duplicate_slide" ,data);
			});
			
			//copy / move slides
			jQuery(".button_copy_slide").click(function(){
				if(jQuery(this).hasClass("button-disabled"))
					return(false);
				
				var dialogCopy = jQuery("#dialog_copy_move");
				
				var textClose = dialogCopy.data("textclose");
				var textUpdate = dialogCopy.data("textupdate");
				var objButton = jQuery(this);
				
				var buttons = {};
				buttons[textUpdate] = function(){
					var slideID = objButton.attr("id").replace("button_copy_slide_","");
					var targetSliderID = jQuery("#selectSliders").val();
					var operation = "copy";
					if(jQuery("#radio_move").prop("checked") == "checked")
						operation = "move";
						
					var data = {slideID:slideID,
								sliderID:sliderID,
								targetSliderID:targetSliderID,
								operation:operation};
					
					var objLoader = objButton.siblings(".loader_copy");
					
					objButton.hide();
					objLoader.show();
					
					UniteAdminRev.ajaxRequest("copy_move_slide" ,data);
					jQuery(this).dialog("close");
				};
				
				jQuery("#dialog_copy_move").dialog({
					modal:true,
					resizable:false,
					width:400,
					height:300,
					closeOnEscape:true,
					dialogClass:"tpdialogs",
					buttons:buttons	
				});	//dialog end
				
			});
			
			// delete single slide
			jQuery(".button_delete_slide").click(function(){
				var slideID = this.id.replace("button_delete_slide_","");
				var data = {slideID:slideID,sliderID:sliderID};
				if(confirm("Delete this slide?") == false)
					return(false);
				
				var objButton = jQuery(this);				
				var objLoader = objButton.siblings(".loader_delete");
				
				objButton.hide();
				objLoader.show();
				
				UniteAdminRev.ajaxRequest("delete_slide" ,data);
			});
			
			//change image
			jQuery(".col-image .slide_image").click(function(){
				var slideID = this.id.replace("slide_image_","");
				UniteAdminRev.openAddImageDialog("Select Slide Image",function(urlImage,imageID){					
					var data = {slider_id:sliderID,slide_id:slideID,url_image:urlImage,image_id:imageID};
					UniteAdminRev.ajaxRequest("change_slide_image" ,data);
				});
			});	
			
			//publish / unpublish item
			jQuery("#list_slides .icon_state").click(function(){
				var objIcon = jQuery(this);
				var objLoader = objIcon.siblings(".state_loader");
				var slideID = objIcon.data("slideid");
				var data = {slider_id:sliderID,slide_id:slideID};
				
				objIcon.hide();
				objLoader.show();
				UniteAdminRev.ajaxRequest("toggle_slide_state" ,data,function(response){
					objIcon.show();
					objLoader.hide();
					var currentState = response.state;
					
					if(currentState == "published"){
						objIcon.removeClass("state_unpublished").addClass("state_published").prop("title","Unpublish Slide");
					}else{
						objIcon.removeClass("state_published").addClass("state_unpublished").prop("title","Publish Slide");
					}
							
				});
			});
			
			//preview slide from the slides list:
			jQuery("#list_slides .icon_slide_preview").click(function(){
				var slideID = jQuery(this).data("slideid");
				openPreviewSlideDialog(slideID,false);
			});
			
		}
		
		
		/**
		 * init "edit slide" view
		 */
		this.initEditSlideView = function(slideID,sliderID){
			
			// TOGGLE SOME ACCORDION
			jQuery('.tp-accordion').click(function() {
				
				var tpacc=jQuery(this);
				if (tpacc.hasClass("tpa-closed")) {
						tpacc.parent().parent().parent().find('.tp-closeifotheropen').each(function() {
							jQuery(this).slideUp(300);
							jQuery(this).parent().find('.tp-accordion').addClass("tpa-closed").addClass("box_closed").find('.postbox-arrow2').html("+");								
						})

						tpacc.parent().find('.toggled-content').slideDown(300);
						tpacc.removeClass("tpa-closed").removeClass("box_closed");
						tpacc.find('.postbox-arrow2').html("-");
				} else {
						tpacc.parent().find('.toggled-content').slideUp(300);
						tpacc.addClass("tpa-closed").addClass("box_closed");
						tpacc.find('.postbox-arrow2').html("+");
				
				}
			})
			
			// MAKE MAX WIDTH OF CONTAINERS.
			jQuery('.mw960').each(function() {
				var newmw = jQuery('#divLayers').width();
				if (newmw<960) newmw=960;
				jQuery(this).css({maxWidth:newmw+"px"});
			})
			
			// SORTING AND DEPTH SELECTOR
			jQuery('#button_sort_depth').on('click',function() {
				jQuery('.layer_sortbox').addClass("depthselected");
				jQuery('.layer_sortbox').removeClass("timeselected");
			});
			
			jQuery('#button_sort_time').on('click',function() {			
				jQuery('.layer_sortbox').removeClass("depthselected");
				jQuery('.layer_sortbox').addClass("timeselected");

			});
			
			
			//add slide top link
			jQuery("#link_add_slide").click(function(){
				
				var data = {
						sliderid:sliderID
					};
				jQuery("#loader_add_slide").show();
				UniteAdminRev.ajaxRequest("add_slide_fromslideview" ,data);
			});			
			
			//save slide actions
			jQuery("#button_save_slide").click(function(){
				var layers = UniteLayersRev.getLayers();
				
				if(JSON && JSON.stringify)
					layers = JSON.stringify(layers);
				
				var data = {
						slideid:slideID,
						params:UniteSettingsRev.getSettingsObject("form_slide_params"),
						layers:layers
					};
				
				data.params.slide_bg_color = jQuery("#slide_bg_color").val();
				
				UniteAdminRev.setAjaxHideButtonID("button_save_slide");
				UniteAdminRev.setAjaxLoaderID("loader_update");
				UniteAdminRev.setSuccessMessageID("update_slide_success");
				UniteAdminRev.ajaxRequest("update_slide" ,data);
			});
			
			//change image actions
			jQuery("#button_change_image").click(function(){
				
				UniteAdminRev.openAddImageDialog("Select Slide Image",function(urlImage,imageID){
						if(imageID == undefined)
							imageID = "";
						
						//set visual image 
						jQuery("#divLayers").css("background-image","url("+urlImage+")");
						
						//update setting input
						jQuery("#image_url").val(urlImage);
						jQuery("#image_id").val(imageID);
						
					}); //dialog
			});	//change image click.
			
			
			// slide options hide / show			
			jQuery("#link_hide_options").click(function(){
				
				if(jQuery("#slide_params_holder").is(":visible") == true){
					jQuery("#slide_params_holder").hide("slow");
					jQuery(this).text("Show Slide Options").addClass("link-selected");
				}else{
					jQuery("#slide_params_holder").show("slow");
					jQuery(this).text("Hide Slide Options").removeClass("link-selected");
				}
				
			});
			
			
			//preview slide actions - open preveiw dialog			
			jQuery("#button_preview_slide").click(function(){				
				openPreviewSlideDialog(slideID,true);
			});
			
			//init background options
			jQuery("#radio_back_image, #radio_back_trans, #radio_back_solid").click(function(){
				var currentType = jQuery("#background_type").val();
				var bgType = jQuery(this).data("bgtype");
				
				if(currentType == bgType)
					return(true);
				
				//disable image button
				if(bgType != "image")
					jQuery("#button_change_image").addClass("button-disabled");
				else
					jQuery("#button_change_image").removeClass("button-disabled");
				
				if(bgType != "solid")
					jQuery("#slide_bg_color").addClass("disabled").prop("disabled","disabled");
				else
					jQuery("#slide_bg_color").removeClass("disabled").prop("disabled","");
				
				jQuery("#background_type").val(bgType);
				
				setSlideBGByType(bgType);
								
			});
			
			//on change bg color event 
			UniteAdminRev.setColorPickerCallback(function(){
				var bgType = jQuery("#background_type").val();
				if(bgType == "solid"){
					var bgColor = jQuery("#slide_bg_color").val();
					jQuery("#divLayers").css("background-color",bgColor);
				}
					
			});
			
			
			//on change title event
			jQuery("#title").on('input',function(e){
				jQuery(".slide_title").text(jQuery("#title").val());
			});
			
			jQuery(".list_slide_links").sortable({
				update:function(){updateSlidesOrderEdit(sliderID)}
			});
			
			
			/**
			 * update slides order in slide edit
			 */
			var updateSlidesOrderEdit = function(sliderID){
				var arrSlideHtmlIDs = jQuery( ".list_slide_links" ).sortable("toArray");
				
				//get slide id's from html (li) id's
				var arrIDs = [];
				jQuery(arrSlideHtmlIDs).each(function(index,value){
					var slideID = value.replace("slidelist_item_","");
					arrIDs.push(slideID);
				});
				
				//save order
				var data = {arrIDs:arrIDs,sliderID:sliderID};
				
				jQuery("#loader_add_slide").show();
				UniteAdminRev.ajaxRequest("update_slides_order" ,data,function(){
					jQuery("#loader_add_slide").hide();
				});
				
			}
			
			jQuery('.inputDatePicker').datepicker({
				dateFormat : 'dd-mm-yy 00:00'
			});
			
		}//init slide view
		
		
		/**
		 * open preview slide dialog
		 */
		var openPreviewSlideDialog = function(slideID,useParams){

			if(useParams === undefined)
				useParams = true;
			
			var iframePreview = jQuery("#frame_preview");
			var previewWidth = iframePreview.width() + 10;
			var previewHeight = iframePreview.height() + 10;
			var iframe = jQuery("#frame_preview");
			
			jQuery("#dialog_preview").dialog({
					modal:true,
					resizable:false,
					minWidth:previewWidth,
					minHeight:previewHeight,
					closeOnEscape:true,
					dialogClass:"tpdialogs",
					buttons:{
						"Close":function(){
							jQuery(this).dialog("close");
						}
					},
					open:function(event,ui){
						var form1 = jQuery("#form_preview_slide")[0];
						
						var objData = {
								slideid:slideID,
							};
						
						if(useParams == true){
							objData.params = UniteSettingsRev.getSettingsObject("form_slide_params"),
							objData.params.slide_bg_color = jQuery("#slide_bg_color").val();							
							objData.layers = UniteLayersRev.getLayers()
						}
						
						var jsonData = JSON.stringify(objData);
						
						jQuery("#preview_slide_data").val(jsonData);
						form1.action = g_urlAjaxActions;
						form1.client_action = "preview_slide";
						form1.submit();
					},
					close:function(){	//distroy the loaded preview
						var form1 = jQuery("#form_preview_slide")[0];
						form1.action = g_urlAjaxActions;
						jQuery("#preview_slide_data").val("empty_output");
						form1.submit();
					}
			});
			
		}
		
		
		/**
		 * set slide background by type (image, solid, bg).
		 */
		var setSlideBGByType = function(bgType){
			switch(bgType){
				case "image":
					var urlImage = jQuery("#image_url").val();
					jQuery("#divLayers").css("background-image","url('"+urlImage+"')");
					jQuery("#divLayers").css("background-color","transparent");
					jQuery("#divLayers").removeClass("trans_bg");
					
				break;			
				case "trans":
					jQuery("#divLayers").css("background-image","none");
					jQuery("#divLayers").css("background-color","transparent");
					jQuery("#divLayers").addClass("trans_bg");
				break;
				case "solid":
					jQuery("#divLayers").css("background-image","none");
					jQuery("#divLayers").removeClass("trans_bg");
					var bgColor = jQuery("#slide_bg_color").val();
					jQuery("#divLayers").css("background-color",bgColor);
				break;
			}

		}

}
