# imageregist

imageregistをあらゆる保存媒体に対応させた方式「advanced認証」のブランチ


## 詳細

imageregistにおいてtype_easyをfalseにした際の動作、これをadvanced認証と定義する。
このブランチのコードはMySQLを使ったサンプルコードとなっています。
データベースの構成はdclamp/buildtools/myweb/init.sql、
データベースの操作及びdclmapの編集例はimageregist/imgr_config.phpです。
(php側からのDBのデータはdclamp/buildtools/docker-compose.ymlを参照してください。)
それ以外はフロント側含む全てのコードに変更ありません。


## 動作確認

docker内でLAMP環境を構築した場合のデモンストレーションです。
以下のコマンドを実行した後、webブラウザでhttp://localhost:8001/exampleへアクセスしてください。

```
$ git clone -b advanced_ir https://github.com/Hayakuchi0/imageregist.git 
$ cd imageregist/dclamp/buildtools
# ./buildDocker.sh
```


## Requirement

git docker docker-compose


## Contribution

リポジトリをforkし、変更内容をCommit、Pull Requestを送信してください。


## License

[MIT](https://github.com/Hayakuchi0/imageregist/blob/master/LICENSE/imageregist)
[MIT](https://github.com/Hayakuchi0/imageregist/blob/master/LICENSE/password_compat.md)


## Authors

[Hayakuchi0](https://github.com/Hayakuchi0)
[Anthony Ferrara](https://github.com/ircmaxell)
