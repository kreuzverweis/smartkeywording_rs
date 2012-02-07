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
include "doGet.php";

global $language;

// Set your return content type
header('Content-type: application/xml');

$server = "kvnode2.uni-koblenz.de:8080"; //"data.kreuzverweis.com"; //$_GET["server"];
$service = $_GET["service"];
$keyword=$_GET['keyword'];
$limit=$_GET['limit'];
 

$path =  "/keywords/".$service."/".$keyword;
if ($limit)
    $path .= '?limit=' . $limit;
else 
    $path .= '?limit=20';
    
/*if ($_GET['split'])
    $path .= "&split=". $_GET['split'];
*/

doGet($server,$path,$language);

?>