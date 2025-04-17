<?php

	class mod_pointApi
	{
		function calcPointToYen( $iArgs_ )
		{
			$point = $iArgs_[ 'point' ];

			$sysDB  = GMList::getDB( 'system' );
			$sysRec = $sysDB->selectRecord( 'ADMIN' );
			$rate   = $sysDB->getData( $sysRec , 'point_to_yen_rate' );

			print '{ "result" : true , "res" : "�|�C���g�̏ꍇ...' . ( $point * $rate ) . '�~����" }';
		}

		function changePointTypeByASP( $iArgs_ )
		{
			$aspID      = $iArgs_[ 'asp_type' ];
			$aspGM      = GMList::getGM( 'asp_type' );
			$aspDB      = $aspGM->getDB();
			$aspRec     = $aspDB->selectRecord( $aspID );
			$useParsent = $aspDB->getData( $aspRec , 'use_parsent' );

			print '{ "result" : true , "res" : ' . ( $useParsent ? 'true' : 'false' ) . ' }';
		}
	}
