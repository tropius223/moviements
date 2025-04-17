<?php

	//★クラス //

	class newsFinder extends baseFinder //
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
		{
			$iTable_ = $this->db->sortTable( $iTable_ , 'open_time' , 'desc' , true );
			$iTable_ = $this->db->sortTable( $iTable_ , 'regist' , 'desc' , true );

			return $iTable_;
		}

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
			global $ACTIVE_ACCEPT;

			switch( $iUserType_ ) //ユーザー種別で分岐
			{
				case 'admin' : //管理者
					{ return $iTable_; }

				default : //その他
				{
					$iTable_ = $this->db->searchTable( $iTable_ , 'open_time' , '<' , time() );
					$iTable_ = $this->db->searchTable( $iTable_ , 'activate' , '=' , $ACTIVE_ACCEPT );

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
			$iTable_ = $this->db->sortTable( $iTable_ , 'open_time' , 'desc' );
			$iTable_ = $this->db->sortTable( $iTable_ , 'regist' , 'desc' , true );

			return $iTable_;
		}
	}
