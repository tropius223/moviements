<?php

	//★クラス //

	abstract class  BaseFinder //
	{
		//■処理 //

		/**
			@brief     クエリパラメータを使ってレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。省略時は空のテーブル。
			@param[in] $iQuery_    クエリパラメータ。省略時はGET配列。
			@param[in] $iUsertype_ ユーザー種別。省略時は現在のユーザー種別。
			@param[in] $iUserID_   ユーザーID。省略時は現在のユーザーID。
			@return    検索後のテーブル。
		*/
		function searchQueryTable( $iTable_ = null , $iQuery_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //テーブルが指定されていない場合
				{ $iTable_ = $this->db->getTable(); }

			if( !$iQuery_ ) //クエリパラメータが指定されていない場合
				{ $iQuery_ = $_GET; }

			if( !$iUserType_ ) //ユーザー種別が指定されていない場合
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //ユーザーIDが指定されていない場合
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			$addTable = $this->db->getTable();
			$addTable = $this->searchQueryTableProc( $addTable , $iQuery_ , $iUserType_ , $iUserID_ );

			$iTable_ = $this->db->andTable( $iTable_ , $addTable );

			foreach( $iQuery_ as $name => $value ) //全てのクエリパラメータを処理。
			{
				if( '_' != substr( $name , 0 , 1 ) ) //パラメータ名の開始文字がハイフンではない場合
					{ continue; }

				$extraMethodName = 'extraSearch' . ucfirst( substr( $name , 1 ) );

				if( method_exists( $this , $extraMethodName ) ) //対応するメソッドがある場合
				{
					$addTable = $this->db->getTable();
					$addTable = $this->{$extraMethodName}( $addTable , $value , $iQuery_ );

					$iTable_ = $this->db->andTable( $iTable_ , $addTable );
				}
			}

			return $iTable_;
		}

		/**
			@brief     参照可能なレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。省略時は空のテーブル。
			@param[in] $iUsertype_ ユーザー種別。省略時は現在のユーザー種別。
			@param[in] $iUserID_   ユーザーID。省略時は現在のユーザーID。
			@return    検索後のテーブル。
		*/
		function searchReadableTable( $iTable_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //テーブルが指定されていない場合
				{ $iTable_ = $this->db->getTable(); }

			if( !$iUserType_ ) //ユーザー種別が指定されていない場合
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //ユーザーIDが指定されていない場合
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			$addTable = $this->db->getTable();
			$addTable = $this->searchReadableTableProc( $addTable , $iUserType_ , $iUserID_ );

			$iTable_ = $this->db->andTable( $iTable_ , $addTable );

			return $iTable_;
		}

		/**
			@brief     テーブルをソートする。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iQuery_    クエリパラメータ。省略時はGET配列。
			@param[in] $iUsertype_ ユーザー種別。省略時は現在のユーザー種別。
			@param[in] $iUserID_   ユーザーID。省略時は現在のユーザーID。
			@return    ソート後のテーブル。
		*/
		function sortTable( $iTable_ , $iQuery_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //テーブルが指定されていない場合
				{ $iTable_ = $this->db->getTable(); }

			if( !$iQuery_ ) //クエリパラメータが指定されていない場合
				{ $iQuery_ = $_GET; }

			if( !$iUserType_ ) //ユーザー種別が指定されていない場合
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //ユーザーIDが指定されていない場合
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			return $this->sortTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ );
		}

		/**
			@brief     所有権のあるレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。省略時は空のテーブル。
			@param[in] $iUsertype_ ユーザー種別。省略時は現在のユーザー種別。
			@param[in] $iUserID_   ユーザーID。省略時は現在のユーザーID。
			@return    検索後のテーブル。
		*/
		function searchMineTable( $iTable_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //テーブルが指定されていない場合
				{ $iTable_ = $this->db->getTable(); }

			if( !$iUserType_ ) //ユーザー種別が指定されていない場合
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //ユーザーIDが指定されていない場合
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			switch( $iUserType_ ) //ユーザー種別で分岐
			{
				case 'admin' : //管理者
					{ return $iTable_; }

				default : //その他
				{
					$ownerMarks = WS::Info( 'table' )->GetOwnerMarks( $this->getType() );

					if( !array_key_exists( $iUserType_ , $ownerMarks ) ) //所有者カラム設定がない場合
						{ return $this->getEmptyTable(); }

					$iTable_ = $this->db->searchTable( $iTable_ , $ownerMarks[ $iUserType_ ] , '=' , $iUserID_ );

					return $iTable_;
				}
			}
		}

		/**
			@brief     タイムスタンプからレコードを検索する。
			@param[in] $iTable_  検索ベースとするテーブル。省略時は空のテーブル。
			@param[in] $iColumn_ 検索するカラム名。
			@param[in] $iBegins_ 開始時刻のタイムスタンプまたは日付の配列(y/m/d/h/i/s)
			@param[in] $iEnds_   終了時刻のタイムスタンプまたは日付の配列(y/m/d/h/i/s)
			@return    検索後のテーブル。
		*/
		function searchPeriodTable( $iTable_ = null , $iColumn_ , $iBegins_ , $iEnds_ ) //
		{
			if( !$iTable_ ) //テーブルが指定されていない場合
				{ $iTable_ = $this->db->getTable(); }

			$hasBegin = ( is_array( $iBegins_ ) ? $iBegins_[ 0 ] : $iBegins_ );
			$hasEnd   = ( is_array( $iEnds_ )   ? $iEnds_[ 0 ]   : $iEnds_ );

			if( !$hasBegin && !$hasEnd ) //開始時刻と終了時刻が共に無効の場合
				{ return $iTable_; }

			if( !$iColumn_ ) //カラム名が空の場合
				{ return $iTable_; }

			if( is_array( $iBegins_ ) ) //開始時刻が配列で指定されている場合
			{
				List( $year , $month , $day , $hour , $min , $sec ) = $iBegins_;

				$beginTime = mktime( $hour , $min , $sec , $month , $day , $year );
			}
			else //開始時刻がタイムスタンプで指定されている場合
				{ $beginTime = $iBegins_; }

			if( is_array( $iEnds_ ) ) //終了時刻が配列で指定されている場合
			{
				List( $year , $month , $day , $hour , $min , $sec ) = $iEnds_;

				$endTime = mktime( $hour , $min , $sec , $month , $day + 1 , $year );
			}
			else //終了時刻がタイムスタンプで指定されている場合
				{ $endTime = $iEnds_; }

			$addTable = $this->db->getTable();

			if( $beginTime ) //開始時刻が有効な場合
			{
				if( $endTime ) //終了時刻が有効な場合
					{ $addTable = $this->db->searchTable( $addTable , $iColumn_ , 'b' , $beginTime , $endTime ); }
				else //終了時刻が無効な場合
					{ $addTable = $this->db->searchTable( $addTable , $iColumn_ , '>' , $beginTime ); }
			}
			else //開始時刻が無効な場合
				{ $addTable = $this->db->searchTable( $addTable , $iColumn_ , '<' , $endTime ); }

			$iTable_ = $this->db->andTable( $iTable_ , $addTable );

			return $iTable_;
		}

		/**
			@brief     できるだけ重複しないようにor結合する。
			@param[in] $iTables_ 結合するテーブル配列。
			@return    結合後のテーブル。
		*/
		function joinTableOr( $iTables_ ) //
		{
			if( !is_array( $iTables_ ) ) //配列ではない場合
				{ return null; }

			$arraySize = count( $iTables_ );

			switch( $arraySize ) //配列サイズで分岐
			{
				case 0 : //空
					{ return null; }

				case 1 : //1つだけ
					{ return $iTables_[ 0 ]; }

				default : //その他
				{
					$splitPosition = ( int )( count( $iTables_ ) / 2 );

					$lhs = array_slice( $iTables_ , 0 , $splitPosition );
					$rhs = array_slice( $iTables_ , $splitPosition );

					$leftTable  = $this->joinTableOr( $lhs );
					$rightTable = $this->joinTableOr( $rhs );
					$table      = $this->db->orTable( $leftTable , $rightTable );

					return $table;
				}
			}
		}

		//■データ取得 //

		/**
			@brief  このクラスが処理するテーブル名を取得する。
			@return テーブル名。
		*/
		function getType() //
			{ return $this->tableName; }

		/**
			@brief  ヒットしない検索条件を持つテーブルを取得する。
			@return 0件のテーブル。
		*/
		function getEmptyTable() //
		{
			$table = $this->db->getTable();
			$table = $this->db->searchTable( $table , 'shadow_id' , '<' , '0' );

			return $table;
		}

		/**
			@brief     Searchクラスによる検索済みのテーブルを取得する。
			@param[in] $iQuery_ クエリパラメータ。省略時はGET配列。
		*/
		function getSearcherTable( $iQuery_ = null )
		{
			if( !$iQuery_ ) //クエリパラメータが指定されていない場合
				{ $iQuery_ = $_GET; }

			$searcher = new Search( $this->gm , $this->getType() );

			if( ini_get( 'magic_quotes_gpc' ) ) //magic_quotesが有効な場合
				{ $searcher->setParamertorSet( $iQuery_ ); }
			else //magic_quotesが無効な場合
				{ $searcher->setParamertorSet( addslashes_deep( $iQuery_ ) ); }

			return $searcher->getResult();
		}

		//■仮想 //

		/**
			@brief     クエリパラメータを使ってレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iQuery_    クエリパラメータ。
			@param[in] $iUsertype_ ユーザー種別。
			@param[in] $iUserID_   ユーザーID。
			@return    検索後のテーブル。
		*/
		abstract function searchQueryTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ); //

		/**
			@brief     参照可能なレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iUsertype_ ユーザー種別。
			@param[in] $iUserID_   ユーザーID。
			@return    検索後のテーブル。
		*/
		abstract function searchReadableTableProc( $iTable_ , $iUserType_ , $iUserID_ ); //

		/**
			@brief     テーブルをソートする。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iQuery_    クエリパラメータ。
			@param[in] $iUsertype_ ユーザー種別。
			@param[in] $iUserID_   ユーザーID。
			@return    ソート後のテーブル。
		*/
		abstract function sortTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ); //

		//■コンストラクタ・デストラクタ //

		/**
			@brief コンストラクタ。
		*/
		function __construct()
		{
			global $LST;

			$className = get_class( $this );
			$isMatch   = preg_match( '/(.*)Finder$/' , $className , $matches );

			if( !$isMatch ) //パターンにマッチしなかった場合
				{ throw new LogicException( 'コンストラクタを完了できません[' . $className . ']' ); }

			$this->tableName = $matches[ 1 ];

			if( !array_key_exists( $this->tableName , $LST ) ) //カラム設定ファイルが見つからない場合
				{ $this->tableName[ 0 ] = strtoupper( $this->tableName[ 0 ] ); }

			if( !array_key_exists( $this->tableName , $LST ) ) //カラム設定ファイルが見つからない場合
				{ $this->tableName[ 0 ] = strtolower( $this->tableName[ 0 ] ); }

			if( !array_key_exists( $this->tableName , $LST ) ) //カラム設定ファイルが見つからない場合
				{ throw new LogicException( 'コンストラクタを完了できません[' . $className . ']' ); }

			$this->gm = GMList::getGM( $this->tableName );
			$this->db = $this->gm->getDB();
		}

		//■変数 //
		private   $tableName = '';   ///<このクラスが処理するテーブル名。
		protected $gm        = null; ///<テーブルのGUIManagerオブジェクト。
		protected $db        = null; ///<テーブルのDatabaseオブジェクト。
	}
