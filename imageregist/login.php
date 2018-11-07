<?php
/**
 * ユーザーのログインを行うためのPOST先
 *
 * ログインに成功したら0を、失敗したらそれ以外の値を返す。
 * 詳細は\hinesmImageRegist\checkVerificationCode関数を参照。
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
    print(checkVerificationCode(
        $_POST[$fd_username],
        $_POST[$fd_verification_code]
    )
    );
}
?>
