<?php
	require_once("imgr_config.php");	
	require_once("check.php");
	if(check_verification_code($_POST[$username],$_POST["verificationCode"])) {
		print("0");
	} else {
		print("1");
	}
?>
