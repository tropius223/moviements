<?php

	/**
	 * �V�X�e���R�[���N���X
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class XXXSystem extends System
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
			return $result;
		}


		/**
		 * �o�^�O�i�K�����B
		 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function registProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank, $check );
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
			// **************************************************************************************
			
			parent::registComp( $gm, $rec, $loginUserType, $loginUserRank );			
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ҏW�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �ҏW�O�i�K�����B
		 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function editProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::editProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );			
		}



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
			// **************************************************************************************
			
			parent::editComp( $gm, $rec, $loginUserType, $loginUserRank );			
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �폜�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �폜�����B
		 * �폜�����s����O�Ɏ��s����������������΁A�����ɋL�q���܂��B
		 * �Ⴆ�΃��[�U�f�[�^���폜����ۂɃ��[�U�f�[�^�ɕR�t����ꂽ�f�[�^���폜����ۂȂǂɗL���ł��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
		 */
		function deleteProc( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::deleteProc( $gm, $rec, $loginUserType, $loginUserRank );			
		}



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
			// **************************************************************************************
			
			parent::deleteComp( $gm, $rec, $loginUserType, $loginUserRank );			
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �����֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �����O�����B
		 * �������������������s�O�ɕύX�������ꍇ�ɗ��p���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param table �t�H�[���̂���̓��͓��e�Ɉ�v���郌�R�[�h���i�[�����e�[�u���f�[�^�B
		 */
		function searchResultProc( &$gm, &$sr, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::searchResultProc( $gm, $sr, $loginUserType, $loginUserRank );			
		}

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
			// **************************************************************************************
			
			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );			
		}


		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ڍ׏��֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �ڍ׏�񂪉{�����ꂽ�Ƃ��ɌĂяo����鏈���B
		 * ���ɑ΂���A�N�Z�X���O����肽���Ƃ��ȂǂɗL�p�ł��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �A�N�Z�X���ꂽ���R�[�h�f�[�^�B
		 */
		function doInfo( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::doInfo( $gm, $rec, $loginUserType, $loginUserRank );			
		}



		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



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
			// **************************************************************************************
			
			parent::drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �o�^���e�m�F�y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �o�^�����i�[�������R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawRegistCheck( &$gm, $rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawRegistCheck( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �o�^�����y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �o�^�����i�[�������R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawRegistComp( &$gm, $rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawRegistComp( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �o�^���s��ʂ�`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawRegistFaled( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawRegistFaled( $gm, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
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
			// **************************************************************************************
			
			parent::drawEditForm( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �ҏW���e�m�F�y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawEditCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawEditCheck( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �ҏW�����y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawEditComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawEditComp( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �ҏW���s��ʂ�`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawEditFaled( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawEditFaled( $gm, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �폜�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �폜�m�F�y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawDeleteCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawDeleteCheck( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �폜�����y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawDeleteComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawDeleteComp( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �����֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �����t�H�[����`�悷��B
		 *
		 * @param sr Search�I�u�W�F�N�g�B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawSearchForm( &$sr, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawSearchForm( $sr, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}

        function drawSearch( &$gm, &$sr, $table, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawSearch( $gm, $sr, $table, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}
        
		/**
		 * �������ʁA�Y���Ȃ���`��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawSearchNotFound( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawSearchNotFound( $gm, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/**
		 * �����G���[��`��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawSearchError( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawSearchError( $gm, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ڍ׃y�[�W�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * �ڍ׏��\���G���[��`��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawInfoError( &$gm, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawInfoError( $gm, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}

		/**
		 * �ڍ׏��y�[�W��`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function drawInfo( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::drawInfo( $gm, $rec, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}



		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/


		/**
		 * �������ʕ`��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
		 */
		function searchResult( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			parent::searchResult( $gm, $rec, $args ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}

		/**
		 * �������ʂ����X�g�`�悷��B
		 * �y�[�W�؂�ւ��͂��̗̈�ŕ`�悷��K�v�͂���܂���B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param table �������ʂ̃e�[�u���f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function getSearchResult( &$gm, $table, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			return parent::getSearchResult( $gm, $table, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}

		/**
		 * �������ʃy�[�W�؂�ւ�����`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g
		 * @param table �������ʂ̃e�[�u���f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 * @param partkey �����L�[
		 */
		function getSearchPageChange( &$gm, $table, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNum, $phpName, $param )
		{
		
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************
			
			return parent::getSearchPageChange( $gm, $table, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNum, $phpName, $param ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}

		/**
		 * �������ʂ̃y�[�W�؂�ւ�����`�悷��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g
		 * @param table �������ʂ̃e�[�u���f�[�^
		 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
		 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
		 */
		function getSearchInfo( &$gm, $table, $loginUserType, $loginUserRank )
		{
		
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************

			return parent::getSearchInfo( $gm, $table, $loginUserType, $loginUserRank ); // ��d�`��ɂȂ�̂ŕҏW����ꍇ�͍폜
		}


	}

?>