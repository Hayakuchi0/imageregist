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
    use \hinesmImageRegist as hir;
if (hir\checkVerificationCode($_POST[$fd_username], $_POST[$fd_verification_code])>0) {
    print("IDまたは認証用画像が異なります。");
} else {
    print("認証に成功しました。:".$_POST["writeContent"]."");
}
?>
