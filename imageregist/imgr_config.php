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
    $fd_regist_img='registImg';//ID登録時にPOSTするFormData型変数にappendで追加する際の名前。ここに認証用画像ファイルが入る。
    $fd_username='username';//ID登録時及び書き込み時にPOSTするFormDataにappendで追加する際の名前。ここにユーザー名が入る。また、ハッシュ値に対応する画素を取得するためGETするクエリの名前。
    $fd_verification_code='verificationCode';//ID登録時及び書き込み時にPOSTするFormDataにappendで追加する際の名前。ここに認証コードが入る。また、ハッシュ値に対応する画素を取得するためGETするクエリの名前。
    $regist_pixel_length=32;//送信する色の数。
    $regist_hash_linenum=16;//サーバーに保存するハッシュ値の数。
    $type_easy=true;//advanced認証を行うかどうかを選ぶ。trueであれば簡易認証を、falseであればadvanced認証を使う。
    $error_messages = array(
        "0" => "Success",/*アクセス成功*/
        "1" => "Not exist posted username yet.",/*ユーザー名として使用できる文字列だが存在しないユーザーを参照した。*/
        "2" => "Invalid image posted.",/*参照先のユーザーが存在するが、画像が正しい画像ではない。*/
        "3" => "Exist posted username already.",/*ユーザー名として使用できる文字列だがユーザーは既に存在する。*/
        "10" => "User is registed now other.",/*参照先のユーザーは他の認証処理をしている最中である。*/
        "11" => "Username is invalid. Because over 40 characters.",/*ユーザー名として40文字以上の文字を使用しているため参照を拒否。*/
        "12" => "Username is invalid. Because Username contain not alnum character.",/*ユーザー名として半角英数字以外を参照したため参照を拒否。*/
        "20" => "Username is not posted.",/*ユーザー名が入力されていないため登録不可能。*/
        "21" => "Image not uploaded.",/*ファイルがアップロードされていないため登録不可能*/
        "22" => "Upload image is not jpg, gif, png.",/*アップロードされたファイルがjpg,gif,png以外であるため登録不可能。*/
        "30" => "Internal server error in registration."/*正常なIDと画像を用いたものの、登録処理のみが失敗した。*/
    );
    /**
     * 認証にadvanced認証を用いる際、ID登録を行う為にregist.phpへアクセスしたときに呼び出される関数。
     *
     * この関数の処理内容はimageregistを利用する開発者が定義する。
     * 開発者が以下の処理を定義し、この関数内で実行する。
     * 1. \hinesmImageRegist\registCheckで登録可能なユーザーかどうかを判定する。登録不可能な場合はエラーメッセージをprintしreturnする。
     * 2. 登録可能なユーザーだった場合、\hinesmImageRegist\getkhでハッシュ値をランダムに生成する。第一引数には$_FILE['regist_img']を設定する。
     * 3. 3で生成したハッシュ値とユーザー名を紐付け、保存媒体に保存する。
     * 4. 成功メッセージをprintする。
     *
     * @return none 戻り値は存在しない。定義しても使用しない。
     */
    function advancedRegist()
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
    /**
     * 認証を行う際、ユーザーの存在確認をするため呼び出される関数。
     *
     * この関数の処理内容は、advanced認証を用いる場合にはimageregistを利用する開発者が定義する。
     * ただし、easy認証ではexistUserEasy関数の戻り値を返さなければならない。
     *
     * @param string $username 存在確認を行いたいユーザー名
     * 
     * @return bool ユーザーが存在する場合にはtrue,存在しない場合にはfalse;
     */
    function existUser($username)
    {
        return existUserEasy($username);
    }
    /**
     * 認証にadvanced認証を用いる際、ユーザーごとに登録されたハッシュ値の一覧を返すため呼び出される関数。
     *
     * この関数の処理内容は、advanced認証を用いる場合にはimageregistを利用する開発者が定義する。
     * ユーザーごとにadvancedRegist関数の3番目の手順で保存媒体に保存したテキストを、
     * 一行ごとにstring型変数として格納した配列へ変換し返す。
     * 
     * @param string $username ハッシュ値の一覧を取得したいユーザ名。
     *
     * @return array ユーザーごとのハッシュ値一覧を返す。ただし、easy認証ではgetHashStringEasy関数の戻り値を返す。
     */
    function getHashString($username)
    {
        return getHashStringEasy($username);
    }
    ///////////ここから下は編集の必要なし/////////////
    /**
     * Easy認証でキーハッシュファイルからユーザーの存在を確認するための関数。
     *
     * この関数の処理内容はimageregistを利用する開発者は定義する必要がない。
     *
     * @param string $username 存在確認を行いたいユーザー名
     *
     * @return bool キーハッシュファイルが存在する場合true、存在しない場合false
     */
    function existUserEasy($username)
    {
        global $id_files_locate_dir;
        $hash_path=$id_files_locate_dir.$username.".kh";
        return file_exists($hash_path);
    }
    /**
     * Easy認証でキーハッシュファイルに記録した文字列を取得するための関数。
     *
     * この関数の処理内容はimageregistを利用する開発者は定義する必要がない。
     *
     * @param string $username ハッシュ値の一覧を取得したいユーザ名。
     *
     * @return array キーハッシュファイルの内容を1行ずつ配列に格納したもの。
     */
    function getHashStringEasy($username)
    {
        global $id_files_locate_dir;
        $hashpath=$id_files_locate_dir.$username.".kh";
        $list=file($hashpath, FILE_IGNORE_NEW_LINES);
        return $list;
    }
    /**
     * ImageRegist特有のエラーをメッセージにしてprintするための関数。
     *
     * エラーメッセージは以下の形式で出力する。
     * ERROR:<エラーコード>;<エラーメッセージ>
     *
     * @param int $error_code エラーコード。\hinesmImageRegist\$error_messagesを参照。
     *
     * @return none 戻り値なし
     */
    function printErrorIR($error_code)
    {
         global $error_messages;
         print("ERROR:".$error_code.";".$error_messages[strval($error_code)]);
    }
}
?>
