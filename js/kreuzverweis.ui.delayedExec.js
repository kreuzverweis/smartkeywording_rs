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
	if (delay == 0) {
		 console.debug("running call for queue "+theQueue+" immediately without delay");
		 theFunc.call();
	} else {
		if (!window[theQueue]) {
			window[theQueue] = new Array();
		} 				
		//console.debug('queueing one call in queue '+theQueue);
		var timer=setTimeout(function (){runIfLatest(theQueue,theFunc);},delay)		
		window[theQueue].push(timer);
		console.debug('queued timer '+timer+" for queue "+theQueue+". "+ window[theQueue].length+" call(s) in queue");		
	}	
}

function runIfLatest(theQueue,theFunc) {	
	var timer = window[theQueue].shift();
	console.debug("processing timer "+timer);
	if (window[theQueue].length == 0) {						
		console.debug('running timer '+timer+" as queue length is "+window[theQueue].length);
		theFunc.call();
	} else {
		console.debug("skipping timer "+timer+" as more calls have been queued ...");
		clearTimeout(timer);
	}
}
