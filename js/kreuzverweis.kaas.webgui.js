/**
   
   Copyright 2012 Kreuzverweis Solutions GmbH

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
         
**/

var suggestions = new Array();
var selected = new Array();
var complReqs = new Array();
var propReqs = new Array();
var waitingForProposals = false;
var jQuery_GET = {};

/**
 * Modify RS form so that hitting RETURN on autocompleted keywords does not result
 * in form submission
 */
function preventAutoSubmit() {	
	console.debug("preventing form autosubmission ...");
	jQuery("#mainform").attr('onSubmit',"return false");
	jQuery(".QuestionSubmit > input[name='resetform']").attr("onClick","reset()");
	jQuery(".QuestionSubmit > input[name='save']").attr("onClick",'jQuery("#mainform").submit()');
	jQuery("#question_copyfrom > input[name='copyfromsubmit']").attr("onClick",'jQuery("#mainform").submit()');
	console.debug("onClick for save button is: "+jQuery(".QuestionSubmit > input[name='save']").attr("onClick"));
}

/**
 * Updates the RS keyword field with the current selection
 */
function updateKeywordField() {
	jQuery("#"+keywordsFieldId).empty();
	//jQuery.each(selected,function(index,value){
		jQuery("#"+keywordsFieldId).append(getKeywordCSV());
	//});
}

/**
 * Read keywords from keywordsField and show them ...
 */
function readInitialKeywords() {
	var keywords = jQuery("#"+keywordsFieldId).text().split(",");
	jQuery.each(keywords,function(index,value){
		var trimmed = jQuery.trim(value);
		if (trimmed != "") {
			selected.push(trimmed);		
			jQuery("#selected").append(jQuery(jQuery(createKeywordUIItem(trimmed)).addClass('primary small')));
			// make clear button visible
			jQuery("#clear").show();
		}
	});
	getProposals(0);
}

/**
 * Hides the RS keyword field
 */
function hideKeywordField() {	
	jQuery("#"+keywordsFieldId).hide();
}

function handleAjaxError(jqXHR) {
	switch (jqXHR.status) {
		case 401:
			// user unauthorized
			console.log("authentication failed, not authorized");
			// if cookies are there but have wrong data
			if(jQuery.cookie('token') && jQuery.cookie('secret')) {
				// display authentication failure message
				var m = createMessage('error', txt_authenticationFailed_title, txt_authenticationFailed_content);
				jQuery(m).appendTo(jQuery('#messages'));
			}
			break;
		case 500:
			// internal server error
			console.log("internal server error occurred");
			var m = createMessage('error', txt_internalServerError_title, txt_internalServerError_content);
			jQuery(m).appendTo(jQuery('#messages'));
			break;
		case 0:
			// abort
			break;
		default:
			console.log("error " + jqXHR.status + " occurred: " + jqXHR.statusText);
			break;
	}
}

function getKeywordCSV() {
	var selectedKeywords = "";
	jQuery.each(selected,function(index,value){
		//console.log(index+" "+value);
		if (selectedKeywords)
			selectedKeywords = selectedKeywords + "," + value;
		else
			selectedKeywords = value;
		}
	);				
	return selectedKeywords;
}

function removeEmptyLines() {
	//console.log('hidden span.btn: ' + jQuery("#suggestions > span.btn[style*='hidden']").length);
	var suggs = jQuery("#suggestions > span.btn:first");
	if(suggs.length > 0) {
		// check for empty lines and remove them
		var markedForRemoval = [];
		var currentLineTop = jQuery(suggs[0]).offset().top;
		var removeLine = true;
		var counter = 0;
		jQuery("#suggestions > span.btn").each(function() {
			counter = counter + 1;
			if(jQuery(this).offset().top != currentLineTop) {
				// new line
				if(removeLine) {
					// last line was completely hidden
					//console.log("removing line with hidden keywords: " + markedForRemoval);
					//for(i in markedForRemoval) {
					jQuery.each(markedForRemoval,function(index,value){
						jQuery(value).remove();
					});						
				}
				markedForRemoval = [];
				currentLineTop = jQuery(this).offset().top;
				removeLine = true;
			}
			if(jQuery(this).offset().top == currentLineTop) {
				// still in line
				if(removeLine) {
					//console.log(jQuery(this).text() + ' hidden: ' + jQuery(this).css('visibility'));
					if(jQuery(this).css('visibility') == 'hidden') {
						//console.log("marking " + jQuery(this).text() + " for removal");
						markedForRemoval.push(jQuery(this));
					} else {
						removeLine = false;
					}
				}
			}
		});
		if(removeLine) {
			// last line was completely hidden
			//console.log("removing line with hidden keywords: " + markedForRemoval);
			jQuery.each(markedForRemoval,function(index,value){
					jQuery(value).remove();
			});			
		}
	}
}

