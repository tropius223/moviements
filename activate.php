<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * activate.php - ��p�v���O����
	 * ���[�U�̃A�N�e�B�x�[�V�����������s���v���O�����B
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		// �w�b�_�[��ǂݍ��݂܂��B
		include_once "custom/head_main.php";

		print System::getHead($gm,$loginUserType,$loginUserRank);

		// �f�[�^�x�[�X�̎擾
		$db		 = $gm[ $_GET['type'] ]->getDB();
		$table	 = $db->getTable();
		$table	 = $db->searchTable($table, 'id', '=', $_GET['id'] );

		$check = false;
		$sys	 = SystemUtil::getSystem( $_GET["type"] );

		if( $db->getRow($table) != 0 )
		{
			$rec	 = $db->getRecord( $table, 0 );

			// �擾�������R�[�h��id,���[���A�h���X���琶�������n�b�V����
			// �A�N�Z�X���ꂽ���[���ɋL�ڂ��ꂽ�n�b�V���l�ƈ�v���邩���m�F
			if(   md5(  $db->getData( $rec, 'id' ). $db->getData( $rec, 'mail' )  ) == $_GET['md5']   )
			{
				// �o�^����
				$check = $sys->activateAction( $gm , $rec , $loginUserType, $loginUserRank );
			}
		}
		if($check)
			$sys->drawActivateComp( $gm , $rec , $loginUserType, $loginUserRank );
		else
			$sys->drawActivateFaled( $gm , $rec , $loginUserType, $loginUserRank );

		print System::getFoot($gm,$loginUserType,$loginUserRank);
	}
	catch( Exception $e_ )
	{
		ob_end_clean();

		//�G���[���b�Z�[�W�����O�ɏo��
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );

		//��O�ɉ����ăG���[�y�[�W���o��
		$className = get_class( $e_ );
		ExceptionUtil::DrawErrorPage( $className );
	}

	ob_end_flush();
?>