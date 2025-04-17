<?php

	/**
		@brief   ���W���[���ݒ�Ǘ��N���X�B
		@details ���W���[���Ɋւ�������Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup PackageInformation
	*/
	class ModuleInfo
	{
		/**
			@brief     ���W���[�����L���ɂȂ��Ă��邩���ׂ�B
			@exception InvalidArgumentException $iModuleName_ �ɖ����Ȓl���w�肵���ꍇ�B
			@param[in] $iModuleName_ ���W���[�����B
			@retval    true  ���W���[�����L���ȏꍇ�B
			@retval    false ���W���[���������ȏꍇ�B
		*/
		function IsEnable( $iModuleName_ )
		{
			Concept::IsString( $iModuleName_ )->OrThrow( 'InvalidArgument' , '���W���[�����������ł�' );

			if( class_exists( 'class_' . $iModuleName_ ) ) //���W���[���N���X�����݂���ꍇ
				{ return true; }
			else //���W���[���N���X��������Ȃ��ꍇ
				{ return false; }
		}
	}

?>
