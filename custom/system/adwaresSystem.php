<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class adwaresSystem extends System
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

			$limitTime = mktime( 0 , 0 , 0 , $_POST[ 'limit_month' ] , $_POST[ 'limit_day' ] + 1 , $_POST[ 'limit_year' ] );

			$db = $gm[ $_GET[ 'type' ] ]->getDB();

			$db->setData( $rec , 'limit_time' , $limitTime );

			AdwaresLogic::setSelectCarrierName( $rec );

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );
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
			
			$limitTime = mktime( 0 , 0 , 0 , $_POST[ 'limit_month' ] , $_POST[ 'limit_day' ] + 1 , $_POST[ 'limit_year' ] );

			$db = $gm[ $_GET[ 'type' ] ]->getDB();

			$db->setData( $rec , 'limit_time' , $limitTime );

			AdwaresLogic::setSelectCarrierName( $rec );

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
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $LOGIN_ID;
			global $terminal_type;
			// **************************************************************************************

			if( $_GET[ '_carrier' ] )
			{
				switch( $_GET[ '_carrier' ] ) //�[���̎�ނŕ���
				{
					case 'docomo'   :
					case 'au'       :
					case 'softbank' :
					case 'iphone'   :
					case 'android'  :
					{
						$db     = $gm[ 'adwares' ]->getDB();
						$tableA = $db->searchTable( $table , 'use_carrier_url' , '=' , false );
						$tableA = $db->searchTable( $tableA , 'url' , '!=' , '' );
						$tableB = $db->searchTable( $table , 'use_carrier_url' , '=' , true );
						$tableB = $db->searchTable( $tableB , 'url_' . $_GET[ '_carrier' ] , '!=' , '' );
						$table  = $db->orTable( $tableA , $tableB );

						break;
					}

					case 'pc' :
					{
						$db    = $gm[ 'adwares' ]->getDB();
						$table = $db->searchTable( $table , 'use_carrier_url' , '=' , false );
					}
				}
			}

			$table = WS::Finder( 'adwares' )->searchQueryTable( $table );
			$table = WS::Finder( 'adwares' )->searchReadableTable( $table );

			if( !$_GET[ 'sort' ] )
				{ $table = WS::Finder( 'adwares' )->sortTable( $table ); }

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );			
		}
	}

?>