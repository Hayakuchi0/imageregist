<?php
/**
 * ユーザーの登録を行う為に使用する関数をまとめたソース。
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
    include_once "check.php";
    include_once "password.php";

    /**
     * 認証用のデータの登録を行うための関数。この関数を用いて登録を行う。
     *
     * @return none
     */
    function regist()
    {
        global $fd_username, $fd_regist_img;
        $registResult = registCheck();
        if ($registResult==0) {
            $saveResult = savekh(
                $_FILES[$fd_regist_img]['tmp_name'],
                $_POST[$fd_username]
            );
            if ($saveResult) {
                print(0);
            } else {
                printErrorIR(30);
            }
        } else {
            printErrorIR($registResult);
        }
    }
    /**
     * アップしたファイルが正常に登録できるか確認する関数。
     *
     * 送信したユーザー名が正常であることを\hinesmImageRegist\UsernameCheck関数で確認する。
     * 送信されたファイルが画像であることも\hinesmImageRegist\isImg関数で確認する。
     * ユーザーが存在するかどうかを\hinesmImageRegist\existUser関数で確認する。
     *
     * @return int アップロードされたファイルが正常に登録できるかどうかを返す。
     *             0であれば正常に登録が可能。
     *             3であればユーザーが登録済み。それ以外は正常。
     *             20であればユーザー名が入力されていないため登録不可能。
     *             21であればアップロードされたファイルが存在しないため登録不可能。
     *             22であればアップロードされたファイルjpg,gif,jpg以外であるため登録不可能。
     *             10以上19以下であればユーザー名が不正であるため登録不可能。詳細は\hinesmImageRegist\UsernameCheckを参照。
     */
    function registCheck()
    {
        global $fd_username, $fd_regist_img;
        //ちゃんと中身を入れたか確認
        if (array_key_exists($fd_username, $_POST)) {
            $user_name=strval($_POST[$fd_username]);
            $ucheck=UsernameCheck($user_name);
            if ($ucheck==0) {
                $img_exist=array_key_exists($fd_regist_img, $_FILES);
                $img_upon=false;
                if ($img_exist) {
                    $img_upon=is_uploaded_file($_FILES[$fd_regist_img]['tmp_name']);
                }
                if ($img_upon) {
                    $img_file=$_FILES[$fd_regist_img]['tmp_name'];
                    if (isImg($img_file)) {
                        if (existUser($user_name)) {
                            return 3;
                        } else {
                            return 0;
                        }
                    } else {
                         return 22;
                    }
                } else {
                    return 21;
                }
            }
            return $ucheck;
        }
        return 20;
    }
    /**
     * 認証用画像に応じたハッシュ値を作成し、保存する関数。
     *
     * 保存先は$id_files_locate_dir内の<ユーザー名>.kh
     * 保存結果をboolean型の値として返す。
     * 保存内容は\hinesmImageRegist\getkhを参照。
     *
     * @param string $img_file  認証用画像ファイルのパス
     * @param string $user_name ユーザー名
     *
     * @return 保存に成功したらtrue,失敗したらfalse
     */
    function savekh($img_file, $user_name)
    {
        global $id_files_locate_dir;
        $key_hash_path=$id_files_locate_dir."".$user_name.".kh";
        $hashlist=getkh($img_file);
        return file_put_contents($key_hash_path, $hashlist);
    }
    /**
     * 認証用画像の画素の位置と、その位置に対応したハッシュ値を記した文字列を返す関数。
     * 1行目に0を入れ、それ以降の行はハッシュ値を出力する。
     * 画像を1次元としたときの位置をランダムに0~100000のランダムな値を選び、そこからランレングス圧縮した$regist_pixel_lengthの数値と同じ回数分の色をパスワードとして、password_hash関数を使用してハッシュ値を作成する。
     * (\hinesmImageRegist\oneDimensionRGBforString関数を参照。)
     * ハッシュ値の前には選ばれたランダムな値を格納する。
     * 1行ごとの内容は以下のとおりである。2行目以降はこの内容を、$regist_hash_linenumと同じ行数分用意する。
     * <乱数>:<乱数と画像に対応したハッシュ値>
     *
     * @param string $img_file 認証用画像ファイルのパス
     *
     * @return 上記のルールに則ったハッシュ一覧文字列
     */
    function getkh($img_file)
    {
        global $regist_pixel_length, $regist_hash_linenum;
        $hashlist="0";
        $img=imagecreatefromfile($img_file);
        $width=imagesx($img);
        $height=imagesy($img);
        for ($i=0;$i<$regist_hash_linenum;$i++) {
            $arr_header=mt_rand(0, 100000);
            $str=oneDimensionString($img, $regist_pixel_length, $arr_header);
            $hash=password_hash($str, PASSWORD_BCRYPT);
            $hashlist=$hashlist."\n".$arr_header.":".$hash;
        }
        return $hashlist;
    }
}
?>
