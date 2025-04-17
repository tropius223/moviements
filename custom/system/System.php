<?php

	/**
	 * システムコールクラス
	 * 
	 * @author 丹羽一智
	 * @version 1.0.0
	 * 
	 */
	class System extends command_base
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// ヘッダー関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * タイトルを出力。
		 * 特定の条件で出力内容を変更したい場合は$bufferにその内容を指定してください
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。 第二引数にリンクするかを真偽値で渡します。
		 */
		function drawTitle( &$gm , $rec , $args )
		{
			$buffer     = SystemUtil::getSystemData( 'site_title' );
			$scriptName = $_SERVER[ 'SCRIPT_NAME' ];

			if( preg_match( '/(\w+\.\w+)$/' , $scriptName , $matches ) ) //スクリプト名の抽出に成功した場合s
				{ $scriptName = $matches[ 1 ]; }

			switch( $scriptName ) //スクリプトの種類で分岐
			{
				case 'info.php' : //詳細ページ
					{ break; }

				case 'search.php' : //検索ページ
					{ break; }

				default :
					{ break; }
			}

			$this->addBuffer( $buffer );
		}

		/**
		 * 説明を出力。
		 * 特定の条件で出力内容を変更したい場合は$bufferにその内容を指定してください
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。 第二引数にリンクするかを真偽値で渡します。
		 */
		function drawDescription( &$gm , $rec , $args )
		{
			$buffer     = SystemUtil::getSystemData( 'description' );
			$scriptName = $_SERVER[ 'SCRIPT_NAME' ];

			if( preg_match( '/(\w+\.\w+)$/' , $scriptName , $matches ) ) //スクリプト名の抽出に成功した場合s
				{ $scriptName = $matches[ 1 ]; }

			switch( $scriptName ) //スクリプトの種類で分岐
			{
				case 'info.php' : //詳細ページ
					{ break; }

				case 'search.php' : //検索ページ
					{ break; }

				default :
					{ break; }
			}

			$this->addBuffer( $buffer );
		}

		/**
		 * キーワードを出力。
		 * 特定の条件で出力内容を変更したい場合は$bufferにその内容を指定してください
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。 第二引数にリンクするかを真偽値で渡します。
		 */
		function drawKeywords( &$gm , $rec , $args )
		{
			$buffer     = SystemUtil::getSystemData( 'keywords' );
			$scriptName = $_SERVER[ 'SCRIPT_NAME' ];

			if( preg_match( '/(\w+\.\w+)$/' , $scriptName , $matches ) ) //スクリプト名の抽出に成功した場合s
				{ $scriptName = $matches[ 1 ]; }

			switch( $scriptName ) //スクリプトの種類で分岐
			{
				case 'info.php' : //詳細ページ
					{ break; }

				case 'search.php' : //検索ページ
					{ break; }

				default :
					{ break; }
			}

			$this->addBuffer( $buffer );
		}

		/**********************************************************************************************************
		 * 汎用システム用メソッド
		 **********************************************************************************************************/

		// アップロードファイルの格納フォルダ指定
		// extで拡張子（jpg等）catで種類（image等）、その他timeformatを指定可能。複数階層の場合は/で区切る。
		var $fileDir = 'cat/Ym'; // 記述例) cat/ext/Y/md -> 格納フォルダ image/jpg/2009/1225
		
		//getHeadとgetFootの呼び出し管理
		static $head = false;
		static $foot = false;
		

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 登録関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**
		 * 登録内容確認。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param edit 編集なのか、新規追加なのかを真偽値で渡す。
		 * @return エラーがあるかを真偽値で渡す。
		 */
		function registCheck( &$gm, $edit, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************

			// チェック処理
			self::$checkData->generalCheck($edit);
		
			/*
           	// 固有のチェック処理
			switch( $_GET['type'] )
			{
				case 'nUser':
					break;
			}*/

			// エラー内容取得
			return self::$checkData->getCheck();
		}
		
		/**
		 * 複製登録条件確認。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param edit 編集なのか、新規追加なのかを真偽値で渡す。
		 * @return 複製登録が可能かを真偽値で返す。
		 */
		function copyCheck( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $LOGIN_ID;
			// **************************************************************************************

			// 管理者は全て無条件に許可
			if( 'admin' == $loginUserType )
				return true;

			switch( $_GET[ 'type' ] )
			{
				default :
					return false;
			}
		}
		
		/**
		 * 削除内容確認。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 * @return エラーがあるかを真偽値で渡す。
		 */
		function deleteCheck(&$gm, &$rec, $loginUserType, $loginUserRank ){
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $TEMPLATE_CLASS_SYSTEM;
			// **************************************************************************************
			
			return self::$checkData->getCheck();
		}
        
		
        // 項目内容消去処理	
		function deletePostData( &$gm, &$rec )
		{
			$count = count($gm[ $_GET['type'] ]->colName);
			for($i=0; $i<$count; $i++)
			{
				if(   isset(  $_POST[ $gm[ $_GET['type'] ]->colName[$i]. "_DELETE" ]  )   )
				{
					if(  $_POST[ $gm[ $_GET['type'] ]->colName[$i]. "_DELETE" ][0] == "true"  )
					{
                        $gm[ $_GET['type']]->getDB()->setData( $rec, $gm[ $_GET['type'] ]->colName[$i] , '' );
					}
				}
			}
		}   

		/**
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 * @param edit 編集なのか、新規追加なのかを真偽値で渡す。
		 * @return エラーがあるかを真偽値で渡す。
		 */
        function registCompCheck( &$gm, $rec, $loginUserType, $loginUserRank ,$edit=false){
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
            global $LOGIN_ID;
            global $CART_STATE_IN;
            global $REGIST_ERROR_DESIGN;
			// **************************************************************************************
        
			// チェック処理
            
			$check			 = true;
            $db	 = $gm[ $_GET['type'] ]->getDB();
            
            if(!$edit){
	            //重複登録チェック
	            $table	 = $db->searchTable(  $db->getTable(), 'id', '=', $db->getData( $rec, 'id' )  );
	            if($db->getRow($table) >= 1){
	                self::$checkData->addError('duplication_id');
	            }
            }

			if( $edit )
			{
				//Const/AdminData/MailDupのチェック
				$options = $gm[ $_GET[ 'type' ] ]->colEdit;

				foreach( $options as $column => $validates )
				{
					$validates = explode( '/' , $validates );

					if( in_array( 'Const' , $validates ) )
						self::$checkData->checkConst( $column , null );

					if( in_array( 'AdminData' , $validates ) )
						self::$checkData->checkAdminData( $column , null );

					if( in_array( 'MailDup' , $validates ) )
						self::$checkData->checkMailDup( $column , null );
				}
			}

           	// 固有のチェック処理
/*			switch( $_GET['type'] )
			{
                case 'adwares':
            }*/
			return self::$checkData->getCheck();
        }

		/**
		 * 登録前段階処理。
		 * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function registProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $ID_LENGTH;
			global $ID_HEADER;
			global $LOGIN_ID;
			global $ACTIVE_NONE;
			// **************************************************************************************
            
			$db	 = $gm[ $_GET['type'] ]->getDB();
            
			// IDと登録時間を記録。
			$db->setData(  $rec, 'id',			 SystemUtil::getNewId( $db, $_GET['type']) );
			$db->setData( $rec, 'regist',		 time() );
			
			// レコードに値を反映。
			switch( $_GET['type'] )
			{
				case 'nUser':
				case 'cUser':
					$db->setData( $rec , 'birth_time' , mktime( 0 , 0 , 0 , $_POST[ 'birth_month' ] , $_POST[ 'birth_day' ] , $_POST[ 'birth_year' ] ) );
					$db->setData( $rec, 'logout',		 time() );
					$db->setData( $rec, 'point',		 SystemUtil::getSystemData('def_point') );
					$db->setData( $rec, 'activate',		 $ACTIVE_NONE );
					break;
			}
			
			if(!$check) { $this->uplodeComp($gm,$db,$rec); } // ファイルのアップロード完了処理
		}



		/**
		 * 登録処理完了処理。
		 * 登録完了時にメールで内容を通知したい場合などに用います。
		 * 
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec レコードデータ。
		 */
		function registComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $MAILSEND_ADDRES;
            global $MAILSEND_NAMES;
			// **************************************************************************************
			
			$db	 = $gm[ $_GET['type'] ]->getDB();
			
			// ユーザ情報であればアクティベーションコードを記載したメールを送信。
			if(  $THIS_TABLE_IS_USERDATA[ $_GET['type'] ]  )
			{
                $activate_template = Template::getLabelFile( "ACTIVATE_MAIL" );
				Mail::send( $activate_template , $MAILSEND_ADDRES, $db->getData( $rec, 'mail' ), $gm[ $_GET['type'] ], $rec, $MAILSEND_NAMES );
				Mail::send( $activate_template , $MAILSEND_ADDRES, $MAILSEND_ADDRES, $gm[ $_GET['type'] ], $rec, $MAILSEND_NAMES );
			}
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 編集関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 編集前段階処理。
		 * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function editProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			
			// **************************************************************************************
            
			// deleteフラグがたっているカラムの内容を消去	
			$this->deletePostData( $gm , $rec );
            
            
			$db	 = $gm[ $_GET['type'] ]->getDB();
			switch( $_GET['type'] )
			{
                case 'nUser':
					$db	 = $gm[ $_GET['type'] ]->getDB();
					$db->setData( $rec, 'birthdaycount', SystemUtil::time(  $_POST['birthmonth'] , $_POST['birthday'] ,$_POST['birthyear'] ) );
                    break;
			}
			
			if(!$check) { $this->uplodeComp($gm,$db,$rec); } // ファイルのアップロード完了処理
		}



		/**
		 * 編集完了処理。
		 * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function editComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			
			// **************************************************************************************
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 削除関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 削除処理。
		 * 削除を実行する前に実行したい処理があれば、ここに記述します。
		 * 例えばユーザデータを削除する際にユーザデータに紐付けられたデータを削除する際などに有効です。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function deleteProc( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $LOGIN_ID;
			// **************************************************************************************
			
			$db		 = $gm[ $_GET['type'] ]->getDB();
            
			// 削除前段階処理
			
			// 削除実行処理
			switch( $_GET['type'] )
			{
				default:
					// レコードを削除します。
					$db->deleteRecord( $rec );
					break;
			}
			
		}



		/**
		 * 削除完了処理。
		 * 登録削除完了時に実行したい処理があればココに記述します。
		 * 削除完了メールを送信したい場合などに利用します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function deleteComp( &$gm, &$rec, $loginUserType, $loginUserRank ){
			global $LOGIN_ID;
            
            $db = $gm[$_GET['type']]->getDB();
            if( $_GET['type'] == $loginUserType && $LOGIN_ID == $db->getData( $rec , 'id' ) ){
                SystemUtil::logout($loginUserType);
            }
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 検索関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 検索前処理。
		 * 検索条件等を検索実行前に変更したい場合に利用します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param sr 検索パラメータがセット済みなSearchオブジェクト
		 */
		function searchResultProc( &$gm, &$sr, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $THIS_TABLE_IS_USERDATA;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			global $LOGIN_ID;
			// **************************************************************************************
			
			$type = SearchTableStack::getType();
			
			$db		 = $gm[ $type ]->getDB();
					
			switch( $type )
			{
                case 'nUser':
				$birthA = $sr->getValue( 'birthdaycount', 'A' );
				$birthB = $sr->getValue( 'birthdaycount', 'B' );
				
                    if( strlen($birthA) ){
                        if( $birthA == 'bottom' ){
						$sr->setValue( 'birthdaycount', "top", 'B' );
                        }else{
                            $y = date('Y');
                            $m = date('m');
                            $d = date('d');
                            
                            $sr->setValue( 'birthdaycountB', "to*".SystemUtil::time($m,$d,$y-$stt[0])."*".SystemUtil::time($m,$d,$y-$stt[1]) );
                        }
                    }
                    if( strlen($birthB) ){
                        if( $birthB == 'top' ){
						$sr->setValue( 'birthdaycount', "bottom", 'A' );
                        }else{
                            $end = explode( '*' , substr( $birthB ,3));
                            
                            $y = date('Y');
                            $m = date('m');
                            $d = date('d');
                            
                            $sr->setValue( 'birthdaycountA', "to*".SystemUtil::time($m,$d,$y-$end[0])."*".SystemUtil::time($m,$d,$y-$end[1]) );
                        }
                        
                    }
                    break;
				default:
					break;
			}
            
			
		}

		/**
		 * 検索処理。
		 * フォーム入力以外の方法で検索条件を設定したい場合に利用します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param table フォームのからの入力内容に一致するレコードを格納したテーブルデータ。
		 */
		function searchProc( &$gm, &$table, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $THIS_TABLE_IS_USERDATA;
			global $ACTIVE_ACTIVATE;
            global $ACTIVE_ACCEPT;
			// **************************************************************************************
			global $LOGIN_ID;
            global $HOME;
			// **************************************************************************************
			
			$type = SearchTableStack::getType();
			$db		 = $gm[ $type ]->getDB();
			
			switch( $type )
			{
                case 'nUser':
                    if(isset($_GET['birthdaycountA_back'])){
                        $_GET['birthdaycountA'] = $_GET['birthdaycountA_back'];
                    }
                    if(isset($_GET['birthdaycountB_back'])){
                        $_GET['birthdaycountB'] = $_GET['birthdaycountB_back'];
                    }

					if( 0 < $_GET[ 'ageA' ] )
						$table = $db->searchTable( $table , 'birth_time' , '<' , mktime( 0 , 0 , 0 , date('n') , date('j') , date( 'Y' ) - $_GET[ 'ageA' ] ) );
					if( $_GET[ 'ageB' ] )
						$table = $db->searchTable( $table , 'birth_time' , '>' , mktime( 0 , 0 , 0 , date('n') , date('j') , date( 'Y' ) - $_GET[ 'ageB' ] ) );


				default:
					if(  $THIS_TABLE_IS_USERDATA[ $_GET['type'] ]  )
					{
						if( $loginUserType != 'admin' )	 {  $table	 = $db->searchTable( $table, 'activate', 'in', array($ACTIVE_ACTIVATE ,$ACTIVE_ACCEPT) ); }
					}
					break;
			}

			if( $_GET[ 'sort' ] )
			{
				$db    = $gm[ 'adwares' ]->getDB();
				$table = $db->sortTable( $table , $_GET[ 'sort' ] , $_GET[ 'sort_PAL' ] );
			}
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 詳細情報関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**
		 * 詳細情報が閲覧されたときに表示して良い情報かを返すメソッド。
		 * activateカラムや公開可否フラグ、registやupdate等による表示期間の設定、アクセス権限によるフィルタなどを行います。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec アクセスされたレコードデータ。
		 * @return 表示して良いかどうかを真偽値で渡す。
		 */
        function infoCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
        {
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
            global $ACTIVE_ACTIVATE;
            global $ACTIVE_ACCEPT;
            global $ACTIVE_NONE;
            global $LOGIN_ID;
			// **************************************************************************************
            
			$db	 = $gm[ $_GET['type'] ]->getDB();
			
			
			// レコードに値を反映。
			switch( $_GET['type'] )
			{
				case 'nUser':
				case 'cUser':
                    if( $loginUserType != 'admin' && $db->getData( $rec, 'activate' ) == $ACTIVE_NONE ){return false;}
					break;
            }
            return true;
        }


		/**
		 * 詳細情報が閲覧されたときに呼び出される処理。
		 * 情報に対するアクセスログを取りたいときなどに有用です。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec アクセスされたレコードデータ。
		 */
		function doInfo( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************

		}

		/**
		 * 詳細情報前処理。
		 * 簡易情報変更で利用
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec アクセスされたレコードデータ。
		 */
		function infoProc( &$gm, &$rec, $loginUserType, $loginUserRank ){
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $PROGRESS_BEGIN;
			// **************************************************************************************

            // 簡易情報変更（情報ページからの内容変更処理）
			if(  isset( $_POST['post'] ) ){
                switch( $_GET['type'] ){
                    case 'nUser':
                    case 'cUser':
                    case 'sound_source':
                        if( $loginUserType == 'admin' ){
                            $db		 = $gm[ $_GET['type'] ]->getDB();
                        
                            for( $i=0; $i<count($db->colName); $i++ ){
                                if(   isset(   $_POST[ $db->colName[$i] ]  )   ){
                                    $db->setData( $rec, $db->colName[$i], $_POST[ $db->colName[$i] ] );
                                }
                            }
                            $db->updateRecord( $rec );
                        }
                        break;
                }
			}
        }



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//   アクティベート関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

        //activate判定及びアクティベート完了処理
        function activateAction( &$gm, &$rec, $loginUserType, $loginUserRank ){
        global $ACTIVE_NONE;
        global $ACTIVE_ACTIVATE;
        global $ACTIVE_ACCEPT;
        global $MAILSEND_ADDRES;
        global $MAILSEND_NAMES;
        
            $db = $gm[ $_GET['type'] ]->getDB();
            
			if(  $db->getData( $rec, 'activate' ) == $ACTIVE_NONE  )
			{

				$db->setData( $rec, 'activate', $ACTIVE_ACCEPT );
				$db->updateRecord( $rec );

                $mail_template = Template::getLabelFile( "REGIST_COMP_MAIL" );
				Mail::send( $mail_template , $MAILSEND_ADDRES, $db->getData( $rec, 'mail' ), $gm[ $_GET['type'] ], $rec , $MAILSEND_NAMES );
				Mail::send( $mail_template , $MAILSEND_ADDRES, $MAILSEND_ADDRES, $gm[ $_GET['type'] ], $rec , $MAILSEND_NAMES );
			}
            return true;
        }


		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//   ログイン関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

        //ログアウト中間処理
        //帰り値をfalseにするとログアウトが中止される
        function logoutProc( $loginUserType ){
        	
        	if( $_SESSION['ADMIN_MODE'] ){
        		unset($_SESSION['ADMIN_MODE']);
        	}
        	
            return true;
        }
        
        //ログイン中間処理
        //返り値をfalseにするとログインが中止される
        function loginProc( $check , &$loginUserType , &$id ){
        	global $gm;
			global $LOGIN_ID;
        	
        	if( $loginUserType == 'admin' && isset($_GET['type']) && isset($_GET['id']) ){
        		$loginUserType = $_GET['type'];
        		$id	= $_GET['id'];
        		$_SESSION['ADMIN_MODE'] = true;
        		return true;
        	}
        	
        	if( $_SESSION['ADMIN_MODE'] ){
        		$loginUserType = 'admin';
        		$id	= 'ADMIN';
        		unset($_SESSION['ADMIN_MODE']);
        		return true;
        	}
        	
        	//falseをスルー
            if(!$check){return $check;}
            return true;
        }
        
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 * 汎用システム描画系用メソッド
		 **********************************************************************************************************/



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 登録関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 登録フォームを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawRegistForm( &$gm, $rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $REGIST_FORM_PAGE_DESIGN;
			// **************************************************************************************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $THIS_TABLE_IS_NOHTML;
			global $MAIL_SEND_FALED_DESIGN;
			global $THIS_TABLE_IS_STEP_PC;
			global $THIS_TABLE_IS_STEP_MOBILE;
			global $terminal_type;
			// **************************************************************************************
            
            $this->setErrorMessage($gm[ $_GET['type'] ]);

			$useStep = false;

			if( 0 < $terminal_type ) //携帯端末の場合
				{ $useStep = $THIS_TABLE_IS_STEP_MOBILE[ $_GET[ 'type' ] ]; }
			else //PC端末の場合
				{ $useStep = $THIS_TABLE_IS_STEP_PC[ $_GET[ 'type' ] ]; }
            
            
			switch(  $_GET['type']  )
			{
				default:
					// 汎用処理
					if( $useStep && $gm[ $_GET[ 'type' ] ]->maxStep >= 2 )
	                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_FORM_PAGE_DESIGN' . $_POST['step'] , 'regist.php?type='. $_GET['type'] );
					else
	                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_FORM_PAGE_DESIGN' , 'regist.php?type='. $_GET['type'] );

			}
			
		
		}



		/**
		 * 登録内容確認ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 登録情報を格納したレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawRegistCheck( &$gm, $rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $REGIST_CHECK_PAGE_DESIGN;
			// **************************************************************************************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $MAIL_SEND_FALED_DESIGN;
			// **************************************************************************************
			switch(  $_GET['type']  )
			{                    
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_CHECK_PAGE_DESIGN' , 'regist.php?type='. $_GET['type'] );
			}
		}



		/**
		 * 登録完了ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 登録情報を格納したレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawRegistComp( &$gm, $rec, $loginUserType, $loginUserRank )
		{
	
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			
            Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_COMP_PAGE_DESIGN' );

		
		}



		/**
		 * 登録失敗画面を描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawRegistFaled( &$gm, $loginUserType, $loginUserRank )
		{
            $this->setErrorMessage($gm[ $_GET['type'] ]);
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
            Template::drawTemplate( $gm[ $_GET['type'] ] , null ,'' , $loginUserRank , '' , 'REGIST_FALED_DESIGN' );

//            Template::simpleDrawTemplate( 'REGIST_FALED_DESIGN' );

		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 編集関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 編集フォームを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawEditForm( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $EDIT_FORM_PAGE_DESIGN;
			global $LOGIN_ID;
            global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
            $this->setErrorMessage($gm[ $_GET['type'] ]);
            
			switch(  $_GET['type']  )
			{
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_FORM_PAGE_DESIGN' , 'edit.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
			
		
		}


		/**
		 * 編集内容確認ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawEditCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $EDIT_CHECK_PAGE_DESIGN;
			global $LOGIN_ID;
            global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			switch(  $_GET['type']  )
			{
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_CHECK_PAGE_DESIGN' , 'edit.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
		
		}



		/**
		 * 編集完了ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawEditComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $EDIT_COMP_PAGE_DESIGN;
			global $LOGIN_ID;
            global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			switch(  $_GET['type']  )
			{
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_COMP_PAGE_DESIGN' , 'edit.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
		}



		/**
		 * 編集失敗画面を描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawEditFaled( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			
			Template::drawErrorTemplate();
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 削除関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 削除確認ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawDeleteCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $DELETE_CHECK_PAGE_DESIGN;
			// **************************************************************************************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $LOGIN_ID;
			global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			switch(  $_GET['type']  )
			{
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'DELETE_CHECK_PAGE_DESIGN' , 'edit.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
			
		
		}



		/**
		 * 削除完了ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawDeleteComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $DELETE_COMP_PAGE_DESIGN;
			global $LOGIN_ID;
			global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			switch(  $_GET['type']  ){
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'DELETE_COMP_PAGE_DESIGN'  );
					break;
			}
		
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 検索関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 検索フォームを描画する。
		 *
		 * @param sr Searchオブジェクト。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawSearchForm( &$sr, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $SEARCH_FORM_PAGE_DESIGN;
			// **************************************************************************************
			$sr->addHiddenForm( 'type', $_GET['type'] );
			
			switch( $_GET['type'] )
			{
				default:
                    $file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_FORM_PAGE_DESIGN' );
                    if( strlen( $file ) )	{ print $sr->getFormString( $file , 'search.php'  ); }
                    else		{ Template::drawErrorTemplate(); }
					break;
			}
		}

        function drawSearch( &$gm, &$sr, $table, $loginUserType, $loginUserRank ){
            SearchTableStack::pushStack($table);
            
            if(  isset( $_GET['multimail'] )  ){
                $db = $gm[ $_GET['type'] ]->getDB();
                $row	 = $db->getRow( $table );
                for($i=0; $i<$row; $i++){
                    $rec	 = $db->getRecord( $table, $i );
                    $_GET['pal'][] = $db->getData( $rec, 'id' );
                }
                $_GET['type'] = 'multimail';
                include_once "regist.php";
            }else{
                Template::drawTemplate( $gm[ $_GET['type'] ] , null , $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_RESULT_DESIGN' );
            }
        }

		/**
		 * 検索結果、該当なしを描画。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawSearchNotFound( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $SEARCH_NOT_FOUND_DESIGN;
			// **************************************************************************************
			global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			Template::drawTemplate( $gm[ $_GET['type'] ] , null , $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_NOT_FOUND_DESIGN' );
/*
			switch( $_GET['type'] )
			{					
				default:
					Template::drawTemplate( $gm[ $_GET['type'] ] , null , $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_NOT_FOUND_DESIGN' );
					break;
			}
*/
		
		}

		/**
		 * 検索エラーを描画。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawSearchError( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			Template::drawErrorTemplate();
		
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 詳細ページ関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 詳細情報表示エラーを描画。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawInfoError( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			Template::drawErrorTemplate();
		
		}

		/**
		 * 詳細情報ページを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawInfo( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $INFO_PAGE_DESIGN;
			// **************************************************************************************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $LOGIN_ID;
			// **************************************************************************************
			
			switch(  $_GET['type']  )
			{
				default:
					// 汎用処理
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'INFO_PAGE_DESIGN' , 'info.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
			
		
		}

		/**
		 * テンプレートの失敗画面を描画する。
		 *
		 * @param gm templateのGUIManager
		 * @param error_name error名  デザインのパーツ名
		 */
		function getTemplateFaled( $gm, $lavel , $error_name  ){
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
            global $loginUserType;
            global $loginUserRank;
			// **************************************************************************************

            $h = Template::getTemplateString( $gm , null , $loginUserType, $loginUserRank , 'stemplate' , $lavel , null , null , 'head' );
            $h .=Template::getTemplateString( $gm , null , $loginUserType, $loginUserRank , 'stemplate' , $lavel , null , null , $error_name );
            $h .=Template::getTemplateString( $gm , null , $loginUserType, $loginUserRank , 'stemplate' , $lavel , null , null , 'foot' );
            return $h;
		}
		



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//   アクティベート関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

        function drawActivateComp( &$gm, &$rec, $loginUserType, $loginUserRank ){
                $gm[ $_GET['type'] ]->draw( Template::getLabelFile('ACTIVATE_DESIGN_HTML'), $rec );
        }
        function drawActivateFaled( &$gm, &$rec, $loginUserType, $loginUserRank ){
                $gm[ $_GET['type'] ]->draw( Template::getLabelFile('ACTIVATE_FALED_DESIGN_HTML'), $rec );
        }

		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/


		/**
		 * ファイルアップロードが行われた場合の一時処理。
		 *
		 * @param db Databaseオブジェクト
		 * @param rec レコードデータ
		 * @param colname アップロードが行われたカラム
		 * @param file ファイル配列
		 */		
		function doFileUpload( &$db, &$rec, $colname, &$file )
		{
			if( isset($_POST[ $colname . '_DELETE' ]) &&
					is_array($_POST[ $colname . '_DELETE' ]) &&
					$_POST[ $colname . '_DELETE' ][0] == "true" ){ return; }
			
			if( $file[ $colname ]['name'] != "" ){
				global $MAX_FILE_SIZE;
				if( isset( $_POST['MAX_FILE_SIZE'] ) ){
					$max_size = $_POST['MAX_FILE_SIZE'];
				}else{
					$max_size = $MAX_FILE_SIZE;
				}
				if( $file[ $colname ]['size'] > $max_size ){ return; }
				
				
				// 拡張子の取得
				preg_match( '/(\.\w*$)/', $file[ $colname ]['name'], $tmp );
				$ext		 = strtolower(str_replace( ".", "", $tmp[1] ));
				
				// ディレクトリの指定
				$dirList	 = explode( '/', $this->fileDir );
				$directory	 = 'file/tmp/';
				if(!is_dir($directory)) { mkdir( $directory, 0777 ); } //ディレクトリが存在しない場合は作成
				
				// ファイルパスの作成
				$fileName	 = $directory.md5( time(). $file[ $colname ]['name'] ).'.'.$ext;
				
				// 許可拡張子のみファイルのアップロード
				switch($ext)
				{
				case 'gif':
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'swf':
				case 'lzh':
				case 'zip':
					move_uploaded_file( $file[ $colname ]['tmp_name'], $fileName );
					$db->setData( $rec, $colname, $fileName );
					break;
				default:
					break;
				}
			}else if( $_POST[ $colname . '_filetmp' ] != ""  ){
				$db->setData( $rec, $colname, $_POST[ $colname.'_filetmp' ] );
				return;
			}else if( $_POST[ $colname ] != "" ){
				$db->setData( $rec, $colname, $_POST[ $colname ] );
			}
		}
	
		
		/**
		 * ファイルアップロードの完了処理。
		 * 一時アップロードとしていたファイルを正式アップロードへと書き換える。
		 *
		 * @param gm GUIManagerオブジェクト
		 * @param db Databaseオブジェクト
		 * @param rec レコードデータ
		 */
		function uplodeComp( &$gm, &$db, &$rec )
		{
			// カラムのうちファイルアップロードタイプのみ内容を確認する
			foreach( $db->colName as $colum )
			{
				if( $gm[$_GET['type']]->colType[$colum] == 'image' ||  $gm[$_GET['type']]->colType[$colum] == 'file' )
				{
					$before	 = $db->getData( $rec, $colum );
					$after	 = preg_replace( '/(file\/tmp\/)(\w*\.\w*)$/', '\2', $before );
					if( $before != $after )
					{// ファイルのアップロードが行われていた場合データを差し替える。
						// 拡張子の取得
						preg_match( '/(\.\w*$)/', $after, $tmp );
						$ext		 = strtolower(str_replace( ".", "", $tmp[1] ));
						// ディレクトリの指定
						$dirList	 = explode( '/', $this->fileDir );
						$directory	 = 'file/';
						foreach( $dirList as $dir )
						{
							switch($dir)
							{
							case 'ext': // 拡張子	
								$directory .= $ext.'/'; 
								break;
							case 'cat':	// 種類別
								switch($ext)
								{
								case 'gif':
								case 'jpg':
								case 'jpeg':
								case 'png':
									$cat = 'image';
									break;
								case 'swf':
									$cat = 'flash';
									break;
								case 'lzh':
								case 'zip':
									$cat = 'archive';
									break;
								default:
									$cat = 'category';
									break;
								}
								$directory .= $cat.'/'; 
								break;
							default:	// timeformat
								$directory .= date($dir).'/'; 
								break;
							}
							if(!is_dir($directory)) { mkdir( $directory, 0777 ); } //ディレクトリが存在しない場合は作成
						}
						if(!is_dir($directory)) { mkdir( $directory, 0777 ); } //ディレクトリが存在しない場合は作成
						
					    if( file_exists($before) && copy($before, $directory.$after) ){ unlink($before); }
						$db->setData( $rec, $colum, $directory.$after );
					}
				}
			}
		}


		/**
		 * 検索結果描画。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。 第二引数にリンクするかを真偽値で渡します。
		 */
		function searchResult( &$gm, $rec, $args )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			
			global $loginUserType;
			global $loginUserRank;
			
			global $resultNum;
			global $pagejumpNum;
			global $phpName;
			// **************************************************************************************
			
			$db		 = $gm->getDB();
			
			$table   = SearchTableStack::getCurrent();
			$row	 = $db->getRow( $table );
			// 変数の初期化。
			if(  !isset( $_GET['page'] )  ){ $_GET['page']	 = 0; }
			
			if( 0 < $_GET[ 'page' ] ) //ページが指定されている場合
			{
				$beginRow = $_GET[ 'page' ] * $resultNum; //ページ内の最初のレコードの行数
				$tableRow = $db->getRow( $table );        //テーブルの行数

				if( $tableRow <= $beginRow ) //テーブルの行数を超えている場合
				{
					$maxPage = ( int )( ( $tableRow - 1 ) / $resultNum ); //表示可能な最大ページ

					$_GET[ 'page' ] = $maxPage;
				}
			}

			if(  $_GET['page'] < 0 || $_GET['page'] * $resultNum + 1 > $db->getRow( $table )  )
			{
				// 検索結果を表示するページがおかしい場合

                $tgm	 = SystemUtil::getGM();
                for($i=0; $i<count($TABLE_NAME); $i++)
                {
                    $tgm[ $_GET['type'] ]->addAlias(  $TABLE_NAME[$i], $tgm[ $TABLE_NAME[$i] ]  );	
                }
				//$this->drawSearchError( $tgm , $loginUserType, $loginUserRank );
			}
			else
			{
				// 検索結果情報を出力。
				$viewTable	 = $db->limitOffset(  $table, $_GET['page'] * $resultNum, $resultNum  );
				
				switch( $args[0] )
				{
					case 'info':
						// 検索結果情報データ生成
						$gm->setVariable( 'RES_ROW', $row );
						
						$gm->setVariable( 'VIEW_BEGIN', $_GET['page'] * $resultNum + 1 );
						if( $row >= $_GET['page'] * $resultNum + $resultNum )
						{
							$gm->setVariable( 'VIEW_END', $_GET['page'] * $resultNum + $resultNum );
							$gm->setVariable( 'VIEW_ROW', $resultNum );
						}
						else
						{
							$gm->setVariable( 'VIEW_END', $row );
							$gm->setVariable( 'VIEW_ROW', $row % $resultNum );
						}
						$this->addBuffer( $this->getSearchInfo( $gm, $viewTable, $loginUserType, $loginUserRank ) );
						
						break;
						
					case 'result':
						// 検索結果をリスト表示
						for($i=0; $i<count((array)($TABLE_NAME)); $i++)
						{
							$tgm[ $_GET['type'] ]->addAlias(  $TABLE_NAME[$i], $tgm[ $TABLE_NAME[$i] ]  );	
						}
						$this->addBuffer( $this->getSearchResult( $gm, $viewTable, $loginUserType, $loginUserRank ) );
						break;
					case 'pageChange':
						$this->addBuffer( $this->getSearchPageChange( $gm, $viewTable, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNum, $phpName, 'page' )  );
						break;
					case 'setResultNum':
						$resultNum				 = $args[1];
						break;
						
					case 'setPagejumpNum':
						$pagejumpNum			 = $args[1];
						break;
						
					case 'setPhpName': // ページャーのリンクphpファイルを指定(未設定時はsearch.php)
						$phpName				 = $args[1];
						break;
					case 'row':
						$this->addBuffer( $row );
						break;
				}
			}
		}
		
		/**
		 * 検索結果描画。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。 第二引数にリンクするかを真偽値で渡します。
		 */
		function searchCreate( &$gm, $rec, $args )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			
			global $loginUserType;
			global $loginUserRank;
			
			global $resultNum;
			global $pagejumpNum;
			// **************************************************************************************

			switch($args[0]){
				case 'new':
					if( isset( $args[1] ))
						$type = $args[1];
					else
						$type = $_GET['type'];
					SearchTableStack::createSearch( $type );
					break;
				case 'run':
					SearchTableStack::runSearch();
					break;
				case 'setPal':
				case 'setParam':
					SearchTableStack::setParam($args[1],array_slice($args,2));
					break;
				case 'setVal':
				case 'setValue':
					SearchTableStack::setValue($args[1],array_slice($args,2));
					break;
				case 'setBetweenVal':
					SearchTableStack::setValue($args[1], Array( 'A' => $args[2], 'B' => $args[3] ) );
					break;
				case 'setAlias':
					SearchTableStack::setAlias($args[1],array_slice($args,2));
					break;
				case 'setAliasParam':
					SearchTableStack::setAliasParam($args[1],array_slice($args,2));
					break;
				case 'set'://予約
					break;
				case 'end':
					SearchTableStack::endSearch();
					break;
				case 'setPartsName':
					SearchTableStack::setPartsName($args[1],$args[2]);
					break;
				case 'sort':
					SearchTableStack::sort($args[1],$args[2]);
					break;
				case 'row':
					$this->addBuffer( SearchTableStack::getCurrentRow() );
					break;
			}
		}
        
		/**
		 * 検索結果をリスト描画する。
		 * ページ切り替えはこの領域で描画する必要はありません。
		 *
		 * @param gm GUIManagerオブジェクト
		 * @param table 検索結果のテーブルデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function getSearchResult( &$_gm, $table, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $gm;
			// **************************************************************************************
			
			$type = SearchTableStack::getType();
			
			switch( $type )
			{
				default:
					if(SearchTableStack::getPartsName('list'))
	                    $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , 'SEARCH_LIST_PAGE_DESIGN' , false , SearchTableStack::getPartsName('list') );
	                else
	                    $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , 'SEARCH_LIST_PAGE_DESIGN' );
                    break;
			}
            return $html;
		}

		/**
		 * 検索結果ページ切り替え部を描画する。
		 *
		 * @param gm GUIManagerオブジェクト
		 * @param table 検索結果のテーブルデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 * @param partkey 分割キー
		 */
		function getSearchPageChange( &$gm, $table, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNum, $phpName, $param )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $phpName;
            
			$type = SearchTableStack::getType();
			
			switch( $type )
			{
				default:					
					$design		 = Template::getTemplate( $loginUserType , $loginUserRank , '' , 'SEARCH_PAGE_CHANGE_DESIGN' );

					if(!strlen($phpName)) { $phpName = 'search.php'; }
					
					$html = SystemUtil::getPager( $gm, $design, $_GET, $row, $pagejumpNum, $resultNum, $phpName, $param , SearchTableStack::getPartsName('change') );
                    break;

			}
            return $html;
		}

		/**
		 * 検索結果のページ切り替え情報を取得する。
		 *
		 * @param gm GUIManagerオブジェクト
		 * @param table 検索結果のテーブルデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function getSearchInfo( &$gm, $table, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			
			$type = SearchTableStack::getType();
			
			switch( $type )
			{
				default:
					if(SearchTableStack::getPartsName('info'))
	                    $html = Template::getTemplateString( $gm , null , $loginUserType , $loginUserRank , '' , 'SEARCH_PAGE_CHANGE_DESIGN' , false , null, SearchTableStack::getPartsName('info') );
	                else
	                    $html = Template::getTemplateString( $gm , null , $loginUserType , $loginUserRank , '' , 'SEARCH_PAGE_CHANGE_DESIGN' , false , null, 'info' );
                    break;

			}
            return $html;
		}

        //main css output
        function css_load( &$gm, $rec, $args ){
        global $css_name;
        global $css_file_paths;
        global $loginUserType;
            if(isset($_GET['css_name'])){
                $css_name = $_GET['css_name'];
            }
            
            $file = Template::getTemplate( '' , 3 , $css_name , 'CSS_LINK_LIST' );
            if(strlen($file))
                $this->addBuffer( '<link rel="stylesheet" type="text/css" href="'.$file.'" media="all" />'."\n" );
            
            if( isset($css_file_paths) ){
                if( isset($css_file_paths['all']) || is_array($css_file_paths['all']) ){
                    foreach( $css_file_paths['all'] as $css_file_path ){
                        $this->addBuffer( '<link rel="stylesheet" type="text/css" href="'.$css_file_path.'" media="all" />'."\n" );
                    }
                }
                if( isset($css_file_paths[$loginUserType]) || is_array($css_file_paths[$loginUserType]) ){
                    foreach( $css_file_paths[$loginUserType] as $css_file_path ){
                        $this->addBuffer( '<link rel="stylesheet" type="text/css" href="'.$css_file_path.'" media="all" />'."\n" );
                    }
                }
            }
        }
        //main js output
        function js_load( &$gm, $rec, $args ){
            global $js_file_paths;
            global $loginUserType;
            
            if( isset($js_file_paths['all']) || is_array($js_file_paths['all']) ){
                foreach( $js_file_paths['all'] as $js_file_path ){
                	$this->addBuffer( '<script type="text/javascript" src="'.$js_file_path.'"></script>'."\n" );
            	}
        	}
            if( isset($js_file_paths[$loginUserType]) || is_array($js_file_paths[$loginUserType]) ){
                foreach( $js_file_paths[$loginUserType] as $js_file_path ){
                    $this->addBuffer( '<script type="text/javascript" src="'.$js_file_path.'"></script>'."\n" );
                }
            }
        }
        //main link output
        function link_load( &$gm, $rec, $args ){
            global $head_link_object;
            
            if( is_null($head_link_object) || !is_array($head_link_object) )
                return;
            foreach( $head_link_object as $head_link ){
                $this->addBuffer( '<link rel="'.$head_link['rel'].'" type="'.$head_link['type'].'" href="'.$head_link['href'].'" />'."\n" );
            }
        }
		/*
		 * errorメッセージの個別表示用
		 */
		function validate( &$gm, $rec, $args ){
			$this->addBuffer( self::$checkData->getError( $args[0] ) ); 
		}
		
		/*
		 * errorメッセージの個別表示用
		 */
		function is_validate( &$gm, $rec, $args ){
			$this->addBuffer( self::$checkData->isError( $args[0], $args[1] ) ); 
		}

		/**
			@brief     カラムのエラーメッセージを出力する。
			@param[in] $iGM_   GUIManagerオブジェクト。
			@param[in] $iRec_  レコードデータ。
			@param[in] $iArgs_ コマンドコメントパラメータ。次の順で指定します。
				@param 0 エラーメッセージを出力するカラム名。複数指定する場合は/で区切ります。
			@remarks   IsValidateEx の呼び出し後にこの関数を引数なしで呼び出すと、 IsValidateEx の引数を再利用します。
		*/
		function ValidateEx( &$iGM_ , $iRec_ , $iArgs_ )
		{
			List( $columns ) = $iArgs_;

			if( !$columns ) //引数が空の場合
				{ $columns = self::$ValidateExCache; }

			foreach( explode( '/' , $columns ) as $column ) //カラムの数だけ繰り返し
				{ $this->addBuffer( self::$checkData->getValidate( $column ) ); }
		}

		/**
			@brief     カラムにエラーメッセージが設定されているか確認する。
			@param[in] $iGM_   GUIManagerオブジェクト。
			@param[in] $iRec_  レコードデータ。
			@param[in] $iArgs_ コマンドコメントパラメータ。次の順で指定します。
				@li 0 エラーメッセージを追加するカラム名。複数指定する時は/で区切ります。
				@li 1 エラーが合った場合に出力する値。省略時はvalidate
		*/
		function IsValidateEx( &$iGM_ , $iRec_ , $iArgs_ )
		{
			List( $columns , $retval ) = $iArgs_;

			self::$ValidateExCache = $columns;

			foreach( explode( '/' , $columns ) as $column ) //カラムの数だけ繰り返し
			{
				$result = self::$checkData->isErrorEx( $column );

				if( $result ) //エラーが合った場合
				{
					$this->addBuffer( $retval );
					break;
				}
			}
		}

		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/


		/**********************************************************************************************************
		 * システム用メソッド
		 **********************************************************************************************************/

        static $checkData = null;
		
		/**
		 * コンストラクタ。
		 */
		function __construct()	{ $this->flushBuffer(); }
	
        /*
         * エラーメッセージをGUIManagerのvariableにセットする
         */
        function setErrorMessage(&$gm){
            if( self::$checkData && !self::$checkData->getCheck() ){
                  $gm->setVariable( 'error_msg' , self::$checkData->getError() );
                  $this->error_msg = "";
            }else{
                $gm->setVariable( 'error_msg' , '' );
            }
        }
		
		/*
		 * ページ全体で共通のheadを返する。
		 * 各種表示ページの最初に呼び出される関数
		 * 
		 * 出力に制限をかけたい場合や分岐したい場合はここで分岐処理を記載する。
		 */
		static function getHead($gm,$loginUserType,$loginUserRank){
			global $NOT_LOGIN_USER_TYPE;
			
			if( self::$head || isset( $_GET['hfnull'] ) ){ return "";}
			
			self::$head = true;
			
			$html = "";
			
			if( $loginUserType == $NOT_LOGIN_USER_TYPE )	{ $html = Template::getTemplateString( null , null , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN' ); }
			else											{ $html = Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN' ); }
			
			if($_SESSION['ADMIN_MODE']){
				$html .= Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN_ADMIN_MODE' );
			}
			return $html;
		}
		
		/*
		 * ページ全体で共通のfootを返す。
		 * 各種表示ページの最後で呼び出される関数
		 * 
		 * 出力に制限をかけたい場合や分岐したい場合はここで分岐処理を記載する。
		 */
		static function getFoot($gm,$loginUserType,$loginUserRank){
			global $NOT_LOGIN_USER_TYPE;
			
			if( self::$foot || isset( $_GET['hfnull'] ) ){ return "";}
			
			self::$foot = true;
			
			if( $loginUserType == $NOT_LOGIN_USER_TYPE )	{ return Template::getTemplateString( null , null , $loginUserType , $loginUserRank , '' , 'FOOT_DESIGN' ); }
			else											{ return Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'FOOT_DESIGN' ); }
		}

		private static $ValidateExCache = '';
	}

	
	class SearchTableStack{
		private static $stack = Array();
		private static $current_count = 0;
		private static $current_search = null;
		//private static $stack_search = Array();
		
		private static $list_parts = Array();
		private static $info_parts = Array();
		private static $change_parts = Array();
		
		static function pushStack(&$table){
			self::$stack[ self::$current_count ] = $table;
		}
		
		static function popStack(){
			self::$stack[ self::$current_count ];
		}
	
		//外部からの強制上書き
		static function setCurrent(&$table){
			self::$stack[ self::$current_count ] = $table;
		}
		
		static function getCurrent(){
			return self::$stack[ self::$current_count ];
		}
		
		static function getCurrentCount(){
			return self::$current_count;
		}
		
		static function getCurrentRow(){
			global $gm;
			
			return $gm[ self::getType() ]->getDB()->getRow( self::$stack[ self::$current_count ] );
		}
		
		static function createSearch($type){
			global $gm;
			self::$current_count++;
			
			self::$current_search = new Search($gm[ $type ],$type);
			self::$current_search->paramReset();
			
			self::$list_parts[ self::$current_count ] = "";
			self::$info_parts[ self::$current_count ] = "";
			self::$change_parts[ self::$current_count ] = "";
		}
	
		static function setValue($coumn_name,$var){
			if( count($var) == 1 ){
				self::$current_search->setValue($coumn_name,$var[0]);
			}else{
				self::$current_search->setValue($coumn_name,$var);
			}
		}
		
		static function setParam($table_name,$var){
			self::$current_search->setParamertor($table_name,$var);
		}
	
		static function setAlias($table_name,$var){
			if( is_array($var) ){
				self::$current_search->setAlias($table_name,implode( ' ', $var ) );
			}else{
				self::$current_search->setAlias($table_name,$var);
			}
		}
		static function setAliasParam($coumn_name,$var){
			self::$current_search->setAliasParam($coumn_name,$var);
		}
		
		static function runSearch(){
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			
			$sys	 = SystemUtil::getSystem( self::getType() );
			
			$sys->searchResultProc( $gm, self::$current_search, $loginUserType, $loginUserRank );
			
			$table = self::$current_search->getResult();
			
			$swapType       = $_GET[ 'type' ];
			$_GET[ 'type' ] = self::getType();

			$sys->searchProc( $gm, $table, $loginUserType, $loginUserRank );
			
			$_GET[ 'type' ] = $swapType;

			self::pushStack( $table );
		}
		
		static function endSearch(){
			self::popStack();
			self::$current_count--;
		}
		
		static function setPartsName( $type, $parts ){
			switch($type){
				case 'list':
					self::$list_parts[ self::$current_count ] = $parts;
					break;
				case 'info':
					self::$info_parts[ self::$current_count ] = $parts;
					break;
				case 'change':
					self::$change_parts[ self::$current_count ] = $parts;
					break;
			}
		}
		
		static function getPartsName($type){
			switch($type){
				case 'list':
					return self::$list_parts[ self::$current_count ];
				case 'info':
					return self::$info_parts[ self::$current_count ];
				case 'change':
					return self::$change_parts[ self::$current_count ];
			}
			return "";
		}
		
		static function getType(){
			if( self::$current_count == 0 )
				return $_GET['type'];
			else
				return self::$current_search->type;
		}
		
		static function sort($key,$param){
			self::$current_search->sort['key'] = $key;
			self::$current_search->sort['param'] = $param;
		}
	}

?>
