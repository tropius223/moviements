<?php

	class click_logLogic //
	{
		//■処理 //

		/**
			@brief         広告のクリック履歴を追加します。
			@param[in,out] $ioRec_      クリックログのレコードデータ。
			@param[in]     $iAdwaresID_ 追加する広告のID。
			@retval        true  レコードデータが変更された場合。
			@retval        false レコードデータに変更がない場合。
		*/
		function setClickAdwaresLog( &$ioRec_ , $iAdwaresID_ ) //
		{
			$db = GMList::getDB( 'click_log' );

			$clickIDs = $this->db->getData( $ioRec_ , 'click_adwares_id_list' );
			$clickIDs = explode( '/' , $clickIDs );

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //IDが含まれていない場合
			{
				$clickIDs[] = $iAdwaresID_;
				$clickIDs   = implode( '/' , $clickIDs );

				$db->setData( $ioRec_ , 'click_adwares_id_list' , $clickIDs );

				return true;
			}

			return false;
		}

		/**
			@brief         広告の成果発生履歴を追加します。
			@param[in,out] $ioRec_      クリックログのレコードデータ。
			@param[in]     $iAdwaresID_ 追加する広告のID。
			@retval        true  レコードデータが変更された場合。
			@retval        false レコードデータに変更がない場合。
		*/
		function setPayAdwaresLog( &$ioRec_ , $iAdwaresID_ ) //
		{
			$db = GMList::getDB( 'click_log' );

			$clickIDs = $this->db->getData( $ioRec_ , 'pay_adwares_id_list' );
			$clickIDs = explode( '/' , $clickIDs );

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //IDが含まれていない場合
			{
				$clickIDs[] = $iAdwaresID_;
				$clickIDs   = implode( '/' , $clickIDs );

				$db->setData( $ioRec_ , 'pay_adwares_id_list' , $clickIDs );

				return true;
			}

			return false;
		}
	}
