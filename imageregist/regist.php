<?php
/**
 * @author Hayakuchi <hayakuchi@hinesm.info>
 * @license MIT License 
 * @copyright 2018 The Author
 */
	require_once("imgr_config.php");
	require_once("regist_tool.php");
	if($type_easy) {
		regist($regist_img,$username,"/");
	} else {
		advanced_regist();
	}
?>
