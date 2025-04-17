<?php

	//���N���X //

	/**
		@brief HTTP�w�b�_�̑��M���ʂ��p�[�X����B
	*/
	class HTTPResponseModel //
	{
		//���f�[�^�擾 //

		/**
			@brief  �擾�����R���e���c���擾����B
			@return �擾�����R���e���c�B
		*/
		function getContents() //
			{ return $this->contents; }

		/**
			@brief  ���s���ꂽCookie��z��Ŏ擾����B
			@return Cookie�z��B
		*/
		function getCookies() //
			{ return $this->cookies; }

		/**
			@brief  �擾�������^�f�[�^��z��Ŏ擾����B
			@return �p�[�X���ꂽ���^�f�[�^�B
		*/
		function getMetaDatas() //
			{ return $this->metaDatas; }

		/**
			@brief  �擾�������^�f�[�^�𕶎���Ŏ擾����B
			@return �擾�������^�f�[�^�B
		*/
		function getMetaString() //
			{ return $this->metaString; }

		/**
			@brief  �X�e�[�^�X�R�[�h���擾����B
			@return �X�e�[�^�X�R�[�h�B
		*/
		function getStatusCode() //
			{ return $this->statusCode; }

		//���R���X�g���N�^�E�f�X�g���N�^ //

		/**
			@brief     �R���X�g���N�^�B
			@exception InvalidArgumentException $iHost_ , $iHeaderString_ �̂����ꂩ�ɕ�����ł͂Ȃ��l���w�肵���ꍇ�B
			@exception RuntimeException         �ʐM���ɖ�肪���������ꍇ�B
			@param[in] $iHost_         �ʐM��z�X�g�B
			@param[in] $iHeaderString_ HTTP�w�b�_������B
		*/
		function __construct( $iHost_ , $iHeaderString_ = null ) //
		{
			if( is_null( $iHeaderString_ ) ) //�w�b�_���w�肳��Ă��Ȃ��ꍇ
			{
				$fp = fopen( $iHost_ , 'rb' );

				if( !$fp ) //URL���J���Ȃ��ꍇ
					{ throw new RuntimeException( '�R���X�g���N�^�������ł��܂���[' . $iHost_ . ']' ); }

				$this->statusCode = '200';

				while( !feof( $fp ) ) //��ɂȂ�܂ŌJ��Ԃ�
					{ $this->contents .= fgets( $fp ); }

				return;
			}

			if( !is_string( $iHost_ ) ) //�z�X�g��������ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iHost_ �͖����ł�[' . $iHost_ . ']' ); }

			if( !is_string( $iHeaderString_ ) ) //�w�b�_��������ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iHeaderString_ �͖����ł�[' . $iHeaderString_ . ']' ); }

			$fp = @fsockopen( $iHost_ , 80 , $errno , $errstr , 1 );

			if( !$fp ) //URL���J���Ȃ��ꍇ
				{ throw new RuntimeException( '�R���X�g���N�^�������ł��܂���[' . $iHost_ . '][' . $iHeaderString_ . ']' ); }

			try
			{
				fputs( $fp , $iHeaderString_ );

				if( !socket_set_timeout( $fp , 3 ) ) //�^�C���A�E�g�̐ݒ�Ɏ��s�����ꍇ
					{ throw new RuntimeException( '�R���X�g���N�^�������ł��܂���[' . $iHost_ . '][' . $iHeaderString_ . ']' ); }

				while( !feof( $fp ) && !$statusCode ) //�X�e�[�^�X�R�[�h������܂ŌJ��Ԃ�
					{ $statusCode = fgets( $fp ); }

				$statusCodes = explode( ' ' , $statusCode );

				$this->statusCode = $statusCodes[ 1 ];

				while( !feof( $fp ) ) //��ɂȂ�܂ŌJ��Ԃ�
				{
					$buffer = fgets( $fp );

					if( "\r\n" == $buffer ) //���s�����̍s�̏ꍇ
						{ break; }

					$this->metaString .= $buffer;
				}

				while( !feof( $fp ) ) //��ɂȂ�܂ŌJ��Ԃ�
					{ $this->contents .= fgets( $fp ); }
			}
			catch( Exception $e )
			{
				fclose( $fp );

				throw $e;
			}

			fclose( $fp );

			foreach( explode( "\r\n" , $this->metaString ) as $value ) //���^�f�[�^������
			{
				List( $key , $value ) = explode( ': ' , $value , 2 );

				if( 'Set-Cookie' == $key ) //Cookie�̏ꍇ
				{
					foreach( explode( '; ' , $value ) as $subValue ) //�S�Ă̒l������
					{
						List( $subKey , $subValue ) = explode( '=' , $subValue , 2 );
						$this->cookies[ $subKey ][] = $subValue;
					}
				}

				$this->metaDatas[ $key ] = $value;
			}
		}

		//���ϐ� //

		private $statusCode = null; ///<�X�e�[�^�X�R�[�hs�B
		private $metaString = null; ///<�擾�������^�f�[�^�B
		private $metaDatas  = null; ///<�p�[�X���ꂽ���^�f�[�^�B
		private $cookies    = null; ///<���s���ꂽCookie�B
		private $contents   = null; ///<�擾�����R���e���c�B
	}
