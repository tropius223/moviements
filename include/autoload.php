<?php

	//★関数 //

	//■マジック //

	/**
		@brief     未定義のクラスの参照をフックする。
		@details   未定義のクラスが参照された場合(インスタンス生成やクラスメソッドの呼び出し)に自動的に呼び出されます。\n
		           クラス定義ファイルを検索してロードすることで、クラスを正しく参照できるようにします。
		@exception IllegalAccessException 不正なクラス名を指定した場合(attentionも参照)
		@exception LogicException         クラス定義ファイルが見つからない場合(attentionも参照)
		@param[in] $iClassName_ 生成するクラス名。
		@attention PHPのバージョンが5.3.0未満の場合、この関数はdieを呼び出して終了します(例外を外部に投げることができないため)
		@remarks   この関数が $iClassName_ のインスタンスを返す必要はありません。\n
		           対応するクラス定義ファイルのロードだけを実行してリターンすれば、クラスは正しく参照されます。
		@remarks   基本システムの設計上、module以下とcustom/view以下の自動ロードには対応できません。
		@remarks   class_existsからの呼び出しの場合、第2引数に明示的にtrueを指定していなければ、この関数はクラス定義ファイルをロードしません。
		@ingroup   SystemComponent
	*/
	function __autoload( $iClassName_ ) //
	{
		try
		{
			//呼び出し元を確認
			$stackTrace = debug_backtrace();
			$thisFrame  = array_shift( $stackTrace );
			$frameData  = array_shift( $stackTrace );
			$funcName   = $frameData[ 'function' ];

			if( 'class_exists' == $funcName ) //class_existsからの呼び出しの場合
			{
				if( 2 > count( $frameData[ 'args' ] ) ) //第2引数が指定されていない場合
					{ return; }

				if( true != $frameData[ 'args' ][ 1 ] ) //第2引数に明示的にtrueが指定されていない場合
					{ return; }
			}

			//ディレクトリトラバーサル検出
			if( preg_match( '/\W/' , $iClassName_ ) ) //英数字以外の文字が含まれる場合
				{ throw new IllegalAccessException( '不正なアクセスです[' . $iClassName_ . ']' ); }

			if( class_exists( 'WS' , false ) ) //WSクラスが定義済みの場合
				{ WS::DefClass( $iClassName_ ); }
			else //WSクラスが未定義の場合
			{
				//include/以下にあると仮定する
				$filePath = 'include/' . $iClassName_ . '.php';

				if( !is_file( $filePath ) ) //ファイルが見つからない場合
					{ throw new LogicException( '__autoload を完了できません[' . $iClassName_ . ']' ); }

				include_once $filePath;
			}
		}
		catch( Exception $e )
		{
			//例外の対応のためにPHPバージョンを解析する
			$version = 0;

			foreach( explode( '.' , phpversion() ) as $versionNum ) //バージョン情報文字列を処理
				{ $version = ( $version * 10 ) + $versionNum; }

			if( 530 <= $version ) //5.3.0以上の場合
				{ throw $e; }
			else //5.3.0未満の場合
			{
				//5.3.0以前はautoloadから再送出できないので、エラーログを出力して停止する
				$fp = fopen( 'logs/error.log' , 'a' );

				if( $fp ) //ファイルがオープンできた場合
				{
					fputs( $fp , date( '*Y/n/j H:i:s' . "\n" ) );
					fputs( $fp , $e->getMessage() . "\n" );
					fputs( $fp , '-----------------------------------------------------' . "\n\n" );
					fclose( $fp );

					//ログファイルが肥大していたらリネーム
					if( 2097152 <= filesize( 'logs/error.log' ) ) //ログファイルのサイズが2MBを超えている場合
					{
						$nowDateString = date( '_Y_m_d_H_i_s' );

						rename( 'logs/error.log' , 'logs/error.log' . $nowDateString );
					}
				}

				die( 'autoload error' );
			}
		}
	}
