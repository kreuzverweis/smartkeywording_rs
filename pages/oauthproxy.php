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

 Some portions of this code are based on http://www.websitetoolbox.com/tool/support/117
 **/

include "../../../include/db.php";
include "../../../include/authenticate.php";

global $language, $userref;

try {
    //echo "userref: $userref ";

    if (_checkBasicFunctions("curl_init,curl_setopt,curl_exec,curl_close")) {

        $clientid = '9bf72ef7-d365-4555-b303-ea686af606b4';
        $clientsecret = '42273579-fb62-4db9-9c9a-4f101111fa17';

        $clientManagerHost = 'services.kreuzverweis.com';
        $clientManagerPort = 443;

        $apiHost = 'services.kreuzverweis.com';
        $apiPort = 443;

        $service = $_GET["service"];
        $keyword = $_GET['keyword'];
        $limit = $_GET['limit'];

        $limit;
        if (!$limit)
            $limit = 20;
        $path = "/keywords/$service/$keyword?limit=$limit";
        //echo "<br/> path is: $path";     
   
        $accessToken;        
        // check if user has a valid access token as cookie
        if (!array_key_exists("oauth_access_token", $_COOKIE)) {
            $userId;
            // check if user has a oauth 2 user id
            $result = sql_query("select oauth_user_id from user where ref=$userref and oauth_user_id is not null");
            if (count($result) == 0) {
                //echo "<br/>no client id for user $userref found in db";
                $userId = getUserId($clientManagerHost, $clientManagerPort, $clientid, $clientsecret, $userref);
            } else {
                //echo "<br/>oauth user id for user $userref found in db ";
                $userId = $result[0]['oauth_user_id'];
                //echo "<br/>oauth user id for user $userref is $userId ";
            }
            //echo "<br/>getting new access token from client manager ... ";

            $accessToken = getAccessTokenForUser($clientManagerHost, $clientManagerPort, $clientid, $clientsecret, $userId);
            //echo "<br/>access token from client manager is $accessToken";
        } else {
            //echo "<br/>using access token from cookie ";
            $accessToken = $_COOKIE['oauth_access_token'];
            //echo "<br/>access token from cookie is $accessToken";
        }        
        //echo "<br/>Time taken for client manager commmunication / access token retrieval = " . number_format(($End - $Start), 2) . " secs";
        
        //echo "<br/><br/><h1>Response</h1>";        
        $ch = curl_init("https://$apiHost:$apiPort$path");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken","Accept-Language: $language"));
        //$response = http_request('GET', $apiHost, $apiPort, $path, array("limit" => $limit), array(), array(), array("Authorization" => "Bearer $accessToken", "Accept-Language" => $language, "Connection" => "close"), 3000);
        $response = curl_exec($ch);
        curl_close($ch);        
        //echo "Time for keyword request = " . number_format(($End - $Start), 2) . " secs <br/>";
        header('Content-type: application/xml');
        echo $response;
    } else {
        header("Status: 501 - Required curl php extension not configured or installed", true, 501);
    }
} catch(Exception $E) {
    print_r("Message: $E->getMessage() <br/>");
    print_r("Trace: $E->getTraceAsString() <br/>");
}

function getUserId($clientManagerHost, $clientManagerPort, $clientid, $clientsecret, $userref) {
    //request $userid

    $ch = curl_init("https://" . $clientManagerHost . "/api/users");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client=$clientid&secret=$clientsecret");
    curl_setopt($ch, CURLOPT_PORT,$clientManagerPort);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);

    //$response = http_request('POST', $clientManagerHost, $clientManagerPort, '/api/users', array(), array('client' => $clientid, 'secret' => $clientsecret), array(), array(), 30);

    $xml = simplexml_load_string($response);
    //print_r($xml);
    //$username = $xml->name;
    //echo "username: $username <br/>";

    $userid = $xml -> id;
    //echo "userid: $userid <br/>";

    sql_query("update user set oauth_user_id='$userid' where ref=$userref");
    //echo "<br/>stored clientid in db ";
    return $userid;
}

function getAccessTokenForUser($clientManagerHost, $clientManagerPort, $clientid, $clientsecret, $user) {
    //get access token for $user and expiration date    
    //echo "<br/>URI is: http://$clientManagerHost:$clientManagerPort/api/users/$user/tokens";
    $ch = curl_init("https://$clientManagerHost:$clientManagerPort/api/users/$user/tokens");
    //curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client=$clientid&secret=$clientsecret");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    $response = curl_exec($ch);
    curl_close($ch);
    
    //$response = http_request('POST', $clientManagerHost, $clientManagerPort, $path, array(), array('client' => $clientid, 'secret' => $clientsecret), array(), array(), 30);
    //echo "<br/>response is: $response";
    $xml = simplexml_load_string($response);
    if (!$xml) {
        //echo "<br/>an error occurred while trying to read the response for the access token request";
        return false;
    } else {
        //$accessTokens = $xml->xpath('token[first()]');
        //echo "<br/>accessTokens is: $accessTokens";
        $accessToken = $xml->value;
        $expires = $xml->expires;
        //echo "<br/>received expiration time $expires";        
        //$expires = '2012-03-30';
        $_COOKIE['oauth_access_token'] = $accessToken;
        //echo "<br/>4";
        //setcookie('oauth_access_token',$accessToken,$expires);
        setcookie('oauth_access_token', $accessToken, strtotime($expires));
        //echo "<br/>5";
        return $accessToken;
    }
}

function _checkBasicFunctions($functionList) {
    $functions = explode(",", $functionList);
    foreach ($functions as $key => $val) {
        $function = trim($val);
        if (!function_exists($function)) {
            return false;
        }
    }
    return true;
}// end _checkBasicFunctions
?>