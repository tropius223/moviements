<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * search.php - �ėp�v���O����
	 * ���������B
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once "custom/head_main.php";

		print System::getHead($gm,$loginUserType,$loginUserRank);

		$sys	 = SystemUtil::getSystem( $_GET["type"] );

		if($_GET["type"] == "undefined" || $_GET["type"] == ""){
			$sys->drawSearchError( $gm, $loginUserType, $loginUserRank );
		}else{

			if( $_POST['run'] == 'true' )
			{
				foreach ($_POST as $key => $tmp) 
				{
					if( is_array($tmp) && $tmp[0] != '' ) { $_GET[$key] = $tmp; }
					else if( $tmp != '' ) { $_GET[$key] = $tmp; }
				}
			}

			// �f�[�^�x�[�X���J��
			$sr		 = new Search(  $gm[ $_GET['type'] ], $_GET['type']  );

			$db		 = $gm[ $_GET['type'] ]->getDB();

			$sys	 = SystemUtil::getSystem( $_GET["type"] );

			if(   $THIS_TABLE_IS_NOHTML[ $_GET['type'] ] || !isset(  $gm[ $_GET['type'] ]  )   )
			{
				$sys->drawSearchError( $gm, $loginUserType, $loginUserRank );
			}
			else
			{
				if(  !isset( $_GET['run'] )  )
				{

					for($i=0; $i<count($gm[ $_GET['type'] ]->colName); $i++)
					{
						if( isset( $_GET[  $gm[ $_GET['type'] ]->colName[$i]  ] ) )
							{ $_POST[  $gm[ $_GET['type'] ]->colName[$i]  ] = $_GET[  $gm[ $_GET['type'] ]->colName[$i]  ]; }
					}

					// ���������`��
					$sys->drawSearchForm( $sr, $loginUserType, $loginUserRank );

				}
				else
				{
					if( $magic_quotes_gpc )
						$sr->setParamertorSet($_GET);
					else
						$sr->setParamertorSet(addslashes_deep($_GET));

					$sys->searchResultProc( $gm, $sr, $loginUserType, $loginUserRank );

					$table	 = $sr->getResult();

					$sys->searchProc( $gm, $table, $loginUserType, $loginUserRank );
					if( strlen($_GET['searchNext']) && strlen($_GET['nextUrl']) )
					{
						SystemUtil::innerLocation( $_GET['nextUrl']."&".$_SERVER['QUERY_STRING'] );
					}
					else if(  $db->getRow( $table ) == 0  )
					{
						$sys->drawSearchNotFound( $gm, $loginUserType, $loginUserRank );
					}
					else
					{
						$sys->drawSearch( $gm, $sr, $table, $loginUserType, $loginUserRank );
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