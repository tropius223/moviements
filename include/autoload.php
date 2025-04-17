<?php

	//���֐� //

	//���}�W�b�N //

	/**
		@brief     ����`�̃N���X�̎Q�Ƃ��t�b�N����B
		@details   ����`�̃N���X���Q�Ƃ��ꂽ�ꍇ(�C���X�^���X������N���X���\�b�h�̌Ăяo��)�Ɏ����I�ɌĂяo����܂��B\n
		           �N���X��`�t�@�C�����������ă��[�h���邱�ƂŁA�N���X�𐳂����Q�Ƃł���悤�ɂ��܂��B
		@exception IllegalAccessException �s���ȃN���X�����w�肵���ꍇ(attention���Q��)
		@exception LogicException         �N���X��`�t�@�C����������Ȃ��ꍇ(attention���Q��)
		@param[in] $iClassName_ ��������N���X���B
		@attention PHP�̃o�[�W������5.3.0�����̏ꍇ�A���̊֐���die���Ăяo���ďI�����܂�(��O���O���ɓ����邱�Ƃ��ł��Ȃ�����)
		@remarks   ���̊֐��� $iClassName_ �̃C���X�^���X��Ԃ��K�v�͂���܂���B\n
		           �Ή�����N���X��`�t�@�C���̃��[�h���������s���ă��^�[������΁A�N���X�͐������Q�Ƃ���܂��B
		@remarks   ��{�V�X�e���̐݌v��Amodule�ȉ���custom/view�ȉ��̎������[�h�ɂ͑Ή��ł��܂���B
		@remarks   class_exists����̌Ăяo���̏ꍇ�A��2�����ɖ����I��true���w�肵�Ă��Ȃ���΁A���̊֐��̓N���X��`�t�@�C�������[�h���܂���B
		@ingroup   SystemComponent
	*/
	function __autoload( $iClassName_ ) //
	{
		try
		{
			//�Ăяo�������m�F
			$stackTrace = debug_backtrace();
			$thisFrame  = array_shift( $stackTrace );
			$frameData  = array_shift( $stackTrace );
			$funcName   = $frameData[ 'function' ];

			if( 'class_exists' == $funcName ) //class_exists����̌Ăяo���̏ꍇ
			{
				if( 2 > count( $frameData[ 'args' ] ) ) //��2�������w�肳��Ă��Ȃ��ꍇ
					{ return; }

				if( true != $frameData[ 'args' ][ 1 ] ) //��2�����ɖ����I��true���w�肳��Ă��Ȃ��ꍇ
					{ return; }
			}

			//�f�B���N�g���g���o�[�T�����o
			if( preg_match( '/\W/' , $iClassName_ ) ) //�p�����ȊO�̕������܂܂��ꍇ
				{ throw new IllegalAccessException( '�s���ȃA�N�Z�X�ł�[' . $iClassName_ . ']' ); }

			if( class_exists( 'WS' , false ) ) //WS�N���X����`�ς݂̏ꍇ
				{ WS::DefClass( $iClassName_ ); }
			else //WS�N���X������`�̏ꍇ
			{
				//include/�ȉ��ɂ���Ɖ��肷��
				$filePath = 'include/' . $iClassName_ . '.php';

				if( !is_file( $filePath ) ) //�t�@�C����������Ȃ��ꍇ
					{ throw new LogicException( '__autoload �������ł��܂���[' . $iClassName_ . ']' ); }

				include_once $filePath;
			}
		}
		catch( Exception $e )
		{
			//��O�̑Ή��̂��߂�PHP�o�[�W��������͂���
			$version = 0;

			foreach( explode( '.' , phpversion() ) as $versionNum ) //�o�[�W������񕶎��������
				{ $version = ( $version * 10 ) + $versionNum; }

			if( 530 <= $version ) //5.3.0�ȏ�̏ꍇ
				{ throw $e; }
			else //5.3.0�����̏ꍇ
			{
				//5.3.0�ȑO��autoload����đ��o�ł��Ȃ��̂ŁA�G���[���O���o�͂��Ē�~����
				$fp = fopen( 'logs/error.log' , 'a' );

				if( $fp ) //�t�@�C�����I�[�v���ł����ꍇ
				{
					fputs( $fp , date( '*Y/n/j H:i:s' . "\n" ) );
					fputs( $fp , $e->getMessage() . "\n" );
					fputs( $fp , '-----------------------------------------------------' . "\n\n" );
					fclose( $fp );

					//���O�t�@�C������債�Ă����烊�l�[��
					if( 2097152 <= filesize( 'logs/error.log' ) ) //���O�t�@�C���̃T�C�Y��2MB�𒴂��Ă���ꍇ
					{
						$nowDateString = date( '_Y_m_d_H_i_s' );

						rename( 'logs/error.log' , 'logs/error.log' . $nowDateString );
					}
				}

				die( 'autoload error' );
			}
		}
	}
