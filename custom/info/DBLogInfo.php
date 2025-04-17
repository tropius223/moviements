<?php

	/**
		@brief   ���O�o�͐ݒ�Ǘ��N���X�B
		@details SQL�̃��O�̏o�͐ݒ�����Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup Information
	*/
	class DBLogInfo
	{
		//��������

		/**
			@brief ���O�o�͐ݒ������������B
		*/
		private static function Initialize()
		{
			if( !self::$Initialized )
			{
				self::ImportGlobalVarConfigs();

				self::$Initialized = true;
			}
		}

		//���擾

		/**
			@brief  ���R�[�h�ǉ��̃��O�o�͐ݒ���擾����B
			@retval true  �o�͂��L���ȏꍇ�B
			@retval false �o�͂������ȏꍇ�B
		*/
		static function AddEnable()
		{
			self::Initialize();

			return self::$AddEnable;
		}

		/**
			@brief  ���R�[�h�폜�̃��O�o�͐ݒ���擾����B
			@retval true  �o�͂��L���ȏꍇ�B
			@retval false �o�͂������ȏꍇ�B
		*/
		static function DeleteEnable()
		{
			self::Initialize();

			return self::$DeleteEnable;
		}

		/**
			@brief  ���R�[�h�X�V�̃��O�o�͐ݒ���擾����B
			@retval true  �o�͂��L���ȏꍇ�B
			@retval false �o�͂������ȏꍇ�B
		*/
		static function UpdateEnable()
		{
			self::Initialize();

			return self::$UpdateEnable;
		}

		/**
			@brief  ���R�[�h����̃��O���o�͂���t�@�C���p�X���擾����B
			@return �t�@�C���p�X�B
		*/
		static function GetLogFilePath()
		{
			self::Initialize();

			return self::$LogFilePath;
		}

		/**
			@brief     �O���[�o���ϐ�����ݒ�l���C���|�[�g����B
			@attention �ڍs����������܂ł̉��@�\�ł��B
		*/
		private static function ImportGlobalVarConfigs()
		{
			global $ADD_LOG;
			global $UPDATE_LOG;
			global $DELETE_LOG;
			global $DB_LOG_FILE;

			self::$AddEnable    = $ADD_LOG;
			self::$UpdateEnable = $UPDATE_LOG;
			self::$DeleteEnable = $DELETE_LOG;
			self::$LogFilePath  = $DB_LOG_FILE;
		}

		//���ϐ�
		private static $Initialized  = false;               ///<�������t���O
		private static $AddEnable    = true;                ///<���R�[�h�ǉ��̃��O�o�͐ݒ�
		private static $UpdateEnable = true;                ///<���R�[�h�ҏW�̃��O�o�͐ݒ�
		private static $DeleteEnable = true;                ///<���R�[�h�폜�̃��O�o�͐ݒ�
		private static $LogFilePath  = 'logs/dbaccess.log'; ///<���O�o�̓t�@�C���̃p�X
	}

?>