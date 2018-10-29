<?php
/**
 * @author Hayakuchi <hayakuchi@hinesm.info>
 * @license MIT License 
 * @copyright 2018 The Author
 */
	$id_files_locate_dir='/var/www/local/imageRegist/';//パスワード画像のハッシュを保存する先。
	$regist_img='registImg';//ID登録時にPOSTするFormData型変数にappendで追加する際の名前。ここに認証用画像ファイルが入る。
	$username='username';//ID登録時及び書き込み時にPOSTするFormDataにappendで追加する際の名前。ここにユーザー名が入る。また、ハッシュ値に対応する画素を取得するためGETするクエリの名前
	$verification_code='verificationCode';//ID登録時及び書き込み時にPOSTするFormDataにappendで追加する際の名前。ここに認証コードが入る。また、ハッシュ値に対応する画素を取得するためGETするクエリの名前
	$regist_pixel_length=32;//送信する色の数
	$regist_hash_linenum=16;//サーバーに保存するハッシュ値の数
	$type_easy=true;
?>
