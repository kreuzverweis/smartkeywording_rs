<?php
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

function HookSmartkeywording_rsCollection_editAdditionalheaderjs(){
    global $baseurl;    
    include "../plugins/smartkeywording_rs/pages/headers.php";
    return false;   
}


# Hook<Pluginname><Pagename><Hookname>
function HookSmartkeywording_rsCollection_editAdditionalfields () {
    global $baseurl, $fields, $n, $name, $lang;    
	   	    
?>
    <script type="text/javascript">
        var keywordsFieldId = "keywords";           
        var txt_suggestions_help = "<?php echo $lang["txt_suggestions_help"]?>";
        var txt_suggestions_help_content = "<?php echo $lang["txt_suggestions_help_content"]?>";
        var txt_selection_help = "<?php echo $lang["txt_selection_help"]?>";
        var txt_selection_help_content = "<?php echo $lang["txt_selection_help_content"]?>";   
        
        jQuery(document).ready(function($) {                          
            preventAutoSubmit();    
            // move webtagrsp to the right position
            $("#webtagrsp").detach().insertAfter("label[for=keywords]");                        
            hideKeywordField();
            readInitialKeywords();            
            initWebgui($);            
        });                                      
    </script>        
               
<?php
    // end if
    include "../plugins/smartkeywording_rs/pages/smartkeywording-gui.php";
    return false; # Allow other plugins to also use this hook.    	
	}	
?>