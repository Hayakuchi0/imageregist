# imageregist

png,jpg,gif画像ファイルデータをパスワードとして用いるためのモジュール


## 詳細

画像データをパスワードとして用いるため、画像データの一部をハッシュ化してデータを保存、認証時に切り取った位置を元に画像データの一部を送信、サーバー側で認証するモジュールです。
現在はテキストデータのみ送信することができます。
フロント側ではhtml5に対応したjavascriptが動作する必要があります。
バックエンドではhttpデーモン、phpが動作する必要があります。
CentOSでのPHP5.4,UbuntuでのPHP7.0における動作を確認しましたが、Windowsではimageregist/imgr\_config.phpの$id\_filed\_locate\_dirを編集する必要があります。


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

```shell
$ git clone https://github.com/Hayakuchi0/imageregist.git 
$ cd imageregist
# php install.php
$ cp imageregist* <httpdのhttpアクセス参照先>
```


## 使用方法

サンプルコードは、exampleディレクトリ内を参照してください。
また、インストール方法を参照してインストールしてから以下の手順を実行してください。

### バックエンド側

check\_verification\_code関数を呼び出すことで認証の成否が判定できます。 
POST対象ではcheck\_verification\_code関数を呼び出すようソースを記述してください。

### フロントエンド側

#### ユーザーの登録

ユーザーの登録は、以下の手順で可能です。

1. ImageRegist型のオブジェクトを作成する。この際、引数にlocalStorageで用いる名前を代入する。これはサイトごとのcookieと同じ役割を果たすので、サイト内ではこの名称は統一する。
2. 1.で作成したオブジェクトのメンバonRegistに登録時の処理を記述した無名関数を代入する。
3. ユーザー固有の画像を格納するfileタグと、ユーザーのIDを入力するためのinputタグを作成、IDをつける。
4. ユーザーの操作によって3.の2つの内容を入力
5. 1.で作成したオブジェクトのregistメソッドを呼び出す。
	1. 第一引数には画像ファイルを格納したfileタグ(3.で作成したタグ)のタグIDを指定する。省略した場合には"imgRegist"がデフォルトとして使用される。
	2. 第二引数にはユーザーのIDを入力するためのinputタグ(3.で作成したタグ)のタグIDを指定する。省略した場合には"nameRegist"がデフォルトとして使用される。
6. 1で作成したオブジェクトのregist関数を呼び出す。


#### 認証及びデータの送信

認証及びデータの送信は、以下の手順で可能です。

1. ImageRegist型のオブジェクトを作成する。この際、引数にlocalStorageで用いる名前を代入する。これはサイトごとのcookieと同じ役割を果たすので、サイト内ではこの名称は統一する。
2. 1.で作成したオブジェクトのメンバに、無名関数としてそれぞれ以下の処理を記述、代入する。
	* onSendにコンテンツ送信完了時の処理
	* onVerifyに接続成功したときの処理
	* onLoginでログインを試みた結果を受信した時の処理
3. ユーザー固有の画像を格納するfileタグと、ユーザーのIDを入力するためのinputタグを作成、IDをつける。
4. ユーザーの操作によって3.2つの内容を入力。
5. 1.で作成したオブジェクトのlogin関数を呼び出すことで認証を行い、localStorageに登録する。(ちなみに、この処理はユーザーの登録時にも行われている。)
	1. 第一引数には画像ファイルを格納したfileタグ(3.で作成したタグ)のタグIDを指定する。省略した場合には"imgRegist"がデフォルトとして使用される。
	2. 第二引数にはユーザーのIDを入力するためのinputタグ(3.で作成したタグ)のタグIDを指定する。省略した場合には"nameRegist"がデフォルトとして使用される。
6. FormData型のオブジェクトを作成し、そこに送信内容を格納する。
7. 1.で作成したオブジェクトのsendメソッドを呼び出すことでデータを送信する。
	1. 第一引数にはポスト先のアドレスを指定する。これは省略不可。
	2. 第二引数にはFormData型の送信内容(6.で作成したオブジェクト)を指定する。これは省略不可。


## Contribution

リポジトリをforkし、変更内容をCommit、Pull Requestを送信してください。

## License

[MIT](https://github.com/Hayakuchi0/imageregist/blob/master/LICENSE/imageregist)
[MIT](https://github.com/Hayakuchi0/imageregist/blob/master/LICENSE/password_compat.md)


## Authors

[Hayakuchi0](https://github.com/Hayakuchi0)
[Anthony Ferrara](https://github.com/ircmaxell)
