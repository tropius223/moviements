<?php

	ob_start();

	try
	{
		include_once 'custom/head_main.php';
		include_once 'custom/track/base.php';

		switch( GetASPType() )
		{
			case 'moba8' :
			{
				include_once 'custom/track/moba8.php';
				$track = new TrackMoba8();
				break;
			}

			case 'appdriver' :
			{
				include_once 'custom/track/appdriver.php';
				$track = new TrackAPPDriver();
				break;
			}

			case 'janet' :
			{
				include_once 'custom/track/janet.php';
				$track = new TrackJANet();
				break;
			}

			case 'smaad' :
			{
				include_once 'custom/track/smaad.php';
				$track = new TrackSMAAD();
				break;
			}

			case 'banner_bridge' :
			{
				include_once 'custom/track/bridge.php';
				$track = new TrackBannerBridge();
				break;
			}

			case 'affesta' :
			{
				include_once 'custom/track/affesta.php';
				$track = new TrackAffesta();
				break;
			}

			default :
			{
				$track = new TrackBase();
				break;
			}
		}

		$track->action();
	}
	catch( Exception $e_ )
	{
		ob_end_clean();

		//エラーメッセージをログに出力
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );
	}

	ob_end_flush();

	function GetASPType()
	{
		if( isset( $_GET[ 'aid' ] ) ) //フォーマット指定可能ASP
		{
			if( isset( $_GET[ 'sid' ] ) )
				{ return 'affesta'; }

			List( $id , $adwares ) = explode( '_' , $_GET[ 'aid' ] , 2 );

			$adDB  = GMList::getDB( 'adwares' );
			$adRec = $adDB->selectRecord( $adwares );
			$aspID = $adDB->getData( $adRec , 'asp_type' );

			$aspDB    = GMList::getDB( 'asp_type' );
			$aspRec   = $aspDB->selectRecord( $aspID );
			$systemID = $aspDB->getData( $aspRec , 'system_id' );

			return $systemID;
		}
		else //その他のASP
		{
			if( isset( $_GET[ 'point_id1' ] ) )
				{ return 'moba8'; }

			if( isset( $_GET[ 'identifier' ] ) )
				{ return 'appdriver'; }

			if( isset( $_GET[ 'user_id' ] ) )
				{ return 'janet'; }

			if( isset( $_GET[ 'user' ] ) )
				{ return 'smaad'; }

			if( isset( $_GET[ 'ps' ] ) )
				{ return 'banner_bridge'; }
		}
	}
