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

include "../../../include/db.php";
include "../../../include/authenticate.php";
if (!checkperm("u")) {exit("Permission denied.");
}
include "../../../include/general.php";
include "headers.php";
include "sk4rs-functions.php";

$config = get_plugin_config("smartkeywording_rs");
//if (getval("submit", "") != "") {
if (isset($_POST['clientid'])) {
    $clientid_input = $_POST['clientid'];
    //echo "received client id: $clientid_input <br/>";
    $config['oauth_client_id'] = $clientid_input;
    set_plugin_config("smartkeywording_rs", $config);
}
if (isset($_POST['clientsecret'])) {
    $clientsecret_input = $_POST['clientsecret'];
    //echo "received client secret: $clientsecret_input <br/>";
    $config['oauth_client_secret'] = $clientsecret_input;
    set_plugin_config("smartkeywording_rs", $config);
}
if (isset($_POST['annotations'])) {
    $annotations_learned = $_POST['annotations'];
    $config['annotations'] = date(DATE_ISO8601);
    set_plugin_config("smartkeywording_rs", $config);
}
//}

$clientid = NULL;
$clientsecret = NULL;
if (isset($config['oauth_client_id']))
    $clientid = $config['oauth_client_id'];
if (isset($config['oauth_client_secret']))
    $clientsecret = $config['oauth_client_secret'];

include "../../../include/header.php";
?>

<div class="BasicsBox">
    <h1><a href="http://kreuzverweis.com"><img src="../images/kreuzverweis_logo.png" style="height:1.5em;"/></a> Smart Keywording Setup</h1>
    <br/>
    <br/>
    <h2><?php echo $lang["installationcheck"]
    ?></h2>
    <a href="">&gt; <?php echo $lang["repeatinstallationcheck"]
    ?></a>
    <table class="InfoTable">
        <tr>
            <td><?php echo $lang["txt_curl_title"]; ?></td>
            <td><?php
            $isCurl = isCurlAvailable();
            if ($isCurl)
                echo $lang["status-ok"];
            else
                echo $lang["status-warning"] . ": " . $lang["txt_curl_missing"];
            ?></td>
        </tr>
        <?php
if ($isCurl) {
$isHook = isHookAvailable();
        ?>
        <tr>
            <td><?php echo $lang["txt_version_title"]; ?></td>
            <td><?php // check for hooks
                if ($isHook)
                    echo $lang["status-ok"];
                else {
                    echo $lang["status-warning"] . ": " . $lang["txt_outdated"];
                }
            ?></td>
        </tr>
        <?php
if ($isHook) {
$isAccessible = isAccessible();
        ?>
        <tr>
            <td><?php echo $lang["txt_service_available_title"]; ?></td>
            <td><?php // check for hooks
                if ($isAccessible) {
                    echo $lang["status-ok"];
                } else {
                    echo $lang["status-warning"] . ": " . $lang["txt_service_unavailable"];
                }
            ?></td>
        </tr>
        <?php
if ($isAccessible) {
$isValid = isValid($clientid, $clientsecret);
        ?>
        <tr>
            <td><?php echo $lang["txt_credentials_valid_title"]; ?></td>
            <td><?php
            if ($isValid)
                echo $lang["status-ok"];
            else {
                echo $lang["status-warning"] . ": " . $lang["txt_credentials_invalid"];
            }
            ?></td>
        </tr>
        <?php }}} ?>
    </table>
    <br/>
    <br/>
    <br/>
    <h2>Client Id + Client Secret</h2>
    <p>
        <?php echo $lang["txt_oauth_help"]
        ?> <a href="https://backoffice.kreuzverweis.com/">Kreuzverweis Backoffice</a>
    </p>
    <form id="form1" name="form1" method="post" action="">
        <?php echo config_text_field("clientid", "Client ID", $clientid, 36); ?>
        <?php echo config_text_field("clientsecret", "Client Secret", $clientsecret, 36); ?>

        <div class="Question">
            <label for="submit"></label>
            <input type="submit" name="submit" value="<?php echo $lang["save"]?>">
        </div>
        <div class="clearerleft"></div>
    </form>
    <br/>
    <br/>
    <h2><?php echo $lang["txt_learning_title"]; ?></h2>
    <p>
        <?php echo $lang["txt_learning"]; ?>
        <br/>
        <?php
if (isset($config["annotations"])) {
echo $lang["txt_learning_enabled"]."<br/>";
echo $config["annotations"];
} else {
echo $lang["txt_learning_enable"];
        ?>

        <form id="annotationsform" action="" method="post">
            <input type="hidden" name="annotations"/>
            <input type="submit" value="<?php echo $lang["txt_learning_button"]; ?>" />
        </form>
        <span id="learning_animation" style="display:none;color: gray;"> Sending data to learn from your keywords, please wait ... <img src="../images/ui-anim_basic_16x16.gif"> </span>
        <br/>
        <span id="learning_messages" style="display:none;"></span>
        <script type="text/javascript">
            var proxyUrl = "<?php echo $baseurl; ?>/plugins/smartkeywording_rs/pages/oauthproxy.php";
        	jQuery(document).ready(function($) {
            	$("#annotationsform").live('submit',function(event) {
                	var sendingOk = false;
                	$("#learning_animation").show();
                	$.ajax({
                    	url : proxyUrl,
                    	async: false,
                    	data : {
                    	   service : "annotations"
                    	},
                    	error : function(jqXHR, textStatus, errorThrown) {
                    	   $("#learning_messages").empty().append("Sorry, the following error occurred while trying to send keywords: "+jqXHR.responseText).show();
                    	},
                    	complete : function () {
                    	   $("#learning_animation").hide();
                    	},
                    	success : function () {
                    	   sendingOk = true;
                    	}
                	});
                	return sendingOk;
            	});
        	});
        </script>

        <?php } ?>
    </p>
    <h2><?php echo $lang["txt_documentation"]; ?></h2>
    <ul>
        <li>
            <a href="https://github.com/kreuzverweis/smartkeywording_rs/wiki">https://github.com/kreuzverweis/smartkeywording_rs/wiki</a>
        </li>
        <li>
            <iframe style="width:300px;vertical-align:top;" src="http://www.youtube.com/embed/aSDTNdDewD4" frameborder="0" allowfullscreen></iframe>
        </li>
    </ul>
</div>
<?php
include "../../../include/footer.php";
