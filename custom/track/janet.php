<?php

	class TrackJANet extends TrackBase
	{
		function action()
		{
			if( !isset( $_GET[ 'attestation_flag' ] ) || '' === $_GET[ 'attestation_flag' ] ) //�t���O�����ݒ�܂��͋󕶎���̏ꍇ
			{
				print 1;
				exit;
			}

			if( 1 == $_GET[ 'attestation_flag' ] ) //�t���O�����F�؂̏ꍇ
			{
				print 1;
				exit;
			}

			List( $id , $adwares ) = explode( '_' , $_GET[ 'user_id' ] , 2 );

			$userRec    = $this->getUserRecord( $id );
			$adwaresRec = $this->getAdwaresRecord( $adwares );
			$enablePay  = $this->enablePay( $id , $adwares );

			if( !$userRec || !$adwaresRec || !$enablePay )
			{
				print 1;
				exit;
			}

			$this->addPayLog( $id , $adwares );
			$this->addKickback( $userRec , $adwaresRec , $_GGT[ 'commision' ] );

			print 1;
			exit;
		}
	}
