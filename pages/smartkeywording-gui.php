<!--
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
-->
    <div id="webtagrsp" class="container" style="float:left;width:600px;">
        <div id="messages" class="row"></div>
        <div class="row">
            <div>
                <div class="row">
                    <div class="c2">
                        <input type="text" placeholder="<?php echo $lang["txt_input_keyword"]?>" id="keyword" style="width:100%;">
                    </div>
                </div>
                <div class="row">
                    <div class="withBG c2" id="suggestionbox" style="width:100%;">
                        <h2 style="padding:5px;vertical-align:middle;" id="empty-suggestion-text"><small><?php echo $lang["txt_suggestions"]
                            ?></small></h2>
                        <div style="width:97%;overflow:auto;" id="suggestions"></div>
                        <div style="position:absolute;padding:0px;margin:5px;line-height:10px;top:0px;right:0px;display:none;" id="sugg_help">
                            <span class="label" id="input-suggestions-label" style="vertical-align: middle;">?</span>
                        </div>
                        <img src="<?php echo $baseurl ?>/plugins/smartkeywording_rs/images/ui-anim_basic_16x16.gif" style="position:absolute;padding:0px;margin:5px;line-height:10px;top:0px;right:0px;display:none;" id="loadingDiv">
                    </div>
                </div>
                <div class="row">
                    <div class="withBG c2" id="selectionbox">
                        <h2 style="padding-left:5px;" id="empty-selection-text"><small><?php echo $lang["txt_selection"]
                            ?></small></h2>
                        <div style="width:97%;overflow:auto;" id="selected"></div>
                        <div style="position:absolute;padding:0px;margin:5px;line-height:10px;top:0px;right:0px;display:none;" id="sel_help">
                            <span style="vertical-align: middle;" class="label" id="input-selected-label">?</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="c2">
                        <span class="btn default" id="clear"> <?php echo $lang["txt_btn_clear"]
                            ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<br/>
