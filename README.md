# imageregist

png,jpg,gif画像ファイルデータをパスワードとして用いるためのモジュール


## 詳細

画像データをパスワードとして用いるため、画像データの一部をハッシュ化してデータを保存、認証時に切り取った位置を元に画像データの一部を送信、サーバー側で認証するモジュールです。
現在はテキストデータのみ送信することができます。
フロント側ではhtml5に対応したjavascriptが動作する必要があります。
バックエンドではhttpデーモン、phpが動作する必要があります。
CentOSでのPHP5.4,UbuntuでのPHP7.0における動作を確認しましたが、Windowsではimageregist/imgr_config.phpの$id_filed_locate_dirを編集する必要があります。


## 動作確認

CentOSでのデモンストレーションです。
以下のコマンドを実行した後、webブラウザでhttp://localhost/exampleへアクセスしてください。

```
$ git clone https://github.com/Hayakuchi0/imageregist.git 
$ cd imageregist
# yum install httpd httpd-devel php php-gd
# php install.php
# cp -r example /var/www/html
# cp imageregist.js /var/www/html
# cp -r imageregist /var/www/html
# systemctl start httpd
```


## Requirement

httpd php php-gd


## インストール方法

install.phpをroot権限で実行することでハッシュの保存先ディレクトリを作成します。
imageregist.js及びimageregistディレクトリをhttpアクセス可能な同一ディレクトリに格納したのち、imageregist.jsをhtmlファイルから読み込むことで使用できます。

```
$ git clone https://github.com/Hayakuchi0/imageregist.git 
$ cd imageregist
# php install.php
$ cp imageregist* <httpdのhttpアクセス参照先>
```


## 使用方法

サンプルコードは、exampleディレクトリ内を参照してください。

### バックエンド側

check_verification_code関数を呼び出すことで認証の成否が判定できます。 

### フロントエンド側

#### ユーザーの登録

ユーザーの登録は、以下の手順で可能です。

* ImageRegist型のオブジェクトを作成(作成の際、引数に登録時の処理を渡すことで登録時の処理を定義、その際の引数のresponseTextは登録の状況を表す(0なら成功、1なら失敗、2なら既に登録済みのIDであることを表す))
* ユーザー名としてタグID,"nameRegist"を用いたタグにユーザー名を格納
* パスワードとして用いる画像を参照するためのタグとしてタグID,"imgRegist"にパスワード用の画像ファイルを格納
* 作成したImageRegist型のオブジェクトのregistメソッドを呼び出す
* 上記のタグ名は、registメソッドの呼び出し時に指定することで変更できる

#### 認証及びデータの送信

認証及びデータの送信は、以下の手順で可能です。

* ImageRegist型のオブジェクトを作成(作成の際、引数に送信時の処理及び認証コード生成の元となる数値の受信時の処理を定義できる)
* ユーザー名としてタグID,"nameSend"を用いたタグにユーザー名を格納
* 送信内容としてタグID,"writeContent"を用いたタグに送信内容を格納
* パスワードとして用いる画像を参照するためのタグとしてタグID,"imgSend"にパスワード用の画像ファイルを格納
* 作成したImageRegist型のオブジェクトのsendメソッドを、POST先と共に呼び出す
* 上記のタグ名は、sendメソッドの呼び出し時に指定することで変更できる


## Contribution

リポジトリをforkし、変更内容をCommit、Pull Requestを送信してください。

## License

[MIT](https://github.com/Hayakuchi0/imageregist/blob/master/LICENSE/imageregist)
[MIT](https://github.com/Hayakuchi0/imageregist/blob/master/LICENSE/password_compat.md)


## Author

[Hayakuchi](https://github.com/Hayakuchi0)
