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
	getProposals(0);
}

/**
 * Hides the RS keyword field
 */
function hideKeywordField() {	
	jQuery("#"+keywordsFieldId).hide();
}