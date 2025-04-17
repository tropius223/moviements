<?php

	/**
		@brief   �V�X�e���ݒ���Ǘ��N���X�B
		@details �V�X�e���S�̂̐ݒ�����Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup Information
	*/
	class SystemInfo
	{
		//��������

		/**
			@brief �V�X�e���ݒ������������B
		*/
		private static function Initialize()
		{
			if( !self::$Initialized ) //����������Ă��Ȃ��ꍇ
			{
				//system�e�[�u������ݒ���擾
				$db    = SystemUtil::getGMforType( 'system' )->getDB();
				$table = $db->getTable();
				$rec   = $db->getRecord( $table , 0 );

				self::$HomeURL     = $db->getData( $rec , 'home' );
				self::$MailAddress = $db->getData( $rec , 'mail_address' );
				self::$MailName    = $db->getData( $rec , 'mail_name' );
				self::$CSSName     = $db->getData( $rec , 'main_css' );

				//�ݒu��Ɋւ���ݒ���擾
				preg_match( '/(.*?)([^\/]+)$/' , $_SERVER[ 'SCRIPT_NAME' ] , $match );
				self::$RealBaseURL  = $match[ 1 ];
				self::$ScriptName   = $match[ 2 ];

				//�O���[�o���ϐ��̐ݒ���C���|�[�g
				self::ImportGlobalVarConfigs();

				self::$DB     = $db;
				self::$Record = $rec;

				self::$Initialized = true;
			}
		}

		//���擾

		/**
			@brief  �V�X�e������������t�H�[���̌`�����擾����B
			@retval buffer   �e���v���[�g�Ɏ����I�ɖ��ߍ��܂��ꍇ
			@retval variable �R�}���h�R�����g�ŔC�ӂ̈ʒu�ɏo�͂���ꍇ
		*/
		static function GetAutoFormType()
		{
			self::Initialize();

			return self::$AutoFormType;
		}

		/**
			@brief     �V�X�e�����R�[�h�ɐݒ肳��Ă���z�[��URL���擾����B
			@return    �z�[��URL�B
			@attention ���ۂ�URL�Ƃ͈قȂ�\��������܂��B
		*/
		static function GetHomeURL()
		{
			self::Initialize();

			return self::$HomeURL;
		}

		/**
			@brief  �V�X�e�������s����cookie�̗L���p�X���擾����B
			@return cookie�̗L���p�X�B
		*/
		static function GetCookieEnablePath()
		{
			self::Initialize();

			return self::$CookieEnablePath;
		}

		/**
			@brief  �V�X�e�����R�[�h�ɐݒ肳��Ă���css���ݒ���擾����B
			@return css���ݒ�B
		*/
		static function GetCSSName()
		{
			self::Initialize();

			return self::$CSSName;
		}

		/**
			@brief  �J�X�^���y�[�W��ۑ�����f�B���N�g���p�X���擾����B
			@return �f�B���N�g���p�X�B
		*/
		static function GetCustomPageDir()
		{
			self::Initialize();

			return self::$CustomPageDir;
		}

		/**
			@brief  ���O�C������ID�Ƃ��ĔF������t�H�[�������擾����B
			@return ID�t�H�[�����B
		*/
		static function GetLoginIDFormName()
		{
			self::Initialize();

			return self::$LoginIDFormName;
		}

		/**
			@brief  ���O�C��ID��ۑ�����Z�b�V�����̃L�[�����擾����B
			@return �L�[���B
		*/
		static function GetLoginIDSaveName()
		{
			self::Initialize();

			return self::$LoginIDSaveName;
		}

		/**
			@brief  ���O�C�����̃p�X���[�h�Ƃ��ĔF������t�H�[�������擾����B
			@return �p�X���[�h�t�H�[�����B
		*/
		static function GetLoginPassFormName()
		{
			self::Initialize();

			return self::$LoginPassFormName;
		}

		/**
			@brief  �V�X�e�������[�����M�̏����Ɏg�p���郁�[���A�h���X���擾����B
			@return ���[���A�h���X�B
		*/
		static function GetMailAddress()
		{
			self::Initialize();

			return self::$MailAddress;
		}

		/**
			@brief  �V�X�e�������[�����M�̏����Ɏg�p���閼�O���擾����B
			@return ���M�Җ��B
		*/
		static function GetMailName()
		{
			self::Initialize();

			return self::$MailName;
		}

		/**
			@brief  �A�b�v���[�h�\�ȃt�@�C���̍ő�T�C�Y���擾����B
			@return �ő�T�C�Y�ݒ�B
		*/
		static function GetMaxUploadByteSize()
		{
			self::Initialize();

			return self::$MaxUploadByteSize;
		}

		/**
			@brief  �摜��������Ȃ��ꍇ�̑�֏o�̓e�L�X�g���擾����B
			@return ��փe�L�X�g�B
		*/
		static function GetNullImageString()
		{
			self::Initialize();

			if( WS::Info( 'User' )->IsMobile() ) //�g�т���A�N�Z�X���Ă���ꍇ
				{ return self::$NullImageStringMobile; }
			else //���̑��̒[������A�N�Z�X���Ă���ꍇ
				{ return self::$NullImageString; }
		}

		/**
			@brief  ���O�C�����Ă��Ȃ����[�U�[�̃��[�U�[��ʖ����擾����B
			@return �񃍃O�C�����[�U�[��ʖ��B
		*/
		static function GetNotLoginUserType()
		{
			self::Initialize();

			return self::$NotLoginUserType;
		}

		/**
			@brief     �V�X�e�����R�[�h�f�[�^���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@exception LogicException           ���R�[�h�ɑ��݂��Ȃ��J�������w�肵���ꍇ�B
			@param[in] $iColumn_ �J�������B
			@return    ���R�[�h�i�[�l�B
		*/
		static function GetParam( $iColumn_ )
		{
			self::Initialize();

			Concept::IsString( $iColumn_ )->OrThrow( 'InvalidArgument' , '�J�������������ł�' );
			Concept::IsTrue( self::HasColumn( $iColumn_ ) )->OrThrow( 'Logic' , 'system�e�[�u���ɂ̓J����[' . $iColumn_ . ']�͑��݂��܂���' );

			return self::$DB->getData( self::$Record , $iColumn_ );
		}

		/**
			@brief  ���ϐ�����擾�����V�X�e���̃x�[�XURL���擾����B
			@return �x�[�XURL�B
		*/
		static function GetRealBaseURL()
		{
			self::Initialize();

			return self::$RealBaseURL;
		}

		/**
			@brief  �x�[�XURL��ۑ�����Z�b�V�����̃L�[�����擾����B
			@return �L�[���B
		*/
		static function GetRealBaseURLSaveName()
		{
			self::Initialize();

			return self::$RealBaseURLSaveName;
		}

		/**
			@brief  ���ϐ�����擾�����A�N�Z�X����php�t�@�C�������擾����B
			@return php�t�@�C�����B
		*/
		static function GetScriptName()
		{
			self::Initialize();

			return self::$ScriptName;
		}

		/**
			@brief  ���ϐ�����擾�����A�N�Z�X����php�t�@�C�������擾����B
			@return php�t�@�C�����B
		*/
		static function GetSystemClassDir()
		{
			self::Initialize();

			return self::$SystemClassDir;
		}

		/**
			@brief     �V�X�e���e�[�u�����w��̃J�����������Ă��邩�m�F����B
			@param[in] $iColumn_ �J�������B
			@retval    true  �J�����������Ă���ꍇ�B
			@retval    false �J�����������Ă��Ȃ��ꍇ�B
		*/
		static function HasColumn( $iColumn_ )
		{
			self::Initialize();

			return WS::Info( 'Table' )->ExistsColumn( 'system' , $iColumn_ );
		}

		//������

		/**
			@brief     �O���[�o���ϐ�����ݒ�l���C���|�[�g����B
			@attention �ڍs����������܂ł̉��@�\�ł��B
		*/
		private static function ImportGlobalVarConfigs()
		{
			global $NOT_LOGIN_USER_TYPE;
			global $LOGIN_KEY_FORM_NAME;
			global $LOGIN_PASSWD_FORM_NAME;
			global $SESSION_NAME;
			global $SESSION_PATH_NAME;
			global $IMAGE_NOT_FOUND;
			global $IMAGE_NOT_FOUND_MOBILE;
			global $system_path;
			global $page_path;
			global $FORM_TAG_DRAW_FLAG;
			global $COOKIE_PATH;
			global $MAX_FILE_SIZE;

			self::$NotLoginUserType      = $NOT_LOGIN_USER_TYPE;
			self::$LoginIDFormName       = $LOGIN_KEY_FORM_NAME;
			self::$LoginPassFormName     = $LOGIN_PASSWD_FORM_NAME;
			self::$LoginIDSaveName       = $SESSION_NAME;
			self::$RealBaseURLSaveName   = $SESSION_PATH_NAME;
			self::$NullImageString       = $IMAGE_NOT_FOUND;
			self::$NullImageStringMobile = $IMAGE_NOT_FOUND_MOBILE;
			self::$SystemClassDir        = $system_path;
			self::$CustomPageDir         = $page_path;
			self::$AutoFormType          = $FORM_TAG_DRAW_FLAG;
			self::$CookieEnablePath      = $COOKIE_PATH;
			self::$MaxFileUploadByteSize = $MAX_FILE_SIZE;
		}

		//���ϐ�
		private static $Initialized           = false;                           ///<�������t���O
		private static $DB                    = null;                            ///<�f�[�^�x�[�X�I�u�W�F�N�g
		private static $Record                = null;                            ///<���R�[�h�f�[�^
		private static $HomeURL               = null;                            ///<�z�[��URL
		private static $MailAddress           = null;                            ///<�������[���A�h���X
		private static $MailName              = null;                            ///<�������M�Җ�
		private static $CSSName               = null;                            ///<�W����css��
		private static $RealBaseURL           = null;                            ///<���ϐ�����擾�����x�[�XURL
		private static $ScriptName            = null;                            ///<���ϐ�����擾����php�t�@�C����
		private static $NotLoginUserType      = 'nobody';                        ///<�񃍃O�C�����[�U�[��ʖ�
		private static $LoginIDFormName       = 'mail';                          ///<���O�C��ID�t�H�[����
		private static $LoginPassFormName     = 'passwd';                        ///<���O�C���p�X���[�h�t�H�[����
		private static $LoginIDSaveName       = 'loginid';                       ///<���O�C��ID��ۑ�����Z�b�V�����L�[
		private static $RealBaseURLSaveName   = 'system_path';                   ///<�x�[�XURL��ۑ�����Z�b�V�����L�[
		private static $NullImageString       = '<span>No Image</span>';         ///<�摜��������Ȃ��ꍇ�̑�փe�L�X�g
		private static $NullImageStringMobile = 'common/img/no_image_80x60.gif'; ///<�g�ђ[���ŉ摜��������Ȃ��ꍇ�̑�փe�L�X�g
		private static $SystemClassDir        = 'custom/system/';                ///<�V�X�e���N���X�i�[�f�B���N�g��
		private static $CustomPageDir         = 'file/page/';                    ///<�J�X�^���y�[�W�ۑ��f�B���N�g��
		private static $AutoFormType          = 'variable';                      ///<�V�X�e���t�H�[���̌`��
		private static $CookieEnablePath      = '/';                             ///<cookie�̗L���p�X
		private static $MaxFileUploadByteSize = 512000;                          ///<�A�b�v���[�h�t�@�C���̍ő�T�C�Y
	}

?>