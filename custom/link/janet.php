<?php

	class LinkJANet extends LinkBase
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

			$url .= $aid;

			header( 'Location:' . $url );
			exit;
		}
	}
