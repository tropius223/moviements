<?php

	class AccessTradeAPILogic
	{
		function GetResultsBySubmit( $iRec_ )
		{
			$apiKey = SystemUtil::getSystemData( 'access_trade_access_id' );

			if( !$apiKey ) //APIキーが設定されていない場合
				{ throw new RuntimeException( 'getResultsBySubmit を完了できません' ); }

			$db     = GMList::getDB( 'accessTradeAPI' );
			$getRow = $db->getData( $iRec_ , 'get_row' );
			$first  = $db->getData( $iRec_ , 'first_category' );
			$second = $db->getData( $iRec_ , 'second_category' );
			$third  = $db->getData( $iRec_ , 'third_category' );

			$api = new AccessTradeAPIModel( $apiKey );
			$api->setCategories( $first , $second , $third );

			return $api->getResults( $getRow );
		}

		function ConvertAPIResultToCommonFormat( $iResult_ )
		{
			List( $pcXML , $mobileXML , $smartphoneXML ) = $iResult_;

			$results = Array();
			$items   = Array();

			foreach( $pcXML->Program as $item ) //全てのアイテムを処理
				{ $items[ mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' ) ][ 'pc' ] = $item; }

			foreach( $mobileXML->Program as $item ) //全てのアイテムを処理
				{ $items[ mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' ) ][ 'mobile' ] = $item; }

			foreach( $smartphoneXML->Program as $item ) //全てのアイテムを処理
				{ $items[ mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' ) ][ 'smartphone' ] = $item; }

			$results = Array();

			foreach( $items as $itemSet )
			{
				$item = $itemSet[ 'pc' ];

				if( !$item )
					{ $items = $itemSet[ 'mobile' ]; }

				if( !$item )
					{ $item = $itemSet[ 'smartphone' ]; }

				$programID = mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' );
				$subData   = self::GetSubData( $programID , $itemSet );
				$creative  = $subData[ 'creative' ];
				$reward    = $subData[ 'reward' ];
				$result    = Array();

				$result[ 'id' ]   = mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' );
				$result[ 'name' ] = mb_convert_encoding( ( string )$item->ProgramName , 'SJIS' , 'UTF8' );
				$result[ 'term' ] = mb_convert_encoding( ( string )$item->RewardCondition , 'SJIS' , 'UTF8' );

				//有効期限
				$endDate = mb_convert_encoding( ( string )$item->EndDate , 'SJIS' , 'UTF8' );

				if( $endDate ) //有効期限の設定がある場合
				{
					List( $y , $m , $d ) = explode( '/' , $endDate );

					$result[ 'use_limit' ] = true;
					$result[ 'limit_y' ]   = $y;
					$result[ 'limit_m' ]   = $m;
					$result[ 'limit_d' ]   = $d;
					$result[ 'limit_t' ]   = mktime( 0 , 0 , 0 , $m , $d + 1 , $y );
				}
				else //有効期限の設定がない場合
					{ $result[ 'use_limit' ] = false; }

				//URLとバナー・テキスト
				foreach( $creative->Banner as $element )
				{
					$bannerTypeID = mb_convert_encoding( ( string )$element->BannerTypeId , 'SJIS' , 'UTF8' );

					if( 1 == $bannerTypeID ) //バナーの場合
					{
						if( preg_match( '/img src="([^"]*)"/' , mb_convert_encoding( ( string )$element->Tag , 'SJIS' , 'UTF8' ) , $match ) )
							{ $result[ 'banner' ] = $match[ 1 ]; }

						$result[ 'url' ] = mb_convert_encoding( ( string )$element->LinkCode , 'SJIS' , 'UTF8' );
					}
					else if( 2 == $bannerTypeID ) //テキストの場合
					{
						if( preg_match( '/>(.*)<img/' , mb_convert_encoding( ( string )$element->Tag , 'SJIS' , 'UTF8' ) , $match ) )
						{
							$result[ 'text' ] = $match[ 1 ];
							$result[ 'text' ] = str_replace( '<br>' , '' , $result[ 'text' ] );
						}

						if( !isset( $result[ 'url' ] ) )
							{ $result[ 'url' ] = mb_convert_encoding( ( string )$element->LinkCode , 'SJIS' , 'UTF8' ); }
					}
				}

				//キャリア
				if( $itemSet[ 'mobile' ] )
				{
					foreach( array( 'docomo' , 'au' , 'softbank' ) as $name ) //全てのキャリアを処理
					{
						if( FALSE !== strpos( $itemSet[ 'mobile' ]->Carrier , $name ) ) //キャリアに対応する場合
						{
							$result[ 'use_carrier' ]      = true;
							$result[ 'carrier' ][ $name ] = $result[ 'url' ];
						}
					}
				}

				if( $itemSet[ $smartphone ] ) //スマートフォン対応の場合
				{
					$result[ 'use_carrier' ]          = true;
					$result[ 'carrier' ][ 'iphone' ]  = $result[ 'url' ];
					$result[ 'carrier' ][ 'android' ] = $result[ 'url' ];
				}

				if( !isset( $result[ 'use_carrier' ] ) ) //キャリアに対応しない場合
					{ $result[ 'use_carrier' ] = false; }

				//報酬条件と金額
				$result[ 'term' ]        = mb_convert_encoding( ( string )$reward->Reward->ResultName , 'SJIS' , 'UTF8' );
				$result[ 'reward_type' ] = mb_convert_encoding( ( string )$reward->Reward->RewardType , 'SJIS' , 'UTF8' );
				$result[ 'reward' ]      = mb_convert_encoding( ( string )$reward->Reward->PartnerReward , 'SJIS' , 'UTF8' );
				$result[ 'reward' ]      = str_replace( '円' , '' , $result[ 'reward' ] );

				switch( $result[ 'reward_type' ] ) //報酬タイプで分岐
				{
					case '0' : //クリック報酬
					case '1' : //定額報酬
					{
						$result[ 'reward_type' ] = 'f';
						break;
					}

					default : //その他(定率報酬)
					{
						$result[ 'reward_type' ] = 'p';
						break;
					}
				}

				$results[] = $result;
			}

			return $results;
		}

		private function GetSubData( $iProgramID_ , $iItemSet_ )
		{
			$apiKey = SystemUtil::getSystemData( 'access_trade_access_id' );

			if( !$apiKey ) //APIキーが設定されていない場合
				{ throw new RuntimeException( 'getResultsBySubmit を完了できません' ); }

			$api = new AccessTradeAPIModel( $apiKey );

			if( $iItemSet_[ 'pc' ] )
				{ return $api->getSubData( $iProgramID_ , 'pc' ); }

			if( $iItemSet_[ 'mobile' ] )
				{ return $api->getSubData( $iProgramID_ , 'mobile' ); }

			if( $iItemSet_[ 'smartphone' ] )
				{ return $api->getSubData( $iProgramID_ , 'smartphone' ); }
		}
	}
