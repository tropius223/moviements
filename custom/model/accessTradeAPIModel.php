<?php

	class accessTradeAPIModel //
	{
		//■処理 //

		function getResults( $iMaxRow_ = null ) //
		{
			if( !$this->apiKey ) //APIキーが設定されていない場合
				{ throw new LogicException( 'getResults を完了できません' ); }

			if( !is_null( $iMaxRow_ ) && !is_scalar( $iMaxRow_ ) ) //スカラでない場合
				{ throw new InvalidArgumentException( '引数 $iMaxRow_ は無効です[' . gettype( $iMaxRow_ ) . ']' ); }

			if( !is_null( $iMaxRow_ ) && 0 >= $iMaxRow_ ) //範囲に収まらない場合
				{ throw new OutOfRangeException( '値が想定範囲を超えています[' . $iMaxRow_ . ']' ); }

			$header = new HTTPHeaderModel();

			$header->setURL( $this->apiURL );
			$header->setParameter( 'ws_type' , 'searchprogram' );
			$header->setParameter( 'ws_ver'  , '1' );
			$header->setParameter( 'ws_id'   , $this->apiKey );
			$header->setParameter( 'stype'   , '1' );

			if( $this->firstCategory ) //カテゴリが指定されている場合
				{ $header->setParameter( 'category1'   , $this->firstCategory ); }

			if( $this->secondCategory ) //カテゴリが指定されている場合
				{ $header->setParameter( 'category2'   , $this->secondCategory ); }

			if( $this->thirdCategory ) //カテゴリが指定されている場合
				{ $header->setParameter( 'category3'   , $this->thirdCategory ); }

			$response = $header->getResponse();

			if( 200 != $response->getStatusCode() ) //正常に取得できなかった場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getStatusCode() . ']' ); }

			$pcXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $pcXML ) //XMLをパースできない場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getContents() . ']' ); }

			$header->setParameter( 'stype' , '1' );

			$response = $header->getResponse();

			if( 200 != $response->getStatusCode() ) //正常に取得できなかった場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getStatusCode() . ']' ); }

			$mobileXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $mobileXML ) //XMLをパースできない場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getContents() . ']' ); }

			$header->setParameter( 'stype' , '0' );
			$header->setParameter( 'sp'    , '1' );

			$response = $header->getResponse();

			if( 200 != $response->getStatusCode() ) //正常に取得できなかった場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getStatusCode() . ']' ); }

			$smartphoneXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $smartphoneXML ) //XMLをパースできない場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getContents() . ']' ); }

			return Array( $pcXML , $mobileXML , $smartphoneXML );
		}

		function getSubData( $iProgramID_ , $iDevice_ )
		{
			$details  = Array();
			$reHeader = new HTTPHeaderModel();
			$crHeader = new HTTPHeaderModel();

			$reHeader->setURL( $this->apiURL );
			$reHeader->setParameter( 'ws_type' , 'searchreward' );
			$reHeader->setParameter( 'ws_ver'  , '1' );
			$reHeader->setParameter( 'ws_id'   , $this->apiKey );
			$reHeader->setParameter( 'pid'     , $iProgramID_ );

			$crHeader->setURL( $this->apiURL );
			$crHeader->setParameter( 'ws_type' , 'searchcreative' );
			$crHeader->setParameter( 'ws_ver'  , '1' );
			$crHeader->setParameter( 'ws_id'   , $this->apiKey );
			$crHeader->setParameter( 'pid'     , $iProgramID_ );

			switch( $iDevice_ )
			{
				case 'pc' :
				{
					$reHeader->setParameter( 'stype' , '0' );
					$crHeader->setParameter( 'stype' , '1' );

					break;
				}

				case 'mobile' :
				{
					$reHeader->setParameter( 'stype' , '1' );
					$crHeader->setParameter( 'stype' , '1' );

					break;
				}

				case 'smartphone' :
				{
					$reHeader->setParameter( 'stype' , '0' );
					$reHeader->setParameter( 'sp'    , '1' );
					$crHeader->setParameter( 'stype' , '0' );
					$crHeader->setParameter( 'sp'    , '1' );

					break;
				}
			}

			$response = $reHeader->getResponse();

			if( 200 != $response->getStatusCode() ) //正常に取得できなかった場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getStatusCode() . ']' ); }

			$subXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $subXML ) //XMLをパースできない場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getContents() . ']' ); }

			$result[ 'reward' ] = $subXML;

			$response = $crHeader->getResponse();

			if( 200 != $response->getStatusCode() ) //正常に取得できなかった場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getStatusCode() . ']' ); }

			$subXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $subXML ) //XMLをパースできない場合
				{ throw new RuntimeException( 'getResults を完了できません[' . $response->getContents() . ']' ); }

			$result[ 'creative' ] = $subXML;

			return $result;
		}

		//■データ変更 //

		function setCategories( $iFirstCategory_ , $iSecondCategory_ , $iThirdCategory_ ) //
		{
			$this->firstCategory  = $iFirstCategory_;
			$this->secondCategory = $iSecondCategory_;
			$this->thirdCategory  = $iThirdCategory_;
		}

		//■コンストラクタ・デストラクタ //

		/**
			@brief     コンストラクタ。
			@param[in] $iAPIKey_ APIキー。
		*/
		function __construct( $iAPIKey_ ) //
		{
			if( !is_string( $iAPIKey_ ) ) //APIキーが文字列ではない場合
				{ throw new InvalidArgumentException( '引数 $iAPIKey_ は無効です' ); }

			$this->apiKey = $iAPIKey_;
		}

		//■変数 //

		var $apiURL         = 'http://xml.accesstrade.net/at/ws.html';
		var $apiKey         = null;
		var $firstCategory  = null;
		var $secondCategory = null;
		var $thirdCategory  = null;
	}
