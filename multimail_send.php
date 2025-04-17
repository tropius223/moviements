<?PHP

	try
	{
	//get or post で渡されたデータにメールを送るだけのPHPプログラム
	//Javascriptからの呼び出しで利用する

	//送信を行ったメール数を表示する。

		include "custom/head_main.php";

		print ' { '; //json start
		if( count($_POST) > 0 ){

			ini_set("mbstring.internal_encoding","UTF-8"); // 内部文字エンコーディングをUTF-8に設定します。  

			//UTF-8の状態でstripslashesを実行する。
			$_POST['main'] = stripslashes($_POST['main']);
			$_POST['sub']  = stripslashes($_POST['sub']);

			ini_set("mbstring.internal_encoding","SJIS"); // 内部文字エンコーディングをSJISに設定します。  

			//UTF-8　→　SJIS
			$main = mb_convert_encoding( ($_POST['main']) ,'SJIS' , 'UTF-8');
			$sub =  mb_convert_encoding( ($_POST['sub']) ,'SJIS' , 'UTF-8');

			//SJISとしてaddslashes
			$main = addslashes($main);
			$sub  = addslashes($sub);

			$send	 = explode(  '/', $_POST['send_id'] );
			$db = $gm[ 'multimail' ]->getDB();
			$rec = $db->getNewRecord();
			$db->setData( $rec , 'sub' , $sub );
			$cnt=0;
			for( $i=0; $i<count($send); $i++ ){
				if( strlen( $send[$i] ) <= 0)
					break;
				// メール着信通知の送信
				for($j=0; $j<count($TABLE_NAME); $j++){
					if(  $THIS_TABLE_IS_USERDATA[ $TABLE_NAME[$j] ]  ){
						$udb		 = $gm[ $TABLE_NAME[$j] ]->getDB();
						$utable		 = $udb->searchTable(  $udb->getTable(), 'id', '=', $send[$i] );
						if( $udb->getRow($utable) != 0 ){
							$urec	 = $udb->getRecord($utable, 0);
							Mail::sendString( $sub , $main , $MAILSEND_ADDRES, $db->getData($urec, 'mail'), $MAILSEND_NAMES );
							$cnt++;
							break;
						}
					}
				}
			}
			print ' "count" : '.$cnt." ,";
			print ' "success" : true ';
		}else{
			if($multimail_send_user[$loginUserType]){
				//送信権限がありません。
				print ' "error" : "001" ,';
			}else if(count($_POST) > 0){
				print ' "error" : "002" ,';
			}else{
				print ' "error" : "000" ,';
			}
			print ' "success" : false ';
		}

		print ' } '; //json end
	}
	catch( Exception $e_ )
	{
		//エラーメッセージをログに出力
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );
	}
?>
