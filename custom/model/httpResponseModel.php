<?php

	//★クラス //

	/**
		@brief HTTPヘッダの送信結果をパースする。
	*/
	class HTTPResponseModel //
	{
		//■データ取得 //

		/**
			@brief  取得したコンテンツを取得する。
			@return 取得したコンテンツ。
		*/
		function getContents() //
			{ return $this->contents; }

		/**
			@brief  発行されたCookieを配列で取得する。
			@return Cookie配列。
		*/
		function getCookies() //
			{ return $this->cookies; }

		/**
			@brief  取得したメタデータを配列で取得する。
			@return パースされたメタデータ。
		*/
		function getMetaDatas() //
			{ return $this->metaDatas; }

		/**
			@brief  取得したメタデータを文字列で取得する。
			@return 取得したメタデータ。
		*/
		function getMetaString() //
			{ return $this->metaString; }

		/**
			@brief  ステータスコードを取得する。
			@return ステータスコード。
		*/
		function getStatusCode() //
			{ return $this->statusCode; }

		//■コンストラクタ・デストラクタ //

		/**
			@brief     コンストラクタ。
			@exception InvalidArgumentException $iHost_ , $iHeaderString_ のいずれかに文字列ではない値を指定した場合。
			@exception RuntimeException         通信中に問題が発生した場合。
			@param[in] $iHost_         通信先ホスト。
			@param[in] $iHeaderString_ HTTPヘッダ文字列。
		*/
		function __construct( $iHost_ , $iHeaderString_ = null ) //
		{
			if( is_null( $iHeaderString_ ) ) //ヘッダが指定されていない場合
			{
				$fp = fopen( $iHost_ , 'rb' );

				if( !$fp ) //URLが開けない場合
					{ throw new RuntimeException( 'コンストラクタを完了できません[' . $iHost_ . ']' ); }

				$this->statusCode = '200';

				while( !feof( $fp ) ) //空になるまで繰り返し
					{ $this->contents .= fgets( $fp ); }

				return;
			}

			if( !is_string( $iHost_ ) ) //ホストが文字列ではない場合
				{ throw new InvalidArgumentException( '引数 $iHost_ は無効です[' . $iHost_ . ']' ); }

			if( !is_string( $iHeaderString_ ) ) //ヘッダが文字列ではない場合
				{ throw new InvalidArgumentException( '引数 $iHeaderString_ は無効です[' . $iHeaderString_ . ']' ); }

			$fp = @fsockopen( $iHost_ , 80 , $errno , $errstr , 1 );

			if( !$fp ) //URLを開けない場合
				{ throw new RuntimeException( 'コンストラクタを完了できません[' . $iHost_ . '][' . $iHeaderString_ . ']' ); }

			try
			{
				fputs( $fp , $iHeaderString_ );

				if( !socket_set_timeout( $fp , 3 ) ) //タイムアウトの設定に失敗した場合
					{ throw new RuntimeException( 'コンストラクタを完了できません[' . $iHost_ . '][' . $iHeaderString_ . ']' ); }

				while( !feof( $fp ) && !$statusCode ) //ステータスコードが取れるまで繰り返し
					{ $statusCode = fgets( $fp ); }

				$statusCodes = explode( ' ' , $statusCode );

				$this->statusCode = $statusCodes[ 1 ];

				while( !feof( $fp ) ) //空になるまで繰り返し
				{
					$buffer = fgets( $fp );

					if( "\r\n" == $buffer ) //改行だけの行の場合
						{ break; }

					$this->metaString .= $buffer;
				}

				while( !feof( $fp ) ) //空になるまで繰り返し
					{ $this->contents .= fgets( $fp ); }
			}
			catch( Exception $e )
			{
				fclose( $fp );

				throw $e;
			}

			fclose( $fp );

			foreach( explode( "\r\n" , $this->metaString ) as $value ) //メタデータを処理
			{
				List( $key , $value ) = explode( ': ' , $value , 2 );

				if( 'Set-Cookie' == $key ) //Cookieの場合
				{
					foreach( explode( '; ' , $value ) as $subValue ) //全ての値を処理
					{
						List( $subKey , $subValue ) = explode( '=' , $subValue , 2 );
						$this->cookies[ $subKey ][] = $subValue;
					}
				}

				$this->metaDatas[ $key ] = $value;
			}
		}

		//■変数 //

		private $statusCode = null; ///<ステータスコードs。
		private $metaString = null; ///<取得したメタデータ。
		private $metaDatas  = null; ///<パースされたメタデータ。
		private $cookies    = null; ///<発行されたCookie。
		private $contents   = null; ///<取得したコンテンツ。
	}
