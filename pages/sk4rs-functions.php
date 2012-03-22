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

$apiHost = 'api.kreuzverweis.com';
$apiPort = 443;

function getKeywords($url) {
    global $baseurl, $accessToken, $language;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken", "Accept-Language: $language"));
    $response = executeRequestAndHandleError("An error occured while getting keyword completions or proposals: Is the plugin correctly setup? Please check <a href='$baseurl/plugins/smartkeywording_rs/pages/setup.php'>your configuration</a>.", $ch);
    return $response;
}

function getUserId($clientid, $clientsecret) {
    global $baseurl, $apiHost, $apiPort, $userref;
    //request $userid
    $ch = curl_init("https://$apiHost:$apiPort/backoffice/users");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client=$clientid&secret=$clientsecret");
    $response = executeRequestAndHandleError("An error occured during user creation: Is the plugin correctly setup? Please check <a href='$baseurl/plugins/smartkeywording_rs/pages/setup.php'>your configuration</a>.", $ch);
    $xml = simplexml_load_string($response);
    $userid = $xml -> id;
    //echo "userid: $userid <br/>";
    sql_query("update user set oauth_user_id='$userid' where ref=$userref");
    //echo "<br/>stored clientid in db ";
    return $userid;
}

function getAccessTokenForUser($clientid, $clientsecret, $user) {
    global $baseurl, $apiHost, $apiPort;
    //get access token for $user and expiration date
    //echo "<br/>URI is: http://$clientManagerHost:$clientManagerPort/api/users/$user/tokens";
    $ch = curl_init("https://$apiHost:$apiPort/backoffice/users/$user/tokens");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client=$clientid&secret=$clientsecret");
    $response = executeRequestAndHandleError("An error occured while trying to get an access token: Is the plugin correctly setup? Please check <a href='$baseurl/plugins/smartkeywording_rs/pages/setup.php'>your configuration</a>.", $ch);
    //echo "<br/>response is: $response";
    $xml = simplexml_load_string($response);
    if (!$xml) {
        //echo "<br/>an error occurred while trying to read the response for the access token request";
        return false;
    } else {
        $accessToken = $xml -> value;
        $expires = $xml -> expires;
        //echo "<br/>received expiration time $expires";
        $_COOKIE['oauth_access_token'] = $accessToken;
        //setcookie('oauth_access_token', $accessToken, strtotime($expires));
        // expire in 10 min. - workaround for missing login hook to remove cookie from previous user on same browser
        setcookie('oauth_access_token', $accessToken, time() + 60 * 10);
        return $accessToken;
    }
}

function getUserCount($clientid, $clientsecret) {
    global $baseurl, $apiHost, $apiPort;
    $ch = curl_init("https://$apiHost:$apiPort/backoffice/users/count?client=$clientid&secret=$clientsecret");
    $usercount = executeRequestAndHandleError("exception while trying to get user count", $ch);    
    return $usercount;
}

function executeRequestAndHandleError($message, $ch) {
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new SmartKeywordingException($message, $ch, $response);
    }
    // check for curl error
    if (curl_errno($ch)) {
        throw new SmartKeywordingException($message, $ch, $response);
    }
    // check for response error
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 300) {
        throw new SmartKeywordingException($message, $ch, $response);
    }
    curl_close($ch);
    list($header, $body) = explode("\r\n\r\n", $response, 2);
    return $body;
}

function isValid($clientid, $clientsecret) {
    try {
        $count = getUserCount($clientid,$clientsecret);
        return true;
    } catch (SmartKeywordingException $e) {
        $ch = $e->getCurlHandle();
        curl_close($ch);
        return false;       
    }
}

function isAccessible() {
    try {
        $count = getUserCount(1,2);
        return true;
    } catch (SmartKeywordingException $e) {
        $ch = $e->getCurlHandle();
        $cerror = curl_errno($ch);
        $responseStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($responseStatus == 401)
            return true;           
        return false;                          
    }
}

function isCurlAvailable() {
    $functions = array("curl_init", "curl_setopt", "curl_exec", "curl_close", "curl_errno", "curl_getInfo");
    foreach ($functions as $function) {
        if (!function_exists($function)) {
            return false;
        }
    }
    return true;
}

function isHookAvailable() {
    $file = file_get_contents('../../../pages/edit.php');
    if ($file) {
        $found = stristr($file, 'hook("addfieldextras")');
        if ($found)
            return true;
    }
    return false;
}

class SmartKeywordingException extends Exception {
    public $curlHandle;
    public $response;
    // Die Exceptionmitteilung neu definieren, damit diese nicht optional ist
    public function __construct($message, $curlHandle, $response) {
        $this -> curlHandle = $curlHandle;
        $this -> response = $response;
        parent::__construct($message);
    }

    public function getCurlHandle() {
        return $this -> curlHandle;
    }

    public function getResponse() {
        return $this -> response;
    }
}
