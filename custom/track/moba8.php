<?php

	class TrackMoba8 extends TrackBase
	{
		function action()
		{
			if( !isset( $_GET[ 'decide_flg' ] ) || 'D' != $_GET[ 'decide_flg' ] ) //ƒLƒƒƒ“ƒZƒ‹‚Å‚Í‚È‚¢ê‡
			{
				List( $id , $adwares ) = explode( '_' , $_GET[ 'point_id1' ] , 2 );

				$userRec    = $this->getUserRecord( $id );
				$adwaresRec = $this->getAdwaresRecord( $adwares );
				$enablePay  = $this->enablePay( $id , $adwares );

				if( !$userRec || !$adwaresRec || !$enablePay )
					{ return; }

				$this->addPayLog( $id , $adwares );
				$this->addKickback( $userRec , $adwaresRec , $_GET[ 'pay_money' ] );

				print 'success';
			}
		}
	}
