<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

$config = get_plugin_config("smartkeywording_rs");

if (getval("submit","")!="") {    
    if (isset($_POST['clientid'])){                
        $clientid_input=$_POST['clientid'];
        //echo "received client id: $clientid_input <br/>";
    }
    if (isset($_POST['clientsecret'])){
        $clientsecret_input=$_POST['clientsecret'];
        //echo "received client secret: $clientsecret_input <br/>";
    }

    //$config=array();
    $config['oauth_client_id']=$clientid_input;
    $config['oauth_client_secret']=$clientsecret_input;

    set_plugin_config("smartkeywording_rs",$config);
    
    //redirect("pages/team/team_home.php");
}
include "../../../include/header.php";
?>

<div class="BasicsBox">  
  <h1><a href="http://kreuzverweis.com"><img src="../images/kreuzverweis_logo.png" style="height:1.5em;"/></a> Smart Keywording Setup</h1>    
  <p><?php echo $lang["txt_oauth_help"] ?> <a href="https://backoffice.kreuzverweis.com/">Kreuzverweis Backoffice</a></p>
    <form id="form1" name="form1" method="post" action="">
    
        <?php echo config_text_field("clientid","Client ID",$config['oauth_client_id'],36);  ?>   
        <?php echo config_text_field("clientsecret","Client Secret",$config['oauth_client_secret'],36);  ?> 
        
        <div class="Question">  
            <label for="submit"></label> 
            <input type="submit" name="submit" value="<?php echo $lang["save"]?>">   
        </div>
        <div class="clearerleft"></div>
    </form>
</div>  

<?php include "../../../include/footer.php";
