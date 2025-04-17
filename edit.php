<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * edit.php - �ėp�v���O����
	 * �o�^�f�[�^�ҏW�����B
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once "custom/head_main.php";

		print System::getHead($gm,$loginUserType,$loginUserRank);
		System::$checkData	 = new CheckData( $gm, true, $loginUserType, $loginUserRank );

		$sys	 = SystemUtil::getSystem( $_GET["type"] );

		if(   $THIS_TABLE_IS_NOHTML[ $_GET['type'] ] || !isset(  $gm[ $_GET['type'] ]  )   )
		{
			$sys->drawEditFaled( $gm, $loginUserType, $loginUserRank );
		}
		else
		{
			$db		 = $gm[ $_GET['type'] ]->getDB();

			if( !isset($_GET['id']) && $_GET['type'] == $loginUserType ){
				$_GET['id'] = $LOGIN_ID;
			}

			$rec	 = $db->selectRecord($_GET['id']);

			if( !isset($rec) )
			{// �ҏW�Ώۂ̃f�[�^�����݂��Ȃ��B
				$sys->drawEditFaled( $gm, $loginUserType, $loginUserRank );
			}
			else
			{
				// ���ҏW�t�H�[����`��
				if( ( !isset( $_POST['post'] ) && !isset( $_POST['delete'] ) && !isset( $_GET['delete'] ) )  || isset($_POST['back']) )
				{
					if(isset($_POST['back']) && $_POST['post'] != 'delete' )
					{
						$rec	 = $db->getNewRecord( $_POST );
					}
					$gm[ $_GET['type'] ]->setForm( $rec );
					$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
					
					$sys->drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
				}
				else
				{
					// �o�^�폜�m�F����
					if(  isset( $_POST['delete'] ) || isset( $_GET['delete'] )  )
					{// �폜�m�F�y�[�W���o�͂��܂��B
						
						$check = $sys->deleteCheck( $gm, $rec, $loginUserType, $loginUserRank );
						if( $check ){
							$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'delete' );
							$sys->drawDeleteCheck( $gm, $rec, $loginUserType, $loginUserRank );
						}else{
							$gm[ $_GET['type'] ]->setForm( $rec );
							$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
							$sys->drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
						}
					}else
					{//�폜�m�F�t���O����
						switch($_POST['post'])
						{
							case 'check': // �ύX���e�m�F��ʂ�`��
								//POST�f�[�^�����݂��Ȃ����ANULL�����̃J����
								for( $i=0; $i<count( $db->colName ); $i++ )
								{
									if(  !isset( $_POST[ $db->colName[$i] ] ) && !isset( $_POST[ $db->colName[$i]."_CHECKBOX" ] ) 
									&& $gm[ $_GET['type'] ]->colType[ $db->colName[$i] ] != 'image' || 
										( $_POST[ $db->colName[$i] ] == null ) && 
											(
												( $THIS_TABLE_IS_USERDATA[ $_GET['type'] ] && 
													(
														$db->colName[$i] == $LOGIN_PASSWD_COLUM[ $_GET['type'] ] ||
														$db->colName[$i] == $LOGIN_PASSWD_COLUM2[ $_GET['type'] ] 
													)
												)
											)
									)
									{
										$_POST[ $db->colName[$i] ]	 = $db->getData( $rec, $db->colName[$i] );
									}
								}

								// ���͓��e�m�F
								$check	 = $sys->registCheck( $gm, true, $loginUserType, $loginUserRank );

								if($check)
								{// ���͓��e�ɖ�肪�����ꍇ

									// �V����POST���e�𗘗p���ă��R�[�h���쐬����B
									$rec	 = $db->setRecord( $rec, $_POST );

									$sys->editProc( $gm, $rec, $loginUserType, $loginUserRank ,true);

									$gm[ $_GET['type'] ]->setHiddenFormRecordEdit( $rec );

									// �o�^���e�m�F�y�[�W���o�́B
									$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'update' );
									$sys->drawEditCheck( $gm, $rec, $loginUserType, $loginUserRank );
								}
								else
								{// ���͓��e�ɕs��������ꍇ
									$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );

									$rec	 = $db->getNewRecord( $_POST );

									$gm[ $_GET['type'] ]->setForm( $rec );

									$sys->drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
								}
								break;

							case 'update': // �o�^���s����
								//POST�f�[�^�����݂��Ȃ����ANULL�����̃J����
								for( $i=0; $i<count( $db->colName ); $i++ )
								{
									if(  !isset( $_POST[ $db->colName[$i] ] ) || 
										( $_POST[ $db->colName[$i] ] == null ) && 
											( 
												$gm[ $_GET['type'] ]->colType[ $db->colName[$i] ] == 'image' || 
												( $THIS_TABLE_IS_USERDATA[ $_GET['type'] ] && 
													(
														$db->colName[$i] == $LOGIN_PASSWD_COLUM[ $_GET['type'] ] ||
														$db->colName[$i] == $LOGIN_PASSWD_COLUM2[ $_GET['type'] ] 
													)
												)
											)
									)
									{
										$_POST[ $db->colName[$i] ]	 = $db->getData( $rec, $db->colName[$i] );
									}
								}

								// �V����POST���e�𗘗p���ă��R�[�h���쐬����B
								$rec	 = $db->setRecord( $rec, $_POST );

								$check	 = $sys->registCompCheck( $gm, $rec ,$loginUserType, $loginUserRank, true);

								if( $check )
								{
									$sys->editProc( $gm, $rec, $loginUserType, $loginUserRank );

									// ���R�[�h��ǉ����܂��B
									$db->updateRecord( $rec );

									$sys->editComp( $gm, $rec, $loginUserType, $loginUserRank );

									// �o�^�����y�[�W���o�͂��܂��B
									$sys->drawEditComp( $gm, $rec, $loginUserType, $loginUserRank );
								}
								else
								{
									$sys->drawEditFaled( $gm, $loginUserType, $loginUserRank );
								}
								break;
							case 'delete': // �o�^�폜���s����
							   $sys->deleteProc( $gm, $rec, $loginUserType, $loginUserRank );

							   // �폜�����y�[�W���o�͂��܂��B
							   $sys->drawDeleteComp( $gm, $rec, $loginUserType, $loginUserRank );

							   $sys->deleteComp( $gm, $rec, $loginUserType, $loginUserRank );
							   break;
						}
					}
				}
			}
		}

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