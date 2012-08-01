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

$accessToken;

include "../../../include/db.php";
include "../../../include/authenticate.php";
include "./sk4rs-functions.php";

function currentUserId() {
    global $userref;
    // check if user has a oauth 2 user id
    $result = sql_query("select oauth_user_id from user where ref=$userref and oauth_user_id is not null and oauth_user_id != ''");
    if (count($result) == 0) {
        //echo "<br/>no user id for user $userref found in db";
        $userId = getUserId($clientid, $clientsecret);
    } else {
        //echo "<br/>oauth user id for user $userref found in db ";
        $userId = $result[0]['oauth_user_id'];
        //echo "<br/>oauth user id for user $userref is $userId ";
    }        
    return $userId;
}

try {
    //echo "userref: $userref ";
    $config = get_plugin_config("smartkeywording_rs");
    $clientid = $config['oauth_client_id'];
    $clientsecret = $config['oauth_client_secret'];    
    // check if user has a valid access token as cookie
    if (!array_key_exists("oauth_access_token", $_COOKIE)) {
        //echo "<br/>getting new access token from client manager ... ";
        $userId = currentUserId();
        $accessToken = getAccessTokenForUser($clientid, $clientsecret, $userId);
        //echo "<br/>access token from client manager is $accessToken";
    } else {
        //echo "<br/>using access token from cookie ";
        $accessToken = $_COOKIE['oauth_access_token'];
        //echo "<br/>access token from cookie is $accessToken";
    }
    $service = $_GET["service"];
    if ($service == "annotations") {
        echo learnAnnotations();
    } else {        
        $keyword = $_GET['keyword'];
        $limit = $_GET['limit'];        
        if (!$limit)
            $limit = 20;
        $path = "/keywords/$service/$keyword?limit=$limit";        
        try {
            $response = getKeywords("https://$apiHost:$apiPort$path");
        } catch (SmartKeywordingException $e) {
            $ch = $e -> getCurlHandle();
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) { 
                $userId = currentUserId();
                getAccessTokenForUser($clientid, $clientsecret, $userId);
                $response = getKeywords("https://$apiHost:$apiPort$path");
            }        
        }
        echo $response;
    }
} catch(SmartKeywordingException $e) {
    $ch = $e -> getCurlHandle();
    $response = $e -> getResponse();
    echo $e -> getMessage();
    curl_close($ch);        
    if ($response) {
        list($header, $body) = explode("\r\n\r\n", $response, 2);
        $tok = strtok($header, "\r\n");
        header($tok);
        echo $body;
    }
} catch (Exception $e) {
    header("HTTP/1.1 501 $e->getMessage()");
}
?>