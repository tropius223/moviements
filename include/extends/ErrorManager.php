<?php

	//★クラス //

	class ErrorManager //
	{
		//■処理 //

		/**
			@brief     発生したエラーメッセージを処理する。
			@param[in] $iErrorMessage_     エラーの内容を表すメッセージ。
			@param[in] $iSourceFileName_   エラーが発生したコードのソースファイル名。
			@param[in] $iSourceLineNumber_ エラーが発生したコードの行番号。
			@exception ErrorException 例外変換が有効な場合。
			@exception Exception      例外変換が有効で、ErrorExceptionクラスが存在しない場合。
		*/
		function errorProcess( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ ) //
		{
			$errorMessage = $this->createErrorLogMessage( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ );

			$this->outputErrorLog( $errorMessage );

			if( $this->errorToException ) //エラーの例外変換が有効な場合
			{
				if( class_exists( 'ErrorException' ) ) //ErrorExceptionクラスがある場合
					{ $exception = new ErrorException( $iErrorMessage_ ); }
				else //ErrorExceptionクラスがない場合
					{ $exception = new Exception( $iErrorMessage_ ); }

				throw $exception;
			}
		}

		/**
			@brief 致命的なエラーによるシャットダウンを処理する。
		*/
		function shutdownProcess() //
		{
			if( $this->shutdownErrorLog ) //シャットダウン時のログ出力が有効な場合
			{
				$fatalErrorMessage = $this->createFatalErrorMessage();

				if( !is_null( $fatalErrorMessage ) ) //致命的なエラーメッセージがある場合
					{ $this->outputShutdownLog( $fatalErrorMessage ); }
			}
		}

		/**
			@brief     ログ出力用のエラーメッセージを構築する。
			@param[in] $iErrorMessage_     エラーの内容を表すメッセージ。
			@param[in] $iSourceFileName_   エラーが発生したコードのソースファイル名。
			@param[in] $iSourceLineNumber_ エラーが発生したコードの行番号。
		*/
		private function createErrorLogMessage( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ ) //
		{
			//スタックトレースを取得する
			$stackTrace  = new StackTrace();
			$traceString = $stackTrace->getString();

			return 'error : ' . $iErrorMessage_ . "\n" . $iSourceFileName_ . ' ' . $iErrorMessage_ . "\n\n" . $traceString . "\n";
		}

		/**
			@brief 致命的なエラーメッセージを構築する。
		*/
		private function createFatalErrorMessage() //
		{
			if( function_exists( 'error_get_last' ) ) //error_get_lastが使用できる場合
				{ $lastError = error_get_last(); }

			if( is_null( $lastError ) ) //最後に発生したエラーがない場合
				{ return null; }

			switch( $lastError[ 'type' ] ) //エラーの種類で分岐
			{
				case E_ERROR           : //エラー
				case E_PARSE           : //パースエラー
				case E_CORE_ERROR      : //PHP起動エラー
				case E_CORE_WARNING    : //PHP起動警告
				case E_COMPILE_ERROR   : //コンパイル時エラー
				case E_COMPILE_WARNING : //コンパイル時警告
				{
					$errorMessage  = 'fatal error : ' . $lastError[ 'message' ] . "\n";
					$errorMessage .= sprintf( '%s,%04d' , $lastError[ 'file' ] , $lastError[ 'line' ] ) . "\n";

					return $errorMessage;
				}

				default : //その他
					{ return null; }
			}
		}

		/**
			@brief     エラーログファイルにメッセージを出力する。
			@param[in] $iErrorMessage_ エラーの内容を表すメッセージ。
		*/
		function outputErrorLog( $iErrorMessage_ ) //
			{ $this->outputErrorLogToFile( $iErrorMessage_ , $this->errorLogFile ); }

		/**
			@brief     エラーログファイルにメッセージを出力する。
			@param[in] $iErrorMessage_ エラーの内容を表すメッセージ。
		*/
		function outputShutdownLog( $iErrorMessage_ ) //
			{ $this->outputErrorLogToFile( $iErrorMessage_ , $this->workDirectory . $this->errorLogFile ); }

		/**
			@brief     エラーログファイルにメッセージを出力する。
			@param[in] $iErrorMessage_ エラーの内容を表すメッセージ。
			@param[in] $iLogFilePath_  エラーログファイルのパス。
		*/
		private function outputErrorLogToFile( $iErrorMessage_ , $iLogFilePath_ ) //
		{
			$fp = fopen( $iLogFilePath_ , 'a' );

			if( $fp ) //ファイルをオープンできた場合
			{
				fputs( $fp , date( '*Y/n/j G:h:i' . "\n" ) );
				fputs( $fp , $iErrorMessage_ . "\n" );
				fputs( $fp , '-----------------------------------------------------' . "\n\n" );
				fclose( $fp );

				if( $this->maxlogFileSize < filesize( $iLogFilePath_ ) ) //ログファイルの最大サイズを超えている場合
				{
					$nowDateString = date( '_Y_m_d_H_i_s' );

					rename( $iLogFilePath_ , $iLogFilePath_ . $nowDateString );
				}
			}
		}

		//■データ変更 //
		/**
			@brief 例外変換の有効・無効を設定する。
			@param $iUsage_ エラーメッセージを例外に変換する場合はtrue。変換しない場合はfalse。
		*/
		function setErrorToExceptionConf( $iUsage_ ) //
			{ $this->ErrorToException = $iUsage_; }

		//■コンストラクタ・デストラクタ //

		/**
			@brief     コンストラクタ。
			@param[in] $iOptions_ 設定配列。
		*/
		function __construct( $iOptions_ = Array() ) //
		{
			if( !is_array( $iOptions_ ) ) //配列でない場合
				{ $iOptions_ = Array(); }

			if( !array_key_exists( 'UseErrorToException' , $iOptions_ ) ) //設定キーがない場合
				{ $iOptions_[ 'UseErrorToException' ] = false; }

			if( !array_key_exists( 'UseShutdownErrorLog' , $iOptions_ ) ) //設定キーがない場合
				{ $iOptions_[ 'UseShutdownErrorLog' ] = true; }

			if( !array_key_exists( 'ErrorLogFile' , $iOptions_ ) ) //設定キーがない場合
				{ $iOptions_[ 'ErrorLogFile' ] = 'logs/error.log'; }

			if( !array_key_exists( 'WorkDirectory' , $iOptions_ ) ) //設定キーがない場合
				{ $iOptions_[ 'WorkDirectory' ] = getcwd(); }

			if( !array_key_exists( 'MaxLogFileSize' , $iOptions_ ) ) //設定キーがない場合
				{ $iOptions_[ 'MaxLogFileSize' ] = 20971520; }

			$this->errorToException = $iOptions_[ 'UseErrorToException' ];
			$this->shutdownErrorLog = $iOptions_[ 'UseShutdownErrorLog' ];
			$this->errorLogFile     = $iOptions_[ 'ErrorLogFile' ];
			$this->workDirectory    = $iOptions_[ 'WorkDirectory' ];
			$this->maxlogFileSize   = $iOptions_[ 'MaxLogFileSize' ];
		}

		//■互換 //

		function GetExceptionStr( $iException_ ) //
		{
			//スタックトレースを取得する
			$stackTrace  = new StackTrace();
			$traceString = $stackTrace->getStringFromException( $iException_ );

			return 'error : ' . $iException_->getMessage() . "\n" . $iException_->getFile() . ' ' . $iException_->getLine() . "\n\n" . $traceString . "\n";
		}

		//■変数 //
		private $errorToException = null; ///<エラーメッセージを例外に変換するならtrue。
		private $shutdownErrorLog = null; ///<致命的エラーの発生時にエラーログを出力するならtrue。
		private $errorLogFile     = null; ///<エラーログを出力するファイル名。
		private $workDirectory    = null; ///<スクリプトの動作パス。
		private $maxlogFileSize   = 0;    ///<ログファイルの最大サイズ。
	}

	//★関数 //

	//■ハンドラ //

	/**
		@brief エラーハンドラ。
		@param[in] $iErrorLevel_       エラーのレベル。
		@param[in] $iErrorMessage_     エラーの内容を表すメッセージ。
		@param[in] $iSourceFileName_   エラーが発生したコードのソースファイル名。
		@param[in] $iSourceLineNumber_ エラーが発生したコードの行番号。
		@param[in] $iErrorContext_     エラー発生時点でのシンボルテーブル。
	*/
	function ErrorManager_ErrorHandler( $iErrorLevel_ , $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ , $iErrorContext_ ) //
	{
		global $EXCEPTION_CONF;

		$errorManager = new ErrorManager( $EXCEPTION_CONF );
		$errorManager->errorProcess( $iErrorMessage_ , $iSourceFileName_ , $iSourceLineNumber_ );
	}

	/**
		@brief シャットダウンハンドラ。
	*/
	function ErrorManager_ShutdownHandler()
	{
		global $EXCEPTION_CONF;

		$errorManager = new ErrorManager( $EXCEPTION_CONF );
		$errorManager->shutdownProcess();
	}

	//ハンドラ登録
	set_error_handler( 'ErrorManager_ErrorHandler' , $EXCEPTION_CONF[ 'ErrorHandlerLevel' ] );
	register_shutdown_function( 'ErrorManager_ShutdownHandler' );
