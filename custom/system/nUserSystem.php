<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class nUserSystem extends System
	{
		function registProc( &$iGM_ , &$iRec_ , $iUserType_ , $iUserRank_  , $iCheckPhase_ = false )
		{
			if( $_SESSION[ 'friend' ] ) //�e��񂪂���ꍇ
			{
				$db = GMList::getDB( 'nUser' );
				$db->setData( $iRec_ , 'parent_id' , $_SESSION[ 'friend' ] );
			}

			return parent::registProc( $iGM_ , $iRec_ , $iUsertype_ , $iUserRank_ , $iCheckPhase_ );
		}

		function registComp( &$iGM_ , &$iRec_ , $iUserType_ , $iUserRank_ )
		{
			$_SESSION[ 'friend' ] = null;

			$db      = GMList::getDB( 'nUser' );
			$nUserID = $db->getData( $iRec_ , 'id' );

			$clickLogDB  = GMList::getDB( 'click_log' );
			$clickLogRec = $clickLogDB->getNewRecord();

			$clickLogDB->setData( $clickLogRec , 'id' , SystemUtil::getNewId( $clickLogDB , 'click_log' ) );
			$clickLogDB->setData( $clickLogRec , 'nuser_id' , $nUserID );
			$clickLogDB->setData( $clickLogRec , 'regist' , time() );

			$clickLogDB->addRecord( $clickLogRec );

			return parent::registComp( $iGM_ , $iRec_ , $iUsertype_ , $iUserRank_ );
		}

		/**
		 * ���������B
		 * �t�H�[�����͈ȊO�̕��@�Ō���������ݒ肵�����ꍇ�ɗ��p���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param table �t�H�[���̂���̓��͓��e�Ɉ�v���郌�R�[�h���i�[�����e�[�u���f�[�^�B
		 */
		function searchProc( &$gm, &$table, $loginUserType, $loginUserRank )
		{
			$table = WS::Finder( 'nUser' )->searchQueryTable( $table );
			$table = WS::Finder( 'nUser' )->sortTable( $table );

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );
		}
	}

?>