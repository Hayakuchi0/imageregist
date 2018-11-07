<?php
/**
 * ユーザーの情報を登録するためのPOST先。
 *
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
    include_once "regist_tool.php";
    if ($type_easy) {
        regist();
    } else {
        advancedRegist();
    }
}
?>
