<?php

	/**
		@brief   ���[�U�[�ݒ���Ǘ��N���X�B
		@details �V�X�e���ɃA�N�Z�X���Ă��郆�[�U�[�̏����Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup Information
	*/
	class UserInfo
	{
		//��������

		/**
			@brief     ���[�U�[��������������B
			@exception IllegalAccessException ���O�C��ID�ɕs���Ȓl���ݒ肳��Ă���ꍇ�B
		*/
		private static function Initialize()
		{
			if( !self::$Initialized ) //����������Ă��Ȃ��ꍇ
			{
//				if( WS::Info( 'System' )->GetRealBaseURL() == $_SESSION[ WS::Info( 'System' )->GetRealBaseURLSaveName() ] ) //URL����v����ꍇ
				{
					self::$ID = $_SESSION[ WS::Info( 'System' )->GetLoginIDSaveName() ];

					if( self::$ID ) //ID���Z�b�g����Ă���ꍇ
					{
						Concept::IsNotMatch( '/\W/' , self::$ID )->OrThrow( 'IllegalAccess' );

						//���[�U�[��ʂ���肷��
						foreach( WS::Info( 'Table' )->GetTableNames() as $tableName ) //�S�Ẵe�[�u��������
						{
							if( !WS::Info( 'Table' )->IsUser( $tableName ) ) //���[�U�[�e�[�u���ł͂Ȃ��ꍇ
								{ continue; }

							$db    = SystemUtil::getGMforType( $tableName )->getDB();
							$table = $db->getTable( 'all' );
							$table = $db->searchTable( $table , 'id' , '=' , self::$ID );
							$table = $db->LimitOffset( $table , 0 , 1 );
							$row   = $db->getRow( $table );

							if( $row ) //ID����v���郌�R�[�h������ꍇ
							{
								$rec            = $db->getRecord( $table , 0 );
								self::$DB       = $db;
								self::$Record   = $rec;
								self::$Type     = $tableName;
								self::$Activate = $db->getData( $rec , 'activate' );
								break;
							}
						}

						Concept::IsNotNull( self::$Record )->OrThrow( 'IllegalAccess' );
					}
				}

				if( !self::$Record ) //���[�U�[���R�[�h��������Ȃ��ꍇ
				{
					self::$Type     = WS::Info( 'System' )->GetNotLoginUserType();
					self::$Activate = WS::Info( 'Activate' )->GetNoneBit();
				}

				//�A�N�Z�X�[������肷��
				self::$Terminal = MobileUtil::getTerminal();

				self::$Initialized = true;
			}
		}

		//���擾

		/**
			@brief  ���[�U�[�̃A�N�e�B�x�[�g���x�����擾����B
			@return �A�N�e�B�x�[�g���x���B
		*/
		static function GetActivate()
		{
			self::Initialize();

			return self::$Activate;
		}

		/**
			@brief  ���[�U�[��ID���擾����B
			@return ���R�[�hID�B
		*/
		static function GetID()
		{
			self::Initialize();

			return self::$ID;
		}

		/**
			@brief     ���[�U�[�̃��R�[�h�f�[�^���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@exception LogicException           ���[�U�[�����O�C�����Ă��Ȃ��A�܂��̓��R�[�h�ɑ��݂��Ȃ��J�������w�肵���ꍇ�B
			@param[in] $iColumn_ �J�������B
			@return    ���R�[�h�i�[�l�B
		*/
		static function GetParam( $iColumn_ )
		{
			self::Initialize();

			Concept::IsString( $iColumn_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::IsLogin() )->OrThrow( 'Logic' );
			Concept::IsTrue( self::HasColumn( $iColumn_ ) )->OrThrow( 'Logic' );

			return self::$DB->getData( self::$Record , $iColumn_ );
		}

		/**
			@brief     ���[�U�[�̃��R�[�h���擾����B
			@exception LogicException ���[�U�[�����O�C�����Ă��Ȃ��ꍇ�B
			@return    ���R�[�h�f�[�^�B
		*/
		static function GetRecord()
		{
			self::Initialize();

			Concept::IsTrue( self::IsLogin() )->OrThrow( 'Logic' );

			return self::$Record;
		}

		/**
			@brief     ���[�U�[�̃A�N�Z�X�[�����擾����B
			@exception LogicException ���[�U�[���g�шȊO�̒[������A�N�Z�X���Ă���ꍇ�B
			@return    �A�N�Z�X�[���̎�ʁB
		*/
		static function GetTerminal()
		{
			self::Initialize();

			Concept::IsTrue( self::IsMobile() )->OrThrow( 'Logic' );

			return ( 0 < self::$Terminal ? true : false );
		}

		/**
			@brief  ���[�U�[�̎�ʂ��擾����B
			@return ���[�U�[��ʖ��B
		*/
		static function GetType()
		{
			self::Initialize();

			return self::$Type;
		}

		/**
			@brief     ���[�U�[�e�[�u�����w��̃J�����������Ă��邩�m�F����B
			@param[in] $iColumn_ �J�������B
			@retval    true  �J�����������Ă���ꍇ�B
			@retval    false �J�����������Ă��Ȃ��A�܂��̓��O�C�����Ă��Ȃ��ꍇ�B
		*/
		static function HasColumn( $iColumn_ )
		{
			self::Initialize();

			if( !self::IsLogin() ) //���O�C�����Ă��Ȃ��ꍇ
				{ return false; }

			return WS::Info( 'Table' )->ExistsColumn( self::GetType() , $iColumn_ );
		}

		/**
			@brief  ���[�U�[�����O�C�����Ă��邩�m�F����B
			@retval true  ���O�C�����Ă���ꍇ�B
			@retval false ���O�C�����Ă��Ȃ��ꍇ�B
		*/
		static function IsLogin()
		{
			self::Initialize();

			return ( self::$Record ? true : false );
		}

		/**
			@brief  ���[�U�[�̃A�N�Z�X�[�����m�F����B
			@retval true  �g�т���A�N�Z�X���Ă���ꍇ�B
			@retval false ���̑��̒[������A�N�Z�X���Ă���ꍇ�B
		*/
		static function IsMobile()
		{
			self::Initialize();

			return ( 0 < self::$Terminal ? true : false );
		}

		//���ϐ�
		private static $Initialized = false; ///<�������t���O
		private static $DB          = null;  ///<�f�[�^�x�[�X�I�u�W�F�N�g
		private static $Record      = null;  ///<���R�[�h�f�[�^
		private static $ID          = null;  ///<���R�[�hID
		private static $Type        = null;  ///<���[�U�[���
		private static $Activate    = null;  ///<�A�N�e�B�x�[�g���x��
		private static $Terminal    = null;  ///<�A�N�Z�X�[��
	}

?>