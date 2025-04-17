<?php

	/**
		@brief   �A�N�e�B�x�[�g���x���Ǘ��N���X�B
		@details �A�N�e�B�x�[�g���x���̐ݒ�����Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup Information
	*/
	class ActivateInfo
	{
		//��������

		/**
			@brief �A�N�e�B�x�[�g���x���ݒ������������B
		*/
		private static function Initialize()
		{
			if( !self::$Initialized ) //����������Ă��Ȃ��ꍇ
			{
				self::ImportGlobalVarConfigs();

				self::$Initialized = true;
			}
		}

		//���擾

		/**
			@brief  �A�N�e�B�x�[�g���x��[�F��]��\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetAcceptBit()
		{
			self::Initialize();

			return self::$ActiveAcceptBit;
		}

		/**
			@brief  �A�N�e�B�x�[�g���x��[���F��]��\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetActivateBit()
		{
			self::Initialize();

			return self::$ActiveActivateBit;
		}

		/**
			@brief  �S�ẴA�N�e�B�x�[�g���x����\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetAllBit()
		{
			self::Initialize();

			return self::$ActiveAllBit;
		}

		/**
			@brief  �A�N�e�B�x�[�g���x��[����]��\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetDenyBit()
		{
			self::Initialize();

			return self::$ActiveDenyBit;
		}

		/**
			@brief  �A�N�e�B�x�[�g���x��[���F��]��\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetNoneBit()
		{
			self::Initialize();

			return self::$ActiveNoneBit;
		}

		//������

		/**
			@brief     �O���[�o���ϐ�����ݒ�l���C���|�[�g����B
			@attention �ڍs����������܂ł̉��@�\�ł��B
		*/
		private static function ImportGlobalVarConfigs()
		{
			global $ACTIVE_NONE;
			global $ACTIVE_ACTIVATE;
			global $ACTIVE_ACCEPT;
			global $ACTIVE_DENY;
			global $ACTIVE_ALL;

			self::$ActiveNoneBit     = $ACTIVE_NONE;
			self::$ActiveActivateBit = $ACTIVE_ACTIVATE;
			self::$ActiveAcceptBit   = $ACTIVE_ACCEPT;
			self::$ActiveDenyBit     = $ACTIVE_DENY;
			self::$ActiveAllBit      = $ACTIVE_ALL;
		}

		//���ϐ�
		private static $Initialized         = false; ///<�������t���O
		private static $ActiveNoneBit     = 1;       ///<���F��
		private static $ActiveActivateBit = 2;       ///<���F��
		private static $ActiveAcceptBit   = 4;       ///<�F��
		private static $ActiveDenyBit     = 8;       ///<����
		private static $ActiveAllBit      = 15;      ///<�S��
	}

?>