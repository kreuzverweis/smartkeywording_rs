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
global $baseurl;  
?>
<link type="text/css" href="<?php echo $baseurl?>/plugins/smartkeywording_rs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <link type="text/css" rel="stylesheet" href="<?php echo $baseurl?>/plugins/smartkeywording_rs/css/bootstrap-popover.css"/>
    <style>
            .c2 {
                margin-top: 5px;
                position: relative;
            }
            .infolabel {
                margin-left: 1px;
                margin-top: 2px;
            }
            .withBG {
                background: none repeat scroll 0 0 #EEEEEE;
                border-radius: 3px 3px 3px 3px;
                line-height: 30px;
                min-height: 30px;
                text-align: left;
                /* padding-left: 5px; */
            }
            #suggestions > .btn {
                float: left;
                margin: 2px;
            }
            #selected > .btn {
                margin: 2px;
            }
            
            
            li.ui-menu-item {
                text-align: left;
            }
        </style>
    
    
    <!--<script type="text/javascript" src="<?php echo $baseurl?>/lib/js/jquery-1.6.1.min.js" ></script>-->
    
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/jquery-1.7.min.js" type="text/javascript"></script>
    <script>
     jQuery.noConflict();
     if (!window.console) console = {log: function() {},debug: function(){}};
    </script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/jquery-ui.min.js" type="text/javascript"></script>    
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/jquery.tools.min.js" type="text/javascript"></script>    
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/bootstrap-alerts.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/bootstrap-buttons.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/bootstrap-twipsy.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/bootstrap-popover.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/kreuzverweis.ui.delayedExec.js" type="text/javascript"></script>        
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/kreuzverweis.ui.messages.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/kreuzverweis.smartkeywording.webgui.js" type="text/javascript"></script>
    <script src="<?php echo $baseurl?>/plugins/smartkeywording_rs/js/kreuzverweis.sk.rs.js" type="text/javascript"></script>