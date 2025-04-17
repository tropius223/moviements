<?php

	//★クラス //

	/**
		@brief   システム関数クラス。
		@details システムの全体で使用する低レベルの関数を実装するためのクラスです。
		@author  matsuki
		@version 1.0.0
		@ingroup SystemComponent
	*/
	class WS extends magics //
	{
		//■処理 //

		/**
			@brief     クラス定義ファイルをロードする。
			@details   クラス名からクラス定義ファイルを推測してロードします。
			@exception InvalidArgumentException $iClassName_ に空文字列を指定した場合。
			@exception IllegalAccessException   不正なクラス名を指定した場合。
			@exception LogicException           クラス定義ファイルが見つからない、またはファイルをロードしてもクラスが定義されなかった場合。
			@param[in] $iClassName_ クラス名。
			@remarks   $iClassName_ が定義済みである場合は、何もせずに返ります。
		*/
		static function DefClass( $iClassName_ ) //
		{
			if( !$iClassName_ ) //クラス名が空の場合
				{ throw new InvalidArgumentException( '引数 $iClassName_ は無効です' ); }

			//ディレクトリトラバーサル検出
			if( preg_match( '/\W/' , $iClassName_ ) ) //英数字以外の文字が含まれる場合
				{ throw new IllegalAccessException( '不正なアクセスです[' . $iClassName_ . ']' ); }

			if( class_exists( $iClassName_ , false ) ) //既にクラスが定義されている場合
				{ return; }

			$filePath = '';

			//キャメルケースの最後の綴りからコンポーネントタイプを推測する
			$isMatch = preg_match( '/(.+)([A-Z][a-z0-9_]+)$/' , $iClassName_ , $match );

			if( $isMatch ) //コンポーネントタイプと推測される綴りが見つかった場合
			{
				$className     = $match[ 1 ];
				$componentType = $match[ 2 ];

				$filePath = self::TryGetComponentFilePath( $componentType , $className );
			}

			//見つからなければ通常クラスを探す
			if( !$filePath ) //ファイルパスが空の場合
				{ $filePath = self::TryGetIncludePath( $iClassName_ ); }

			if( !$filePath ) //ファイルパスが空の場合
				{ throw new LogicException( 'DefClass を完了できません[' . $iClassName_ . ']' ); }

			if( !file_exists( $filePath ) ) //ファイルが見つからない場合
				{ throw new LogicException( 'DefClass を完了できません[' . $iClassName_ . '][' . $filePath . ']' ); }

			include_once $filePath;

			if( !class_exists( $iClassName_ , false ) ) //ロードしてもクラスが定義されなかった場合
				{ throw new LogicException( 'DefClass を完了できません[' . $iClassName_ . '][' . $filePath . ']' ); }
		}

		//■データ取得 //

		/**
			@brief     コンポーネントオブジェクトを取得する。
			@param[in] $iComponentType_ コンポーネントタイプ。
			@param[in] $iName_          コンポーネントの名前。
			@exception $iCComponentType_ , $iName_ に空文字列を指定した場合。
			@return    コンポーネントオブジェクト。
		*/
		static private function GetComponent( $iComponentType_ , $iName_ ) //
		{
			if( !$iComponentType_ ) //コンポーネントタイプが空の場合
				{ throw new InvalidArgumentException( '引数 $iComponentType_ は無効です[ ' . $iName_ . ' ]' ); }

			if( !$iName_ ) //名前が空の場合
				{ throw new InvalidArgumentException( '引数 $iName_ は無効です[ ' . $iComponentType_ . ' ]' ); }

			if( !array_key_exists( $iComponentType_ , self::$ComponentCaches ) ) //コンポーネントタイプの配列が存在しない場合
				{ self::$ComponentCaches[ $iComponentType_ ] = Array(); }

			if( !array_key_exists( $iName_ , self::$ComponentCaches[ $iComponentType_ ] ) ) //コンポーネントが存在しない場合
			{
				$componentName                                        = $iName_ . $iComponentType_;
				self::$ComponentCaches[ $iComponentType_ ][ $iName_ ] = new $componentName;
			}

			return self::$ComponentCaches[ $iComponentType_ ][ $iName_ ];
		}

		static function HasComponent( $iName_ ) //
		{
			try
			{
				self::DefClass( $iName_ );

				return true;
			}
			catch( Exception $e )
				{ return false; }
		}

		/**
			@brief     コンポーネントファイルのインクルードパスを取得する。
			@param[in] $iComponentType_ コンポーネントタイプ。
			@param[in] $iName_          コンポーネント名。
			@retval    ファイルパス コンポーネントタイプが見つかった場合。
			@retval    null         コンポーネントタイプが登録されていない場合。
		*/
		static private function TryGetComponentFilePath( $iComponentType_ , $iName_ ) //
		{
			if( array_key_exists( $iComponentType_ , self::$ComponentPathConfigs ) ) //コンポーネント設定配列にキーが存在する場合
				{ $originPath = self::$ComponentPathConfigs[ $iComponentType_ ]; }
			else //コンポーネントタイプに対応する設定が登録されていない場合
				{ return null; }

			//通常パス
			$includePath = str_replace( '@' , $iName_ , $originPath );

			if( file_exists( $includePath ) ) //ファイルが見つかった場合
				{ return $includePath; }

			//先頭大文字
			$includePath = str_replace( '@' , ucfirst( $iName_ ) , $originPath );

			if( file_exists( $includePath ) ) //ファイルが見つかった場合
				{ return $includePath; }

			//先頭小文字
			$includePath = str_replace( '@' , lcfirst( $iName_ ) , $originPath );

			if( file_exists( $includePath ) ) //ファイルが見つかった場合
				{ return $includePath; }

			return null;
		}

		/**
			@brief     クラスファイルのインクルードパスを取得する。
			@param[in] $iClassName_ クラス名。
			@retval    ファイルパス クラスファイルが見つかった場合。
			@retval    null         クラスファイルが登録されていない場合。
		*/
		static private function TryGetIncludePath( $iClassName_ ) //
		{
			foreach( self::$ClassPathConfigs as $originPath ) //phpファイルを検索
			{
				//通常パス
				$includePath = str_replace( '@' , $iClassName_ , $originPath );

				if( file_exists( $includePath ) ) //ファイルが見つかった場合
					{ return $includePath; }

				//先頭大文字
				$includePath = str_replace( '@' , ucfirst( $iClassName_ ) , $originPath );

				if( file_exists( $includePath ) ) //ファイルが見つかった場合
					{ return $includePath; }

				//先頭小文字
				$includePath = str_replace( '@' , lcfirst( $iClassName_ ) , $originPath );

				if( file_exists( $includePath ) ) //ファイルが見つかった場合
					{ return $includePath; }
			}

			return null;
		}

		//■糖衣 //

		/**
			@brief     Drawerコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Drawer( $iName_ )
			{ return self::GetComponent( 'Drawer' , $iName_ ); }

		/**
			@brief     Finderコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Finder( $iName_ )
			{ return self::GetComponent( 'Finder' , $iName_ ); }

		/**
			@brief     Infoコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Info( $iName_ )
			{ return self::GetComponent( 'Info' , $iName_ ); }

		/**
			@brief     Logicコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Logic( $iName_ )
			{ return self::GetComponent( 'Logic' , $iName_ ); }

		/**
			@brief     Modelコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Model( $iName_ )
			{ return self::GetComponent( 'Model' , $iName_ ); }

		/**
			@brief     Systemコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function System( $iName_ )
			{ return self::GetComponent( 'System' , $iName_ ); }

		/**
			@brief     Testerコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Tester( $iName_ )
			{ return self::GetComponent( 'Tester' , $iName_ ); }

		/**
			@brief     Utilコンポーネントを取得する。
			@param[in] コンポーネントの名前。
			@return    コンポーネントオブジェクト。
		*/
		static function Util( $iName_ )
			{ return self::GetComponent( 'Util' , $iName_ ); }

		//■変数 //
		static private $ComponentCaches = Array(); ///<生成済みのコンポーネントオブジェクトのキャッシュ格納配列

		static private $ComponentPathConfigs = Array ///<コンポーネントの種類とインクルードパス設定配列
		(
			'Drawer' => 'custom/drawer/@Drawer.php' ,
			'Finder' => 'custom/finder/@Finder.php' ,
			'Info'   => 'custom/info/@Info.php' ,
			'Logic'  => 'custom/logic/@Logic.php' ,
			'Model'  => 'custom/model/@Model.php' ,
			'System' => 'custom/system/@System.php' ,
			'Tester' => 'custom/tester/@Tester.php' ,
			'Util'   => 'include/util/@Util.php' ,
		);

		static private $ClassPathConfigs = Array ///<クラスのインクルードパス設定配列
		(
			'include/@.php' ,
			'include/base@.php' ,
			'include/extends@.php' ,
			'custom/@.php' ,
			'custom/api@.php' ,
			'custom/cron@.php' ,
			'module/@.inc' ,
			'module/command@.inc' ,
		);
	}
