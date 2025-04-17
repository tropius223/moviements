<?php

	/**
		@brief   �e���v���[�g�ݒ���Ǘ��N���X�B
		@details �e���v���[�g�Ɋւ���ݒ���Ǘ����܂��B
		@author  ���� ����
		@version 1.01
		@date    2010/8/19
		@ingroup Information
	*/
	class TemplateInfo
	{
		//��������

		/**
			@brief �e���v���[�g�ݒ������������B
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
			@brief     �e���v���[�g�̕W���t�@�C�������擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@exception LogicException           �W�����ł��Ȃ��e���v���[�g�̃��x�����w�肵���ꍇ�B
			@param[in] $iLabelName_ �e���v���[�g�̃��x�����B
			@return    �e���v���[�g�̕W���t�@�C�����B
		*/
		static function GetDefaultFileName( $iLabelName_ )
		{
			self::Initialize();

			Concept::IsString( $iLabelName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( array_key_exists( $iLabelName_ , self::$DefaultFileNames ) )->OrThrow( 'Logic' );

			return self::$DefaultFileNames[ $iLabelName_ ];
		}

		/**
			@brief     �e���v���[�g�̕W���i�[��f�B���N�g�������擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iUserType_   �e���v���[�g���g�p���郆�[�U�[��ʁB
			@param[in] $iTargetName_ �e���v���[�g�̃^�[�Q�b�g���B
			@param[in] $iLabelName_  �e���v���[�g�̃��x�����B
			@return    �e���v���[�g�̕W���i�[��f�B���N�g�����B
		*/
		static function GetDefaultDirName( $iUserType_ , $iTargetName_ , $iLabelName_ )
		{
			self::Initialize();

			Concept::IsString( $iLabelName_ )->OrThrow( 'InvalidArgument' );

			switch( $iLabelName_ )
			{
				case 'HEAD_DESIGN'            :
				case 'HEAD_DESIGN_ADMIN_MODE' :
				case 'FOOT_DESIGN'            :
					{ return 'other/common/'; }

				case 'TOP_PAGE_DESIGN'        :
				case 'OTHER_PAGE_DESIGN'      :
				{
					Concept::IsString( $iUserType_ )->OrThrow( 'InvalidArgument' );

					return 'other/users/' . $iUserType_ . '/';
				}

				case 'INCLUDE_DESIGN' :
				{
					Concept::IsString( $iUserType_ )->OrThrow( 'InvalidArgument' );

					return 'other/include/' . $iUserType_ . '/';
				}

				case 'LOGIN_PAGE_DESIGN'           :
				case 'LOGIN_FALED_DESIGN'          :
				case 'ACTIVATE_DESIGN_HTML'        :
				case 'ACTIVATE_EXISTS_DESIGN_HTML' :
				case 'ACTIVATE_FALED_DESIGN_HTML'  :
				case 'ERROR_PAGE_DESIGN'           :
				case 'SEARCH_PAGE_CHANGE_DESIGN'   :
				case 'REGIST_FALED_DESIGN'         :
					{ return 'other/common/'; }

				case 'ACTIVATE_MAIL'      :
				case 'ACTIVATE_COMP_MAIL' :
				case 'REGIST_COMP_MAIL'   :
				{
					Concept::IsString( $iUserType_ )->OrThrow( 'InvalidArgument' );

					return 'other/mail_contents/';
				}

				case 'REGIST_ERROR_DESIGN' :
				{
					Concept::IsString( $iLabelName_ )->OrThrow( 'InvalidArgument' );

					return $iTargetName_ . '/';
				}

				default :
				{
					Concept::IsString( $iLabelName_ , $iUserType_ )->OrThrow( 'InvalidArgument' );

					return $iTargetName_ . '/' . $iUserType_ . '/';
				}
			}
		}

		/**
			@brief     �e���v���[�g�̕W�����C�A�E�g���x�������擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@exception LogicException           �W�����ł��Ȃ��e���v���[�g�̃��x�����w�肵���ꍇ�B
			@param[in] $iLabelName_ �e���v���[�g�̃��x�����B
			@return    �e���v���[�g�̕W�����C�A�E�g���x�����B
		*/
		static function GetDefaultLayoutName( $iLabelName_ )
		{
			self::Initialize();

			Concept::IsString( $iLabelName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( array_key_exists( $iLabelName_ , self::$DefaultLayoutNames ) )->OrThrow( 'Logic[' . $iLabelName_ . ']' );

			return self::$DefaultLayoutNames[ $iLabelName_ ];
		}

		/**
			@brief  ��I�[�i�[�e���v���[�g��\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetOUtsiderTemplateBit()
		{
			self::Initialize();

			return self::$OutsiderTemplateBit;
		}

		/**
			@brief  �I�[�i�[�e���v���[�g��\���r�b�g�l���擾����B
			@return �r�b�g�l�B
		*/
		static function GetOwnerTemplateBit()
		{
			self::Initialize();

			return self::$OwnerTemplateBit;
		}

		/**
			@brief  �e���v���[�g�t�@�C���̊i�[�f�B���N�g���p�X���擾����B
			@return �f�B���N�g���p�X�B
		*/
		static function GetTemplateDir()
		{
			self::Initialize();

			if( WS::Info( 'User' )->isMobile() ) //�g�т���A�N�Z�X���Ă���ꍇ
				{ return self::$MobileTemplateDir; }
			else //���̑��̒[������A�N�Z�X���Ă���ꍇ
				{ return self::$TemplateDir; }
		}

		//������

		/**
			@brief     �O���[�o���ϐ�����ݒ�l���C���|�[�g����B
			@attention �ڍs����������܂ł̉��@�\�ł��B
		*/
		private static function ImportGlobalVarConfigs()
		{
			global $template_path;
			global $FORM_TAG_DRAW_FLAG;

			self::$TemplateDir  = $template_path;
			self::$AutoFormType = $FORM_TAG_DRAW_FLAG;
		}

		//���ϐ�
		private static $Initialized          = false;              ///<�������t���O
		private static $OwnerTemplateBit     = 1;                  ///<��I�[�i�[�l
		private static $OutsiderTemplateBit  = 2;                  ///<�I�[�i�[�l
		private static $AutoFormType          = 'variable';        ///<�V�X�e���t�H�[���̌`��
		private static $TemplateDir          = 'template/pc/';     ///<�e���v���[�g�t�@�C���̊i�[�f�B���N�g��
		private static $MobileTemplateDir    = 'template/mobile/'; ///<���o�C���p�e���v���[�g�t�@�C���̊i�[�f�B���N�g��

		private static $DefaultFileNames = ///<�e���v���[�g�̕W���t�@�C�����ꗗ
		Array(
			'HEAD_DESIGN'                 => 'Head.html' ,
			'HEAD_DESIGN_ADMIN_MODE'      => 'HeadAdminMode.html' ,
			'TOP_PAGE_DESIGN'             => 'Index.html' ,
			'FOOT_DESIGN'                 => 'Foot.html' ,
			'LOGIN_PAGE_DESIGN'           => 'Login.html' ,
			'LOGIN_FALED_DESIGN'          => 'LoginFailed.html' ,
			'ACTIVATE_DESIGN_HTML'        => 'Activate.html' ,
			'ACTIVATE_EXISTS_DESIGN_HTML' => 'ActivateExists.html' ,
			'ACTIVATE_FALED_DESIGN_HTML'  => 'ActivateFailed.html' ,
			'ERROR_PAGE_DESIGN'           => 'Error.html' ,
			'REGIST_FORM_PAGE_DESIGN'     => 'Regist.html' ,
			'REGIST_CHECK_PAGE_DESIGN'    => 'RegistCheck.html' ,
			'REGIST_COMP_PAGE_DESIGN'     => 'RegistComp.html' ,
			'REGIST_ERROR_DESIGN'         => 'RegistError.html' ,
			'REGIST_FALED_DESIGN'         => 'RegistFailed.html' ,
			'EDIT_FORM_PAGE_DESIGN'       => 'Edit.html' ,
			'EDIT_CHECK_PAGE_DESIGN'      => 'EditCheck.html' ,
			'EDIT_COMP_PAGE_DESIGN'       => 'EditComp.html' ,
			'DELETE_CHECK_PAGE_DESIGN'    => 'DeleteCheck.html' ,
			'DELETE_COMP_PAGE_DESIGN'     => 'DeleteComp.html' ,
			'SEARCH_FORM_PAGE_DESIGN'     => 'Search.html' ,
			'SEARCH_RESULT_DESIGN'        => 'SearchResult.html' ,
			'SEARCH_NOT_FOUND_DESIGN'     => 'SearchFailed.html' ,
			'SEARCH_LIST_PAGE_DESIGN'     => 'List.html' ,
			'SEARCH_PAGE_CHANGE_DESIGN'   => 'SearchPageChange.html' ,
			'INFO_PAGE_DESIGN'            => 'Info.html' ,
			'ACTIVATE_MAIL'               => 'Activate.txt' ,
			'ACTIVATE_COMP_MAIL'          => 'ActivateComp.txt' ,
			'REGIST_COMP_MAIL'            => 'RegistComp.txt'
		);

		private static $DefaultLayoutNames = ///<���C�A�E�g�̕W���t�@�C�����ꗗ
		Array(
			'TOP_PAGE_DESIGN'             => 'index' ,
			'LOGIN_PAGE_DESIGN'           => 'other' ,
			'LOGIN_FALED_DESIGN'          => 'error' ,
			'ACTIVATE_DESIGN_HTML'        => 'other' ,
			'ACTIVATE_EXISTS_DESIGN_HTML' => 'other' ,
			'ACTIVATE_FALED_DESIGN_HTML'  => 'error' ,
			'ERROR_PAGE_DESIGN'           => 'error' ,
			'REGIST_FORM_PAGE_DESIGN'     => 'input' ,
			'REGIST_CHECK_PAGE_DESIGN'    => 'inputCheck' ,
			'REGIST_COMP_PAGE_DESIGN'     => 'inputComp' ,
			'REGIST_FALED_DESIGN'         => 'other' ,
			'EDIT_FORM_PAGE_DESIGN'       => 'input' ,
			'EDIT_CHECK_PAGE_DESIGN'      => 'inputCheck' ,
			'EDIT_COMP_PAGE_DESIGN'       => 'inputComp' ,
			'DELETE_CHECK_PAGE_DESIGN'    => 'inputCheck' ,
			'DELETE_COMP_PAGE_DESIGN'     => 'inputComp' ,
			'SEARCH_FORM_PAGE_DESIGN'     => 'search' ,
			'SEARCH_RESULT_DESIGN'        => 'searchResult' ,
			'SEARCH_NOT_FOUND_DESIGN'     => 'search' ,
			'INFO_PAGE_DESIGN'            => 'info' ,
			'OTHER_PAGE_DESIGN'           => 'other'
		);
	}

?>