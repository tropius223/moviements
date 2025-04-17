<?php

	/**
		@file
		@brief �A�N�Z�X�������[�U�[�ɑ΂��ăN���b�N�|�C���g�̉��Z���s���B
	*/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		$sysDB    = GMList::getDB( 'system' );
		$sysRec   = $sysDB->selectRecord( 'ADMIN' );
		$useClick = $sysDB->getData( $sysRec , 'use_click_point' );

		if( !$useClick ) //�N���b�N�|�C���g�̎g�p��������Ă��Ȃ��ꍇ
			{ throw new IllegalAccessException( '�s���ȃA�N�Z�X�ł�' ); }

		print System::getHead( $gm , $loginUserType , $loginUserRank );

		switch( $loginUserType ) //���[�U�[��ʂŕ���
		{
			case 'nUser' : //���O�C�����[�U�[
			{
				$addPoint = addClickPoint( $rec );

				if( $addPoint ) //�|�C���g�����Z�ł����ꍇ
					{ Template::drawTemplate( $gm[ 'system' ] , $rec , $loginUserType , $loginUserRank , '' , 'CLICK_POINT_RESULT_DESIGN' ); }
				else //�|�C���g�����Z�ł��Ȃ��ꍇ
					{ Template::drawTemplate( $gm[ 'system' ] , $rec , $loginUserType , $loginUserRank , '' , 'CLICK_POINT_FAILED_DESIGN' ); }

				break;
			}

			default : //���̑�
			{
				Template::drawTemplate( $gm[ 'system' ] , $rec , $loginUserType , $loginUserRank , '' , 'TOP_PAGE_DESIGN' );

				break;
			}
		}

		print System::getFoot( $gm , $loginUserType , $loginUserRank );
	}
	catch( Exception $e )
	{
		ob_end_clean();

		//�G���[���b�Z�[�W�����O�ɏo��
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e );

		$errorManager->OutputErrorLog( $errorMessage );

		//��O�ɉ����ăG���[�y�[�W���o��
		$className = get_class( $e );
		ExceptionUtil::DrawErrorPage( $className );
	}

	ob_end_flush();
