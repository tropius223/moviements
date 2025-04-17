<?php

	/**
	 * 拡張命令クラス
	 * 
	 * @author 丹羽一智
	 * @version 1.0.0
	 * 
	 */
	class Extension extends command_base
	{
		/**********************************************************************************************************
		 *　アプリケーション固有メソッド
		 **********************************************************************************************************/


		/**
		 * 時間に応じた挨拶を表示します。
		 *
		 * @param gm GUIManagerオブジェクトです。このメソッドでは利用しません。
		 * @param rec 登録情報のレコードデータです。このメソッドでは利用しません。
		 * @param args コマンドコメント引数配列です。このメソッドでは利用しません。
		 */
		function hello( &$gm, $rec, $args )
		{
		
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			
			$message	 = "";
			switch(  date( "G", time() )  )
			{
				case '0':
				case '1':
				case '2':
				case '3':
					$message = '遅くまでご苦労様です。';
					break;
					
				case '4':
				case '5':
				case '6':
				case '7':
				case '8':
				case '9':
				case '10':
					$message = 'おはようございます。';
					break;
					
				case '11':
				case '12':
				case '13':
				case '14':
				case '15':
				case '16':
					$message = 'こんにちは。';
					break;
					
				case '17':
				case '18':
				case '19':
					$message = 'こんばんは。';
					break;
					
				case '20':
				case '21':
				case '22':
				case '23':
					$message = '遅くまでご苦労様です。';
					break;
			}
			
			$this->addBuffer( $message );
		}



        /**
         * サイト固有情報の出力
         *
         * args データを示す文字列
         */
		function getSiteProfile( &$gm, $rec, $args ){
        global $HOME;

            switch($args[0]){
                case 'home':
                    $this->addBuffer( $HOME );
                    break;
                case 'site_title'        :
                case 'uuid'              :
                case 'minimum_payment'   :
                case 'click_point'       :
                case 'point_to_yen_rate' :
                    $sgm = SystemUtil::getGMforType('system');
                    $sdb = $sgm->getDB();
                    $rec = $sdb->selectRecord( 'ADMIN' );
                    $this->addBuffer( $sdb->getData( $rec , $args[ 0 ] ) );
                    break;
				case 'use_click_point' :
                    $sgm   = SystemUtil::getGMforType('system');
                    $sdb   = $sgm->getDB();
                    $rec   = $sdb->selectRecord( 'ADMIN' );
					$value = $sdb->getData( $rec , $args[ 0 ] );
                    $this->addBuffer( $value ? 'TRUE' : 'FALSE' );
            }
        }

		function getUserProfile( &$iGM_ , $iRec_ , $iArgs_ )
		{
			global $loginUserType;
			global $LOGIN_ID;

			if( $NOT_LOGIN_USER_TYPE == $loginUserType ) //ログインしていない場合
				{ return; }

			$column = array_shift( $iArgs_ );

			$db    = GMList::getDB( $loginUserType );
			$rec   = $db->selectRecord( $LOGIN_ID );
			$value = $db->getData( $rec , $column );

			$this->addBuffer( $value );
		}

         //css_list output
         function draw_css_list( &$gm , $rec , $args ){
         global $css_name;
             $tgm = SystemUtil::getGMforType('template');
             $db = $tgm->getDB();
             $table = $db->searchTable( $db->getTable() , 'label' , '=' , 'CSS_LINK_LIST' );

             $row = $db->getRow($table);
             $check = '';
             for($i=0;$i<$row;$i++){
                 $rec = $db->getRecord( $table , $i );
                 $check .= '/'.$db->getData( $rec , 'target_type' );
             }
             $check = substr( $check , 1);
         
             $this->addBuffer( $gm->getCCResult( $rec, '<!--# form option value '.$css_name.' '.$check.' '.$check.' #-->' ) );
         }
         

		/*		ログイン中のユーザーの情報を得る		*/
		/*		p0 : カラム名		*/
		function self( &$_gm , $_rec , $_args )
		{
			global $loginUserType;
			global $LOGIN_ID;

			if( 'nobody' == $loginUserType )
				return;

			$gm  = SystemUtil::getGMforType( $loginUserType );
			$db  = $gm->getDB();
			$rec = $db->selectRecord( $LOGIN_ID );

			$this->addBuffer( $db->getData( $rec , $_args[0] ) );
		}

		function getMaxExchangeYen( &$iGM_ , $iRec_ , $iArgs_ )
		{
			global $LOGIN_ID;

			$point = SystemUtil::getTableData( 'nUser'  , $LOGIN_ID , 'point' );
			$rate  = SystemUtil::getTableData( 'system' , 'ADMIN'   , 'point_to_yen_rate' );

			$this->addBuffer( $point * $rate );
		}

		/*		換金可能ポイント合計取得		*/
		/*		p0 : ユーザーID		*/
		function myPoint( &$_gm , $_rec , $_args )
		{
			global $LOGIN_ID;

			$point = SystemUtil::getTableData('nUser',$LOGIN_ID,'point');

			if( 'tranc' == $_args[ 0 ] )
			{
				$point  = ( int )( $point / 1000 );
				$point *= 1000;
			}

			$this->addBuffer( $point );
		}

		/*		カラムの合計値を取得		*/
		/*		p0 : テーブル名		*/
		/*		p1 : カラム姪		*/
		/*		p2 ~ pn : カラム、演算子、値の順で検索条件		*/
		function getSum( &$_gm , $_rec , $_args )
		{
			$type = array_shift( $_args );
			$sum  = array_shift( $_args );

			$db    = Systemutil::getGMforType( $type )->getDB();
			$table = $db->getTable();

			while( count( $_args ) )
			{
				$column = array_shift( $_args );
				$op     = array_shift( $_args );
				$value  = array_shift( $_args );

				$table = $db->searchTable( $table , $column , $op , $value );
			}

			$this->addBuffer( $db->getSum( $sum , $table ) );
		}

		/*		自動的にログアウトさせる		*/
		function logout( &$_gm , $_rec , $_args )
		{
			global $loginUserType;

			$sys = SystemUtil::getSystem( $loginUserType );
			if( $sys->logoutProc( $loginUserType ) )
			SystemUtil::logout( $loginUserType );
		}

		/**
			@brief キャリア毎のジャンプ先URLを取得する。
		*/
		function getCarrierURL( &$iGM_ , $iRec_ , $iArgs_ )
		{
			global $terminal_type;

			$db = GMList::getDB( 'adwares' );

			switch( $terminal_type ) //端末の種類で分岐
			{
				case MobileUtil::$TYPE_NUM_DOCOMO: //DoCoMo
				{
					$this->addBuffer( $db->getData( $iRec_ , 'url_docomo' ) );

					break;
				}

				case MobileUtil::$TYPE_NUM_AU: //AU
				{
					$this->addBuffer( $db->getData( $iRec_ , 'url_ai' ) );

					break;
				}

				case MobileUtil::$TYPE_NUM_SOFTBANK: //SoftBank
				{
					$this->addBuffer( $db->getData( $iRec_ , 'url_softbank' ) );

					break;
				}

				case MobileUtil::$TYPE_NUM_IPHONE: //iPhone
				{
					$this->addBuffer( $db->getData( $iRec_ , 'url_iphone' ) );

					break;
				}

				case MobileUtil::$TYPE_NUM_ANDROID: //Android
				{
					$this->addBuffer( $db->getData( $iRec_ , 'url_android' ) );

					break;
				}
			}
		}

		function getNoAceptAdwaresRow( &$iGM_ , $iRec_ , $iArgs_ )
		{
			global $LOGIN_ID;

			$category = array_shift( $iArgs_ );
			$carrier  = array_shift( $iArgs_ );

			$db    = GMList::getDB( 'adwares' );
			$table = $db->getTable();
			$table = $db->searchTable( $table , 'open' , '=' , TRUE );

			$tableA = $db->searchTable( $table , 'use_limit_time' , '=' , false );
			$tableB = $db->searchTable( $table , 'use_limit_time' , '=' , true );
			$tableB = $db->searchTable( $tableB , 'limit_time' , '>' , time() );
			$table  = $db->orTable( $tableA , $tableB );

			if( $category )
				{ $table = $db->searchTable( $table , 'category' , '=' , $category ); }

			if( $carrier )
			{
				$tableA = $db->searchTable( $table , 'use_carrier_url' , '=' , false );
				$tableB = $db->searchTable( $table , 'use_carrier_url' , '=' , true );
				$tableB = $db->searchTable( $tableB , 'url_' . $carrier , '!=' , '' );
				$table  = $db->orTable( $tableA , $tableB );
			}

			$row = $db->getRow( $table );

			$this->addBuffer( $row );
		}

		function replaceGlobal( &$iGM_ , $iRec_ , $iArgs_ )
		{
			$globalName = array_shift( $iArgs_ );
			$keyName    = array_shift( $iArgs_ );
			$value      = array_shift( $iArgs_ );

			switch( strtoupper( $globalName ) )
			{
				case 'GET' :
				{
					if( array_key_exists( $keyName , $_GET ) )
					{
						self::$Replace[ 'GET' ][ $keyName ] = $_GET[ $keyName ];
						$_GET[ $keyName ]                   = $value;
					}

					break;
				}
				case 'POST' :
				{
					if( array_key_exists( $keyName , $_POST ) )
					{
						self::$Replace[ 'POST' ][ $keyName ] = $_POST[ $keyName ];
						$_POST[ $keyName ]                   = $value;

					}

					break;
				}

				default :
					{ throw Exception( "コマンドコメント引数エラー" ); }
			}
		}

		function restoreGlobal( &$iGM_ , $iRec_ , $iArgs_ )
		{
			if( array_key_exists( 'GET' , self::$Replace ) )
			{
				foreach( self::$Replace[ 'GET' ] as $key => $value )
					{ $_GET[ $key ] = $value; }
			}

			if( array_key_exists( 'POST' , self::$Replace ) )
			{
				foreach( self::$Replace[ 'POST' ] as $key => $value )
					{ $_POST[ $key ] = $value; }
			}

			self::$Replace = Array();
		}

		function getCarrierName( &$iGM_ , $iRec_ , $iArgs_ )
		{
			global $terminal_type;

			$selector = Array(
				MobileUtil::$TYPE_NUM_DOCOMO   => 'docomo'   ,
				MobileUtil::$TYPE_NUM_AU       => 'au'       ,
				MobileUtil::$TYPE_NUM_SOFTBANK => 'softbank' ,
				MobileUtil::$TYPE_NUM_IPHONE   => 'iphone'   ,
				MobileUtil::$TYPE_NUM_ANDROID  => 'android'
			);

			if( !array_key_exists( $terminal_type , $selector ) )
				{ return 'other'; }

			$this->addBuffer( $selector[ $terminal_type ] );
		}

		function casePrint( $iGM_ , $iRec_ , $iArgs_ )
		{
			$caseName    = array_shift( $iArgs_ );
			$printString = array_shift( $iArgs_ );

			preg_match( '/(.*?)([^\/]+)$/' , $_SERVER[ 'SCRIPT_NAME' ] , $match );
			$path   = $match[ 1 ];
			$script = $match[ 2 ];

			switch( $caseName )
			{
				case 'top' :
				{
					if( 'index.php' != $script )
						{ return; }

					break;
				}

				case 'adwaresSearch' :
				{
					if( 'search.php' != $script )
						{ return; }

					if( 'adwares' != $_GET[ 'type' ] )
						{ return; }

					break;
				}

                                case 'sound_sourceSearch' :
				{
					if( 'search.php' != $script )
						{ return; }

					if( 'sound_source' != $_GET[ 'type' ] )
						{ return; }

					break;
				}

				case 'nUserRegist' :
				{
					if( 'regist.php' != $script )
						{ return; }

					if( 'nUser' != $_GET[ 'type' ] )
						{ return; }

					break;
				}

				case 'companyInfo' :
				{
					if( 'info.php' != $script )
						{ return; }

					if( 'company' != $_GET[ 'type' ] )
						{ return; }

					break;
				}

				case 'inquiry' :
				{
					if( 'regist.php' != $script )
						{ return; }

					if( 'inquiry' != $_GET[ 'type' ] )
						{ return; }

					break;
				}

				default :
					{ return; }
			}

			$this->addBuffer( $printString );
		}

		function ime( $iGM_ , $iRec_ , $iArgs_ )
		{
			return;

			$mode = array_shift( $iArgs_ );

			if( 'on' == $mode )
				{ $this->addBuffer( 'style="ime-mode:active;"' ); }
			else
				{ $this->addBuffer( 'style="ime-mode:inactive;"' ); }
		}

		function getReadableRow( $iGM_ , $iRec_ , $iArgs_ )
		{
			$type  = array_shift( $iArgs_ );
			$db    = GMList::getDB( $type );
			$table = $db->getTable();
			$table = WS::Finder( $type )->searchReadableTable( $table );
			$table = WS::Finder( $type )->searchPCTable( $table );

			while( count( $iArgs_ ) )
			{
				$column = array_shift( $iArgs_ );
				$op     = array_shift( $iArgs_ );
				$value  = array_shift( $iArgs_ );

				if( 'b' == $op )
				{
					$subValue = array_shift( $iArgs_ );
					$table = $db->searchTable( $table , $column , $op , $value , $subValue );
				}
				else
					{ $table = $db->searchTable( $table , $column , $op , $value ); }
			}

			$row = $db->getRow( $table );

			$this->addBuffer( $row );
		}

		function drawCarrierNames( $iGM_ , $iRec_ , $iArgs_ )
		{
			$db    = GMList::getDB( 'adwares' );
			$names = Array();

			foreach( Array( 'url_docomo' => 'DoCoMo' , 'url_au' => 'AU' , 'url_softbank' => 'Softbank' , 'url_iphone' => 'iPhone' , 'url_android' => 'android' ) as $column => $name )
			{
				$url = $db->getData( $iRec_ , $column );

				if( $url )
					{ $names[] = $name; }
			}

			$this->addBuffer( implode( '/' , $names ) );
		}

		static $Replace = Array();
	}
?>