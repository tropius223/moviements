<?php

	//★クラス //

	class adwaresFinder extends baseFinder //
	{
		//■実装 //

		/**
			@brief     クエリパラメータを使ってレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iQuery_    クエリパラメータ。
			@param[in] $iUserType_ ユーザー種別。
			@param[in] $iUserID_   ユーザーID。
			@retval    検索後のテーブル 検索処理が正常に行われた場合。
			@retval    null             検索処理が正常に行われなかった場合。
		*/
		function searchQueryTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ) //
			{ return $iTable_; }

		/**
			@brief     参照可能なレコードを検索する。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iUsertype_ ユーザー種別。
			@param[in] $iUserID_   ユーザーID。
			@retval    検索後のテーブル 検索処理が正常に行われた場合。
			@retval    null             検索処理が正常に行われなかった場合。
		*/
		function searchReadableTableProc( $iTable_ , $iUserType_ , $iUserID_ ) //
		{
			switch( $iUserType_ ) //ユーザー種別で分岐
			{
				case 'admin' : //管理者
					{ return $iTable_; }

				default : //その他
				{
					$tableA = $this->db->searchTable( $iTable_ , 'use_limit_time' , '=' , true );
					$tableA = $this->db->searchTable( $tableA , 'limit_time' , '>' , time() );

					$tableB = $this->db->searchTable( $iTable_ , 'use_limit_time' , '=' , false );

					$iTable_ = $this->db->orTable( $tableA , $tableB );

					$iTable_ = $this->db->searchTable( $iTable_ , 'open' , '=' , true );

					return $iTable_;
				}
			}
		}

		/**
			@brief     テーブルをソートする。
			@param[in] $iTable_    検索ベースとするテーブル。
			@param[in] $iQuery_    クエリパラメータ。
			@param[in] $iUsertype_ ユーザー種別。
			@param[in] $iUserID_   ユーザーID。
			@return    ソート後のテーブル。
		*/
		function sortTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ) //
		{
			$iTable_ = $this->db->sortTable( $iTable_ , 'regist' , 'desc' , true );

			return $iTable_;
		}

		function searchPCTable( $iTable_ ) //
		{
			$table = $this->db->searchTable( $iTable_ , 'use_carrier_url' , '=' , false );

			return $table;
		}

		function extraSearchUseable( $iTable_ , $iValue_ , $iQuery_ )
		{
			global $LOGIN_ID;

			if( is_array( $iValue_ ) && 'true' == $iValue_[ 0 ] )
			{
				$clickLogDB    = GMList::getDB( 'click_log' );
				$clickLogTable = $clickLogDB->getTable();
				$clickLogTable = $clickLogDB->searchTable( $clickLogTable , 'nuser_id' , '=' , $LOGIN_ID );
				$clickLogTable = $clickLogDB->limitOffset( $clickLogTable , 0 , 1 );
				$clickLogRec   = $clickLogDB->getRecord( $clickLogTable , 0 );
				$payAdwaresID  = $clickLogDB->getData( $clickLogRec , 'pay_adwares_id_list' );
				$payAdwaresIDs = explode( '/' , $payAdwaresID );

				$iTable_ = $this->db->searchTable( $iTable_ , 'id' , 'not in' , $payAdwaresIDs );
			}

			return $iTable_;
		}
	}
