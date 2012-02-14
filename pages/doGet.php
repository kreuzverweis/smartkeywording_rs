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
         
   Portions of this code are based on http://www.websitetoolbox.com/tool/support/117

**/

function _checkBasicFunctions($functionList) {
  $functions = explode(",",$functionList);
  foreach ($functions as $key=>$val) {
    $function = trim($val);
    if (!function_exists($function)) {
        return false;
    }
  }
  return true;
} // end _checkBasicFunctions


function doGet($HOST,$URL,$LANG){        
    $acceptLangHeader = "Accept-Language: ". $LANG;                    
    if (_checkBasicFunctions("curl_init,curl_setopt,curl_exec,curl_close")) {        
        $ch = curl_init("http://".$HOST.$URL);
        curl_setopt($ch, CURLOPT_HEADER, 0);        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
        if ($LANG)  
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($acceptLangHeader));
        $response = curl_exec($ch);      
        curl_close($ch);
    } else if (_checkBasicFunctions("fsockopen,fputs,feof,fread,fgets,fclose")) {   
        $fsock = fsockopen($HOST, 80, $errno, $errstr, 30);
        if (!$fsock) {
            echo "Error! $errno - $errstr";
        } else {
            $headers .= "GET $URL HTTP/1.1\r\n";
            if ($LANG)
                $headers .= $acceptLangHeader."\r\n";
            $headers .= "HOST: $HOST\r\n";
            $headers .= "Connection: close\r\n\r\n";
            fputs($fsock, $headers);
            // Needed to omit extra initial information
            $get_info = false;
            while (!feof($fsock)) {
                if ($get_info) {
                    $response .= fread($fsock, 1024);
                } else {
                    if (fgets($fsock, 1024) == "\r\n") {
                        $get_info = true;
                    }
                }
            }
            fclose($fsock);
        }// if
    }            
    echo $response;
}
?>