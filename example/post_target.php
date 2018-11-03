<?php
/**
 * ユーザーの認証を実際に行う場合のサンプルソース。
 *
 * PHP Version >= 5.4
 *
 * @category  Sample
 * @package   ImageRegist
 * @author    Hayakuchi <hayakuchi@hinesm.info>
 * @copyright 2018 The Author
 * @license   MIT License 
 * @link      https://github.com/Hayakuchi0/imageregist/blob/master/README.md
 */
    require_once "../imageregist/imgr_config.php";    
    require_once "../imageregist/check.php";
if (\hinesmImageRegist\checkVerificationCode($_POST[$username], $_POST[$verification_code])) {
    print("認証に成功しました。:".$_POST["writeContent"]."");
} else {
    print("IDまたは認証用画像が異なります。");
}
?>
