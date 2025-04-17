<?php

	/**
		@brief   �e�[�u���ݒ���Ǘ��N���X�B
		@details �e�[�u���ƃJ�����̐ݒ�����Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup Information
	*/
	class TableInfo
	{
		//��������

		/**
			@brief �V�X�e���ݒ������������B
		*/
		private static function Initialize()
		{
			if( !self::$Initialized ) //����������Ă��Ȃ��ꍇ
			{
				self::$Initialized = true;

				self::ImportGlobalVarConfigs();
			}
		}

		//���ǉ�

		/**
			@brief     csv�t�@�C������e�[�u���̃J�����ݒ��ǉ�����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@exception FileIOException          csv�t�@�C���̓ǂݍ��݂Ɏ��s�����ꍇ�B
			@exception RuntimeException         �e�[�u���ɑ΂��铯���̃J������񂪊��ɑ��݂���ꍇ�B
			@param[in] $iTableName_   �e�[�u�����B
			@param[in] $iLstFilePath_ �e�[�u���̃J�����\�����L�q����csv�t�@�C���̃p�X�B
			@remarks   1�̃e�[�u���ɕ�����csv�t�@�C��������ꍇ�́A�e�t�@�C�����ɂ��̃��\�b�h���Ăяo���Ă��������B
		*/
		static function LoadColumn( $iTableName_ , $iLstFilePath_ )
		{
			Concept::IsString( $iTableName_ , $iLstFilePath_ )->OrThrow( 'InvalidArgument' , '�e�[�u�����܂��̓t�@�C���p�X�������ł�' );

			$fp = fopen( $iLstFilePath_ , 'rb' );

			Concept::IsResource( $fp )->OrThrow( 'FileIOException' , '�t�@�C�����J���܂���[' . $iLstFilePath_ . ']' );

			while( !feof( $fp ) )
			{
				$datas = fgetcsv( $fp );

				if( !is_array( $datas ) ) //�ǂݍ��݂Ɏ��s�����ꍇ
					{ continue; }

				if( 1 == count( $datas ) && is_null( $datas[ 0 ] ) ) //��s�������ꍇ
					{ continue; }

				foreach( $datas as &$data ) //�S�Ẵt�B�[���h����������
					{ $data = trim( $data ); }

				List( $name , $type , $maxSize , $registerCheck , $editcheck , $regex , $step , $replace ) = $datas;

				Concept::IsFalse( self::ExistsColumn( $iTableName_ , $name ) )->OrThrow( 'Logic' , '[' . $iTableName_ . ']�e�[�u���̃J�����ݒ�[' . $name . ']���d�����Ă��܂�' );

				$configs = Array();

				$configs[ 'type' ]          = $type;
				$configs[ 'size' ]          = $maxSize;
				$configs[ 'registercheck' ] = $registCheck;
				$configs[ 'editCheck' ]     = $editCheck;
				$configs[ 'regex' ]         = $regex;
				$configs[ 'step' ]          = $step;
				$configs[ 'replace' ]       = $replace;

				if( 0 >= $configs[ 'step' ] ) //�X�e�b�v�ݒ肪0�ȉ��̏ꍇ
					{ $configs[ 'step' ] = 0; }

				self::$Columns[ $iTableName_ ][ $name ] = $configs;
			}
		}

		/**
			@brief     �e�[�u���ݒ��ǉ�����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��͓����̃e�[�u���ݒ肪���ɑ��݂���ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@param[in] $iConfigs_   �e�[�u���̐ݒ�l���i�[�����A�z�z��B
				@li isUser               ���̃e�[�u�������[�U�[�ƔF�����A���O�C����������Ȃ�true�B
				@li loginIDColumn        ���O�C���Ɏg�p���郆�[�U�[���ʃJ�����B���[�U�[�e�[�u���łȂ��ꍇ�͖�������܂��B
				@li loginPassColumn      ���O�C���Ɏg�p����p�X���[�h�J�����B���[�U�[�e�[�u���łȂ��ꍇ�͖�������܂��B
				@li loginPassCheckColumn [�C��]�p�X���[�h�̓��͊m�F�Ɏg�p����J�����B���[�U�[�e�[�u���łȂ��ꍇ�͖�������܂��B
				@li lstFilePath          �e�[�u���̃J�����\�����L�q����csv�t�@�C���̃p�X�B���݂��Ȃ��t�@�C�����w�肷�邱�Ƃ͂ł��܂���B
				@li tdbFilePath          �e�[�u���̃��R�[�h�f�[�^���L�q����csv�t�@�C���̃p�X�B���݂��Ȃ��t�@�C�����w�肷�邱�Ƃ͂ł��܂���B
				@li idHeader             ���̃e�[�u���̃��R�[�hID�̓������B
				@li idLength             ���̃e�[�u���̃��R�[�hID�̓��������܂߂������B
				@li enableQuickLogin     [�C��]���̃e�[�u���̃N�C�b�N���O�C����������Ȃ�true�B���[�U�[�e�[�u���łȂ��ꍇ�͖�������܂��B
				@li registrableUsers     [�C��]���̃e�[�u����o�^�\�ȃ��[�U�[���X�g�B�z��Ŏw�肵�܂�
				@li editableUsers        [�C��]���̃e�[�u����ҏW�\�ȃ��[�U�[���X�g�B�z��Ŏw�肵�܂��B
				@li ownerMarks           [�C��]���̃e�[�u���̏��L��ID���i�[����J�������X�g�B���L�҃e�[�u����/�J�������̘A�z�z��Ŏw�肵�܂��B\n
				                         ���L�҂��w�肳��Ă���e�[�u����ID����v���郆�[�U�[�ɂ����ҏW�ł��Ȃ��Ȃ�܂��B
			@attention �ݒ荀�� loginPassCheckColumn �͏����I�ɔp�~�����\��ł��B
		*/
		static function RegisterTable( $iTableName_ , $iConfigHash_ )
		{
			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsArray( $iConfigHash_ )->OrThrow( 'InvalidArgument' );

			Concept::IsFalse( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			foreach( self::$RequestConfigTypes as $name => $type ) //���ʐݒ荀�ڂ̏���
				{ Concept::IsInType( $type , $iConfigHash_[ $name ] )->OrThrow( 'InvalidArgument' , '[' . $iTableName_ . '][' . $type . '][' . $name . '][' . $iConfigHash_[ $name ] . ']' ); }

			if( $iConfigHash_[ 'isUser' ] ) //���[�U�[�e�[�u���̏ꍇ
			{
				foreach( self::$RequestUserConfigTypes as $name => $type ) //���[�U�[��p�ݒ荀�ڂ̏���
					{ Concept::IsInType( $type , $iConfigHash_[ $name ] )->OrThrow( 'InvalidArgument' ); }
			}

			self::$Tables[ $iTableName_ ] = $iConfigHash_;
		}

		//�����X�g

		/**
			@brief  �e�[�u�����ꗗ���擾����B
			@return �e�[�u�������i�[�����z��B
		*/
		static function GetTableNames()
		{
			self::Initialize();

			return array_keys( self::$Tables );
		}

		//���e�[�u���ݒ�擾

		/**
			@brief     �e�[�u���ݒ肪���݂��邩�m�F����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@retval    true  �e�[�u���ݒ肪���݂���ꍇ�B
			@retval    false �e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
		*/
		static function ExistsTable( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );

			return array_key_exists( $iTableName_ , self::$Tables );
		}

		/**
			@brief     �e�[�u����ҏW�\�ȃ��[�U�[�ꗗ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �ҏW�\���[�U�[�����i�[�����z��B
		*/
		static function GetEditableUsers( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			if( is_array( self::$Tables[ $iTableName_ ][ 'editableUsers' ] ) ) //�z�񂪐ݒ肳��Ă���ꍇ
				{ return self::$Tables[ $iTableName_ ][ 'editableUsers' ]; }
			else //null���ݒ肳��Ă���ꍇ
				{ return Array(); }
		}

		/**
			@brief     �e�[�u���̃��R�[�hID�̐ړ��q���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    ���R�[�hID�̐ړ��q�B
		*/
		static function GetIDHeader( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'idHeader' ];
		}

		/**
			@brief     �e�[�u���̃��R�[�hID�̃T�C�Y���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    ���R�[�hID�̃T�C�Y�B
		*/
		static function GetIDLength( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'idLength' ];
		}

		/**
			@brief     ���O�C��ID�Ɏg�p����J���������擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �J�������B
		*/
		static function GetLoginIDColumn( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'loginIDColumn' ];
		}

		/**
			@brief     ���O�C���p�X���[�h���̓`�F�b�N�Ɏg�p����J���������擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �J�������B
		*/
		static function GetLoginPassCheckColumn( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'loginPassCheckColumn' ];
		}

		/**
			@brief     ���O�C���p�X���[�h�Ɏg�p����J���������擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �J�������B
		*/
		static function GetLoginPassColumn( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'loginPassColumn' ];
		}

		/**
			@brief     �e�[�u���\����`�t�@�C���̃p�X���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �t�@�C���̃p�X�B
		*/
		static function GetLstFilePath( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'lstFilePath' ];
		}

		/**
			@brief     �e�[�u���̏��L�҃`�F�b�N�ݒ�ꗗ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    ���L�҃��[�U�[����ID�i�[�J���������i�[�����A�z�z��B
		*/
		static function GetOwnerMarks( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			if( is_array( self::$Tables[ $iTableName_ ][ 'ownerMarks' ] ) ) //�z�񂪐ݒ肳��Ă���ꍇ
				{ return self::$Tables[ $iTableName_ ][ 'ownerMarks' ]; }
			else //null���ݒ肳��Ă���ꍇ
				{ return Array(); }
		}

		/**
			@brief     �e�[�u����o�^�\�ȃ��[�U�[�ꗗ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �o�^�\���[�U�[�����i�[�����z��B
		*/
		static function GetRegistrableUsers( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			if( is_array( self::$Tables[ $iTableName_ ][ 'registrableUsers' ] ) ) //�z�񂪐ݒ肳��Ă���ꍇ
				{ return self::$Tables[ $iTableName_ ][ 'registrableUsers' ]; }
			else //null���ݒ肳��Ă���ꍇ
				{ return Array(); }
		}

		/**
			@brief     ���R�[�h�f�[�^�t�@�C���̃p�X���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@return    �t�@�C���̃p�X�B
		*/
		static function GetTdbFilePath( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'tdbFilePath' ];
		}

		/**
			@brief     ���̃e�[�u�����N�C�b�N���O�C���������������m�F����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@retval    true  �e�[�u���̃N�C�b�N���O�C����������Ă���ꍇ�B
			@retval    false �e�[�u���̃N�C�b�N���O�C�����֎~����Ă���ꍇ�B
		*/
		static function IsEnableQuickLogin( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'isEnableQuickLogin' ];
		}

		/**
			@brief     ���̃e�[�u�������[�U�[�����������m�F����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���A�܂��̓e�[�u���ݒ肪���݂��Ȃ��ꍇ�B
			@param[in] $iTableName_ �e�[�u�����B
			@retval    true  �e�[�u�������[�U�[�e�[�u���̏ꍇ�B
			@retval    false �e�[�u�����f�[�^�e�[�u���̏ꍇ�B
		*/
		static function IsUser( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsTable( $iTableName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Tables[ $iTableName_ ][ 'isUser' ];
		}

		//���J�����ݒ�擾

		/**
			@brief     �e�[�u���ɃJ���������݂��邩�m�F����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@retval    true  �J���������݂���ꍇ�B
			@retval    false �J���������݂��Ȃ��ꍇ�B
		*/
		static function ExistsColumn( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );

			if( array_key_exists( $iTableName_ , self::$Columns ) ) //�J�����ݒ肪���݂���ꍇ
				{ return array_key_exists( $iColumnName_ , self::$Columns[ $iTableName_ ] ); }
			else //�J�����ݒ肪���݂��Ȃ��ꍇ
				{ return false; }
		}

		/**
			@brief     �J�����̕ҏW���`�F�b�N�ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    �ҏW���`�F�b�N�ݒ�B
		*/
		static function GetColumnEditCheck( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'editCheck' ];
		}

		/**
			@brief     �J�����̐��K�\���ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    ���K�\���ݒ�B
		*/
		static function GetColumnRegex( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'regex' ];
		}

		/**
			@brief     �J�����̓o�^���`�F�b�N�ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    �o�^���`�F�b�N�ݒ�B
		*/
		static function GetColumnRegiserCheck( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'registerCheck' ];
		}

		/**
			@brief     �J�����̒u���ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    �u���ݒ�B
		*/
		static function GetColumnReplace( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'replace' ];
		}

		/**
			@brief     �e�[�u���̃J�������ꗗ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@return    �J���������i�[�����z��B
		*/
		static function getColumns( $iTableName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ )->OrThrow( 'InvalidArgument' );

			if( array_key_exists( $iTableName_ , self::$Columns ) ) //�J�����ݒ肪���݂���ꍇ
				{ return array_keys( self::$Columns[ $iTableName_ ] ); }
			else //�J�����ݒ肪���݂��Ȃ��ꍇ
				{ return Array(); }
		}

		/**
			@brief     �J�����̃T�C�Y�ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    �T�C�Y�ݒ�B
		*/
		static function GetColumnSize( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'size' ];
		}

		/**
			@brief     �J�����̓o�^�X�e�b�v�ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    �o�^�X�e�b�v�ݒ�B
		*/
		static function GetColumnStep( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'step' ];
		}

		/**
			@brief     �J�����̌^�ݒ���擾����B
			@exception InvalidArgumentException �����ɕs���Ȓl���w�肵���ꍇ�B
			@param[in] $iTableName_  �e�[�u�����B
			@param[in] $iColumnName_ �J�������B
			@return    �^�ݒ�B
		*/
		static function GetColumnType( $iTableName_ , $iColumnName_ )
		{
			self::Initialize();

			Concept::IsString( $iTableName_ , $iColumnName_ )->OrThrow( 'InvalidArgument' );
			Concept::IsTrue( self::ExistsColumn( $iTableName_ , $iColumnName_ ) )->OrThrow( 'InvalidArgument' );

			return self::$Columns[ $iTableName_ ][ $iColumnName_ ][ 'type' ];
		}

		//������

		/**
			@brief     �O���[�o���ϐ�����ݒ�l���C���|�[�g����B
			@attention �ڍs����������܂ł̉��@�\�ł��B
		*/
		static function ImportGlobalVarConfigs()
		{
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $LOGIN_KEY_COLUM;
			global $LOGIN_PASSWD_COLUM;
			global $LOGIN_PASSWD_COLUM2;
			global $LST;
			global $TDB;
			global $ID_HEADER;
			global $ID_LENGTH;
			global $THIS_TABLE_IS_QUICK;
			global $THIS_TABLE_REGIST_USER;
			global $THIS_TABLE_EDIT_USER;
			global $THIS_TABLE_OWNER_COLUM;
			global $ADD_LST;

			foreach( $TABLE_NAME as $table ) //�S�Ẵe�[�u��������
			{
				$configs = Array();

				$configs[ 'isUser' ] = $THIS_TABLE_IS_USERDATA[ $table ];

				if( $configs[ 'isUser' ] ) //���[�U�[�e�[�u���̏ꍇ
				{
					$configs[ 'loginIDColumn' ]        = $LOGIN_KEY_COLUM[ $table ];
					$configs[ 'loginPassColumn' ]      = $LOGIN_PASSWD_COLUM[ $table ];
					$configs[ 'loginPassCheckColumn' ] = $LOGIN_PASSWD_COLUM2[ $table ];
				}

				$configs[ 'lstFilePath' ]      = $LST[ $table ];
				$configs[ 'tdbFilePath' ]      = $TDB[ $table ];
				$configs[ 'idHeader' ]         = $ID_HEADER[ $table ];
				$configs[ 'idLength' ]         = $ID_LENGTH[ $table ];
				$configs[ 'enableQuickLogin' ] = $THIS_TABLE_IS_QUICK[ $table ];
				$configs[ 'registrableUsers' ] = $THIS_TABLE_REGIST_USER[ $table ];
				$configs[ 'editableUsers' ]    = $THIS_TABLE_EDIT_USER[ $table ];
				$configs[ 'ownerMarks' ]       = $THIS_TABLE_OWNER_COLUM[ $table ];

				self::RegisterTable( $table , $configs );

				self::LoadColumn( $table , $LST[ $table ] );

				if( is_array( $ADD_LST ) ) //�ǉ��J�����ݒ肪���݂���ꍇ
				{
					if( array_key_exists( $table , $ADD_LST ) ) //�ǉ��J�������ݒ肳��Ă���ꍇ
					{
						foreach( $ADD_LST[ $table ] as $lst ) //�ǉ��J����������
							{ self::LoadColumn( $table , $lst ); }
					}
				}
			}
		}

		//���ϐ�
		static private $Initialized = false;   ///<�������t���O�B
		static private $Tables      = Array(); ///<�e�[�u���ݒ�i�[�z��B
		static private $Columns     = Array(); ///<�J�����ݒ�i�[�z��B

		static private $RequestConfigTypes = ///<�e�[�u���̐ݒ荀�ڂ��Ƃ̌^
		Array(
			'isUser'           => 'bool' ,
			'lstFilePath'      => 'string' ,
			'tdbFilePath'      => 'string' ,
			'idHeader'         => 'string' ,
			'idLength'         => 'numeric' ,
			'registrableUsers' => 'array/null' ,
			'editableUsers'    => 'array/null' ,
			'ownerMarks'       => 'array/null'
		);

		static private $RequestUserConfigTypes = ///<���[�U�[�e�[�u���̐ݒ荀�ڂ��Ƃ̌^
		Array(
			'enableQuickLogin'     => 'bool/null' ,
			'loginIDColumn'        => 'string' ,
			'loginPassColumn'      => 'string' ,
			'loginPassCheckColumn' => 'string/null'
		);
	}
?>