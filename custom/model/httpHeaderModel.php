<?php

	//���N���X //

	/**
		@brief HTTP�w�b�_���\�z���đ��M����B
	*/
	class HTTPHeaderModel //
	{
		//������ //

		/**
			@brief     ���X�|���X�I�u�W�F�N�g���擾����B
			@details   ���݂̐ݒ�ŒʐM���s���A���ʂ����X�|���X�I�u�W�F�N�g�Ŏ擾���܂��B
			@exception LogicException �ݒ肪�s�\���ȏ�Ԃł��̃��\�b�h���Ăяo�����ꍇ�B
			@return    ���X�|���X�I�u�W�F�N�g�B
		*/
		function getResponse() //
		{
			if( !$this->urlHost ) //�z�X�g����̏ꍇ
				{ throw new LogicException( 'getResponse �������ł��܂���' ); }

			if( 'GET' == $this->method ) //���\�b�h��GET�̏ꍇ
			{
				$url = $this->toURL();

				return new HTTPResponseModel( $url );
			}
			else //���\�b�h��POST�̏ꍇ
			{
				$header = $this->toString();

				return new HTTPResponseModel( $this->urlHost , $header );
			}
		}

		//���f�[�^�擾 //

		/**
			@brief     HTTP URL���擾����B
			@exception LogicException �ݒ肪�s�\���ȏ�Ԃł��̃��\�b�h���Ăяo�����ꍇ�B
			@return    URL������B
		*/
		function toURL() //
		{
			if( !$this->urlHost ) //�z�X�g����̏ꍇ
				{ throw new LogicException( 'toString �������ł��܂���' ); }

			$parameters = Array();

			foreach( $this->parameters as $key => $value ) //�S�Ẵp�����[�^������
			{
				if( is_array( $value ) ) //�l���z��̏ꍇ
				{
					foreach( $value as $subValue ) //�S�Ă̒l������
						{ $parameters[] = $key . '[]=' . $subValue; }
				}
				else //�l���X�J���̏ꍇ
					{ $parameters[] = $key . '=' . $value; }
			}

			$parameter = implode( '&' , $parameters );

			return 'http://' . $this->urlHost . $this->urlPath . '?' . $parameter;
		}

		/**
			@brief     HTTP�w�b�_�ݒ�𕶎���Ŏ擾����B
			@exception LogicException �ݒ肪�s�\���ȏ�Ԃł��̃��\�b�h���Ăяo�����ꍇ�B
			@return    HTTP�w�b�_������B
		*/
		function toString() //
		{
			if( !$this->urlHost ) //�z�X�g����̏ꍇ
				{ throw new LogicException( 'toString �������ł��܂���' ); }

			$parameters = Array();
			$cookies    = Array();

			foreach( $this->parameters as $key => $value ) //�S�Ẵp�����[�^������
			{
				if( is_array( $value ) ) //�l���z��̏ꍇ
				{
					foreach( $value as $subValue ) //�S�Ă̒l������
						{ $parameters[] = $key . '[]=' . urlencode( $subValue ); }
				}
				else //�l���X�J���̏ꍇ
					{ $parameters[] = $key . '=' . urlencode( $value ); }
			}

			foreach( $this->cookies as $key => $value ) //�S�Ă�Cookie������
			{
				if( is_array( $value ) ) //�l���z��̏ꍇ
				{
					foreach( $value as $subValue ) //�S�Ă̒l������
						{ $cookies[] = $key . '[]=' . urlencode( $subValue ); }
				}
				else //�l���X�J���̏ꍇ
					{ $cookies[] = $key . '=' . urlencode( $value ); }
			}

			$parameter = implode( '&' , $parameters );
			$cookie    = implode( '&' , $cookies );

			if( 'GET' == $this->method && $parameter ) //GET�p�����[�^������ꍇ
				{ $request =  $this->method . ' ' . $this->urlPath . '?' . $parameter . ' HTTP/1.1' . "\r\n"; }
			else //GET�p�����[�^���Ȃ��A�܂���POST���\�b�h�̏ꍇ
				{ $request =  $this->method . ' ' . $this->urlPath . ' HTTP/1.1' . "\r\n"; }

			$request .= 'Host: ' . $this->urlHost . "\r\n";
			$request .= 'User-Agent: ' . $this->userAgent . "\r\n";
			$request .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
			$request .= 'Content-Length: ' . strlen( $parameter ) . "\r\n";
			$request .= 'Connection: Close' . "\r\n";

			if( $this->referer ) //���t�@�����ݒ肳��Ă���ꍇ
				{ $request .= 'Referer: ' . $this->referer . "\r\n"; }

			if( $cookie ) //Cookie�����݂���ꍇ
				{ $request .= 'Cookie: ' . $cookie . "\r\n"; }

			if( 'POST' == $this->method && $parameter ) //POST�p�����[�^�����݂���ꍇ
				{ $request .= "\r\n" . $parameter; }

			$request .= "\r\n";

			return $request;
		}

		//���f�[�^�ύX //

		/**
			@brief     ���M����Cookie��ݒ肷��B
			@exception InvalidArgumentException $iName_ �ɃX�J���ł͂Ȃ��l���w�肵���ꍇ�B
			@param[in] $iName_  Cookie�̖��O�B
			@param[in] $iValue_ Cookie�̒l�B
		*/
		function setCookie( $iName_ , $iValue_ ) //
		{
			if( !is_scalar( $iName_ ) ) //�X�J���ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iName_ �͖����ł�[' . gettype( $iName_ ) . ']' ); }

			$this->cookies[ $iName_ ] = $iValue_;
		}

		/**
			@brief     �p�����[�^�̑��M���@��ݒ肷��B
			@exception InvalidArgumentException $iMethod_ �ɖ����Ȓl���w�肵���ꍇ�B
			@param[in] $iMethod_ ���M���@(GET/POST)
		*/
		function setMethod( $iMethod_ ) //
		{
			if( !is_scalar( $iMethod_ ) ) //�X�J���ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iMethod_ �͖����ł�[' . gettype( $iMethod_ ) . ']' ); }

			$method = strtoupper( $iMethod_ );

			switch( $method ) //�l�ŕ���
			{
				case 'GET' :
				case 'POST' :
				{
					$this->method = $method;

					break;
				}

				default :
					{ throw new InvalidArgumentException( '���� $iMethod_ �͖����ł�[' . $iMethod_ . ']' ); }
			}
		}

		/**
			@brief     ���M����p�����[�^��ݒ肷��B
			@exception InvalidArgumentException $iName_ �ɃX�J���ł͂Ȃ��l���w�肵���ꍇ�B
			@param[in] $iName_  �p�����[�^�̖��O�B
			@param[in] $iValue_ �p�����[�^�̒l�B
		*/
		function setParameter( $iName_ , $iValue_ ) //
		{
			if( !is_scalar( $iName_ ) ) //�X�J���ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iName_ �͖����ł�[' . gettype( $iName_ ) . ']' ); }

			$this->parameters[ $iName_ ] = $iValue_;
		}

		/**
			@brief     ���t�@������ݒ肷��B
			@exception InvalidArguentException $iReferer_ �ɃX�J���ł͂Ȃ��l���w�肵���ꍇ�B
			@param[in] $iReferer_ ���t�@���B
		*/
		function setReferer( $iReferer_ ) //
		{
			if( !is_scalar( $iReferer_ ) ) //�X�J���ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iReferer_ �͖����ł�[' . gettype( $iReferer_ ) . ']' ); }

			$this->referer = $iReferer_;
		}

		/**
			@brief     �ʐM��URL��ݒ肷��B
			@exception InvalidArgumentException $iURL_ �ɖ����Ȓl���w�肵���ꍇ�B
			@param[in] $iURL_ �ʐM��URL�B
		*/
		function setURL( $iURL_ ) //
		{
			if( !is_scalar( $iURL_ ) ) //�X�J���ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iURL_ �͖����ł�[' . gettype( $iURL_ ) . ']' ); }

			$parseResult = parse_url( $iURL_ );

			if( !$parseResult ) //URL���p�[�X�ł��Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iURL_ �͖����ł�[' . $iURL_ . ']' ); }

			if( 'http' != $parseResult[ 'scheme' ] ) //URL�̃X�L�[����HTTP�ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iURL_ �͖����ł�[' . $iURL_ . ']' ); }

			if( !$parseResult[ 'host' ] ) //URL�̃z�X�g���s���̏ꍇ
				{ throw new InvalidArgumentException( '���� $iURL_ �͖����ł�[' . $iURL_ . ']' ); }

			$this->urlHost = $parseResult[ 'host' ];
			$this->urlPath = $parseResult[ 'path' ];
		}

		/**
			@brief     ���M���郆�[�U�[�G�[�W�F���g��ݒ肷��B
			@exception InvalidArgumentException $iUserAgent_ �ɃX�J���ł͂Ȃ��l���w�肵���ꍇ�B
			@param[in] $iUserAgnet_ ���[�U�[�G�[�W�F���g�̒l�B
		*/
		function setUserAgent( $iUserAgent_ ) //
		{
			if( !is_scalar( $iUserAgent_ ) ) //�X�J���ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iUserAgent_ �͖����ł�[' . gettype( $iUserAgent_ ) . ']' ); }

			$this->userAgent = $iUserAgent_;
		}

		//���R���X�g���N�^�E�f�X�g���N�^

		/**
			@brief �R���X�g���N�^�B
		*/
		function __construct()
			{ $this->userAgent = 'PHP/' . phpversion(); }

		//���ϐ� //

		private $urlHost    = null;    ///<�ʐM��URL�̃z�X�g���B
		private $urlPath    = null;    ///<�ʐM��URL�̃p�X�B
		private $method     = 'GET';   ///<�p�����[�^�̑��M���\�b�h�B
		private $userAgent  = null;    ///<�ʐM���̃��[�U�[�G�[�W�F���g�B
		private $parameters = Array(); ///<���M����p�����[�^�B
		private $cookies    = Array(); ///<���M����Cookie�B
		private $referer    = null;    ///<���M���郊�t�@���B
	}
