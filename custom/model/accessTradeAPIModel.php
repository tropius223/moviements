<?php

	class accessTradeAPIModel //
	{
		//������ //

		function getResults( $iMaxRow_ = null ) //
		{
			if( !$this->apiKey ) //API�L�[���ݒ肳��Ă��Ȃ��ꍇ
				{ throw new LogicException( 'getResults �������ł��܂���' ); }

			if( !is_null( $iMaxRow_ ) && !is_scalar( $iMaxRow_ ) ) //�X�J���łȂ��ꍇ
				{ throw new InvalidArgumentException( '���� $iMaxRow_ �͖����ł�[' . gettype( $iMaxRow_ ) . ']' ); }

			if( !is_null( $iMaxRow_ ) && 0 >= $iMaxRow_ ) //�͈͂Ɏ��܂�Ȃ��ꍇ
				{ throw new OutOfRangeException( '�l���z��͈͂𒴂��Ă��܂�[' . $iMaxRow_ . ']' ); }

			$header = new HTTPHeaderModel();

			$header->setURL( $this->apiURL );
			$header->setParameter( 'ws_type' , 'searchprogram' );
			$header->setParameter( 'ws_ver'  , '1' );
			$header->setParameter( 'ws_id'   , $this->apiKey );
			$header->setParameter( 'stype'   , '1' );

			if( $this->firstCategory ) //�J�e�S�����w�肳��Ă���ꍇ
				{ $header->setParameter( 'category1'   , $this->firstCategory ); }

			if( $this->secondCategory ) //�J�e�S�����w�肳��Ă���ꍇ
				{ $header->setParameter( 'category2'   , $this->secondCategory ); }

			if( $this->thirdCategory ) //�J�e�S�����w�肳��Ă���ꍇ
				{ $header->setParameter( 'category3'   , $this->thirdCategory ); }

			$response = $header->getResponse();

			if( 200 != $response->getStatusCode() ) //����Ɏ擾�ł��Ȃ������ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getStatusCode() . ']' ); }

			$pcXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $pcXML ) //XML���p�[�X�ł��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getContents() . ']' ); }

			$header->setParameter( 'stype' , '1' );

			$response = $header->getResponse();

			if( 200 != $response->getStatusCode() ) //����Ɏ擾�ł��Ȃ������ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getStatusCode() . ']' ); }

			$mobileXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $mobileXML ) //XML���p�[�X�ł��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getContents() . ']' ); }

			$header->setParameter( 'stype' , '0' );
			$header->setParameter( 'sp'    , '1' );

			$response = $header->getResponse();

			if( 200 != $response->getStatusCode() ) //����Ɏ擾�ł��Ȃ������ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getStatusCode() . ']' ); }

			$smartphoneXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $smartphoneXML ) //XML���p�[�X�ł��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getContents() . ']' ); }

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

			if( 200 != $response->getStatusCode() ) //����Ɏ擾�ł��Ȃ������ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getStatusCode() . ']' ); }

			$subXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $subXML ) //XML���p�[�X�ł��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getContents() . ']' ); }

			$result[ 'reward' ] = $subXML;

			$response = $crHeader->getResponse();

			if( 200 != $response->getStatusCode() ) //����Ɏ擾�ł��Ȃ������ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getStatusCode() . ']' ); }

			$subXML = simplexml_load_string( $response->getContents() );

			if( FALSE === $subXML ) //XML���p�[�X�ł��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResults �������ł��܂���[' . $response->getContents() . ']' ); }

			$result[ 'creative' ] = $subXML;

			return $result;
		}

		//���f�[�^�ύX //

		function setCategories( $iFirstCategory_ , $iSecondCategory_ , $iThirdCategory_ ) //
		{
			$this->firstCategory  = $iFirstCategory_;
			$this->secondCategory = $iSecondCategory_;
			$this->thirdCategory  = $iThirdCategory_;
		}

		//���R���X�g���N�^�E�f�X�g���N�^ //

		/**
			@brief     �R���X�g���N�^�B
			@param[in] $iAPIKey_ API�L�[�B
		*/
		function __construct( $iAPIKey_ ) //
		{
			if( !is_string( $iAPIKey_ ) ) //API�L�[��������ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iAPIKey_ �͖����ł�' ); }

			$this->apiKey = $iAPIKey_;
		}

		//���ϐ� //

		var $apiURL         = 'http://xml.accesstrade.net/at/ws.html';
		var $apiKey         = null;
		var $firstCategory  = null;
		var $secondCategory = null;
		var $thirdCategory  = null;
	}
