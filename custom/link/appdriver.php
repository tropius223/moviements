<?php

	class LinkAPPDriver extends LinkBase
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
			$appID = $db->getData( $rec , 'app_driver_id' );

			if( FALSE === strpos( $url , '?' ) )
				{ $url .= '?' . $param; }
			else
				{ $url .= '&' . $param; }

			if( !$aspID )
				{ $url = $this->extendURL( $url ); }

			header( 'Location:' . $url );
			exit;
		}

		function makeParam( $iDB_ , $iRec_ , $iAID_ )
		{
			$aspID = $iDB_->getData( $iRec_ , 'asp_type' );
			$appID = $iDB_->getData( $iRec_ , 'app_driver_id' );

			$aspDB    = GMList::getDB( 'asp_type' );
			$aspRec   = $aspDB->selectRecord( $aspID );
			$aspParam = $aspDB->getData( $aspRec , 'param' );

			if( $appID )
				{ $aspParam = '_' . $aspParam; }

			return $aspParam . '=' . $iAID_;
		}

		function ExtendURL( $iURL_ , $iAID_ )
		{
			$sysDB   = GMList::getDB( 'system' );
			$sysRec  = $sysDB->selectRecord( 'ADMIN' );
			$mediaID = $sysDB->getData( $sysRec , 'appdriver_media_id' );
			$siteKey = $sysDB->getData( $sysRec , 'appdriver_site_key' );

			preg_match( '/media_id=(\w+)/' , $iURL_ , $matches );
			$mediaID = $matches[ 1 ];

			$sha256Seed = $iAID_ . ';' . $mediaID . ';' . $siteKey;
			$sha256     = hash( 'sha256' , $sha256Seed );

			$digest = 'digest=' . $sha256;

			return $iURL_ . '&' . $digest;
		}
	}
