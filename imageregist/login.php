<?php
/**
 * ユーザーのログインを行うためのPOST先
 *
 * ログインに成功したら0を、失敗したら1を返す。
 * PHP Version >= 5.4
 *
 * @category  PostTarget
 * @package   ImageRegist
 * @author    Hayakuchi <hayakuchi@hinesm.info>
 * @copyright 2018 The Author
 * @license   MIT License 
 * @link      https://github.com/Hayakuchi0/imageregist/blob/master/README.md
 */
namespace hinesmImageRegist {
    include_once "imgr_config.php";
    include_once "check.php";
    if (checkVerificationCode($_POST[$username], $_POST["verificationCode"])) {
        print("0");
    } else {
        print("1");
    }
}
?>
