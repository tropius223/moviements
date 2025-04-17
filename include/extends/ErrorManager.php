<?php

	//���N���X //

	class ErrorManager //
	{
		//������ //

		/**
			@brief     ���������G���[���b�Z�[�W����������B
			@param[in] $iErrorMessage_     �G���[�̓��e��\�����b�Z�[�W�B
			@param[in] $iSourceFileName_   �G���[�����������R�[�h�̃\�[�X�t�@�C�����B
			@param[in] $iSourceLineNumber_ �G���[�����������R�[�h�̍s�ԍ��B
			@exception ErrorException ��O�ϊ����L���ȏꍇ�B
			@exception Exception      ��O�ϊ����L���ŁAErrorException�N���X�����݂��Ȃ��ꍇ�B
		*/
		function errorProcess( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ ) //
		{
			$errorMessage = $this->createErrorLogMessage( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ );

			$this->outputErrorLog( $errorMessage );

			if( $this->errorToException ) //�G���[�̗�O�ϊ����L���ȏꍇ
			{
				if( class_exists( 'ErrorException' ) ) //ErrorException�N���X������ꍇ
					{ $exception = new ErrorException( $iErrorMessage_ ); }
				else //ErrorException�N���X���Ȃ��ꍇ
					{ $exception = new Exception( $iErrorMessage_ ); }

				throw $exception;
			}
		}

		/**
			@brief �v���I�ȃG���[�ɂ��V���b�g�_�E������������B
		*/
		function shutdownProcess() //
		{
			if( $this->shutdownErrorLog ) //�V���b�g�_�E�����̃��O�o�͂��L���ȏꍇ
			{
				$fatalErrorMessage = $this->createFatalErrorMessage();

				if( !is_null( $fatalErrorMessage ) ) //�v���I�ȃG���[���b�Z�[�W������ꍇ
					{ $this->outputShutdownLog( $fatalErrorMessage ); }
			}
		}

		/**
			@brief     ���O�o�͗p�̃G���[���b�Z�[�W���\�z����B
			@param[in] $iErrorMessage_     �G���[�̓��e��\�����b�Z�[�W�B
			@param[in] $iSourceFileName_   �G���[�����������R�[�h�̃\�[�X�t�@�C�����B
			@param[in] $iSourceLineNumber_ �G���[�����������R�[�h�̍s�ԍ��B
		*/
		private function createErrorLogMessage( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ ) //
		{
			//�X�^�b�N�g���[�X���擾����
			$stackTrace  = new StackTrace();
			$traceString = $stackTrace->getString();

			return 'error : ' . $iErrorMessage_ . "\n" . $iSourceFileName_ . ' ' . $iErrorMessage_ . "\n\n" . $traceString . "\n";
		}

		/**
			@brief �v���I�ȃG���[���b�Z�[�W���\�z����B
		*/
		private function createFatalErrorMessage() //
		{
			if( function_exists( 'error_get_last' ) ) //error_get_last���g�p�ł���ꍇ
				{ $lastError = error_get_last(); }

			if( is_null( $lastError ) ) //�Ō�ɔ��������G���[���Ȃ��ꍇ
				{ return null; }

			switch( $lastError[ 'type' ] ) //�G���[�̎�ނŕ���
			{
				case E_ERROR           : //�G���[
				case E_PARSE           : //�p�[�X�G���[
				case E_CORE_ERROR      : //PHP�N���G���[
				case E_CORE_WARNING    : //PHP�N���x��
				case E_COMPILE_ERROR   : //�R���p�C�����G���[
				case E_COMPILE_WARNING : //�R���p�C�����x��
				{
					$errorMessage  = 'fatal error : ' . $lastError[ 'message' ] . "\n";
					$errorMessage .= sprintf( '%s,%04d' , $lastError[ 'file' ] , $lastError[ 'line' ] ) . "\n";

					return $errorMessage;
				}

				default : //���̑�
					{ return null; }
			}
		}

		/**
			@brief     �G���[���O�t�@�C���Ƀ��b�Z�[�W���o�͂���B
			@param[in] $iErrorMessage_ �G���[�̓��e��\�����b�Z�[�W�B
		*/
		function outputErrorLog( $iErrorMessage_ ) //
			{ $this->outputErrorLogToFile( $iErrorMessage_ , $this->errorLogFile ); }

		/**
			@brief     �G���[���O�t�@�C���Ƀ��b�Z�[�W���o�͂���B
			@param[in] $iErrorMessage_ �G���[�̓��e��\�����b�Z�[�W�B
		*/
		function outputShutdownLog( $iErrorMessage_ ) //
			{ $this->outputErrorLogToFile( $iErrorMessage_ , $this->workDirectory . $this->errorLogFile ); }

		/**
			@brief     �G���[���O�t�@�C���Ƀ��b�Z�[�W���o�͂���B
			@param[in] $iErrorMessage_ �G���[�̓��e��\�����b�Z�[�W�B
			@param[in] $iLogFilePath_  �G���[���O�t�@�C���̃p�X�B
		*/
		private function outputErrorLogToFile( $iErrorMessage_ , $iLogFilePath_ ) //
		{
			$fp = fopen( $iLogFilePath_ , 'a' );

			if( $fp ) //�t�@�C�����I�[�v���ł����ꍇ
			{
				fputs( $fp , date( '*Y/n/j G:h:i' . "\n" ) );
				fputs( $fp , $iErrorMessage_ . "\n" );
				fputs( $fp , '-----------------------------------------------------' . "\n\n" );
				fclose( $fp );

				if( $this->maxlogFileSize < filesize( $iLogFilePath_ ) ) //���O�t�@�C���̍ő�T�C�Y�𒴂��Ă���ꍇ
				{
					$nowDateString = date( '_Y_m_d_H_i_s' );

					rename( $iLogFilePath_ , $iLogFilePath_ . $nowDateString );
				}
			}
		}

		//���f�[�^�ύX //
		/**
			@brief ��O�ϊ��̗L���E������ݒ肷��B
			@param $iUsage_ �G���[���b�Z�[�W���O�ɕϊ�����ꍇ��true�B�ϊ����Ȃ��ꍇ��false�B
		*/
		function setErrorToExceptionConf( $iUsage_ ) //
			{ $this->ErrorToException = $iUsage_; }

		//���R���X�g���N�^�E�f�X�g���N�^ //

		/**
			@brief     �R���X�g���N�^�B
			@param[in] $iOptions_ �ݒ�z��B
		*/
		function __construct( $iOptions_ = Array() ) //
		{
			if( !is_array( $iOptions_ ) ) //�z��łȂ��ꍇ
				{ $iOptions_ = Array(); }

			if( !array_key_exists( 'UseErrorToException' , $iOptions_ ) ) //�ݒ�L�[���Ȃ��ꍇ
				{ $iOptions_[ 'UseErrorToException' ] = false; }

			if( !array_key_exists( 'UseShutdownErrorLog' , $iOptions_ ) ) //�ݒ�L�[���Ȃ��ꍇ
				{ $iOptions_[ 'UseShutdownErrorLog' ] = true; }

			if( !array_key_exists( 'ErrorLogFile' , $iOptions_ ) ) //�ݒ�L�[���Ȃ��ꍇ
				{ $iOptions_[ 'ErrorLogFile' ] = 'logs/error.log'; }

			if( !array_key_exists( 'WorkDirectory' , $iOptions_ ) ) //�ݒ�L�[���Ȃ��ꍇ
				{ $iOptions_[ 'WorkDirectory' ] = getcwd(); }

			if( !array_key_exists( 'MaxLogFileSize' , $iOptions_ ) ) //�ݒ�L�[���Ȃ��ꍇ
				{ $iOptions_[ 'MaxLogFileSize' ] = 20971520; }

			$this->errorToException = $iOptions_[ 'UseErrorToException' ];
			$this->shutdownErrorLog = $iOptions_[ 'UseShutdownErrorLog' ];
			$this->errorLogFile     = $iOptions_[ 'ErrorLogFile' ];
			$this->workDirectory    = $iOptions_[ 'WorkDirectory' ];
			$this->maxlogFileSize   = $iOptions_[ 'MaxLogFileSize' ];
		}

		//���݊� //

		function GetExceptionStr( $iException_ ) //
		{
			//�X�^�b�N�g���[�X���擾����
			$stackTrace  = new StackTrace();
			$traceString = $stackTrace->getStringFromException( $iException_ );

			return 'error : ' . $iException_->getMessage() . "\n" . $iException_->getFile() . ' ' . $iException_->getLine() . "\n\n" . $traceString . "\n";
		}

		//���ϐ� //
		private $errorToException = null; ///<�G���[���b�Z�[�W���O�ɕϊ�����Ȃ�true�B
		private $shutdownErrorLog = null; ///<�v���I�G���[�̔������ɃG���[���O���o�͂���Ȃ�true�B
		private $errorLogFile     = null; ///<�G���[���O���o�͂���t�@�C�����B
		private $workDirectory    = null; ///<�X�N���v�g�̓���p�X�B
		private $maxlogFileSize   = 0;    ///<���O�t�@�C���̍ő�T�C�Y�B
	}

	//���֐� //

	//���n���h�� //

	/**
		@brief �G���[�n���h���B
		@param[in] $iErrorLevel_       �G���[�̃��x���B
		@param[in] $iErrorMessage_     �G���[�̓��e��\�����b�Z�[�W�B
		@param[in] $iSourceFileName_   �G���[�����������R�[�h�̃\�[�X�t�@�C�����B
		@param[in] $iSourceLineNumber_ �G���[�����������R�[�h�̍s�ԍ��B
		@param[in] $iErrorContext_     �G���[�������_�ł̃V���{���e�[�u���B
	*/
	function ErrorManager_ErrorHandler( $iErrorLevel_ , $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ , $iErrorContext_ ) //
	{
		global $EXCEPTION_CONF;

		$errorManager = new ErrorManager( $EXCEPTION_CONF );
		$errorManager->errorProcess( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ );
	}

	/**
		@brief �V���b�g�_�E���n���h���B
	*/
	function ErrorManager_ShutdownHandler()
	{
		global $EXCEPTION_CONF;

		$errorManager = new ErrorManager( $EXCEPTION_CONF );
		$errorManager->shutdownProcess();
	}

	//�n���h���o�^
	set_error_handler( 'ErrorManager_ErrorHandler' , $EXCEPTION_CONF[ 'ErrorHandlerLevel' ] );
	register_shutdown_function( 'ErrorManager_ShutdownHandler' );
