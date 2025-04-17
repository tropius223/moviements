<?php

	/**
		@brief   ��O���[�e�B���e�B�N���X�B
		@details ��O�Ɋւ���֐����܂Ƃ߂��N���X�ł��B
	*/
	class ExceptionUtil
	{
		/**
			@brief   ��O�G���[�y�[�W���o�͂���B
			@details ��O�̎�ނɉ����ăG���[�e���v���[�g���o�͂��܂��B\n
			         �Ή�����e���v���[�g��������Ȃ��ꍇ�͕W���̃G���[�e���v���[�g���o�͂���܂��B
			@param   $className_ ��O�I�u�W�F�N�g�̃N���X���B
			@remarks ��O�G���[�e���v���[�g��target�ɏ������̃N���X���Alabel��EXCEPTION_DESIGN���w�肵�܂��B
		*/
		static function DrawErrorPage( $className_ )
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;

			try
			{
				ob_start();

				System::$head = false;
				System::$foot = false;

				if( $_GET[ 'type' ] && !is_array( $_GET[ 'type' ] ) && $gm[ $_GET[ 'type' ] ] )
					$tGM = SystemUtil::getGMforType( $_GET[ 'type' ] );
				else
					$tGM = SystemUtil::getGMforType( 'system' );

				print System::getHead( $gm , $loginUserType , $loginUserRank );

				//��O�I�u�W�F�N�g�̃e���v���[�g����������
				$template = Template::getTemplate( $loginUserType , $loginUserRank , $className_ , 'EXCEPTION_DESIGN' );

				if( $template && file_exists( $template ) )
					print $tGM->getString( $template );
				else
				{
					//Exception�I�u�W�F�N�g�̃e���v���[�g����������
					if( 'Exception' != $className_ )
						$template = Template::getTemplate( $loginUserType , $loginUserRank , 'exception' , 'EXCEPTION_DESIGN' );

					if( $template && file_exists( $template ) )
						print $tGM->getString( $template );
					else
						Template::drawErrorTemplate();
				}

				print System::getFoot( $gm , $loginUserType , $loginUserRank );

				ob_end_flush();
			}
			catch( Exception $e_ )
			{
				ob_end_clean();

				print System::getHead( $gm , $loginUserType , $loginUserRank );
				Template::drawErrorTemplate();
				print System::getFoot( $gm , $loginUserType , $loginUserRank );
			}
		}
	}
?>