function adText() {
	var turn = 1;
	intervalID = setInterval(function(){	
		turn = turn + 1;
		if (jQuery.i18n.prop('txt_infobox_title'+turn) == '[txt_infobox_title'+turn+']')
			turn = 1;
		jQuery('#infobox_text').fadeOut(4000);	
		jQuery('#infobox_title').fadeOut(4000,function() {
			jQuery('#infobox_title').empty();
			jQuery('#infobox_text').empty();
			jQuery('#infobox_title').append(jQuery.i18n.prop('txt_infobox_title'+turn));
			jQuery('#infobox_text').append(jQuery.i18n.prop('txt_infobox_text'+turn));
			jQuery('#infobox_title').fadeIn(4000);
			jQuery('#infobox_text').fadeIn(4000);
		});
	}, 20000);
	
	//jQuery('#ad_text')
}

function getProposals(delay) {		
	if (!delay && delay!=0)
		delay = 2500;	
	delayedExec(delay, function() {						
		if(selected.length > 0) {
			waitingForProposals = true;	
			jQuery("#loadingDiv").show();
			var url = "../plugins/smartkeywording_rs/pages/kaasproxy.php";
			var keywords = getKeywordCSV();
			jQuery.ajax({
				url : url,
				data : {
					keyword : encodeURIComponent(keywords),
					service : "proposals",
					limit : 20
				},
				success : function(xmlResponse) {
					var newSuggestions = new Array();
					jQuery("keyword", xmlResponse).each(function() {
						newSuggestions.push(jQuery("label", this).text());
					});
					// remove invalid ones
					jQuery("#suggestions > span").each(function() {
						//console.log("checking " + jQuery(this).text());
						var index = jQuery.inArray(jQuery(this).text(), newSuggestions)
						//console.log("index is "+index);
						if(index > -1) {
							// remove it from newLabels and make it visible
							jQuery(this).css("visibility", "visible");
							//console.log("suggested label already there: " + jQuery(this).text());
							newSuggestions.splice(index, 1);
						} else {
							// make it invisible
							//console.log("hiding label that is no longer valid: " + jQuery(this).text());
							jQuery(this).css("visibility", "hidden");
							suggestions.splice(jQuery.inArray(jQuery(this).text(), suggestions), 1);
						}
					});
					// add new ones
					if(newSuggestions.length == 0) {
						console.log("no new suggestions to add");
					}
					jQuery.each(newSuggestions,function(index,value){
						// check if label already in list
						// if yes
						//console.log("adding new suggestion "+value);
						ui = createKeywordUIItem(value);
						jQuery(ui).appendTo(jQuery("#suggestions")).fadeIn(2000);
						suggestions.push(value);
						}
					);					
					delayedExec(300, function() {removeEmptyLines();
					}, 'qLineRemoval');
				},
				error : function(jqXHR, textStatus, errorThrown) {
					handleAjaxError(jqXHR);
				},
				complete : function() {
					if(propReqs.length <= 1) {
						jQuery("#loadingDiv").hide();
						waitingForProposals = false;
					}					
				}
			});
		} else {
			console.log("not requesting proposals as no keyword is selected");
			clear();
		}
	}, 'qGetProposals');
}

function deSelect(ui) {
	if(jQuery(ui).parent()[0] == jQuery("#suggestions")[0]) {
		suggestions.splice(jQuery.inArray(jQuery(ui).text(), suggestions), 1);
		selected.push(jQuery(ui).text());		
		jQuery(ui).clone().css("display", "none").addClass('primary small').appendTo(jQuery("#selected")).fadeIn(500);
		//jQuery(ui).fadeOut(500, function() {
			jQuery(ui).css("visibility", "hidden");
		//});
		getProposals(3500);
	} else if(jQuery(ui).parent()[0] == jQuery("#selected")[0]) {
		selected.splice(jQuery.inArray(jQuery(ui).text(), selected), 1);
		jQuery(ui).fadeOut(500, function() {
			jQuery(ui).remove();
		});		
		getProposals(0);
	} else {//if it has been autocompleted or entered manually
		jQuery(ui).css("visibility", "none").addClass('primary small');
		jQuery('#empty-suggestion-text').hide();
		jQuery('#empty-selection-text').hide();
		jQuery("#selected").append(jQuery(ui));
		jQuery(ui).fadeIn(500);
		selected.push(jQuery(ui).text());		
		jQuery('#suggestions').empty();
		suggestions = [];
		getProposals(0);
		if(jQuery("#clear").css("display") == "none") {
			jQuery("#clear").toggle(500);
		}
	}
	updateKeywordField();
}

