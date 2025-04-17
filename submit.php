<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * submit.php - �ėp�v���O����
	 * �V�K�f�[�^���M�����B
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		if( !$gm[ $_GET[ 'type' ] ] ) //GUIManager�I�u�W�F�N�g��������Ȃ��ꍇ
			{ throw new IllegalAccessException( '�s���ȃA�N�Z�X�ł�[' . $_GET[ 'type' ] . ']' ); }

		if( !$THIS_TABLE_IS_SUBMIT[ $_GET[ 'type' ] ] ) //���M�\�ȃe�[�u���Ɏw�肳��Ă��Ȃ��ꍇ
			{ throw new IllegalAccessException( '�s���ȃA�N�Z�X�ł�[' . $_GET[ 'type' ] . ']' ); }

		print System::getHead( $gm , $loginUserType , $loginUserRank );

		System::$checkData = new CheckData( $gm , false , $loginUserType , $loginUserRank );
		$sys               = SystemUtil::getSystem( $_GET[ 'type' ] );
		$db                = $gm[ $_GET[ 'type' ] ]->getDB();

		if( isset( $_POST[ 'back' ] ) ) //�߂邪�����ꂽ�ꍇ
		{
			$_POST[ 'post' ] = '';

			if( 1 < $_POST[ 'step' ] ) //�X�e�b�v��1���傫���ꍇ
				{ --$_POST[ 'step' ]; }
		}

		// �o�^�����̓t�H�[����`��
		if( !$_POST[ 'post' ] ) //�t�H�[�������M����Ă��Ȃ��ꍇ
		{
			if( !$_POST[ 'step' ] ) //�X�e�b�v���Ȃ��ꍇ
				{ $_POST[ 'step' ] = 1; }

			if( $_GET[ 'copy' ] && $sys->copyCheck( $gm , $loginUserType , $loginUserRank ) ) //�R�s�[���w�肳��Ă���ꍇ
			{
				$rec = $db->selectRecord( $_GET[ 'copy' ] );

				$gm[ $_GET[ 'type' ] ]->setForm( $rec );
			}
			else //�R�s�[���w�肳��Ă��Ȃ��ꍇ
			{
				$rec = $db->getNewRecord( $_GET );

				$gm[ $_GET[ 'type' ] ]->setForm( $_GET );
			}

			$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'post' , 'check' );
			$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'step' , $_POST[ 'step' ] );

			//�t�H�[����S��hidden�Œǉ�
			foreach( $gm[ $_GET[ 'type' ] ]->colStep as $key => $step ) //�S�ẴX�e�b�v�ݒ������
			{
				if( $step && $_POST[ 'step' ] > $step ) //�ߋ��̃X�e�b�v�̏ꍇ
					{ $gm[ $_GET[ 'type' ] ]->addHiddenForm( $key , ( $_POST[ 'back' ] ? $_POST[ $key ] : $_GET[ $key ] ) ); }
			}

			$sys->drawSubmitForm( $gm , $rec , $loginUserType , $loginUserRank );
		}
		else //�t�H�[�������M����Ă���ꍇ
		{
			switch( $_POST[ 'post' ] ) //post�̎�ނŕ���
			{
				case 'check' : //�o�^���m�F���
				{
					$success = $sys->submitCheck( $gm , false , $loginUserType , $loginUserRank );

					if( $success ) //�f�[�^�������ȏꍇ
						{ ++$_POST[ 'step' ]; }

					if( $gm[ $_GET[ 'type' ] ]->maxStep >= 2 && $gm[ $_GET[ 'type' ] ]->maxStep + 1 > $_POST[ 'step' ] ) //�Ō�̃X�e�b�v�ł͂Ȃ��ꍇ
						{ $success = false; }

					$rec = $db->getNewRecord( $_POST );

					if( $success ) //���M���e�ɖ�肪�Ȃ��ꍇ
					{
						$sys->submitProc( $gm , $rec , $loginUserType , $loginUserRank , true );

						$gm[ $_GET[ 'type' ] ]->setHiddenFormRecord( $rec );

						// �o�^���e�m�F�y�[�W���o�́B
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'post' , 'submit' );
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'step' , $_POST[ 'step' ] );
						$sys->drawSubmitCheck( $gm , $rec , $loginUserType , $loginUserRank );
					}
					else //���M���e�ɖ�肪����ꍇ
					{
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'post' , 'check' );
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'step' , $_POST[ 'step' ] );

						$gm[ $_GET[ 'type' ] ]->setForm( $rec );

						///step�̈قȂ鍀�ڂ�S��hidden�Œǉ�
						foreach( $gm[ $_GET[ 'type' ] ]->colStep as $key => $step )
						{
							if( $step && $_POST[ 'step' ] > $step ) //�ߋ��̃X�e�b�v�̏ꍇ
								{ $gm[ $_GET[ 'type' ] ]->addHiddenForm( $key , $_POST[ $key ] ); }
						}

						$sys->drawSubmitForm( $gm , $rec , $loginUserType , $loginUserRank );
					}

					break;
				}

				case 'submit' : //���M����
				{
					// �V����POST���e�𗘗p���ă��R�[�h���쐬����B
					$rec     = $db->getNewRecord( $_POST );
					$success = $sys->submitCompCheck( $gm , $rec , $loginUserType , $loginUserRank );

					if( $success ) //���M���e�ɖ�肪�Ȃ��ꍇ
					{
						$sys->submitProc( $gm , $rec , $loginUserType , $loginUserRank );
						$sys->submitComp( $gm , $rec , $loginUserType , $loginUserRank );

						// �o�^�����y�[�W���o�͂��܂��B
						$sys->drawSubmitComp( $gm , $rec , $loginUserType , $loginUserRank );
					}
					else
						{ $sys->drawSubmitFaled( $gm , $loginUserType , $loginUserRank ); }

					break;
				}

				default : //���̑�
					{ throw new IllegalAccessException( '�s���ȃA�N�Z�X�ł�[' . $_GET[ 'type' ] . ']' ); }
			}
		}

		print System::getFoot( $gm , $loginUserType , $loginUserRank );
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
