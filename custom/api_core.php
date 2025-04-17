<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * api_core.php - api�A�N�Z�X�p
	 * JavaScript����f�[�^���擾������ύX�����肷��ۂ�
	 * ���ύX�̃t�H�[����info��index�ɖ��ߍ��ޏꍇ���Ɏg�p�B
	 *
	 * </PRE>
	 *******************************************************************************************************/
	
	class Api_core
	{
		/**********************************************************************************************************
		 *�@ajax_core
		 **********************************************************************************************************/
		
		
		/**
		 * �f�[�^�̍X�V�B
		 * 
		 * @param table �X�V���郌�R�[�h�̃e�[�u�����B
		 * @param id �X�V���郌�R�[�hID�B
		 * @param column �X�V����J�����B
		 * @param value �Z�b�g����l�B
		 */
		function update( $param )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			
			if( $loginUserType == 'admin' && $loginUserRank == $ACTIVE_ACTIVATE )
			{
				$db	 = $gm[ $param['table'] ]->getDB();
				
				$rec	 = $db->selectRecord( $param['id'] );
				if( isset($rec) )
				{
					$db->setData( $rec , $param['column'] , $param['value'] );
					$db->updateRecord( $rec );
				}
			}
			
			if( $param['info_change_flg'] ) { info_change_result( $param, ' { "success" : true , "msg" : "success update." } ' ); }
		}
		
		
		/**
		 * �f�[�^�̍폜�B
		 * 
		 * @param type �폜���郌�R�[�h�̃e�[�u�����B
		 * @param id �폜���郌�R�[�hID�B
		 */
		function delete( $param )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			
			if( $loginUserType == 'admin' && $loginUserRank == $ACTIVE_ACTIVATE )
			{
				$db		 = $gm[ $param['type'] ]->getDB();
				
    	        $rec	 = $db->selectRecord( $param['id'] );
				$draws	 = '{ "success" : false , "msg" : "no match id." }';
				if( isset($rec) )
				{
					$_GET['type'] =	$param['type'];	 // system.php�����ł�$_GET['type']�ŏ����𕪊򂵂Ă���̂ŔO�̂���
					
					$sys = SystemUtil::getSystem( $param['type'] );
					$sys->deleteProc( $gm, $rec, $loginUserType, $loginUserRank );
					$sys->deleteComp( $gm, $rec, $loginUserType, $loginUserRank );
					
					$draws = '{ "success" : true , "msg" : "success delete." }';
				}
			}
			
			if( $param['info_change_flg'] ) { info_change_result( $param, $draws ); }

		}
		
		
		/**
		 * �s�撬�����̎擾�B
		 * 
		 * @param id �s�撬�������擾����s���{����ID�B
		 */
		function load_addsub( $param )
		{
			global $gm;
			print $gm['parentCategory']->getCCResult( null, '<!--# ecode drawAddsubAjaxForm '.$param['id'].' #-->' );
		}

		function GetSecondCategoryForm( $param )
		{
			global $gm;

			print $gm['moba8SecondCategory']->getCCResult( null , '<!--# code tableSelectForm second_category moba8SecondCategory name id  �I�����Ă�������  parent_id = ' . $param[ 'first_category' ] . ' #-->' );
		}

		/**********************************************************************************************************
		 *�@info_change_sys
		 **********************************************************************************************************/
		
		/**
		 * �f�[�^�̍X�V�B
		 * 
		 * @param type �X�V���郌�R�[�h�̃e�[�u�����B
		 * @param id �X�V���郌�R�[�hID�B
		 */
		function set( $param )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			
			if( $loginUserType == 'admin' && $loginUserRank == $ACTIVE_ACTIVATE )
			{
				$db	 = $gm[ $param['type'] ]->getDB();
				
				$rec = $db->selectRecord( $param['id'] );
				if( isset($rec) )
				{
					foreach( $db->colName as $name )
					{
						if( isset($param[$name]) && strlen($param[$name]) ) { $db->setData( $rec, $name, $param[ $name ] ); }
					}
					
					$db->updateRecord( $rec );
				}
			}

			if( $_SERVER[ 'HTTP_REFERER' ] )
			{
				global $HOME;
				preg_match( '/.*\/([^\/]*(\?.*)?)/' , $_SERVER[ 'HTTP_REFERER' ] , $match );
				SystemUtil::innerLocation( $match[ 1 ] );
			}
			else
				SystemUtil::innerLocation( 'info.php?type=' . $param[ 'type' ] . '&id=' . $param[ 'id' ] );

//			if( $param['info_change_flg'] ) { $this->info_change_result( $param, '{ "success" : true , "msg" : "success table set." }' ); }

		}		
				
				
		/**
		 * info_change_sys�Ɠ����̏�����Ԃ��B
		 * 
		 * @param js JSON�f�[�^�ԋp�t���O
		 * @param draws JSON�f�[�^
		 * @param jump �����[�h��
		 */
		function  info_change_result( &$param, $draws = '' )
		{
			if( $param['js'] == "true" ) { print $draws; }
			else
			{
				$jump = "index.php";
				if( isset($param['jump']) && strlen($param['jump']) )  { $jump = $param['jump']; }
				SystemUtil::innerLocation( $jump);
			}
		}
		
	}

?>