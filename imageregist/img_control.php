<?php
/**
 * 画像データを扱う為に使用する関数をまとめたソース。
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
        //画像ファイルのヘッダ一覧
    $format_list=array(
        "png_header"=>"89504e470d0a1a0a",/*8byteのpngシグネチャ*/
        "jpg_header"=>"ffd8ff",/*3byteのjpgシグネチャ(正確には先頭2byteがシグネチャだが、3byte目まではどのフォーマットもFFに統一されているので3byte目まで用意。)*/
        "gif_header"=>"474946",/*3byteのgifシグネチャ*/
        "bmp_bm_header"=>"424d",/*2byteのbmpシグネチャ。ビットマップ*/
        "bmp_ic_header"=>"4943",/*2byteのbmpシグネチャ。モノクロアイコン*/
        "bmp_ci_header"=>"4349",/*2byteのBMPシグネチャ。カラーアイコン*/
        "bmp_pt_header"=>"5054",/*2byteのBMPシグネチャ。モノクロポインタ*/
        "bmp_cp_header"=>"4350"/*2byteのBMPシグネチャ。カラーポインタ*/
    );
    /**
     * 引数に渡されたファイルが正常に処理できる画像ファイルかどうかを判定する関数。
     *
     * @param string $img_file 検査したい画像ファイルのファイルパス
     *
     * @return bool 画像ファイルが、幅高さともに1以上のjpg,gif,pngのいずれかの画像ファイルであるかを表す。
     */
    function is_img($img_file)
    {
        $img=imagecreatefromfile($img_file);
        $width=imagesx($img);
        $height=imagesy($img);
        return (($width*$height)>0);
    }
    /**
     * 画像ファイルの形式を判定する関数。jpgかpngかだった場合にtrueを返す。
     * 形式を返すだけであり、画像ファイルとして正常に処理できることは保証しない。
     * 画像の形式が対象外ならotherを返す。
     *
     * @param string $img_file 検査したい画像ファイルのファイルパス。
     *
     * @return string 画像ファイルの形式を返す。jpg,gif,png,otherの4パターンのいずれか。
     */
    function get_img_type($img_file)
    {
        global $format_list;
        $fp=fopen($img_file, "r");
        $header=fread($fp, 8);
        fclose($fp);
        foreach ($format_list as $key => $value) {
            if (compare_string_head(bin2hex($header), $value)) {
                $names=(explode("_", $key));
                if ($names[0]==="bmp") {//PHP7じゃないとbmp読み込めない
                    return "other";
                };
                return $names[0];
            }
        }
        return "other";
    }
    /**
     * 文字列の先頭が一致するかどうかを確認する関数。
     * 二つの引数の短いほうと長さを合わせ、その二つの文字列が一致したらtrueを返す。
     *
     * @param string $str1 比較対象の文字列
     * @param string $str2 比較対象の文字列
     *
     * @return bool 文字列が一致するか
     */
    function compare_string_head($str1,$str2)
    {
        $str_length=min(strlen($str1), strlen($str2));
        return (substr($str1, 0, $str_length)===substr($str2, 0, $str_length));
    }
    /**
     * 画像のパスから画像データリソースを読み込む関数。 
     *
     * @param string $img_file 読み込み対象の画像ファイルのパス
     * @param string $img_type Optional. 読み込み対象の画像ファイルの拡張子。
                               引数を省略した場合画像の拡張子は自動的に判定される。
                               引数を省略しなかった場合、判定処理は省略される。
     *
     * @return resource php-gdで用いるための画像データリソース
     */
    function imagecreatefromfile($img_file,$img_type)
    {
        if (func_num_args()<2) {
            $img_type=get_img_type($img_file);
        }
        switch($img_type) {
        case "png":
            return imagecreatefrompng($img_file);
          break;
        case "jpg":
            return imagecreatefromjpeg($img_file);
          break;
        case "gif":
            return imagecreatefromgif($img_file);
          break;
        case "bmp":
            //return imagecreatefrombmp($img_file);//PHP7以降じゃないと関数がない。
            //break;
        default:
            error_log("invaild imagetype ".$img_type);
        }
        return null;    
    }
    /**
     * ランレングス圧縮した一次元配列画像データを返す関数。
     * ARGB形式の色4byte,繰り返し回数4byteの合計8Byteのデータを繰り返し連結して、画像データを再現する。
     *
     * @param resource $img        一次元配列データへと変換したい画像リソース。
     * @param int      $img_length 一次元配列にした画像データ全体から切り出される画像データの圧縮後の長さを表す。
     * @param int      $arr_header 一次元配列にした画像データ全体から切り出される画像データの開始位置を表す。
     *
     * @return array $img_lengthの8倍の配列数のint型配列。中身は0~255の数値。先頭から8配列ごとに、赤色,赤色,青色,緑色,残りの4byteは繰り返し回数を4byteの自然数int型として表した場合と同じ回数が入る。
     */
    function oneDimensionRGBforRLE($img,$img_length,$arr_header)
    {
        $ret=array();
        $colarr=array(256*256*256,256*256,256,1);
        $width=imagesx($img);
        $height=imagesy($img);
        $srcLength=$width*$height;
        $selectionIndex=0;
        $beforeColor=imagecolorat(
            $img,
            getXfromArrayHeader(
                $arr_header,
                0,
                $width
            ),
            getYfromArrayHeader(
                $arr_header,
                $selectionIndex,
                $width,
                $height
            )
        );
        for ($index=0;$index<$img_length;$index++) {
            for ($i=0;$i<4;$i++) {
                if ($i==0) {//アルファ値を取得出来ないのでREDと同じにする。
                    $out=($beforeColor/$colarr[1])%256;
                } else {
                    $out=($beforeColor/$colarr[$i])%256;
                };
                $ret[($index*8)+$i]=(int)($out);
            }
            $continuityNum=0;
            $x=getXfromArrayHeader($arr_header, $selectionIndex, $width);
            $y=getYfromArrayHeader($arr_header, $selectionIndex, $width, $height);
            $locate=$arr_header+$selectionIndex;
            $indexmod=1;
            $colorChanged=false;
            while (!(($indexmod==0)||$colorChanged)) {
                $selectionIndex+=1;
                $indexmod=(($arr_header+$selectionIndex)%$srcLength);
                $x=getXfromArrayHeader(
                    $arr_header,
                    $selectionIndex,
                    $width
                );
                $y=getYfromArrayHeader(
                    $arr_header,
                    $selectionIndex,
                    $width,
                    $height
                );
                $colorChanged=(imagecolorat($img, $x, $y)!=$beforeColor);
                $continuityNum+=1;
            }
            for ($i=0;$i<4;$i++) {
                $ret[($index*8)+4+$i]=(($continuityNum>>((3-$i)*8))%256);
            }
            $beforeColor=imagecolorat($img, $x, $y);
        }
        return $ret;
    }
    /**
     * 現在選んでいる一次元配列の位置から、そのx位置を返す関数。
     *
     * @param int $arr_header     配列位置
     * @param int $selectionIndex 配列位置の追加位置
     * @param int $width          2次元画像だった場合の幅
     *
     * @return int 算出されたx位置
     */
    function getXfromArrayHeader($arr_header,$selectionIndex,$width)
    {
        return ($arr_header+$selectionIndex)%$width;
    }
    /**
     * 現在選んでいる一次元配列の位置から、そのy位置を返す関数。
     *
     * @param int $arr_header     配列位置
     * @param int $selectionIndex 配列位置の追加位置
     * @param int $width          2次元画像だった場合の幅
     * @param int $height         2次元画像だった場合の高さ
     *
     * @return int 算出されたy位置
     */
    function getYfromArrayHeader($arr_header,$selectionIndex,$width,$height)
    {
        return (floor(($arr_header+$selectionIndex)/$width))%$height;
    }
    /**
     * 画像を一次元配列にするための関数。
     * 配列長は画素数の4倍であり、それぞれ4nは赤、4n+1は赤、4n+2は青、4n+3は緑を表す0~255のint型の配列。
     * $arr_headerと同じ開始位置から開始し、$img_lengthと同じ画素数を用意する。
     *
     * @param resource $img        元となる画像データ
     * @param int      $img_length 一次元配列の画素数
     * @param int      $arr_header 一次元配列作成の開始位置
     *
     * @return array 変換後の一次元配列
     */
    function oneDimensionRGB($img,$img_length,$arr_header)
    {
        //ARGB format 1 dimension image
        $ret=array();
        $colarr=array(256*256*256,256*256,256,1);
        $width=imagesx($img);
        $height=imagesy($img);
        for ($i=0;$i<$img_length;$i++) {
            $x=($arr_header+$i)%$width;
            $y=(floor(($arr_header+$i)/$width))%$height;
            $col=imagecolorat($img, $x, $y);
            for ($j=0;$j<4;$j++) {
                if ($j==0) {//アルファ値を取得出来ないのでREDと同じにする。
                    $out=($col/$colarr[1])%256;
                } else {
                    $out=($col/$colarr[$j])%256;
                };
                $ret[($i*4)+$j]=(int)($out);
            }
        }
        return $ret;
    }
    /**
     * 画像を一次元配列にするための関数。
     * 配列長は画素数の4倍であり、それぞれ4nは赤、4n+1は赤、4n+2は青、4n+3は緑を表す0~255のint型の配列。
     *
     * @param resource $img 元となる画像データ
     *
     * @return array 変換後の一次元配列
     */
    function oneDimensionRGBFull($img)
    {
        return oneDimensionRGB($img, imagesx($img)*imagesy($img), 0);
    }
    /**
     * 画像を文字列にするための関数。
     * 文字列長は画素数の8倍であり、それぞれ8n及び8n+1は赤、8n+2及び8n+3は赤、8n+4及び8n+5は青、8n+6及び8n+7は緑を表す0~255のint型の配列を16進数表記したもの。
     *
     * @param resource $img 元となる画像データ
     *
     * @return string 
     */
    function oneDimensionRGBFullString($img)
    {
        return array_to_hex(oneDimensionRGBFull($img));
    }
    /**
     * 整数型の一次元配列を16進数表記の文字列に変換する関数。
     * 各値を16進数表記に直し、結合する。
     * 15以下であれば、先頭に0をつける。
     *
     * @param array $arr 元となるint型配列
     *
     * @return string int型配列の各要素を16進数表記にしてつなげた文字列
     */
    function array_to_hex($arr)
    {
        $ret="";
        $arr_length=count($arr);
        for ($i=0;$i<$arr_length;$i++) {
            if (16>$arr[$i]) {
                $ret=$ret."0";    
            }
            $arhex=dechex($arr[$i]);
            $ret=$ret.strval($arhex);
        }
        return $ret;
    }
    /**
     * 画像データをランレングス圧縮して16進数表記の文字列に変換する関数。
     * ランレングス圧縮した一次元配列画像データを返す関数。
     * ARGB形式の色4byte,繰り返し回数4byteの合計8Byteのデータを繰り返し連結して、画像データを再現する。
     * その再現したデータを2桁0字詰めの16進数表記の文字列を連結して返す。
     *
     * @param resource $img        元となる画像データ
     * @param int      $img_length ランレングス圧縮で記録する色の数
     * @param int      $arr_header 画像データを切り取る画素の開始位置
     *
     * @return string 画像データをランレングス圧縮し、切り取った配列を16進数表記の文字列に変換したもの
     */
    function oneDimensionString($img,$img_length,$arr_header)
    {
        return array_to_hex(oneDimensionRGBforRLE($img, $img_length, $arr_header));
    }
    /**
     * 整数型の配列の要素をそれぞれ加算する。
     * 加算結果が256以上となった場合、256の余除算を格納する。
     * $data_arrのほうが要素数が少なくなければならない。
     *
     * @param array $data_arr  加算対象となるint型の配列
     * @param array $color_arr 加算対象となるint型の配列
     *
     * @return array 加算結果を格納したint型の配列
     */
    function array_add($data_arr,$color_arr)
    {
        $ret=array();
        $da_length=count($data_arr);
        $ca_length=count($color_arr);
        for ($i=0;$i<$da_length;$i++) {
            $ret[$i]=binary_add($data_arr[$i], $color_arr[$i]);
        }
        return $ret;
    }
    /**
     * バイナリを加算するための関数。
     *
     * 整数型の値を加算して、256の余除算を返す関数。
     *
     * @param int $byte1 加算する値
     * @param int $byte2 加算する値
     *
     * @return int 加算結果
     */
    function binary_add($byte1,$byte2)
    {
        return ($byte1+$byte2)%256;
    }
}
?>
