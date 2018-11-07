<?php
/**
 * ユーザーの存在、正常な認証を確認する関数をまとめたソース。
 *
 * PHP Version >= 5.4
 *
 * @category  Function
 * @package   ImageRegist
 * @author    Hayakuchi <hayakuchi@hinesm.info>
 * @copyright 2018 The Author
 * @license   MIT License 
 * @link      https://github.com/Hayakuchi0/imageregist/blob/master/README.md
 */
namespace hinesmImageRegist {
    include_once "imgr_config.php";
    include_once "password.php";
    /**
     * 認証コードに対応するユーザー名が、正しく認識されたかどうかを返す。
     *
     * ディレクトリトラバーサル防止及び処理の効率化のため、ユーザー名が正常化を確認したから。\hinesmImageRegist\usernameCheck関数を参照。
     * 次にユーザーが登録済みかどうかを調べる。詳細は\hinesmImageRegist\existUser関数を参照。
     * \hinesmImageRegist\getHashStringで取得したハッシュ一覧と、認証コードを比較する。
     * 詳細は\hinesmImageRegist\compareHashlist関数を参照。
     *
     * @param string $username          認証したいユーザー名
     * @param string $verification_code 画像データと開始位置から算出した認証コード
     *
     * @return int ユーザー名に対応する認証コードであり、正常に認証できてる場合0である。
     *             ユーザーが存在しない場合は1である。
     *             ユーザーが存在し、認証コードの比較に失敗した場合は2である。
     *             ユーザー名が不正、または現在ロック中である場合は10以上19以下である。詳細は\hinesmImageRegist\usernameCheck関数を参照。
     */
    function checkVerificationCode($username, $verification_code)
    {
        $ucheck=usernameCheck($username);
        if ($ucheck>0) {
            return $ucheck;
        }
        if (!existUser($username)) {
            return 1;
        }
        if (compareHashlist(
            $username,
            $verification_code,
            getHashString($username)
        )
        ) {
            return 0;
        }
        return 2;
    }
    /**
     * ユーザー名が正常に使用できるものかを確認するための関数。
     * ロックファイルが存在する、40文字以上、半角英数字以外の文字列を使用している、
     * これらに該当する場合にはfalseを返す。
     *
     * @param stirng $username 確認対象のユーザー名
     *
     * @return int 使用可能なユーザー名かどうかを返す。
     *             0であれば使用可能なユーザーである。
     *             10であれば他の認証処理をしている途中である。
     *             11であればユーザー名が40文字以上である。
     *             12であればユーザー名が半角英数字以外である。
     */
    function usernameCheck($username)
    {
        global $id_files_locate_dir;
        $lockpath=$id_files_locate_dir."".$username.".kl";
        if (file_exists($lockpath)) {
            return 10;
        } else if (strlen($username)>40) {
            return 11;
        } else if (!(ctype_alnum($username))) {
            return 12;
        }
        return 0;
    }
    /**
     * 認証コードが正しいかを確認する関数。
     *
     * まずユーザー名に対応したロックファイルを作成する。
     * ユーザー名が正常に認識されていた場合ハッシュ値一覧と$verification_codeを比較する。
     * imgr_config.phpの$type_easyがtrueであればキーハッシュのログイン回数を更新する。
     * そうでない場合、\hinesmImageRegist\refreshLoginNumberAdvenceを呼び出す。
     * その後、ロックファイルを削除する。
     *
     * @param string $username          認証したいユーザー名。
     * @param string $verification_code 画像データと開始位置から算出した認証コード。
     * @param array  $list              ユーザーごとに定義された、
     *                                  画像に対応するハッシュ値の一覧を表した文字列を行ごとに分けて配列にしたもの。
     *                                  配列にする前の認証コードの文法は、\hinesmImageRegist\getkh関数を参照すること。
     *
     * @return bool ユーザーが正常に認証された場合true,そうでない場合はfalseを返す。
     */
    function compareHashlist($username, $verification_code, $list)
    {
        global $id_files_locate_dir,$regist_hash_linenum,$type_easy;
        $lockpath=$id_files_locate_dir."".$username.".kl";
        touch($lockpath);
        $login_num=(((int)$list[0])%$regist_hash_linenum)+1;
        $hash_point_data=explode(":", $list[$login_num]);
        error_log("penpennnnnnnnnn:".$verification_code);
        if (password_verify($verification_code, $hash_point_data[1])) {
            $out=strval($list[0]+1);
            for ($i=0;$i<$regist_hash_linenum;$i++) {
                $out=$out."\n".$list[$i+1];
            }
            if ($type_easy) {
                $hashpath=$id_files_locate_dir."".$username.".kh";
                file_put_contents($hashpath, $out);
            } else {
                refreshLoginNumberAdvance($username, $out);
            }
            unlink($lockpath);
            return true;
        }
        unlink($lockpath);
        return false;
    }
}
?>
