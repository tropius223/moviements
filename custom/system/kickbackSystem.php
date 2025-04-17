<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class kickbackSystem extends System
	{
		/**********************************************************************************************************
		 * �ėp�V�X�e���p���\�b�h
		 **********************************************************************************************************/

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �폜�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * �폜���������B
		 * �o�^�폜�������Ɏ��s����������������΃R�R�ɋL�q���܂��B
		 * �폜�������[���𑗐M�������ꍇ�Ȃǂɗ��p���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function deleteComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $KICKBACK_STATE_ON;
			// **************************************************************************************
			$db = $gm[ $_GET['type'] ]->getDB();
			$state = $db->getData( $rec, 'state');
			
			if( $state == $KICKBACK_STATE_ON ){
				$point		= $db->getData( $rec, 'point');
				$owner_id	= $db->getData( $rec, 'owner');
				
				$db = $gm[ 'nUser' ]->getDB();
				$_rec = $db->selectRecord( $owner_id );
				$now_point = $db->getData( $_rec, 'point' );

				if( $now_point < $point )
					{ $db->setData( $_rec, 'point', 0 ); }
				else
					{ $db->setData( $_rec, 'point', $now_point - $point ); }

				$db->updateRecord( $_rec );
			}
			
			parent::deleteComp( $gm, $rec, $loginUserType, $loginUserRank );			
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
			global $KICKBACK_STATE_OFF;
			global $KICKBACK_STATE_ON;
			// **************************************************************************************

			if( 'admin' != $loginUserType )
			{
				$db    = $gm[ $_GET[ 'type' ] ]->getDB();
				$table = $db->searchTable( $table , 'owner' , '=' , $LOGIN_ID );
			}else if( isset( $_POST ) && isset($_POST['id']) )
			{
				$db    = $gm[ $_GET[ 'type' ] ]->getDB();
				$rec = $db->selectRecord( $_POST['id'] );
				if($rec){
					$now_state = $db->getData( $rec, 'state');
					$next_state = $_POST[ 'state' ];
					
					if( $now_state != $next_state ){
						$owner_id	= $db->getData( $rec, 'owner');
						$point		= $db->getData( $rec, 'point');
						
						$db->setData( $rec, 'state' , $next_state );
						$db->updateRecord( $rec );
						
						$db = $gm[ 'nUser' ]->getDB();
						$_rec = $db->selectRecord( $owner_id );
						$now_point = $db->getData( $_rec, 'point' );
						switch($now_state){
							case $KICKBACK_STATE_ON:
								//on����off��  ���Z

								if( $now_point < $point )
									{ $db->setData( $_rec, 'point', 0 ); }
								else
									{ $db->setData( $_rec, 'point', $now_point - $point ); }

								break;
							case $KICKBACK_STATE_OFF:
								//off����on��  ���Z
								$db->setData( $_rec, 'point', $now_point + $point );
								break;
						}
						$db->updateRecord( $_rec );

						ChangeTier( $next_state , $_POST[ 'id' ] );
					}
				}
				unset($_POST['id']);
				unset($_POST['state']);
			}

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );			
		}
	}

?>