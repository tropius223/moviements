<?php

	class  nUserFinder extends baseFinder //
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
			{ return $iTable_; }

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

		function extraSearchThisMonth( $iTable_ , $iValue_ , $iQuery_ )
		{
			if( 'true' == $iValue_ )
				{ $iTable_ = $this->db->searchTable( $iTable_ , 'regist' , '>=' , mktime( 0 , 0 , 0 , date( 'n' ) , 1 , date( 'Y' ) ) ); }

			return $iTable_;
		}

		function extraSearchPreviousMonth( $iTable_ , $iValue_ , $iQuery_ )
		{
			if( 'true' == $iValue_ )
			{
				$iTable_ = $this->db->searchTable( $iTable_ , 'regist' , '>=' , mktime( 0 , 0 , 0 , date( 'n' ) - 1 , 1 , date( 'Y' ) ) );
				$iTable_ = $this->db->searchTable( $iTable_ , 'regist' , '<' , mktime( 0 , 0 , 0 , date( 'n' ) , 1 , date( 'Y' ) ) );
			}

			return $iTable_;
		}
	}
