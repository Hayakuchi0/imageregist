<?php
	require_once("../imageregist/imgr_config.php");	
	require_once("../imageregist/check.php");
	if(check_verification_code($_POST[$username],$_POST["verificationCode"])) {
		print("認証に成功しました。:".$_POST["writeContent"]."");
	} else {
		print("IDまたは認証用画像が異なります。");
	}
?>
