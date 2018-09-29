var irpath=document.currentScript.src.substr(0,document.currentScript.src.lastIndexOf("/")+1);//このimageregist.jsが存在するディレクトリ
/**
 * <ul>
 *   <li>{@link ImageRegist#onRegist}
 *   <li>{@link ImageRegist#onSend}
 *   <li>{@link ImageRegist#onVerify}
 *   <li>{@link ImageRegist#regist}
 *   <li>{@link ImageRegist#send}
 *   <li>{@link ImageRegist#sendContent}
 *   <li>{@link ImageRegist#srcToBytesForRLE}
 *   <li>{@link ImageRegist#bytesToHexString}
 *   <li>{@link ImageRegist#colorsAdd}
 *   <li>{@link ImageRegist#getSrcRGBA}
 * </ul>
 * @constructor
 */
var ImageRegist = function(localStorageName){
	var self=this;
	/**
	 * このImageRegistオブジェクトで認証に用いる画像をブラウザに保存するときに使用するlocalStorageのItem名。string型。
	 * 必須となるstring型の引数です。
	 */
	this.localStorageName=localStorageName;
	/**
	 * このImageRegistオブジェクトで認証に用いる画像をブラウザに保存するときに使用するlocalStorageのItem名。string型。
	 * 初期値は"username"。
	 */
	this.userName="username";
	/**
	 * ID登録時のレスポンスが戻ってきたときに呼び出される抽象メソッド。このメソッドを書き換えることでそのときの処理を記述することができる。
	 * @param {XMLHttpRequest} xhr registメソッドを用いてajaxリクエストを送るのに使用したXMLHttpRequestオブジェクト
	 */
	this.onRegist=function(xhr){
		console.log("response:"+xhr.responseText);
	};
	/**
	 * コンテンツを送信したときに呼び出されるメソッド。このメソッドを書き換えることでそのときの処理を記述することができる。
	 * @param {XMLHttpRequest} xhr sendメソッド内のsendContentメソッドを用いてデータの内容を送信するためのajaxリクエストを送るのに使用したXMLHttpRequestオブジェクト
	 */
	this.onSend=function(xhr){
		console.log("arrHeader:"+xhr.responseText);
	};
	/**
	 * 画像データのハッシュ値を算出するための、開始位置を表す値を送信したときに呼び出されるメソッド。このメソッドを書き換えることでそのときの処理を記述することができる。
	 * @param {XMLHttpRequest} xhr sendメソッド内から開始位置を受信するためのajaxリクエストを送るのに使用したXMLHttpRequestオブジェクト
	 */
	this.onVerify=function(xhr){
		console.log("verifyCode:"+xhr.responseText);
	};
	/**
	 * ログインのためにlocalStorageへ画像ファイルを登録し、その画像ファイルでログインが可能かどうかを検証する際に呼び出されるメソッド。このメソッドを書き換えることでそのときの処理を記述することができる。
	 * @param {XMLHttpRequest} xhr sendメソッド内から開始位置を受信するためのajaxリクエストを送るのに使用したXMLHttpRequestオブジェクト
	 */
	this.onLogin=function(xhr){
		console.log("login:"+xhr.responseText);
	};
	/**
	ID登録用のメソッド。この関数を呼び出すことでIDを登録する。
	登録した際の挙動は、onRegistメソッドで定義する。
	@param {string} [imageTagName] ID登録の際のパスワードとして用いる画像ファイルデータを参照するためのタグのIDを表す文字列。使用しない場合にはimgRegistがデフォルトとして用いられる。
	@param {string} [usernameTagName] ID登録の際のユーザー名として用いる入力フォームを表すタグのIDを表す文字列。使用しない場合にはnameRegistがデフォルトとして用いられる。
	*/
	this.regist=function(imageTagName,usernameTagName) {
		//登録用の関数です。
		if(imageTagName==undefined) {
			imageTagName="imgRegist";
		}
		if(usernameTagName==undefined) {
			usernameTagName="nameRegist";
		}
		var xhr= new XMLHttpRequest();
		var userNameTag=document.getElementById(usernameTagName);
		var userName=userNameTag.value;
		var upImageTag=document.getElementById(imageTagName);
		var fd = new FormData();
		fd.append("username",userName);
		fd.append("registImg",upImageTag.files[0]);
		xhr.onreadystatechange=function() {
			if(xhr.readyState>=4) {
				self.login(imageTagName,usernameTagName);
				self.onRegist(xhr);
				xhr.abort();
			}
		};
		xhr.open("POST",irpath + "imageregist/regist.php",true);
		xhr.send(fd);
	};
	/**
	データ送付用のメソッド。
	@param {string} postTargetPath POST対象のプログラムのパスを表す文字列です。
	@param {FormData} postContent ポストする内容を格納したタグのIDを表す文字列です。使用しない場合は、"writeContent"がデフォルトとして用いられます。
	*/
	this.send=function(postTargetPath,postContent) {
		var self=this;
		var xhr = new XMLHttpRequest();
		var fd=new FormData();
		var userName=localStorage.getItem(self.userName);
		fd.append("username",userName);
		xhr.onreadystatechange=function() {
			if(xhr.readyState==4) {
				var loginnum=xhr.responseText;
				self.onVerify(xhr);
				xhr.abort();
				if(!isNaN(loginnum)) {
					self.sendContent(loginnum,postTargetPath,userName,postContent);
				}
			}
		};
		xhr.open("GET",irpath+"imageregist/verification.php?username="+userName,true);
		xhr.send();
	};
	/**
	 * データの送信を行うための関数。
	 * 引数となった位置を元にログイン時に使用する認証コードを生成し、データを送信する。
	 * データの送信を行うためのフォームcontentFormに既にデータは入っている。
	 * @param {int} loginNumber 認証コードを生成するための、画素の開始位置
	 * @param {string} postTargetPath POST対象のパス
	 * @param {Element} userNameTag ユーザー名を格納したタグ
	 * @param {FormData} contentForm データを送信するためのフォームデータ
	 * @param {file} verificationImage 参照対象の画像ファイル
	 */
	this.sendContent=function(loginNumber,postTargetPath,userName,contentForm) {
		var self=this;
		var imgLength=32;//通信に使う画素数。
		var arrayStart=loginNumber;//認証コードを使う最初の画素。
		var img = new Image();
		img.onload = function() {
			var canvas=document.createElement("canvas");
			var context=canvas.getContext("2d");
			canvas.width=img.width;
			canvas.height=img.height;
			context.drawImage(img,0,0);
			var src=context.getImageData(0,0,img.width,img.height);//画像のデータを持つRGBA配列。4nが赤,4n+1が緑,4n+2が青,4n+3がアルファチャンネル
			arrayStart=arrayStart%(src.data.length);//認証コードを使う最初の画素
			var arr=self.srcToBytesForRLE(src,arrayStart,imgLength);
			var verificationCode = self.bytesToHexString(arr);
			contentForm.append("username",userName);
			contentForm.append("verificationCode",verificationCode);
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange=function() {
				if(xhr.readyState>=4) {
					self.onSend(xhr);
					xhr.abort();
				}
			};
			xhr.open("POST",postTargetPath,true);
			xhr.send(contentForm);
		};
		img.src=localStorage.getItem(self.localStorageName);
	};
	/**
	 * 画像のソースデータを、ランレングス圧縮した0~255のint型配列に変換するメソッド。
	 * 添字番号が以下の場合、以下のとおりの内容である。
	 * 4n:赤色
	 * 4n+1:赤色
	 * 4n+2:緑色
	 * 4n+3:青色
	 * @param {ImageData} src 元となる画像データ
	 * @param {int} startPixel 画像を切り取る開始位置
	 * @param {int} pixelLength 変換後の配列の長さの1/16
	 * @return {array} 変換後の配列
	 */
	this.srcToBytes=function(src,startPixel,pixelLength) { //使わない予定だが念のため作成
		startPixel=(startPixel*4)%(src.data.length);//超過したら長さの余除算を入れる。
		pixelLength=pixelLength*4;
		var arr=[];
		//RGBAをARGBに変換してarrに格納。
		for(var i=0;i<pixelLength;i++) {
			if((i%4)==0) {
				//アルファチャンネルを格納する場合
				//arr[i]=src.data[arrayStart+i+3]//アルファ値が取得出来ないのでREDにする。
				arr[i]=src.data[(startPixel+i)%src.data.length];
			} else {
				//RGBを格納する場合
				arr[i]=src.data[(startPixel+i-1)%src.data.length];
			}
		}
		return arr;
	};
	/**
	 * 画像のソースデータを、ランレングス圧縮した0~255のint型配列に変換するメソッド。
	 * 添字番号が以下の場合、以下のとおりの内容である。
	 * 8n:赤色
	 * 8n+1:赤色
	 * 8n+2:緑色
	 * 8n+3:青色
	 * 8n+4~8n+7:4byteで整数を表した場合と同じ方式で上記の色の繰り返し回数を格納
	 * @param {ImageData} src 元となる画像データ
	 * @param {int} startPixel 画像を切り取る開始位置
	 * @param {int} pixelLength 配列の長さの1/16
	 * @return {array} 変換後の配列
	 */
	this.srcToBytesForRLE=function(src,startPixel,pixelLength) {
		startPixel=(startPixel*4)%(src.data.length);
		var arr=[];
		var self=this;
		var selectionIndex=0;	
		var beforeColor=self.getSrcRGBA(src,startPixel);
		//RGBAをARGBに変換してarrに格納。
		for(var i=0;i<pixelLength;i++) {
			for(j=0;j<4;j++) {
				if((j%4)==0) { //アルファチャンネルを格納する場合
					//arr[(i*8)+j]=src.data[arrayStart+i+3] //RGBAの内容をARGBとして格納するため、色を3byte先取りする。
					arr[(i*8)+j]=src.data[(startPixel+selectionIndex+j)%src.data.length]; //アルファ値が取得できないのでREDを格納しておく。
				} else { //RGBを格納する場合
					arr[(i*8)+j]=src.data[(startPixel+selectionIndex+j-1)%src.data.length];//RGBAの内容をARGBとして格納するため、色を1byte前倒しする
				}
			}
			var continuityNum=0;
			//最後のピクセル、前回走査した色と違う色のいずれかの場合でない場合次のピクセルへ
			while(!((((startPixel+selectionIndex)>0)&&(((startPixel+selectionIndex)%src.data.length)==0))||(self.getSrcRGBA(src,(startPixel+selectionIndex))!=beforeColor))) {
				selectionIndex+=4;
				continuityNum+=1;
			}
			for(var j=0;j<4;j++) {
				arr[(i*8)+4+j]=((continuityNum>>((3-j)*8))%256);
			}
			//今配列に格納した色を前の色として格納
			beforeColor=self.getSrcRGBA(src,(startPixel+selectionIndex));
		}
		return arr;
	};
	/**
	 * int型配列のそれぞれの値を16進数表記の文字列へ変換し、結合するメソッド。
	 * 15以下の値の場合には先頭に0を付与する。
	 * @param {array} dataArray 変換対象の配列
	 * @return {string} 変換後の16進数表記文字列
	 */
	this.bytesToHexString=function(dataArray){
		var ret="";
		for(var i=0;i<dataArray.length;i++) {
			if(dataArray[i]<16) {
				ret=ret+"0";
			}
			ret=ret+(dataArray[i].toString(16));
		}
		return ret;
	};
	/**
	 * int型配列2つのそれぞれの値を加算し、256の余除算を格納した値を格納する。
	 * 配列長はdataArrayと同じになる。
	 * @param {array} dataArray 加算する元となるint型配列。この配列の配列長と同じ配列が返される。
	 * @param {array} colorArray 加算する元となるint型配列。dataArrayよりも配列長が短くてはならない。
	 * @return {array} 加算結果となる配列
	 */
	this.colorsAdd=function(dataArray,colorArray){
		var ret=[];
		for(var i=0;i<dataArray.length;i++) {
			ret[i]=(dataArray[i]+colorArray[i])%256;
		}
		return ret;
	};
	/**
	 * ImageDataの添字に対応するRGBA値をint型の値として返す。
	 * @param {ImageData} src 取得元となる画像データ
	 * @param {index} index 取得したいRGBA値の添え字。4の倍数でなければならない。
	 * @return {int} RGBA値を取得
	 */
	this.getSrcRGBA=function(src,index) {
		var red=src.data[index%src.data.length]<<24;
		var green=src.data[(index+1)%src.data.length]<<16;
		var blue=src.data[(index+2)%src.data.length]<<8;
		var alpha=src.data[(index+3)%src.data.length];
		return red|green|blue|alpha;
	};
	/**
	 * ログイン処理として、localStorageへ登録を行う。
	 * 登録を行い、ログインの成否をonLogin関数で確認する。
	 * ログインが成功した場合onLogin関数の第一引数のXMLHttpResponseのresponseTextフィールドは0,失敗した場合には1となる。
	 * @param {string} [imageTagName] 認証の際のパスワードとして用いる画像ファイルデータを参照するためのタグのIDを表す文字列です。使用しない場合には"imgRegist"がデフォルトとして用いられます。
	 * @param {string} [usernameTagName] ID登録の際のユーザー名として用いる入力フォームを表すタグのIDを表す文字列です。使用しない場合には"nameRegist"がデフォルトとして用いられます。
	 */
	this.login=function(imageTagName,usernameTagName) {
		var self=this;
		self.setLocalStorageImage(imageTagName, usernameTagName, function() {
			var loginIR = new ImageRegist(
				self.localStorageName
			);
			loginIR.onSend=function(xhr){
				self.onLogin(xhr);
			};
			loginIR.onVerify=function() {};
			loginIR.send(irpath+"imageregist/login.php",new FormData(),usernameTagName);
		});
	};
	/**
	 * localStorageへ画像とユーザー名の登録を行う。
	 * localStorageのItemとして、このImageRegistオブジェクトのusername及びlocalStorageNameプロパティをItem名としてユーザー名及び認証画像を登録する。
	 * @param {string} [imageTagName] 認証の際のパスワードとして用いる画像ファイルデータを参照するためのタグのIDを表す文字列です。使用しない場合には"imgRegist"がデフォルトとして用いられます。
	 * @param {string} [usernameTagName] ID登録の際のユーザー名として用いる入力フォームを表すタグのIDを表す文字列です。使用しない場合には"nameRegist"がデフォルトとして用いられます。
	 */
	this.setLocalStorageImage=function(imageTagId, usernameTagId, onloadFunction) {
		if(imageTagId==undefined) {
			imageTagId="imgRegist";
		}
		if(usernameTagId==undefined) {
			usernameTagId="nameRegist";
		}
		var veriImgTag=document.getElementById(imageTagId);
		var nameTag=document.getElementById(usernameTagId);
		var myname=nameTag.value;
		var reader=new FileReader();
		reader.readAsDataURL(veriImgTag.files[0]);
		reader.onload=function() {
			var img = new Image();
			img.onload = function() {
				var canvas=document.createElement("canvas");
				var context=canvas.getContext("2d");
				canvas.width=img.width;
				canvas.height=img.height;
				context.drawImage(img,0,0);
				localStorage.setItem(self.userName,myname);
				localStorage.setItem(self.localStorageName,canvas.toDataURL());
				onloadFunction();
			};
			img.src=reader.result;
		};
	};
};
