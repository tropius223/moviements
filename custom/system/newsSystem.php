<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class newsSystem extends System
	{
		/**********************************************************************************************************
		 * �ėp�V�X�e���p���\�b�h
		 **********************************************************************************************************/



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �o�^�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * �o�^�O�i�K�����B
		 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function registProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************

			$db = $gm[ $_GET[ 'type' ] ]->getDB();
			$db->setData( $rec , 'id' , SystemUtil::getNewId( $db , $_GET[ 'type' ] ) );
			$db->setData( $rec , 'regist' , time() );
			$db->setData( $rec , 'open_time' , mktime( 0 , 0 , 0 , $_POST[ 'open_month' ] , $_POST[ 'open_day' ] , $_POST[ 'open_year' ] ) );

//			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ҏW�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * �ҏW�O�i�K�����B
		 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function editProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			$db = $gm[ $_GET[ 'type' ] ]->getDB();
			$db->setData( $rec , 'open_time' , mktime( 0 , 0 , 0 , $_POST[ 'open_month' ] , $_POST[ 'open_day' ] , $_POST[ 'open_year' ] ) );
			parent::editProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );
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
			$table = WS::Finder( 'news' )->searchReadableTable( $table );
			$table = WS::Finder( 'news' )->sortTable( $table );

			return parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );
		}
	}

?>