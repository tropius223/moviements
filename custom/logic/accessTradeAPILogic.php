<?php

	class AccessTradeAPILogic
	{
		function GetResultsBySubmit( $iRec_ )
		{
			$apiKey = SystemUtil::getSystemData( 'access_trade_access_id' );

			if( !$apiKey ) //API�L�[���ݒ肳��Ă��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResultsBySubmit �������ł��܂���' ); }

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

			foreach( $pcXML->Program as $item ) //�S�ẴA�C�e��������
				{ $items[ mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' ) ][ 'pc' ] = $item; }

			foreach( $mobileXML->Program as $item ) //�S�ẴA�C�e��������
				{ $items[ mb_convert_encoding( ( string )$item->ProgramId , 'SJIS' , 'UTF8' ) ][ 'mobile' ] = $item; }

			foreach( $smartphoneXML->Program as $item ) //�S�ẴA�C�e��������
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

				//�L������
				$endDate = mb_convert_encoding( ( string )$item->EndDate , 'SJIS' , 'UTF8' );

				if( $endDate ) //�L�������̐ݒ肪����ꍇ
				{
					List( $y , $m , $d ) = explode( '/' , $endDate );

					$result[ 'use_limit' ] = true;
					$result[ 'limit_y' ]   = $y;
					$result[ 'limit_m' ]   = $m;
					$result[ 'limit_d' ]   = $d;
					$result[ 'limit_t' ]   = mktime( 0 , 0 , 0 , $m , $d + 1 , $y );
				}
				else //�L�������̐ݒ肪�Ȃ��ꍇ
					{ $result[ 'use_limit' ] = false; }

				//URL�ƃo�i�[�E�e�L�X�g
				foreach( $creative->Banner as $element )
				{
					$bannerTypeID = mb_convert_encoding( ( string )$element->BannerTypeId , 'SJIS' , 'UTF8' );

					if( 1 == $bannerTypeID ) //�o�i�[�̏ꍇ
					{
						if( preg_match( '/img src="([^"]*)"/' , mb_convert_encoding( ( string )$element->Tag , 'SJIS' , 'UTF8' ) , $match ) )
							{ $result[ 'banner' ] = $match[ 1 ]; }

						$result[ 'url' ] = mb_convert_encoding( ( string )$element->LinkCode , 'SJIS' , 'UTF8' );
					}
					else if( 2 == $bannerTypeID ) //�e�L�X�g�̏ꍇ
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

				//�L�����A
				if( $itemSet[ 'mobile' ] )
				{
					foreach( array( 'docomo' , 'au' , 'softbank' ) as $name ) //�S�ẴL�����A������
					{
						if( FALSE !== strpos( $itemSet[ 'mobile' ]->Carrier , $name ) ) //�L�����A�ɑΉ�����ꍇ
						{
							$result[ 'use_carrier' ]      = true;
							$result[ 'carrier' ][ $name ] = $result[ 'url' ];
						}
					}
				}

				if( $itemSet[ $smartphone ] ) //�X�}�[�g�t�H���Ή��̏ꍇ
				{
					$result[ 'use_carrier' ]          = true;
					$result[ 'carrier' ][ 'iphone' ]  = $result[ 'url' ];
					$result[ 'carrier' ][ 'android' ] = $result[ 'url' ];
				}

				if( !isset( $result[ 'use_carrier' ] ) ) //�L�����A�ɑΉ����Ȃ��ꍇ
					{ $result[ 'use_carrier' ] = false; }

				//��V�����Ƌ��z
				$result[ 'term' ]        = mb_convert_encoding( ( string )$reward->Reward->ResultName , 'SJIS' , 'UTF8' );
				$result[ 'reward_type' ] = mb_convert_encoding( ( string )$reward->Reward->RewardType , 'SJIS' , 'UTF8' );
				$result[ 'reward' ]      = mb_convert_encoding( ( string )$reward->Reward->PartnerReward , 'SJIS' , 'UTF8' );
				$result[ 'reward' ]      = str_replace( '�~' , '' , $result[ 'reward' ] );

				switch( $result[ 'reward_type' ] ) //��V�^�C�v�ŕ���
				{
					case '0' : //�N���b�N��V
					case '1' : //��z��V
					{
						$result[ 'reward_type' ] = 'f';
						break;
					}

					default : //���̑�(�藦��V)
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

			if( !$apiKey ) //API�L�[���ݒ肳��Ă��Ȃ��ꍇ
				{ throw new RuntimeException( 'getResultsBySubmit �������ł��܂���' ); }

			$api = new AccessTradeAPIModel( $apiKey );

			if( $iItemSet_[ 'pc' ] )
				{ return $api->getSubData( $iProgramID_ , 'pc' ); }

			if( $iItemSet_[ 'mobile' ] )
				{ return $api->getSubData( $iProgramID_ , 'mobile' ); }

			if( $iItemSet_[ 'smartphone' ] )
				{ return $api->getSubData( $iProgramID_ , 'smartphone' ); }
		}
	}
