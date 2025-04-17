<?php

	/**
		@brief   �R���Z�v�g�N���X�B
		@details �X�N���v�g�̃R���Z�v�g�𖞂����Ă��Ȃ��ꍇ�ɗ�O���X���[���܂��B
	*/
	class ConceptCheck
	{
		private static $ExceptionName = 'InvalidQueryException'; ///<�X���[�����O�N���X��

		/**
			@brief     �X���[�����O��ύX����B
			@exception InvalidArgumentException $name_ �ɋ󕶎�����w�肵���A�܂��� $name_ �N���X��������Ȃ��ꍇ�B
			@param     $name_ ��O�N���X���B
		*/
		private static function SetExceptionName( $name_ )
		{
			if( !$name_ )
				throw new InvalidArgumentException( '$name_ �͕K�{�̃p�����[�^�ł�' );

			if( !class_exists( $name_ ) )
				throw new InvalidArgumentException( '�N���X ' . $name_ . ' �͒�`����Ă��܂���' );

			self::$ExceptionName = $name_;
		}

		/**
			@brief     �K�{�p�����[�^���`�F�b�N����B
			@exception InvalidQueryException �p�����[�^���Z�b�g����Ă��Ȃ��ꍇ�B
			@param     $method_    �`�F�b�N����p�����[�^�z��B
			@param     $keys_      �`�F�b�N����L�[�z��B
			@param     $option_    �`�F�b�N�����B
			@param     $exception_ ��O�N���X���B
		*/
		static function IsEssential( &$method_ , $keys_ , $option_ = 'and' , $exception_ = 'InvalidQueryException' )
		{
			self::SetExceptionName( $exception_ );

			switch( $option_ )
			{
				case 'and' :
					foreach( $keys_ as $key )
					{
						if( !array_key_exists( $key , $method_ ) )
							throw new self::$ExceptionName( $key . '�͕K�{�̃p�����[�^�ł�' );
					}
					break;

				case 'or' :
					foreach( $keys_ as $key )
					{
						if( array_key_exists( $key , $method_ ) )
							return;
					}
					throw new self::$ExceptionName( '���̃p�����[�^�̂����ꂩ���w�肵�Ȃ���΂����܂���:' . implode( ',' , $keys_ ) );

				default :
					throw new InvalidArgumentException( '�s���ȃI�v�V�����ł�' );
			}
		}

		/**
			@brief     null�p�����[�^���`�F�b�N����B
			@exception InvalidQueryException �p�����[�^����̏ꍇ�B
			@param     $method_    �`�F�b�N����p�����[�^�z��B
			@param     $keys_      �`�F�b�N����L�[�z��B
			@param     $option_    �`�F�b�N�����B
			@param     $exception_ ��O�N���X���B
		*/
		static function IsNotNull( &$method_ , $keys_ , $option_ = 'and' , $exception_ = 'InvalidQueryException' )
		{
			self::SetExceptionName( $exception_ );

			switch( $option_ )
			{
				case 'and' :
					foreach( $keys_ as $key )
					{
						if( !$method_[ $key ] )
							throw new self::$ExceptionName( $key . '����ł�' );
					}
					break;

				case 'or' :
					foreach( $keys_ as $key )
					{
						if( $method_[ $key ] )
							return;
					}
					throw new self::$ExceptionName( '���̃p�����[�^�̂����ꂩ�ɗL���Ȓl���w�肵�Ȃ���΂����܂���:' . implode( ',' , $keys_ ) );

				default :
					throw new InvalidArgument( '�s���ȃI�v�V�����ł�' );
			}
		}

		/**
			@brief     �p�����[�^�̌^���`�F�b�N����B
			@exception InvalidQueryException �p�����[�^���z��̏ꍇ�B
			@param     $method_    �`�F�b�N����p�����[�^�z��B
			@param     $keys_      �`�F�b�N����L�[�z��B
			@param     $exception_ ��O�N���X���B
		*/
		static function IsScalar( &$method_ , $keys_ , $exception_ = 'InvalidQueryException' )
		{
			self::SetExceptionName( $exception_ );

			foreach( $keys_ as $key )
			{
				if( is_array( $method_[ $key ] ) )
					throw new self::$ExceptionName( $key . '�ɔz����w�肷�邱�Ƃ͂ł��܂���' );
			}
		}

		/**
			@brief     �p�����[�^�̌^���`�F�b�N����B
			@exception InvalidQueryException �p�����[�^���X�J���̏ꍇ�B
			@param     $method_    �`�F�b�N����p�����[�^�z��B
			@param     $keys_      �`�F�b�N����L�[�z��B
			@param     $exception_ ��O�N���X���B
		*/
		static function IsArray( &$method_ , $keys_ , $exception_ = 'InvalidQueryException' )
		{
			self::SetExceptionName( $exception_ );

			foreach( $keys_ as $key )
			{
				if( !is_array( $method_[ $key ] ) )
					throw new self::$ExceptionName( $key . '�ɃX�J�����w�肷�邱�Ƃ͂ł��܂���' );
			}
		}
	}
?>
