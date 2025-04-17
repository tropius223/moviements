<?php

	class TrackAppDriver extends TrackBase
	{
		function action()
		{
			if( '59.106.111.152' != $_SERVER[ 'REMOTE_ADDR' ] )
			{
				print 'IP ERROR';
				exit;
			}

			List( $id , $adwares ) = explode( '_' , $_GET[ 'identifier' ] , 2 );

			$userRec    = $this->getUserRecord( $id );
			$adwaresRec = $this->getAdwaresRecord( $adwares );
			$enablePay  = $this->enablePay( $id , $adwares , $_GET[ 'achieve_id' ] );

			if( !$userRec || !$adwaresRec )
			{
				print '0';
				exit;
			}

			if( 'OK' != $enablePay )
			{
				print $enablePay;
				exit;
			}

			$this->addPayLog( $id , $adwares );
			$this->addKickback( $userRec , $adwaresRec , $_GET[ 'point' ] , $_GET[ 'achieve_id' ] );

			print '1';
			exit;
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
				{ return '0'; }

			$rec      = $db->getRecord( $table , 0 );
			$clickIDs = $db->getData( $rec , 'click_adwares_id_list' );
			$payIDs   = $db->getData( $rec , 'pay_adwares_id_list' );

			$clickIDs = explode( '/' , $clickIDs );
			$payIDs   = explode( '/' , $payIDs );

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //クリックされていない場合
				{ return '0'; }

			$kDB    = GMList::getDB( 'kickback' );
			$kTable = $kDB->getTable();
			$kTable = $kDB->searchTable( $kTable , 'owner' , '=' , $iUserID_ );
			$kTable = $kDB->searchTable( $kTable , 'adwares' , '=' , $iAdwaresID_ );
			$kTable = $kDB->searchTable( $kTable , 'identity' , '=' , $iIdentityID_ );
			$kTable = $kDB->limitOffset( $kTable , 0 , 1 );
			$kRow   = $kDB->getRow( $kTable );

			if( 0 < $kRow ) //既に成果発生済みの場合
				{ return '1'; }

			return 'OK';
		}

		/*		成果を発生させる		*/
		/*		p0 : ユーザーレコード		*/
		/*		p1 : 広告レコード		*/
		/*		p2 : 成果額		*/
		function addKickback( $_userRec , $_adwaresRec , $_point = 0 , $_identity = '' )
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
			$db->setData( $rec , 'identity' , $_identity );
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
