<?php

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �s���ȃN�G���p�����[�^���󂯎�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���삪�󂯕t�����Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
	*/
	class InvalidQueryException extends Exception
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �s���ȃA�N�Z�X�����������ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A�A�N�Z�X�������Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
	*/
	class IllegalAccessException extends Exception
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �t�@�C���̓��o�͂Ɏ��s�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
	*/
	class FileIOException extends Exception
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �f�[�^�x�[�X�̍X�V�Ɏ��s�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���삪�K�p����Ȃ������\��������|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
	*/
	class UpdateFailedException extends Exception
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ���炩�̗��R�ŉ�ʏo�͂Ɏ��s�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
	*/
	class OutputFailedException extends Exception
	{}

	if( !class_exists( 'RuntimeException' ) )
	{
		/**
			@brief   ��O�I�u�W�F�N�g�B
			@details ���s���ɃG���[�����������ꍇ�ɃX���[����܂��B\n
			         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		*/
		class RuntimeException extends Exception
		{}
	}

	if( !class_exists( 'InvalidArgumentException' ) )
	{
		/**
			@brief   ��O�I�u�W�F�N�g�B
			@details �֐��̃p�����[�^�ɕs���Ȓl���w�肳�ꂽ�ꍇ�ɃX���[����܂��B\n
			         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		*/
		class InvalidArgumentException extends Exception
		{}
	}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �R�}���h�R�����g�̃p�����[�^�ɕs���Ȓl���w�肳�ꂽ�ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
	*/
	class InvalidCCArgumentException extends Exception
	{}
?>
