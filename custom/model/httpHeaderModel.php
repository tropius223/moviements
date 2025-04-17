<?php

	//★クラス //

	/**
		@brief HTTPヘッダを構築して送信する。
	*/
	class HTTPHeaderModel //
	{
		//■処理 //

		/**
			@brief     レスポンスオブジェクトを取得する。
			@details   現在の設定で通信を行い、結果をレスポンスオブジェクトで取得します。
			@exception LogicException 設定が不十分な状態でこのメソッドを呼び出した場合。
			@return    レスポンスオブジェクト。
		*/
		function getResponse() //
		{
			if( !$this->urlHost ) //ホストが空の場合
				{ throw new LogicException( 'getResponse を完了できません' ); }

			if( 'GET' == $this->method ) //メソッドがGETの場合
			{
				$url = $this->toURL();

				return new HTTPResponseModel( $url );
			}
			else //メソッドがPOSTの場合
			{
				$header = $this->toString();

				return new HTTPResponseModel( $this->urlHost , $header );
			}
		}

		//■データ取得 //

		/**
			@brief     HTTP URLを取得する。
			@exception LogicException 設定が不十分な状態でこのメソッドを呼び出した場合。
			@return    URL文字列。
		*/
		function toURL() //
		{
			if( !$this->urlHost ) //ホストが空の場合
				{ throw new LogicException( 'toString を完了できません' ); }

			$parameters = Array();

			foreach( $this->parameters as $key => $value ) //全てのパラメータを処理
			{
				if( is_array( $value ) ) //値が配列の場合
				{
					foreach( $value as $subValue ) //全ての値を処理
						{ $parameters[] = $key . '[]=' . $subValue; }
				}
				else //値がスカラの場合
					{ $parameters[] = $key . '=' . $value; }
			}

			$parameter = implode( '&' , $parameters );

			return 'http://' . $this->urlHost . $this->urlPath . '?' . $parameter;
		}

		/**
			@brief     HTTPヘッダ設定を文字列で取得する。
			@exception LogicException 設定が不十分な状態でこのメソッドを呼び出した場合。
			@return    HTTPヘッダ文字列。
		*/
		function toString() //
		{
			if( !$this->urlHost ) //ホストが空の場合
				{ throw new LogicException( 'toString を完了できません' ); }

			$parameters = Array();
			$cookies    = Array();

			foreach( $this->parameters as $key => $value ) //全てのパラメータを処理
			{
				if( is_array( $value ) ) //値が配列の場合
				{
					foreach( $value as $subValue ) //全ての値を処理
						{ $parameters[] = $key . '[]=' . urlencode( $subValue ); }
				}
				else //値がスカラの場合
					{ $parameters[] = $key . '=' . urlencode( $value ); }
			}

			foreach( $this->cookies as $key => $value ) //全てのCookieを処理
			{
				if( is_array( $value ) ) //値が配列の場合
				{
					foreach( $value as $subValue ) //全ての値を処理
						{ $cookies[] = $key . '[]=' . urlencode( $subValue ); }
				}
				else //値がスカラの場合
					{ $cookies[] = $key . '=' . urlencode( $value ); }
			}

			$parameter = implode( '&' , $parameters );
			$cookie    = implode( '&' , $cookies );

			if( 'GET' == $this->method && $parameter ) //GETパラメータがある場合
				{ $request =  $this->method . ' ' . $this->urlPath . '?' . $parameter . ' HTTP/1.1' . "\r\n"; }
			else //GETパラメータがない、またはPOSTメソッドの場合
				{ $request =  $this->method . ' ' . $this->urlPath . ' HTTP/1.1' . "\r\n"; }

			$request .= 'Host: ' . $this->urlHost . "\r\n";
			$request .= 'User-Agent: ' . $this->userAgent . "\r\n";
			$request .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
			$request .= 'Content-Length: ' . strlen( $parameter ) . "\r\n";
			$request .= 'Connection: Close' . "\r\n";

			if( $this->referer ) //リファラが設定されている場合
				{ $request .= 'Referer: ' . $this->referer . "\r\n"; }

			if( $cookie ) //Cookieが存在する場合
				{ $request .= 'Cookie: ' . $cookie . "\r\n"; }

			if( 'POST' == $this->method && $parameter ) //POSTパラメータが存在する場合
				{ $request .= "\r\n" . $parameter; }

			$request .= "\r\n";

			return $request;
		}

		//■データ変更 //

		/**
			@brief     送信するCookieを設定する。
			@exception InvalidArgumentException $iName_ にスカラではない値を指定した場合。
			@param[in] $iName_  Cookieの名前。
			@param[in] $iValue_ Cookieの値。
		*/
		function setCookie( $iName_ , $iValue_ ) //
		{
			if( !is_scalar( $iName_ ) ) //スカラではない場合
				{ throw new InvalidArgumentException( '引数 $iName_ は無効です[' . gettype( $iName_ ) . ']' ); }

			$this->cookies[ $iName_ ] = $iValue_;
		}

		/**
			@brief     パラメータの送信方法を設定する。
			@exception InvalidArgumentException $iMethod_ に無効な値を指定した場合。
			@param[in] $iMethod_ 送信方法(GET/POST)
		*/
		function setMethod( $iMethod_ ) //
		{
			if( !is_scalar( $iMethod_ ) ) //スカラではない場合
				{ throw new InvalidArgumentException( '引数 $iMethod_ は無効です[' . gettype( $iMethod_ ) . ']' ); }

			$method = strtoupper( $iMethod_ );

			switch( $method ) //値で分岐
			{
				case 'GET' :
				case 'POST' :
				{
					$this->method = $method;

					break;
				}

				default :
					{ throw new InvalidArgumentException( '引数 $iMethod_ は無効です[' . $iMethod_ . ']' ); }
			}
		}

		/**
			@brief     送信するパラメータを設定する。
			@exception InvalidArgumentException $iName_ にスカラではない値を指定した場合。
			@param[in] $iName_  パラメータの名前。
			@param[in] $iValue_ パラメータの値。
		*/
		function setParameter( $iName_ , $iValue_ ) //
		{
			if( !is_scalar( $iName_ ) ) //スカラではない場合
				{ throw new InvalidArgumentException( '引数 $iName_ は無効です[' . gettype( $iName_ ) . ']' ); }

			$this->parameters[ $iName_ ] = $iValue_;
		}

		/**
			@brief     リファラ情報を設定する。
			@exception InvalidArguentException $iReferer_ にスカラではない値を指定した場合。
			@param[in] $iReferer_ リファラ。
		*/
		function setReferer( $iReferer_ ) //
		{
			if( !is_scalar( $iReferer_ ) ) //スカラではない場合
				{ throw new InvalidArgumentException( '引数 $iReferer_ は無効です[' . gettype( $iReferer_ ) . ']' ); }

			$this->referer = $iReferer_;
		}

		/**
			@brief     通信先URLを設定する。
			@exception InvalidArgumentException $iURL_ に無効な値を指定した場合。
			@param[in] $iURL_ 通信先URL。
		*/
		function setURL( $iURL_ ) //
		{
			if( !is_scalar( $iURL_ ) ) //スカラではない場合
				{ throw new InvalidArgumentException( '引数 $iURL_ は無効です[' . gettype( $iURL_ ) . ']' ); }

			$parseResult = parse_url( $iURL_ );

			if( !$parseResult ) //URLをパースできない場合
				{ throw new InvalidArgumentException( '引数 $iURL_ は無効です[' . $iURL_ . ']' ); }

			if( 'http' != $parseResult[ 'scheme' ] ) //URLのスキームがHTTPではない場合
				{ throw new InvalidArgumentException( '引数 $iURL_ は無効です[' . $iURL_ . ']' ); }

			if( !$parseResult[ 'host' ] ) //URLのホストが不明の場合
				{ throw new InvalidArgumentException( '引数 $iURL_ は無効です[' . $iURL_ . ']' ); }

			$this->urlHost = $parseResult[ 'host' ];
			$this->urlPath = $parseResult[ 'path' ];
		}

		/**
			@brief     送信するユーザーエージェントを設定する。
			@exception InvalidArgumentException $iUserAgent_ にスカラではない値を指定した場合。
			@param[in] $iUserAgnet_ ユーザーエージェントの値。
		*/
		function setUserAgent( $iUserAgent_ ) //
		{
			if( !is_scalar( $iUserAgent_ ) ) //スカラではない場合
				{ throw new InvalidArgumentException( '引数 $iUserAgent_ は無効です[' . gettype( $iUserAgent_ ) . ']' ); }

			$this->userAgent = $iUserAgent_;
		}

		//■コンストラクタ・デストラクタ

		/**
			@brief コンストラクタ。
		*/
		function __construct()
			{ $this->userAgent = 'PHP/' . phpversion(); }

		//■変数 //

		private $urlHost    = null;    ///<通信先URLのホスト名。
		private $urlPath    = null;    ///<通信先URLのパス。
		private $method     = 'GET';   ///<パラメータの送信メソッド。
		private $userAgent  = null;    ///<通信時のユーザーエージェント。
		private $parameters = Array(); ///<送信するパラメータ。
		private $cookies    = Array(); ///<送信するCookie。
		private $referer    = null;    ///<送信するリファラ。
	}
