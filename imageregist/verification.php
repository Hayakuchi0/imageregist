<?php
/**
 * ログインの際に認証コードを生成するのに必要になるPHPリクエスト先。
 *
 * ハッシュに対応した画像の位置を送信する。
 * imgr_config.phpにて設定されたusernameをクエリ名として送信されると、そのユーザーの次のハッシュに対応した画素の開始位置を送信する。
 * 送信されたユーザー名やクエリが不正だった場合、対応する画素の開始位置は送信されず、エラーメッセージを送信する。
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
    $uname=strval($_GET[$fd_username]);
    $ucheck=usernameCheck($uname);
    if ($ucheck==0) {
        if (existUser($uname)) {
            $list=getHashString($uname);
            $login_num=(((int)$list[0])%$regist_hash_linenum)+1;
            $hash_point_data=explode(":", $list[$login_num]);
            print($hash_point_data[0]);
        } else {
            printErrorIR(1);
        }
    } else {
        printErrorIR($ucheck);
    }
}
?>
