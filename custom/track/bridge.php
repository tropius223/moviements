<?php

	class TrackBannerBridge extends TrackBase
	{
		function action()
		{
			List( $id , $adwares ) = explode( '_' , $_GET[ 'ps' ] , 2 );

			$userRec    = $this->getUserRecord( $id );
			$adwaresRec = $this->getAdwaresRecord( $adwares );
			$enablePay  = $this->enablePay( $id , $adwares );

			if( !$userRec || !$adwaresRec || !$enablePay )
				{ return; }

			$this->addPayLog( $id , $adwares );
			$this->addKickback( $userRec , $adwaresRec , $_GET[ 'price' ] );
		}
	}
