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
    include_once "img_control.php";
    include_once "password.php";
    /**
     * ユーザー名が正常に使用できるものかを確認するための関数。
     * ロックファイルが存在する、40文字以上、半角英数字以外の文字列を使用している、
     * これらに該当する場合にはfalseを返す。
     *
     * @param stirng $username  確認対象のユーザー名
     * @param string $user_list 改行で区切られたユーザー名の一覧を表すstring型の変数。
     *                          先頭が/であればこの変数は使わずに、簡易認証のkhファイルを使用する。
     *
     * @return bool 使用可能なユーザー名だった場合true,そうでない場合false
     */
    function usernameCheck($username, $user_list)
    {
        global $id_files_locate_dir;
        $lockpath=$id_files_locate_dir."".$username.".kl";
        if (file_exists($lockpath)) {
            print("他の認証処理を行っています。");
        } else if (strlen($username)>40) {
            //文字列の長さを確認
            print("文字数が長過ぎます！\n");
        } else if (!(ctype_alnum($username))) {
            //半角英数字か確認
            print("使用できる文字列は半角英数字のみです。\n");
        } else {
            return true;
        }
        return false;
    };
    /**
     * 認証コードに対応するユーザー名が、正しく認識されたかどうかを返す。(簡易認証版)
     *
     * ディレクトリトラバーサル防止及び処理の効率化のため、ユーザー名が使用可能かどうかを調べて、その時点で使用不能なユーザー名だった場合にはその場でfalseを返す。
     * 認証コードが正常に認証された場合trueを返す。そうでない場合falseを返す。
     * 認証コードの文法は、hinesmImageRegist/getkh関数を参照すること。
     *
     * @param string $username          認証したいユーザー名
     * @param string $verification_code 画像データと開始位置から算出した認証コード
     *
     * @return bool ユーザー名に対応する認証コードだった場合のみtrue
     */
    function checkVerificationCode($username, $verification_code)
    {
        if (usernameCheck($username, '/')) {
            global $id_files_locate_dir,$regist_hash_linenum;
            $hashpath=$id_files_locate_dir."".$username.".kh";
            $list=file($hashpath, FILE_IGNORE_NEW_LINES);
            return (compareHashlist($username, $verification_code, $list));
        }
        return false;
    }
    /**
     * 認証コードに対応するユーザーが、正しく認識されたかどうかを返す。(advance版)
     *
     * ディレクトリトラバーサル防止及び処理の効率化のため、ユーザー名が使用可能かどうかを調べて、その時点で使用不能なユーザー名だった場合にはその場でtrueを返す。
     * その確認にはhinesmImageRegist/usernameCheckを使うのでそちらを参照すること。
     * 認証コードの文法は、hinesmImageRegist/getkh関数を参照すること。
     *
     * @param string $username          認証したいユーザー名。
     * @param string $verification_code 画像データと開始位置から算出した認証コード。
     * @param string $hashlist_string   ユーザーごとに定義された、画像に対応するハッシュ値の一覧を表した文字列。
     *                                  文法はhinesmImageRegist/getkh関数を参照。
     * @param string $user_list         ユーザーの一覧が格納された文字列。
     *                                  文法はhinesmImageRegist/usernameCheck関数を参照。
     *
     * @return bool ユーザー名に対応する正しい認証コードだった場合のみtrue、それ以外はfalse。
     */
    function checkVerificationCodeAdvance(
        $username,
        $verification_code,
        $hashlist_string,
        $user_list
    ) {
        if (usernameCheck($username, $user_list)) {
            $list=explode("\n", $hashlist_string);
            return compareHashlist($username, $verification_code, $list);
        }
        return false;
    }
    /**
     * 認証コードが正しいかを確認する関数。
     *
     * まずユーザー名に対応したロックファイルを作成する。
     * ユーザー名が正常に認識されていた場合ハッシュ値一覧と$verification_codeを比較する。
     * imgr_config.phpの$type_easyがtrueであればキーハッシュのログイン回数を更新する。
     * そうでない場合、refresh_login_number_advenceを呼び出す。
     * その後、ロックファイルを削除する。
     *
     * @param string $username          認証したいユーザー名。
     * @param string $verification_code 画像データと開始位置から算出した認証コード。
     * @param array  $list              ユーザーごとに定義された、
     *                                  画像に対応するハッシュ値の一覧を表した文字列を行ごとに分けて配列にしたもの。
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