function createKeywordUIItem(label, score) {
	var x = jQuery('<span>').attr("class", "btn");
	x.attr("score", score);
	//x.css('display:inline');
	x.text(label);
	return x;
}

function setRecMethod() {	
	if (jQuery_GET["split"]) {
		console.log('parameter split found, setting cookie to '+jQuery_GET["split"]);
		jQuery.cookie("split", 	jQuery_GET["split"]);
	} 
}

function sleep(milliseconds) {
	var start = new Date().getTime();
	while((new Date().getTime() - start) < milliseconds) {
		// Do nothing
	}
}

function clear() {
	selected = [];
	suggestions = [];
	jQuery("#suggestions > span").remove();	
	jQuery("#selected > span").remove();
	jQuery("#empty-suggestion-text").fadeIn();
	jQuery("#empty-selection-text").fadeIn();
	jQuery('#clear').hide(100);
	updateKeywordField();
}

function default_data() {
	return jQuery('#');
}

function initWebgui($) {
	$("#input-suggestions-label").popover({
		title : function() {
			return txt_suggestions_help;
		},
		content : function() {
			return txt_suggestions_help_content;
		},
		offset : 0,
		trigger : 'hover'
	});

	$("#input-selected-label").popover({
		title : function() {
			return txt_selection_help;
		},
		content : function() {
			return txt_selection_help_content;
		},
		offset : 0,
		trigger : 'hover'
	});
		

	$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
		if(options.url.indexOf("/completions") === 0) {
			while(complReqs.length > 0) {
				var req = complReqs.pop();
				console.log("aborting completion request " + req.options.url);
				req.jqxhr.abort();
			}
			complReqs.push({
				options : options,
				jqxhr : jqXHR
			});
		}
		if(options.url.indexOf("/propose") === 0) {
			while(propReqs.length > 0) {
				var req = propReqs.pop();
				console.log("aborting proposal request " + req.options.url);
				req.jqxhr.abort();
			}
			propReqs.push({
				options : options,
				jqxhr : jqXHR
			});
		}
	});	

	$("#suggestions").selectable({
		selected : function(event, ui) {
			deSelect(ui.selected);
		}
	});

	$("#selected").selectable({
		selected : function(event, ui) {
			deSelect(ui.selected);
		}
	});
	
	$("#selectionbox").mouseenter(function(){
		$("#sel_help").show();
	});
	
	$("#selectionbox").mouseleave(function(){
		$("#sel_help").hide();
	});	
	
	$("#suggestionbox").mouseenter(function(){
		$("#sugg_help").show();
	});
	
	$("#suggestionbox").mouseleave(function(){
		$("#sugg_help").hide();
	});	

	$("#clear").click(function() {
		clear();
	});

	$('#keyword').bind('keypress', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		// if ENTER is pressed
		 if (code == 13) { 
		 	$('#keyword').autocomplete("close");
		 	var ui = createKeywordUIItem($(this).val(), 0.0);
			deSelect(ui);
			$(this).val("");
			return false;
		 }
	});


	$("#keyword").autocomplete({
		source : function(request, response) {
			$.ajax({
				url : "../plugins/smartkeywording_rs/pages/kaasproxy.php",
				data : {keyword: encodeURIComponent(request.term), service: "completions", limit: 10},
				dataType : "xml",
				error : function(jqXHR, textStatus, errorThrown) {
					handleAjaxError(jqXHR);
				},
				complete : function() {
					$("#keyword").removeClass("ui-autocomplete-loading");
				},
				success : function(xmlResponse, jqxhr) {
					if($('keyword', xmlResponse).length == 0) {
						console.log("no completion for "+$('#keyword').val());
						response();
					} else {
						// current input
						var input = $('#keyword').val().toLowerCase();
						var inputLength = input.length;
						var firstCompletion = $($("keyword > label",xmlResponse)[0]).text().toLowerCase();						
						if ( firstCompletion.substr(0,inputLength) == input ) {
							response($("keyword", xmlResponse).map(function() {
								return {
									value : $("label", this).text() + ($.trim($("synonyms", this).text()) || ""),
									score : $("score", this).text()
								};
							}));
						} else {
							console.log("catched belated autocomplete response");
						}
					}
				}
			})
		},
		delay : 200,
		minLength : 3,
		autoFocus : false,		
		open : function() {			
			$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
		},
		close : function() {			
			$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
		}
	});
}
