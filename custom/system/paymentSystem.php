<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class paymentSystem extends System
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
		function registProc( &$gm, &$rec, $loginUserType, $loginUserRank , $check = false )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $LOGIN_ID;
			global $ACTIVE_NONE;
			// **************************************************************************************

			$rate = SystemUtil::getTableData( 'system' , 'ADMIN' , 'point_to_yen_rate' );

			$db  = $gm[ $_GET[ 'type' ] ]->getDB();
			$yen = $db->getData( $rec , 'value_yen' );

			$db->setData( $rec , 'owner'    , $LOGIN_ID );
			$db->setData( $rec , 'activate' , $ACTIVE_NONE );
			$db->setData( $rec , 'value'    , $yen / $rate );

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank , $check );
		}

		/**
		 * �o�^�������������B
		 * �o�^�������Ƀ��[���œ��e��ʒm�������ꍇ�Ȃǂɗp���܂��B
		 * 
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec ���R�[�h�f�[�^�B
		 */
		function registComp( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************

			$db       = $gm[ $_GET[ 'type' ] ]->getDB();
			$owner_id = $db->getData( $rec, 'owner' );
			$value    = $db->getData( $rec, 'value' );

			$db = $gm[ 'nUser' ]->getDB();
			$_rec = $db->selectRecord( $owner_id );
			$db->setData( $_rec, 'point', $db->getData( $_rec, 'point') - $value );
			$db->updateRecord( $_rec );

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank, $check );
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �����֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

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
			// **************************************************************************************

			if( 'admin' == $loginUserType && isset( $_POST ) && isset($_POST['id']) )
			{
				$db    = $gm[ $_GET[ 'type' ] ]->getDB();
				$rec = $db->selectRecord( $_POST['id'] );
				if($rec){
					$db->setData( $rec, 'activate' , $_POST[ 'activate'  ]  );
					$db->updateRecord( $rec );
				}
				unset($_POST['id']);
				unset($_POST['activate']);
			}

			if( 'nUser' == $loginUserType )
			{
				$db    = GMList::getDB( $_GET[ 'type' ] );
				$table = $db->searchTable( $table , 'owner' , '=' , $LOGIN_ID );
			}

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );
		}

		/**********************************************************************************************************
		 * �ėp�V�X�e���`��n�p���\�b�h
		 **********************************************************************************************************/

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �o�^�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �o�^�t�H�[����`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawRegistForm( &$gm, $rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $LOGIN_ID;
			// **************************************************************************************

			$point = SystemUtil::getTableData('nUser',$LOGIN_ID,'point');
			
			$rate = SystemUtil::getSystemData('point_to_yen_rate');
			$min  = SystemUtil::getSystemData('minimum_payment');

			if( $min > $point * $rate )
				Template::drawTemplate( $gm[ $_GET['type'] ] , null ,'' , $loginUserRank , '' , 'MINIMUM_PAYMENT_DESIGN' );
			else
				parent::drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}
	}

?>