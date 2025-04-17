<?php

	//★クラス //

	class StackTrace //
	{
		//■処理 //

		/**
			@brief  スタックトレースを文字列化して取得する。
			@return スタックトレースの内容を表す文字列。
		*/
		function getString() //
		{
			$stackTrace = debug_backtrace();
			$stackTrace = array_reverse( $stackTrace );

			array_pop( $stackTrace );

			return $this->createString( $stackTrace );
		}

		/**
			@brief  スタックトレースを文字列化して取得する。
			@return スタックトレースの内容を表す文字列。
		*/
		function getStringFromException( $iException_ ) //
		{
			$stackTrace = $iException_->getTrace();
			$stackTrace = array_reverse( $stackTrace );

			return $this->createString( $stackTrace );
		}

		/**
			@brief     スタックトレースを文字列化する。
			@param[in] $iStackTrace_ スタックトレース。
			@retval    スタックトレースの内容を表す文字列
			@retval    空文字列                           $iStackTrace_ に不正な値を指定した場合。
		*/
		function createString( $iStackTrace_ ) //
		{
			if( !is_array( $iStackTrace_ ) ) //配列でない場合
				{ return ''; }

			$results = Array();

			foreach( $iStackTrace_ as $frameData ) //全てのフレームを処理
			{
				$functionName = $this->createFunctionNameString( $frameData );
				$codeInfo     = $this->createCodeInfoString( $frameData );

				$results[] = sprintf( '%-24s %s' , $codeInfo , $functionName );
			}

			return implode( "\n▼\n" , $results );
		}

		/**
			@brief     フレームデータから関数呼び出し情報を文字列化する。
			@param[in] $iFrameData_ フレームデータ。
			@retval    関数呼び出し情報を表す文字列
			@retval    空文字列                     $iFrameData_ に不正な値を指定した場合。
		*/
		function createFunctionNameString( $iFrameData_ ) //
		{
			if( !is_array( $iFrameData_ ) ) //配列でない場合
				{ return ''; }

			if( array_key_exists( 'function' , $iFrameData_ ) ) //関数名情報がある場合
			{
				if( array_key_exists( 'class' , $iFrameData_ ) ) //クラス名情報がある場合
				{
					if( array_key_exists( 'object' , $iFrameData_ ) ) //インスタンスがある場合
						{ $functionName = $iFrameData_[ 'class' ] . '->' . $iFrameData_[ 'function' ]; }
					else //インスタンスがない場合
						{ $functionName = $iFrameData_[ 'class' ] . '::' . $iFrameData_[ 'function' ]; }
				}
				else //クラス名情報がない場合
					{ $functionName = $iFrameData_[ 'function' ]; }

				$arguments     = $this->createArgumentsString( $iFrameData_ );
				$functionName .= $arguments;
			}
			else //関数名情報がない場合
				{ $functionName = ''; }

			return $functionName;
		}

		/**
			@brief     フレームデータから引数情報を文字列化する。
			@param[in] $iFrameData_ フレームデータ。
			@retval    引数情報を表す文字列
			@retval    空文字列             $iFrameData_ に不正な値を指定した場合。
		*/
		function createArgumentsString( $iFrameData_ ) //
		{
			if( !is_array( $iFrameData_ ) ) //配列でない場合
				{ return ''; }

			if( array_key_exists( 'args' , $iFrameData_ ) && count( $iFrameData_[ 'args' ] ) ) //引数情報がある場合
			{
				$arguments = Array();

				foreach( $iFrameData_[ 'args' ] as $value ) //全ての引数を処理
				{
					if( is_object( $value ) ) //オブジェクトの場合
					{
						$arguments[] = 'object( ' . get_class( $value ) . ' )';

						continue;
					}

					if( is_array( $value ) ) //配列の場合
					{
						if( 0 < count( $value ) ) //要素がある場合
						{
							$elements = Array();
							$keys     = array_keys( $value );

							for( $i = 0 ; $i < 5 && $i < count( $value ) ; ++$i ) //最大5つまで要素を処理
								{ $elements[] = gettype( $value[ $keys[ $i ] ] ); }

							$arguments[] = 'array[' . count( $value ) . ']( ' . implode( ' , ' , $elements ) . ' )';
						}
						else //要素がない場合
							{ $arguments[] = 'empty array'; }

						continue;
					}

					if( is_bool( $value ) ) //bool値の場合
					{
						$arguments[] = ( $value ? 'true' : 'false' );

						continue;
					}

					if( is_null( $value ) ) //nullの場合
					{
						$arguments[] = 'null';

						continue;
					}

					$arguments[] = gettype( $value ) . '( ' . $value . ' )';
				}

				$arguments = '( ' . implode( ' , ' , $arguments ) . ' )';
			}
			else //引数情報がない場合
				{ $arguments = ''; }

			return $arguments;
		}

		/**
			@brief     フレームデータからコード位置情報を文字列化する。
			@param[in] $iFrameData_ フレームデータ。
			@retval    コード位置情報を表す文字列
			@retval    空文字列                   $iFrameData_ に不正な値を指定した場合。
		*/
		function createCodeInfoString( $iFrameData_ ) //
		{
			if( !is_array( $iFrameData_ ) ) //配列でない場合
				{ return ''; }

			if( array_key_exists( 'file' , $iFrameData_ ) ) //ソースファイル名がある場合
			{
				$sourceFileName   = $iFrameData_[ 'file' ];
				$sourceLineNumber = $iFrameData_[ 'line' ];
			}
			else //ソースファイル名がない場合
			{
				$sourceFileName   = $iFrameData_[ 'args' ][ 2 ];
				$sourceLineNumber = $iFrameData_[ 'args' ][ 3 ];
			}

			$isMatch = preg_match( '/([^\\/\\\\]+)$/' , $sourceFileName , $matches );

			if( $isMatch ) //マッチした場合
				{ $sourceFileName = $matches[ 1 ]; }

			$codeInfo = sprintf( '%s,%04d' , $sourceFileName , $sourceLineNumber );

			return $codeInfo;
		}
	}
