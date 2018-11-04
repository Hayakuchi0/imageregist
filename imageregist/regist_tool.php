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
     * @param string $input_file_imgtn  POSTするFormData型変数の
     *                                  append関数に追加する
     *                                  file型の画像データのFormData上での名前。
     * @param string $input_text_usertn POSTするFormData型変数の
     *                                  append関数に追加する
     *                                  ユーザー名を表すstring型変数のFormData上での名前。
     * @param string $user_list         改行で区切られたユーザー名の一覧を表すstring型の変数。
                                        先頭が/であればこの変数は使わずに、
                                        簡易認証のkhファイルを使用する。
     *
     * @return none
     */
    function regist($input_file_imgtn,$input_text_usertn,$user_list)
    {
        if (registCheck($input_file_imgtn, $input_text_usertn, $user_list)) {
            print(registSave(
                $_FILES[$input_file_imgtn]['tmp_name'],
                $_POST[$input_text_usertn]
            ));
        } else {
            print("ID登録に失敗しました。");
        }
    }
    /**
     * アップしたファイルが正常に処理できるか確認する関数。
     * 制限として文字数40文字以内、半角英数字以外使用禁止としている。この制限は撤廃可能。
     * ただし、img_tnが画像であることもここでチェックしてる。現在対応しているのはjpgとpngとgifのみ。
     *
     * @param string $img_tn    POSTするFormData型変数のappend関数に追加するユーザー名の名前
     * @param string $user_tn   POSTするFormData型変数のappend関数に追加するfile型の名前
     * @param string $user_list 改行で区切られたユーザー名の一覧を表すstring型の変数。
                                先頭が/であればこの変数は使わずに、簡易認証のkhファイルを使用する。
     *
     * @return bool アップロードされたファイルが正常に処理できるかどうかを返す値
     */
    function registCheck($img_tn,$user_tn,$user_list)
    {
        if (array_key_exists($user_tn, $_POST)) {
            //ちゃんと中身を入れたか確認
            $user_name=strval($_POST[$user_tn]);
            $ucheck=usernameCheck($user_name, $user_list);
            if ($ucheck) {
                $img_exist=array_key_exists($img_tn, $_FILES);
                $img_upon=false;
                if ($img_exist) {
                    $img_upon=is_uploaded_file($_FILES[$img_tn]['tmp_name']);
                }
                if ($img_upon) {
                    $img_file=$_FILES[$img_tn]['tmp_name'];
                    if (isImg($img_file)) {
                         return true;
                    } else {
                         print("対応している画像形式はjpg,gif,pngのみです。");
                    }
                } else {
                    print("画像がアップロードされていません。");
                }
            }
        } else {
            print("ユーザー名が入力されていません。");
        }
        return false;
    }
    /**
     * 認証用画像の画素の位置と、その位置に対応したハッシュ値を保存する関数。
     * 保存先は$id_files_locate_dir内の<ユーザー名>.kh
     * 保存内容はsavekh関数を参照。
     * 結果をメッセージの出力で表示する。
     * コロン区切りで先頭の数字でIDの登録に関するメッセージIDを返す。
     *
     * @param string $img_file  認証用画像ファイルのパス
     * @param string $user_name ユーザー名
     *
     * @return int メッセージID。0は登録の成功、1は保存の失敗、2はIDが既に存在することを意味する。その場合にはキーハッシュは更新されない。
     */
    function registSave($img_file,$user_name)
    {
        global $id_files_locate_dir;
        $key_hash_path=$id_files_locate_dir."".$user_name.".kh";
        if (!file_exists($key_hash_path)) {
            if (savekh($img_file, $key_hash_path)) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }
    /**
     * 認証用画像に応じたハッシュ値を保存する関数。
     * 保存結果をboolean型の値として返す。
     * 保存内容は\hinesmImageRegist\getkhを参照。
     *
     * @param string $img_file      認証用画像ファイルのパス
     * @param string $key_hash_path 認証用画像ファイル保存先のパス
     *
     * @return 保存に成功したらtrue,失敗したらfalse
     */
    function savekh($img_file,$key_hash_path)
    {
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
