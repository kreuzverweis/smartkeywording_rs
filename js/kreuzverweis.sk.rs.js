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
	jQuery("#"+keywordsFieldId).append(getKeywordCSV());
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
	requestProposals(0);
}

/**
 * Hides the RS keyword field in edit.php
 */
function hideKeywordField() {	
	jQuery("#"+keywordsFieldId).hide();
}


function addSKAutocomplete(fieldId) {
	jQuery("#"+fieldId).autocomplete({
		source : function(request, response) {
			jQuery.ajax({
				url : proxyUrl,
				data : {keyword: encodeURIComponent(request.term), service: "completions", limit: 10},
				dataType : "xml",
				error : function(jqXHR, textStatus, errorThrown) {
					handleAjaxError(jqXHR);
				},
				complete : function() {
					jQuery("#"+fieldId).removeClass("ui-autocomplete-loading");
				},
				success : function(xmlResponse, jqxhr) {
					if(jQuery('keyword', xmlResponse).length == 0) {
						console.debug("no completions found");
						response();
					} else {
						response(jQuery("keyword", xmlResponse).map(function() {
							return {
								value : jQuery("label", this).text() + (jQuery.trim(jQuery("synonyms", this).text()) || ""),
								score : jQuery("score", this).text()
							};
						}));						
					}
				}
			})
		},
		delay : 200,
		minLength : 3,
		autoFocus : false,		
		open : function() {			
			jQuery(this).removeClass("ui-corner-all").addClass("ui-corner-top");
		},
		close : function() {			
			jQuery(this).removeClass("ui-corner-top").addClass("ui-corner-all");
		}
	});
}
