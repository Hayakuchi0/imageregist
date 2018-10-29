<?php
/**
 * @author Hayakuchi <hayakuchi@hinesm.info>
 * @license MIT License 
 * @copyright 2018 The Author
 */
	require_once("imgr_config.php");
	require_once("img_control.php");
	require_once("password.php");
	/**
	 * ユーザー名が正常に使用できるものかを確認するための関数。
	 * ロックファイルが存在する、40文字以上、半角英数字以外の文字列を使用している、
	 * これらに該当する場合にはfalseを返す。
	 * @param stirng $username 確認対象のユーザー名
	 * @param string $user_list 改行で区切られたユーザー名の一覧を表すstring型の変数。先頭が/であればこの変数は使わずに、簡易認証のkhファイルを使用する。
	 * @return bool 使用可能なユーザー名だった場合true,そうでない場合false
	 */
	function username_check($username,$user_list) {
		global $id_files_locate_dir;
		$lockpath=$id_files_locate_dir."".$username.".kl";
		if(file_exists($lockpath)) {
			print("他の認証処理を行っています。");
		} else if(strlen($username)>40) {
//文字列の長さを確認
			print("文字数が長過ぎます！\n");
		} else if(!(ctype_alnum($username))) {
//半角英数字か確認
			print("使用できる文字列は半角英数字のみです。\n");
		} else {
			return true;
		}
		return false;
	};
	/**
	 * 認証コードに対応するユーザー名が、正しく認識されたかどうかを返す。
	 * ディレクトリトラバーサル防止及び処理の効率化のため、ユーザー名が使用可能かどうかを調べて、その時点で使用不能なユーザー名だった場合に規制する。
	 * その後ユーザー名に対応したロックファイルを作成する。
	 * その後認証コードをpassword_verifyで認証し、失敗したらロックファイルを消してfalseを返す。
	 * 認証に成功したら、キーハッシュを記述したファイルの1行目に、1を加算しロックファイルを削除する。
	 * その後trueを返す。
	 * 認証コードの文法は、regist.phpのsavekh関数を参照すること。
	 * @param string $username 認証したいユーザー名
	 * @param string $verification_code 画像データと開始位置から算出した認証コード
	 * @return bool ユーザー名に対応する認証コードだった場合のみtrue
	 */
	function check_verification_code($username,$verification_code) {
		if(username_check($username,'/')) {
			global $id_files_locate_dir,$regist_hash_linenum;
			$hashpath=$id_files_locate_dir."".$username.".kh";
			$list=file($hashpath,FILE_IGNORE_NEW_LINES);
			return (compare_hashlist($username,$verification_code,$list)>=0);
		}
		return false;
	}
	function check_verification_code_advance($username,$verification_code,$hashlist_string,$user_list) {
		if(username_check($username,$user_list)) {
			$list=explode("\n",$hashlist_string);
			return compare_hashlist($username,$verification_code,$list);
		}
		return -1;
	}
	function compare_hashlist($username,$verification_code,$list) {
		global $id_files_locate_dir,$regist_hash_linenum,$type_easy;
		$lockpath=$id_files_locate_dir."".$username.".kl";
		touch($lockpath);
		$login_num=(((int)$list[0])%$regist_hash_linenum)+1;
		$hash_point_data=explode(":",$list[$login_num]);
		if(password_verify($verification_code,$hash_point_data[1])) {
			if($type_easy) {
				$hashpath=$id_files_locate_dir."".$username.".kh";
				$list[0]=$list[0]+1;
				$out=$list[0];
				for($i=0;$i<$regist_hash_linenum;$i++) {
					$out=$out."\n".$list[$i+1];
				}
				file_put_contents($hashpath,$out);
			}
			unlink($lockpath);
			return ($list[0]+1);
		}
		unlink($lockpath);
		return -1;
	}
?>
