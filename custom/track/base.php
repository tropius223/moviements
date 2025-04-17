<?php

	class TrackBase
	{
		function action()
		{
			$aspDB    = GMList::getDB( 'asp_type' );
			$aspTable = $aspDB->getTable();
			$aspRow   = $aspDB->getRow( $aspTable );

			$pointParam = 'point';

			for( $i = 0 ; $aspRow > $i ; ++$i )
			{
				$aspRec   = $aspDB->getRecord( $aspTable , $i );
				$aspParam = $aspDB->getData( $aspRec , 'point_param' );

				if( $aspParam && isset( $_GET[ $aspParam ] ) )
				{
					$pointParam = $aspParam;

					break;
				}
			}

			List( $id , $adwares ) = explode( '_' , $_GET[ 'aid' ] , 2 );

			$userRec    = $this->getUserRecord( $id );
			$adwaresRec = $this->getAdwaresRecord( $adwares );
			$enablePay  = $this->enablePay( $id , $adwares );

			if( !$userRec || !$adwaresRec || !$enablePay )
				{ return; }

			$this->addPayLog( $id , $adwares );
			$this->addKickback( $userRec , $adwaresRec , $_GET[ $pointParam ] );
		}

		/**
			@brief     新規に成果の発生が可能か確認する。
			@param[in] $iUserID_    ユーザーID。
			@param[in] $iAdwaresID_ 広告ID。
			@retval    true  成果の発生が可能な場合。
			@retval    false 広告がクリックされていない、または成果発生済みの場合。
		*/
		function enablePay( $iUserID_ , $iAdwaresID_ , $iIdentityID_ = null ) //
		{
			$db    = GMList::getDB( 'click_log' );
			$table = $db->getTable();
			$table = $db->searchTable( $table , 'nuser_id' , '=' , $iUserID_ );
			$table = $db->limitOffset( $table , 0 , 1 );
			$row   = $db->getRow( $table );

			if( !$row ) //クリックログが見つからない場合
				{ return false; }

			$rec      = $db->getRecord( $table , 0 );
			$clickIDs = $db->getData( $rec , 'click_adwares_id_list' );
			$payIDs   = $db->getData( $rec , 'pay_adwares_id_list' );

			$clickIDs = explode( '/' , $clickIDs );
			$payIDs   = explode( '/' , $payIDs );

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //クリックされていない場合
				{ return false; }

			if( in_array( $iAdwaresID_ , $payIDs ) ) //成果発生済みの場合
				{ return false; }

			return true;
		}

		/*		アクセスログがあるならtrue		*/
		/*		p0 : ユーザーID		*/
		/*		p1 : 広告ID		*/
		function searchAccess( $_id , $_adwares )
		{
			$db    = SystemUtil::getGMforType( 'access' )->getDB();
			$table = $db->getTable();
			$table = $db->searchTable( $table , 'owner' , '=' , $_id );
			$table = $db->searchTable( $table , 'adwares' , '=' , $_adwares );

			return ( $db->getRow( $table ) ? true : false );
		}

		/*		ユーザーレコード取得		*/
		/*		p0 : ユーザーID		*/
		function getUserRecord( $_id )
		{
			$db    = SystemUtil::getGMforType( 'nUser' )->getDB();
			$rec   = $db->selectRecord( $_id );

			return ( $rec ? $rec: null );
		}

		/*		広告レコード取得		*/
		/*		p0 : 広告ID		*/
		function getAdwaresRecord( $_id )
		{
			$db    = SystemUtil::getGMforType( 'adwares' )->getDB();
			$rec   = $db->selectRecord( $_id );

			return ( $rec ? $rec: null );
		}

		/**
			@brief     成果発生ログを追加する。
			@param[in] $iUserID_    ユーザーID。
			@param[in] $iAdwaresID_ 広告ID。
		*/
		function addPayLog( $iUserID_ , $iAdwaresID_ ) //
		{
			$db    = GMList::getDB( 'click_log' );
			$table = $db->getTable();
			$table = $db->searchTable( $table , 'nuser_id' , '=' , $iUserID_ );
			$table = $db->limitOffset( $table , 0 , 1 );
			$row   = $db->getRow( $table );

			if( !$row ) //クリックログが見つからない場合
				{ return; }

			$rec    = $db->getRecord( $table , 0 );
			$payIDs = $db->getData( $rec , 'pay_adwares_id_list' );

			if( 0 < strlen( $payIDs ) ) //データが存在する場合
				{ $payIDs = explode( '/' , $payIDs ); }
			else //データが空の場合
				{ $payIDs = Array(); }

			if( !in_array( $iAdwaresID_ , $payIDs ) ) //成果が発生していない場合
			{
				$payIDs[] = $iAdwaresID_;
				$payIDs   = implode( '/' , $payIDs );

				$db->setData( $rec , 'pay_adwares_id_list' , $payIDs );
				$db->updateRecord( $rec );
			}
		}

		/*		成果を発生させる		*/
		/*		p0 : ユーザーレコード		*/
		/*		p1 : 広告レコード		*/
		/*		p2 : 成果額		*/
		function addKickback( $_userRec , $_adwaresRec , $_point = 0 )
		{
			global $KICKBACK_STATE_DEF;

			$db = GMList::getDB( 'kickback' );

			$pointType = $db->getData( $_adwaresRec , 'point_type' );
			$point     = $db->getData( $_adwaresRec , 'point' );

			if( 'p' == $pointType )
				$point = ( int )( $_point * $point / 100.0 );

			$rate = SystemUtil::getSystemData( 'point_to_yen_rate' );

			$rec = $db->getNewRecord();
			$db->setData( $rec , 'id' , SystemUtil::getNewId( $db , 'kickback' ) );
			$db->setData( $rec , 'owner' , $db->getData( $_userRec , 'id' ) );
			$db->setData( $rec , 'adwares' , $db->getData( $_adwaresRec , 'id' ) );
			$db->setData( $rec , 'point' , $point );
			$db->setData( $rec , 'point_yen' , $point * $rate );
			$db->setData( $rec , 'state' , $KICKBACK_STATE_DEF );
			$db->setData( $rec , 'regist' , time() );

			$db->addRecord( $rec );

			$db = GMList::getDB('nUser');
			$db->setData( $_userRec , 'point' , $db->getData( $_userRec , 'point' ) + $point );
			$db->updateRecord( $_userRec );

			$userID    = $db->getData( $_userRec , 'id' );
			$adwaresID = $db->getData( $_adwaresRec , 'id' );

			CreateTier( $userID , $adwaresID , $point , $rec );
		}
	}
