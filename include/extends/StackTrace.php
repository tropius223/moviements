<?php

	//���N���X //

	class StackTrace //
	{
		//������ //

		/**
			@brief  �X�^�b�N�g���[�X�𕶎��񉻂��Ď擾����B
			@return �X�^�b�N�g���[�X�̓��e��\��������B
		*/
		function getString() //
		{
			$stackTrace = debug_backtrace();
			$stackTrace = array_reverse( $stackTrace );

			array_pop( $stackTrace );

			return $this->createString( $stackTrace );
		}

		/**
			@brief  �X�^�b�N�g���[�X�𕶎��񉻂��Ď擾����B
			@return �X�^�b�N�g���[�X�̓��e��\��������B
		*/
		function getStringFromException( $iException_ ) //
		{
			$stackTrace = $iException_->getTrace();
			$stackTrace = array_reverse( $stackTrace );

			return $this->createString( $stackTrace );
		}

		/**
			@brief     �X�^�b�N�g���[�X�𕶎��񉻂���B
			@param[in] $iStackTrace_ �X�^�b�N�g���[�X�B
			@retval    �X�^�b�N�g���[�X�̓��e��\��������
			@retval    �󕶎���                           $iStackTrace_ �ɕs���Ȓl���w�肵���ꍇ�B
		*/
		function createString( $iStackTrace_ ) //
		{
			if( !is_array( $iStackTrace_ ) ) //�z��łȂ��ꍇ
				{ return ''; }

			$results = Array();

			foreach( $iStackTrace_ as $frameData ) //�S�Ẵt���[��������
			{
				$functionName = $this->createFunctionNameString( $frameData );
				$codeInfo     = $this->createCodeInfoString( $frameData );

				$results[] = sprintf( '%-24s %s' , $codeInfo , $functionName );
			}

			return implode( "\n��\n" , $results );
		}

		/**
			@brief     �t���[���f�[�^����֐��Ăяo�����𕶎��񉻂���B
			@param[in] $iFrameData_ �t���[���f�[�^�B
			@retval    �֐��Ăяo������\��������
			@retval    �󕶎���                     $iFrameData_ �ɕs���Ȓl���w�肵���ꍇ�B
		*/
		function createFunctionNameString( $iFrameData_ ) //
		{
			if( !is_array( $iFrameData_ ) ) //�z��łȂ��ꍇ
				{ return ''; }

			if( array_key_exists( 'function' , $iFrameData_ ) ) //�֐�����񂪂���ꍇ
			{
				if( array_key_exists( 'class' , $iFrameData_ ) ) //�N���X����񂪂���ꍇ
				{
					if( array_key_exists( 'object' , $iFrameData_ ) ) //�C���X�^���X������ꍇ
						{ $functionName = $iFrameData_[ 'class' ] . '->' . $iFrameData_[ 'function' ]; }
					else //�C���X�^���X���Ȃ��ꍇ
						{ $functionName = $iFrameData_[ 'class' ] . '::' . $iFrameData_[ 'function' ]; }
				}
				else //�N���X����񂪂Ȃ��ꍇ
					{ $functionName = $iFrameData_[ 'function' ]; }

				$arguments     = $this->createArgumentsString( $iFrameData_ );
				$functionName .= $arguments;
			}
			else //�֐�����񂪂Ȃ��ꍇ
				{ $functionName = ''; }

			return $functionName;
		}

		/**
			@brief     �t���[���f�[�^����������𕶎��񉻂���B
			@param[in] $iFrameData_ �t���[���f�[�^�B
			@retval    ��������\��������
			@retval    �󕶎���             $iFrameData_ �ɕs���Ȓl���w�肵���ꍇ�B
		*/
		function createArgumentsString( $iFrameData_ ) //
		{
			if( !is_array( $iFrameData_ ) ) //�z��łȂ��ꍇ
				{ return ''; }

			if( array_key_exists( 'args' , $iFrameData_ ) && count( $iFrameData_[ 'args' ] ) ) //������񂪂���ꍇ
			{
				$arguments = Array();

				foreach( $iFrameData_[ 'args' ] as $value ) //�S�Ă̈���������
				{
					if( is_object( $value ) ) //�I�u�W�F�N�g�̏ꍇ
					{
						$arguments[] = 'object( ' . get_class( $value ) . ' )';

						continue;
					}

					if( is_array( $value ) ) //�z��̏ꍇ
					{
						if( 0 < count( $value ) ) //�v�f������ꍇ
						{
							$elements = Array();
							$keys     = array_keys( $value );

							for( $i = 0 ; $i < 5 && $i < count( $value ) ; ++$i ) //�ő�5�܂ŗv�f������
								{ $elements[] = gettype( $value[ $keys[ $i ] ] ); }

							$arguments[] = 'array[' . count( $value ) . ']( ' . implode( ' , ' , $elements ) . ' )';
						}
						else //�v�f���Ȃ��ꍇ
							{ $arguments[] = 'empty array'; }

						continue;
					}

					if( is_bool( $value ) ) //bool�l�̏ꍇ
					{
						$arguments[] = ( $value ? 'true' : 'false' );

						continue;
					}

					if( is_null( $value ) ) //null�̏ꍇ
					{
						$arguments[] = 'null';

						continue;
					}

					$arguments[] = gettype( $value ) . '( ' . $value . ' )';
				}

				$arguments = '( ' . implode( ' , ' , $arguments ) . ' )';
			}
			else //������񂪂Ȃ��ꍇ
				{ $arguments = ''; }

			return $arguments;
		}

		/**
			@brief     �t���[���f�[�^����R�[�h�ʒu���𕶎��񉻂���B
			@param[in] $iFrameData_ �t���[���f�[�^�B
			@retval    �R�[�h�ʒu����\��������
			@retval    �󕶎���                   $iFrameData_ �ɕs���Ȓl���w�肵���ꍇ�B
		*/
		function createCodeInfoString( $iFrameData_ ) //
		{
			if( !is_array( $iFrameData_ ) ) //�z��łȂ��ꍇ
				{ return ''; }

			if( array_key_exists( 'file' , $iFrameData_ ) ) //�\�[�X�t�@�C����������ꍇ
			{
				$sourceFileName   = $iFrameData_[ 'file' ];
				$sourceLineNumber = $iFrameData_[ 'line' ];
			}
			else //�\�[�X�t�@�C�������Ȃ��ꍇ
			{
				$sourceFileName   = $iFrameData_[ 'args' ][ 2 ];
				$sourceLineNumber = $iFrameData_[ 'args' ][ 3 ];
			}

			$isMatch = preg_match( '/([^\\/\\\\]+)$/' , $sourceFileName , $matches );

			if( $isMatch ) //�}�b�`�����ꍇ
				{ $sourceFileName = $matches[ 1 ]; }

			$codeInfo = sprintf( '%s,%04d' , $sourceFileName , $sourceLineNumber );

			return $codeInfo;
		}
	}
