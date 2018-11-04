<?php
/**
 * ImageRegistを使用するために、ロックファイルの生成先を作成するインストールスクリプト。
 *
 * 設定ファイルに記述した位置へ作成します。
 * easy認証ではキーハッシュ値もここに作成します。
 * PHP Version >= 5.4
 *
 * @category  InstallScript
 * @package   ImageRegist
 * @author    Hayakuchi <hayakuchi@hinesm.info>
 * @copyright 2018 The Author
 * @license   MIT License 
 * @link      https://github.com/Hayakuchi0/imageregist/blob/master/README.md
 */
namespace hinesmImageRegist {
    include_once "imageregist/imgr_config.php";
    mkdir($id_files_locate_dir, 0777, true);
    chmod($id_files_locate_dir, 0777);
}
?>
