<?php

	class adwaresLogic
	{
		function setSelectCarrierName( &$ioRec_ )
		{
			$db      = GMList::getDB( 'adwares' );
			$carrier = $db->getData( $ioRec_ , 'use_carrier_url' );

			if( !$carrier ) //キャリア別URLを使用しない場合
			{
				$db->setData( $ioRec_ , 'selected_carrier' , '' );
				return;
			}

			$docomo   = $db->getData( $ioRec_ , 'url_docomo' );
			$au       = $db->getData( $ioRec_ , 'url_au' );
			$softbank = $db->getData( $ioRec_ , 'url_softbank' );
			$iphone   = $db->getData( $ioRec_ , 'url_iphone' );
			$android  = $db->getData( $ioRec_ , 'url_android' );

			$carrierNames = Array();

			if( $docomo )
				{ $carrierNames[] = 'docomo'; }

			if( $au )
				{ $carrierNames[] = 'au'; }

			if( $softbank )
				{ $carrierNames[] = 'softbank'; }

			if( $iphone )
				{ $carrierNames[] = 'iphone'; }

			if( $android )
				{ $carrierNames[] = 'android'; }

			$db->setData( $ioRec_ , 'selected_carrier' , implode( '/' , $carrierNames ) );
		}

		function CreateRecordByAPIResult( $iAPIType_ , $iResult_ , $iSubmitRec_ )
		{
			$db          = GMList::getDB( $iAPIType_ );
			$category    = $db->getData( $iSubmitRec_ , 'import_category' );
			$open        = $db->getData( $iSubmitRec_ , 'open_status' );
			$usePointSet = $db->getData( $iSubmitRec_ , 'use_point_setting' );
			$useLimitSet = $db->getData( $iSubmitRec_ , 'use_limit_setting' );

			$apiColumn = Array( 'moba8API' => 'moba8_id' , 'accessTradeAPI' => 'access_trade_id' );
			$aspID     = Array( 'moba8API' => 'ASP0005'  , 'accessTradeAPI' => 'ASP0004' );

			$adDB    = GMList::getDB( 'adwares' );
			$adTable = $adDB->getTable();
			$adTable = $adDB->searchTable( $adTable , $apiColumn[ $iAPIType_ ] , '=' , $iResult_[ 'id' ] );
			$adTable = $adDB->limitOffset( $adTable , 0 , 1 );
			$adRow   = $adDB->getRow( $adTable );

			if( $adRow ) //既にインポートされている場合
				{ return null; }

			$adRec = $adDB->getNewRecord();

			$adDB->setData( $adRec , 'id'                     , SystemUtil::getNewId( $adDB , 'adwares' ) );
			$adDB->setData( $adRec , $apiColumn[ $iAPIType_ ] , $iResult_[ 'id' ] );
			$adDB->setData( $adRec , 'category'               , $category );
			$adDB->setData( $adRec , 'name'                   , $iResult_[ 'name' ] );
			$adDB->setData( $adRec , 'text'                   , $iResult_[ 'text' ] );
			$adDB->setData( $adRec , 'banner'                 , $iResult_[ 'banner' ] );
			$adDB->setData( $adRec , 'term'                   , $iResult_[ 'term' ] );
			$adDB->setData( $adRec , 'open'                   , $open );
			$adDB->setData( $adRec , 'asp_type'               , $aspID[ $iAPIType_ ] );
			$adDB->setData( $adRec , 'regist'                 , time() );

			if( $useLimitSet ) //有効期限一括設定を使用する場合
			{
				$year  = $db->getData( $iSubmitRec_ , 'limit_year' );
				$month = $db->getData( $iSubmitRec_ , 'limit_month' );
				$day   = $db->getData( $iSubmitRec_ , 'limit_day' );

				$adDB->setData( $adRec , 'use_limit_time' , TRUE );
				$adDB->setData( $adRec , 'limit_year'     , $year );
				$adDB->setData( $adRec , 'limit_month'    , $month );
				$adDB->setData( $adRec , 'limit_day'      , $day );
				$adDB->setData( $adRec , 'limit_time'     , mktime( 0 , 0 , 0 , $month , $day , $year ) );
			}
			else
			{
				$adDB->setData( $adRec , 'use_limit_time' , $iResult_[ 'use_limit' ] );
				$adDB->setData( $adRec , 'limit_year'     , $iResult_[ 'limit_y' ] );
				$adDB->setData( $adRec , 'limit_month'    , $iResult_[ 'limit_m' ] );
				$adDB->setData( $adRec , 'limit_day'      , $iResult_[ 'limit_d' ] );
				$adDB->setData( $adRec , 'limit_time'     , $iResult_[ 'limit_t' ] );
			}


			if( $usePointSet ) //ポイント一括設定を使用する場合
			{
				$adDB->setData( $adRec , 'point'            , $db->getData( $iSubmitRec_ , 'point' ) );
				$adDB->setData( $adRec , 'point_type'       , $db->getData( $iSubmitRec_ , 'point_type' ) );
				$adDB->setData( $adRec , 'point_view_type'  , $db->getData( $iSubmitRec_ , 'point_view_type' ) );
				$adDB->setData( $adRec , 'point_view_value' , $db->getData( $iSubmitRec_ , 'point_view_value' ) );
			}
			else
			{
				$adDB->setData( $adRec , 'point'      , $iResult_[ 'reward' ] );
				$adDB->setData( $adRec , 'point_type' , $iResult_[ 'reward_type' ] );
			}

			if( $iResult_[ 'use_carrier' ] ) //キャリアに対応する場合
			{
				$adDB->setData( $adRec , 'use_carrier_url' , $iResult_[ 'use_carrier' ] );
				$adDB->setData( $adRec , 'url_docomo'      , $iResult_[ 'carrier' ][ 'docomo' ] );
				$adDB->setData( $adRec , 'url_au'          , $iResult_[ 'carrier' ][ 'au' ] );
				$adDB->setData( $adRec , 'url_softbank'    , $iResult_[ 'carrier' ][ 'softbank' ] );
				$adDB->setData( $adRec , 'url_iphone'      , $iResult_[ 'carrier' ][ 'iphone' ] );
				$adDB->setData( $adRec , 'url_android'     , $iResult_[ 'carrier' ][ 'android' ] );
			}
			else //キャリアに対応しない場合
			{
				$adDB->setData( $adRec , 'use_carrier_url' , $iResult_[ 'use_carrier' ] );
				$adDB->setData( $adRec , 'url'             , $iResult_[ 'url' ] );
			}

			return $adRec;
		}

		function CreateRecordByMoba8APIResult( $iResult_ , $iSubmitRec_ )
		{
			$db          = GMList::getDB( 'moba8API' );
			$category    = $db->getData( $iSubmitRec_ , 'import_category' );
			$open        = $db->getData( $iSubmitRec_ , 'open_status' );
			$usePointSet = $db->getData( $iSubmitRec_ , 'use_point_setting' );
			$useLimitSet = $db->getData( $iSubmitRec_ , 'use_limit_setting' );

			$adDB    = GMList::getDB( 'adwares' );
			$adTable = $adDB->getTable();
			$adTable = $adDB->searchTable( $adTable , 'moba8_id' , '=' , $iResult_[ 'id' ] );
			$adRow   = $adDB->getRow( $adTable );

			if( $adRow ) //既にインポートされている場合
				{ return null; }

			$adRec = $adDB->getNewRecord();

			$adDB->setData( $adRec , 'id'             , SystemUtil::getNewId( $adDB , 'adwares' ) );
			$adDB->setData( $adRec , 'moba8_id'       , $iResult_[ 'id' ] );
			$adDB->setData( $adRec , 'category'       , $category );
			$adDB->setData( $adRec , 'name'           , $iResult_[ 'name' ] );

			if( $useLimitSet ) //有効期限一括設定を使用する場合
			{
				$year  = $db->getData( $iSubmitRec_ , 'limit_year' );
				$month = $db->getData( $iSubmitRec_ , 'limit_month' );
				$day   = $db->getData( $iSubmitRec_ , 'limit_day' );

				$adDB->setData( $adRec , 'use_limit_time' , TRUE );
				$adDB->setData( $adRec , 'limit_year'     , $year );
				$adDB->setData( $adRec , 'limit_month'    , $month );
				$adDB->setData( $adRec , 'limit_day'      , $day );
				$adDB->setData( $adRec , 'limit_time'     , mktime( 0 , 0 , 0 , $month , $day , $year ) );
			}
			else
			{
				$adDB->setData( $adRec , 'use_limit_time' , TRUE );
				$adDB->setData( $adRec , 'limit_year'     , $iResult_[ 'limitYear' ] );
				$adDB->setData( $adRec , 'limit_month'    , $iResult_[ 'limitMonth' ] );
				$adDB->setData( $adRec , 'limit_day'      , $iResult_[ 'limitDay' ] );
				$adDB->setData( $adRec , 'limit_time'     , $iResult_[ 'limitTime' ] );
			}

			foreach( $iResult_[ 'material' ] as $material )
			{
				switch( $material[ 'type' ] )
				{
					case 'text' :
					{
						$adDB->setData( $adRec , 'text' , $material[ 'value' ] );

						break;
					}

					case 'banner' :
					{
						$adDB->setData( $adRec , 'banner' , $material[ 'value' ] );

						break;
					}

					default :
						{ break; }
				}
			}

			if( $iResult_[ 'carrier' ][ 'docomo' ] == $iResult_[ 'carrier' ][ 'au' ] && $iResult_[ 'carrier' ][ 'docomo' ] == $iResult_[ 'carrier' ][ 'softbank' ] && $iResult_[ 'carrier' ][ 'docomo' ] == $iResult_[ 'carrier' ][ 'smartphone' ] )
			{
				$adDB->setData( $adRec , 'use_carrier_url' , false );
				$adDB->setData( $adRec , 'url'             , $iResult_[ 'carrier' ][ 'docomo' ] );
				$adDB->setData( $adRec , 'url_docomo'      , '' );
				$adDB->setData( $adRec , 'url_au'          , '' );
				$adDB->setData( $adRec , 'url_softbank'    , '' );
				$adDB->setData( $adRec , 'url_iphone'      , '' );
				$adDB->setData( $adRec , 'url_android'     , '' );
			}
			else
			{
				$adDB->setData( $adRec , 'use_carrier_url' , true );
				$adDB->setData( $adRec , 'url'             , '' );
				$adDB->setData( $adRec , 'url_docomo'      , $iResult_[ 'carrier' ][ 'docomo' ] );
				$adDB->setData( $adRec , 'url_au'          , $iResult_[ 'carrier' ][ 'au' ] );
				$adDB->setData( $adRec , 'url_softbank'    , $iResult_[ 'carrier' ][ 'softbank' ] );
				$adDB->setData( $adRec , 'url_iphone'      , $iResult_[ 'carrier' ][ 'smartphone' ] );
				$adDB->setData( $adRec , 'url_android'     , $iResult_[ 'carrier' ][ 'smartphone' ] );
			}

			$adDB->setData( $adRec , 'term' , $iResult_[ 'comment' ] );

			if( $usePointSet ) //ポイント一括設定を使用する場合
			{
				$adDB->setData( $adRec , 'point'            , $db->getData( $iSubmitRec_ , 'point' ) );
				$adDB->setData( $adRec , 'point_type'       , $db->getData( $iSubmitRec_ , 'point_type' ) );
				$adDB->setData( $adRec , 'point_view_type'  , $db->getData( $iSubmitRec_ , 'point_view_type' ) );
				$adDB->setData( $adRec , 'point_view_value' , $db->getData( $iSubmitRec_ , 'point_view_value' ) );
			}
			else
			{
				foreach( $iResult_[ 'reward' ] as $reward )
				{
					if( 'sale' != $reward[ 'type' ] )
						{ continue; }

					if( $reward[ 'rate' ] )
					{
						$adDB->setData( $adRec , 'point'      , $reward[ 'rate' ] );
						$adDB->setData( $adRec , 'point_type' , 'p' );
					}
					else
					{
						$adDB->setData( $adRec , 'point'      , $reward[ 'amount' ] );
						$adDB->setData( $adRec , 'point_type' , 'f' );
					}
				}
			}

			$adDB->setData( $adRec , 'open'     , $open );
			$adDB->setData( $adRec , 'asp_type' , 'ASP0005' );
			$adDB->setData( $adRec , 'regist'   , time() );

			return $adRec;
		}
	}
