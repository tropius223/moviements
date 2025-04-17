<?php

	class LinkBase
	{
		function action()
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $LOGIN_ID;

			$db  = $gm[ 'adwares' ]->getDB();
			$rec = $db->selectRecord( $_GET[ 'id' ] );

			if( !$rec )
			{
				print System::getHead( $gm , $loginUserType , $loginUserRank );
				Template::drawErrorTemplate();
				print System::getFoot( $gm , $loginUserType , $loginUserRank );
				return;
			}

			if( !$db->getData( $rec , 'open' ) )
			{
				header( 'Location:other.php?key=invalid_link' );
				exit();
			}

			$this->addClickLog( $LOGIN_ID , $_GET[ 'id' ] );

			$url   = $this->loadURL( $db , $rec );
			$aid   = $this->makeAID( $db , $rec , $LOGIN_ID );
			$param = $this->makeParam( $db , $rec , $aid );

			if( FALSE === strpos( $url , '?' ) )
				{ $url .= '?' . $param; }
			else
				{ $url .= '&' . $param; }

			header( 'Location:' . $url );
			exit;
		}

		function makeAID( $iDB_ , $iRec_ , $iLoginID_ )
		{
			$aid = $iDB_->getData( $iRec_ , 'id' );

			return $iLoginID_ . '_' . $aid;
		}

		function makeParam( $iDB_ , $iRec_ , $iAID_ )
		{
			$aspID = $iDB_->getData( $iRec_ , 'asp_type' );

			$aspDB    = GMList::getDB( 'asp_type' );
			$aspRec   = $aspDB->selectRecord( $aspID );
			$aspParam = $aspDB->getData( $aspRec , 'param' );

			return $aspParam . '=' . $iAID_;
		}

		function loadURL( $iDB_ , $iRec_ )
		{
			global $terminal_type;

			$useCarrier = $iDB_->getData( $iRec_ , 'use_carrier_url' );

			if( $useCarrier ) //キャリア別URlを使用する場合
			{
				switch( $terminal_type ) //端末の種類で分岐
				{
					case MobileUtil::$TYPE_NUM_DOCOMO :
					{
						$url = $iDB_->getData( $iRec_ , 'url_docomo' );
						break;
					}
					case MobileUtil::$TYPE_NUM_AU :
					{
						$url = $iDB_->getData( $iRec_ , 'url_au' );
						break;
					}
					case MobileUtil::$TYPE_NUM_SOFTBANK :
					{
						$url = $iDB_->getData( $iRec_ , 'url_softbank' );
						break;
					}
					case MobileUtil::$TYPE_NUM_IPHONE :
					{
						$url = $iDB_->getData( $iRec_ , 'url_iphone' );
						break;
					}
					case MobileUtil::$TYPE_NUM_ANDROID :
					{
						$url = $iDB_->getData( $iRec_ , 'url_android' );
						break;
					}
				}
			}
			else //共通URlを使用する場合
				{ $url = $iDB_->getData( $iRec_ , 'url' ); }

			return $url;
		}

		/**
			@brief     広告のクリックログを追加する。
			@param[in] $iUserID_    ユーザーID。
			@param[in] $iAdwaresID_ 広告ID。
		*/
		function addClickLog( $iUserID_ , $iAdwaresID_ ) //
		{
			$db    = GMList::getDB( 'click_log' );
			$table = $db->getTable();
			$table = $db->searchTable( $table , 'nuser_id' , '=' , $iUserID_ );
			$table = $db->limitOffset( $table , 0 , 1 );
			$row   = $db->getRow( $table );

			if( !$row ) //クリックログが見つからない場合
				{ return; }

			$rec      = $db->getRecord( $table , 0 );
			$clickIDs = $db->getData( $rec , 'click_adwares_id_list' );

			if( 0 < strlen( $clickIDs ) ) //データが存在する場合
				{ $clickIDs = explode( '/' , $clickIDs ); }
			else //データが空の場合
				{ $clickIDs = Array(); }

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //成果が発生していない場合
			{
				$clickIDs[] = $iAdwaresID_;
				$clickIDs   = implode( '/' , $clickIDs );
				$db->setData( $rec , 'click_adwares_id_list' , $clickIDs );
				$db->updateRecord( $rec );
			}
		}
	}
