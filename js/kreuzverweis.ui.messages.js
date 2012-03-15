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

function createMessage(type,title,content) {
	var newDiv = jQuery('<div>')
			.attr('class', "alert-message "+type+" fade in")
			.attr('data-alert', "alert");
	var closeButton = jQuery('<a>')
			.attr("class", "close")
			.attr("href","#")
			.attr("style","border: 1px solid white; color: white; padding-right:2px;padding-left:2px;border-radius:10px 10px 10px 10px;")
			.text('x');
	jQuery(newDiv)		
		.append(jQuery('<strong>').text(title+' '))
		.append(content)
		.append(closeButton);
	return jQuery(newDiv);
}