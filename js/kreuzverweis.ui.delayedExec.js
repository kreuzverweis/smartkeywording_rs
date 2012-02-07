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

/*
 * delay: the delay to wait before the function is executed
 * theFunc: the function to be executed
 * theQueue: the name of the queue for the function calls to be executed
*/
function delayedExec(delay,theFunc,theQueue) {
	if (!window[theQueue]) {
		window[theQueue] = new Array();
	} 			
	//console.log('queueing one call for '+theFunc);
	var timer=setTimeout(function (){runIfLatest(theQueue,theFunc);},delay)
	//console.log('timer is: '+timer);
	window[theQueue].push(timer);	
}

function runIfLatest(theQueue,theFunc) {
	var timer = window[theQueue].shift();
	// stop oldest one
	if (window[theQueue].length == 0) {				
		//console.log('running function after delay, this is '+this);
		theFunc.call();
	} else {
		clearTimeout(timer);
	}
}
