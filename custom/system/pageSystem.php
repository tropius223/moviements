<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class pageSystem extends System
	{
		/**********************************************************************************************************
		 * �ėp�V�X�e���p���\�b�h
		 **********************************************************************************************************/



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �o�^�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**
		 * �o�^���e�m�F�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param edit �ҏW�Ȃ̂��A�V�K�ǉ��Ȃ̂���^�U�l�œn���B
		 * @return �G���[�����邩��^�U�l�œn���B
		 */
		function registCheck( &$gm, $edit, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			$result	 = parent::registCheck( $gm, $edit, $loginUserType, $loginUserRank );
			
			if($result){
				$db = $gm['page']->getDB();
				$table = $db->getTable();
				$table = $db->searchTable( $table, 'name', '=', $_POST['name'] );
				foreach( $_POST['authority'] as $auth ){
					$table_buf[] = $db->searchTable( $db->getTable(), 'authority', 'in', '%'.$auth.'%' );
				}
				$table2 = $db->getTable();
				foreach($table_buf as $table_auth){
					$table2 = $db->orTable($table2,$table_auth);
				}
				
				$table = $db->andTable($table,$table2);
				
				if($edit){
					$table = $db->searchTable( $table, 'id', '!', $_POST['id'] );
				}
				
				$row = $db->getRow($table);
				if($row){
					System::$checkData->addError('name_dup');
					$result = false;
				}
			}
			return $result;
		}

		/**
		 * �o�^�������������B
		 * �o�^�������Ƀ��[���œ��e��ʒm�������ꍇ�Ȃǂɗp���܂��B
		 * 
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec ���R�[�h�f�[�^�B
		 */
		function registComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $page_path;
			// **************************************************************************************

			$db = $gm['page']->getDB();
			
			$new_id = $db->getData( $rec , 'id' );
			fileWrite( $page_path.$new_id.".dat" , $_POST['html'] );
			
			parent::registComp( $gm, $rec, $loginUserType, $loginUserRank );
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ҏW�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * �ҏW���������B
		 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function editComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $page_path;
			// **************************************************************************************
			
			fileWrite( $page_path.$_GET['id'].".dat" , $_POST['html'] );
			
			parent::editComp( $gm, $rec, $loginUserType, $loginUserRank );			
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ҏW�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * �ҏW�t�H�[����`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawEditForm( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $EDIT_FORM_PAGE_DESIGN;
			global $LOGIN_ID;
            global $NOT_LOGIN_USER_TYPE;
            
            global $page_path;
			// **************************************************************************************
			
            $this->setErrorMessage($gm[ $_GET['type'] ]);

			$db		 = $gm[ 'page' ]->getDB();
			$_GET['html'] = fileRead( $page_path.$_GET['id'].".dat");
			
			parent::drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
		}

	}

?>