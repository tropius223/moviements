<?php

	/**
		@file
		@brief �����\���Ɋւ���f�[�^��CSV���o�͂���B
	*/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		switch( $loginUserType ) //���[�U�[��ʂŕ���
		{
			case 'admin' : //�Ǘ���
				{ break; }

			default : //���̑�
				{ throw new Exception( $loginUserType ); }
		}

		$nDB    = $gm[ 'nUser' ]->getDB();
		$pDB    = $gm[ 'payment' ]->getDB();
		$pTable = $pDB->getTable();

		switch( $_POST[ 'status' ] ) //�X�e�[�^�X�w��ŕ���
		{
			case 'accept' : //��������
			{
				$pTable = $pDB->searchTable( $pTable , 'activate' , '=' , $ACTIVE_ACCEPT );

				break;
			}

			case 'activate' : //������
			{
				$pTable = $pDB->searchTable( $pTable , 'activate' , '=' , $ACTIVE_ACTIVATE );

				break;
			}

			case 'none' : //�����F
			{
				$pTable = $pDB->searchTable( $pTable , 'activate' , '=' , $ACTIVE_NONE );

				break;
			}

			default : //���̑�
				{ break; }
		}

		$beginTime = mktime( 0 , 0 , 0 , $_POST[ 'month' ] , 1 , $_POST[ 'year' ] );
		$endTime   = mktime( 0 , 0 , 0 , $_POST[ 'month' ] + 1 , 1 , $_POST[ 'year' ] );
		$pTable    = $pDB->searchTable( $pTable , 'regist' , 'b' , $beginTime , $endTime );
		$pRow      = $pDB->getRow( $pTable );
		$result    = Array();

		for( $i = 0 ; $i < $pRow ; ++$i ) //�S�Ẵ��R�[�h������
		{
			$pRec   = $pDB->getRecord( $pTable , $i );
			$userID = $pDB->getData( $pRec , 'owner' );

			$nRec = $nDB->selectRecord( $userID );

			$result[] = CreateCSVLineString( $nRec , $pRec );
		}

		$fileName = 'payment.csv';

		header( 'Cache-Control: public' );
		header( 'Pragma:' );
		header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
		header( 'Content-type: application/x-octet-stream; charset=Shift_JIS' );

		print join( ',' , Array( '���[�U�[ID' , '�����\��ID' , '���[�U�[��' , '���z' , '��s��' , '�x�X��' , '�������' , '�����ԍ�' , '�������`' ) ) . "\n";
		print join( "\n" , $result ) . "\n";
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

	function CreateCSVLineString( $iNRec_ , $iPRec_ )
	{
		$nDB    = SystemUtil::getGMforType( 'nUser' )->getDB();
		$pDB    = SystemUtil::getGMforType( 'payment' )->getDB();
		$result = Array();

		$result[] = $nDB->getData( $iNRec_ , 'id' );
		$result[] = $pDB->getData( $iPRec_ , 'id' );
		$result[] = $nDB->getData( $iNRec_ , 'name' );
		$result[] = $pDB->getData( $iPRec_ , 'value' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_name' );
		$result[] = $nDB->getData( $iNRec_ , 'branch_name' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_type' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_number' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_user' );

		foreach( $result as &$value )
		{
			if( preg_match( '/,/' , $value , $match ) )
				{ $value = '"' . $value . '"'; }
		}

		return implode( ',' , $result );
	}
