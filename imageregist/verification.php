<?php
/**
 * @author Hayakuchi <hayakuchi@hinesm.info>
 * @license MIT License 
 * @copyright 2018 The Author
 */
/**
 * ログインの際に認証コードを生成するのに必要になる、
 * ハッシュに対応した画像の位置を送信するPHPリクエスト先。
 * imgr_config.phpにて設定されたusernameをクエリ名として送信されると、そのユーザーの次のハッシュに対応した画素の開始位置を送信します。
 * 送信されたユーザー名やクエリが不正だった場合、対応する画素の開始位置は送信されず、エラーメッセージを送信します。
 */
	require_once("imgr_config.php");
	require_once("check.php");
	if($type_easy) {
		$uname=strval($_GET[$username]);
		if(username_check($uname,"/")) {
			$hashpath=$id_files_locate_dir.$uname.".kh";
			if(file_exists($hashpath)) {
				$list=file($hashpath,FILE_IGNORE_NEW_LINES);
				$login_num=(((int)$list[0])%$regist_hash_linenum)+1;
				$hash_point_data=explode(":",$list[$login_num]);
				print($hash_point_data[0]);
			} else {
				print("そのユーザーは存在しません。ユーザー名をもう一度確認して下さい。");
			}
		} else {
			print("不正なユーザー名なので、ログイン処理を中断しました。");
		}
	} else {
	}
?>
