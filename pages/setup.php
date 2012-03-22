<?php
include "../../../include/db.php";
include "../../../include/authenticate.php";
if (!checkperm("u")) {exit("Permission denied.");
}
include "../../../include/general.php";
include "sk4rs-functions.php";



if (getval("submit", "") != "") {
    if (isset($_POST['clientid'])) {
        $clientid_input = $_POST['clientid'];
        //echo "received client id: $clientid_input <br/>";
    }
    if (isset($_POST['clientsecret'])) {
        $clientsecret_input = $_POST['clientsecret'];
        //echo "received client secret: $clientsecret_input <br/>";
    }

    $config['oauth_client_id'] = $clientid_input;
    $config['oauth_client_secret'] = $clientsecret_input;

    set_plugin_config("smartkeywording_rs", $config);

}

$config = get_plugin_config("smartkeywording_rs");
$clientid = $config['oauth_client_id'];
$clientsecret = $config['oauth_client_secret'];
   
include "../../../include/header.php";
?>

<div class="BasicsBox">
    <h1><a href="http://kreuzverweis.com"><img src="../images/kreuzverweis_logo.png" style="height:1.5em;"/></a> Smart Keywording Setup</h1>
    <p>
        <?php echo $lang["txt_oauth_help"]
        ?> <a href="https://backoffice.kreuzverweis.com/">Kreuzverweis Backoffice</a>
    </p>
    <form id="form1" name="form1" method="post" action="">
        <?php echo config_text_field("clientid", "Client ID", $clientid, 36);?>
        <?php echo config_text_field("clientsecret", "Client Secret", $clientsecret, 36);?>

        <div class="Question">
            <label for="submit"></label>
            <input type="submit" name="submit" value="<?php echo $lang["save"]?>">
        </div>
        <div class="clearerleft"></div>
    </form>
    
    <h1><?php echo $lang["installationcheck"]?></h1>
    <a href="">&gt; <?php echo $lang["repeatinstallationcheck"]?></a>
    <table class="InfoTable">
        <tr>
            <td><?php echo $lang["txt_curl_title"];?></td>
            <td>
                <?php 
                    $isCurl = isCurlAvailable();
                    if ($isCurl) 
                        echo $lang["status-ok"];
                    else
                        echo $lang["status-warning"].": ".$lang["txt_curl_missing"];                   
                ?>
           </td>
       </tr>
       <?php        
       if ($isCurl) { 
           $isHook = isHookAvailable();?>
           <tr>
                <td><?php echo $lang["txt_version_title"];?></td>
                <td><?php // check for hooks
                    if ($isHook) 
                        echo $lang["status-ok"];
                    else {
                        echo $lang["status-warning"].": ".$lang["txt_outdated"];
                    }
                    ?>
                </td>
            </tr>  
            <?php 
            if ($isHook) {
                $isAccessible = isAccessible();?>
                <tr>
                    <td><?php echo $lang["txt_service_available_title"]; ?></td>
                    <td><?php // check for hooks
                        if ($isAccessible) {
                            echo $lang["status-ok"];
                        } else {
                            echo $lang["status-warning"].": ".$lang["txt_service_unavailable"];
                        }
                        ?>
                    </td>
                </tr>  
                <?php 
                if ($isAccessible) {
                    $isValid = isValid($clientid, $clientsecret);?> 
                    <tr>
                        <td><?php echo $lang["txt_credentials_valid_title"];?></td>
                        <td><?php                                         
                                if ($isValid)
                                    echo $lang["status-ok"];
                                else {
                                    echo $lang["status-warning"].": ".$lang["txt_credentials_invalid"];
                                }
                            ?>
                        </td>
                    </tr> 
     <?php }}} ?>
    </table>
</div>
<?php
include "../../../include/footer.php";
