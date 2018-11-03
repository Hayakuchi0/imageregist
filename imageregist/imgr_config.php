<?php
/**
 * ImageRegistの設定ファイル。
 *
 * ImageRegistを使うに当たって、ユーザーが設定できる変更内容を記したphpソース。
 * 特にadvanced認証を使う場合、ここの定義は必須です。
 * PHP Version >= 5.4
 *
 * @category  Config
 * @package   ImageRegist
 * @author    Hayakuchi <hayakuchi@hinesm.info>
 * @copyright 2018 The Author
 * @license   MIT License 
 * @link      https://github.com/Hayakuchi0/imageregist/blob/master/README.md
 */
namespace hinesmImageRegist {
    $id_files_locate_dir='/var/www/local/imageRegist/';//パスワード画像のハッシュを保存する先。
    $regist_img='registImg';//ID登録時にPOSTするFormData型変数にappendで追加する際の名前。ここに認証用画像ファイルが入る。
    $username='username';//ID登録時及び書き込み時にPOSTするFormDataにappendで追加する際の名前。ここにユーザー名が入る。また、ハッシュ値に対応する画素を取得するためGETするクエリの名前
    $verification_code='verificationCode';//ID登録時及び書き込み時にPOSTするFormDataにappendで追加する際の名前。ここに認証コードが入る。また、ハッシュ値に対応する画素を取得するためGETするクエリの名前
    $regist_pixel_length=32;//送信する色の数
    $regist_hash_linenum=16;//サーバーに保存するハッシュ値の数
    $type_easy=true; //advanced認証を行うかどうかを選ぶ。trueであれば簡易認証を、falseであればadvanced認証を使う。
    /**
     * 認証にadvanced認証を用いる際、ID登録を行う為にregist.phpへアクセスしたときに呼び出される関数。
     *
     * この関数の処理内容はimageregistを利用する開発者が定義する。
     * 開発者が以下の処理を定義し、この関数内で実行する。
     * 1. すでに登録されているユーザーの一覧を、改行区切りの文字列として用意する。
     * 2. 1で用意した文字列を第三引数として、hinesmImageRegist/registCheckで登録可能なユーザーかどうかを判定する。登録不可能な場合はエラーメッセージをprintしreturnする。
     * 3. 登録可能なユーザーだった場合、hinesmImageRegist/getkhでハッシュ値をランダムに生成する。第一引数には$_FILE['regist_img']を設定する。
     * 4. 3で生成したハッシュ値とユーザー名を紐付け、保存媒体に保存する。
     * 5. 成功メッセージをprintする。
     *
     * @return none 戻り値は存在しない。定義しても使用しない。
     */
    function advancedRegist()
    {
    }
    /**
     * 認証にadvanced認証を用いる際、ログイン用の認証コードを生成するためにverification.phpへアクセスしたときに呼び出される関数。
     *
     * この関数の処理内容はimageregistを利用する開発者が定義する。
     * 開発者が以下の処理を定義し、この関数内で実行する。
     * 1. 改行区切りのユーザー一覧を表す文字列を用意する。
     * 2. username_check関数で$_GET[$username]が、1で用意された文字列に含まれるかを確認、ユーザー名が不正であるかユーザーがユーザー一覧に存在しない場合エラーメッセージをprintしreturnする。
     * 3. ユーザーIDが$_GET[$username]であるアカウントがアカウント登録時に
     *    advancedRegist関数で生成したハッシュ一覧の文字列を用意する。
     * 4. 3で用意された文字列の1行目の数値をログイン回数とする。
     * 5. 3で用意された文字列の、ログイン回数+1 行目の文字列を取り出す。
     * 6. 5で用意された文字列は前後半を半角コロンで区切られているため、その前半の値をprintする。
     *
     * @return none 戻り値は存在しない。定義しても使用しない。
     */
    function advancedVerification()
    {
    }
    /**
     * 認証にadvanced認証を用いる際、ログイン回数を更新するために呼び出される関数。
     *
     * この関数の処理内容はimageregistを利用する開発者が定義する。
     * ユーザー名に対応したhashlistを、保存媒体に保存する。
     *
     * @param string $username 保存先のユーザー名。
     * @param string $hashlist 保存するhashlist。
     *
     * @return bool 保存に成功したらtrue,失敗したらfalse。
     */
    function refreshLoginNumberAdvance($username, $hashlist)
    {
        return false;
    }
}
?>
