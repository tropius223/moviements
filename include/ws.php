<?php

	//���N���X //

	/**
		@brief   �V�X�e���֐��N���X�B
		@details �V�X�e���̑S�̂Ŏg�p����჌�x���̊֐����������邽�߂̃N���X�ł��B
		@author  matsuki
		@version 1.0.0
		@ingroup SystemComponent
	*/
	class WS extends magics //
	{
		//������ //

		/**
			@brief     �N���X��`�t�@�C�������[�h����B
			@details   �N���X������N���X��`�t�@�C���𐄑����ă��[�h���܂��B
			@exception InvalidArgumentException $iClassName_ �ɋ󕶎�����w�肵���ꍇ�B
			@exception IllegalAccessException   �s���ȃN���X�����w�肵���ꍇ�B
			@exception LogicException           �N���X��`�t�@�C����������Ȃ��A�܂��̓t�@�C�������[�h���Ă��N���X����`����Ȃ������ꍇ�B
			@param[in] $iClassName_ �N���X���B
			@remarks   $iClassName_ ����`�ς݂ł���ꍇ�́A���������ɕԂ�܂��B
		*/
		static function DefClass( $iClassName_ ) //
		{
			if( !$iClassName_ ) //�N���X������̏ꍇ
				{ throw new InvalidArgumentException( '���� $iClassName_ �͖����ł�' ); }

			//�f�B���N�g���g���o�[�T�����o
			if( preg_match( '/\W/' , $iClassName_ ) ) //�p�����ȊO�̕������܂܂��ꍇ
				{ throw new IllegalAccessException( '�s���ȃA�N�Z�X�ł�[' . $iClassName_ . ']' ); }

			if( class_exists( $iClassName_ , false ) ) //���ɃN���X����`����Ă���ꍇ
				{ return; }

			$filePath = '';

			//�L�������P�[�X�̍Ō�̒Ԃ肩��R���|�[�l���g�^�C�v�𐄑�����
			$isMatch = preg_match( '/(.+)([A-Z][a-z0-9_]+)$/' , $iClassName_ , $match );

			if( $isMatch ) //�R���|�[�l���g�^�C�v�Ɛ��������Ԃ肪���������ꍇ
			{
				$className     = $match[ 1 ];
				$componentType = $match[ 2 ];

				$filePath = self::TryGetComponentFilePath( $componentType , $className );
			}

			//������Ȃ���Βʏ�N���X��T��
			if( !$filePath ) //�t�@�C���p�X����̏ꍇ
				{ $filePath = self::TryGetIncludePath( $iClassName_ ); }

			if( !$filePath ) //�t�@�C���p�X����̏ꍇ
				{ throw new LogicException( 'DefClass �������ł��܂���[' . $iClassName_ . ']' ); }

			if( !file_exists( $filePath ) ) //�t�@�C����������Ȃ��ꍇ
				{ throw new LogicException( 'DefClass �������ł��܂���[' . $iClassName_ . '][' . $filePath . ']' ); }

			include_once $filePath;

			if( !class_exists( $iClassName_ , false ) ) //���[�h���Ă��N���X����`����Ȃ������ꍇ
				{ throw new LogicException( 'DefClass �������ł��܂���[' . $iClassName_ . '][' . $filePath . ']' ); }
		}

		//���f�[�^�擾 //

		/**
			@brief     �R���|�[�l���g�I�u�W�F�N�g���擾����B
			@param[in] $iComponentType_ �R���|�[�l���g�^�C�v�B
			@param[in] $iName_          �R���|�[�l���g�̖��O�B
			@exception $iCComponentType_ , $iName_ �ɋ󕶎�����w�肵���ꍇ�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static private function GetComponent( $iComponentType_ , $iName_ ) //
		{
			if( !$iComponentType_ ) //�R���|�[�l���g�^�C�v����̏ꍇ
				{ throw new InvalidArgumentException( '���� $iComponentType_ �͖����ł�[ ' . $iName_ . ' ]' ); }

			if( !$iName_ ) //���O����̏ꍇ
				{ throw new InvalidArgumentException( '���� $iName_ �͖����ł�[ ' . $iComponentType_ . ' ]' ); }

			if( !array_key_exists( $iComponentType_ , self::$ComponentCaches ) ) //�R���|�[�l���g�^�C�v�̔z�񂪑��݂��Ȃ��ꍇ
				{ self::$ComponentCaches[ $iComponentType_ ] = Array(); }

			if( !array_key_exists( $iName_ , self::$ComponentCaches[ $iComponentType_ ] ) ) //�R���|�[�l���g�����݂��Ȃ��ꍇ
			{
				$componentName                                        = $iName_ . $iComponentType_;
				self::$ComponentCaches[ $iComponentType_ ][ $iName_ ] = new $componentName;
			}

			return self::$ComponentCaches[ $iComponentType_ ][ $iName_ ];
		}

		static function HasComponent( $iName_ ) //
		{
			try
			{
				self::DefClass( $iName_ );

				return true;
			}
			catch( Exception $e )
				{ return false; }
		}

		/**
			@brief     �R���|�[�l���g�t�@�C���̃C���N���[�h�p�X���擾����B
			@param[in] $iComponentType_ �R���|�[�l���g�^�C�v�B
			@param[in] $iName_          �R���|�[�l���g���B
			@retval    �t�@�C���p�X �R���|�[�l���g�^�C�v�����������ꍇ�B
			@retval    null         �R���|�[�l���g�^�C�v���o�^����Ă��Ȃ��ꍇ�B
		*/
		static private function TryGetComponentFilePath( $iComponentType_ , $iName_ ) //
		{
			if( array_key_exists( $iComponentType_ , self::$ComponentPathConfigs ) ) //�R���|�[�l���g�ݒ�z��ɃL�[�����݂���ꍇ
				{ $originPath = self::$ComponentPathConfigs[ $iComponentType_ ]; }
			else //�R���|�[�l���g�^�C�v�ɑΉ�����ݒ肪�o�^����Ă��Ȃ��ꍇ
				{ return null; }

			//�ʏ�p�X
			$includePath = str_replace( '@' , $iName_ , $originPath );

			if( file_exists( $includePath ) ) //�t�@�C�������������ꍇ
				{ return $includePath; }

			//�擪�啶��
			$includePath = str_replace( '@' , ucfirst( $iName_ ) , $originPath );

			if( file_exists( $includePath ) ) //�t�@�C�������������ꍇ
				{ return $includePath; }

			//�擪������
			$includePath = str_replace( '@' , lcfirst( $iName_ ) , $originPath );

			if( file_exists( $includePath ) ) //�t�@�C�������������ꍇ
				{ return $includePath; }

			return null;
		}

		/**
			@brief     �N���X�t�@�C���̃C���N���[�h�p�X���擾����B
			@param[in] $iClassName_ �N���X���B
			@retval    �t�@�C���p�X �N���X�t�@�C�������������ꍇ�B
			@retval    null         �N���X�t�@�C�����o�^����Ă��Ȃ��ꍇ�B
		*/
		static private function TryGetIncludePath( $iClassName_ ) //
		{
			foreach( self::$ClassPathConfigs as $originPath ) //php�t�@�C��������
			{
				//�ʏ�p�X
				$includePath = str_replace( '@' , $iClassName_ , $originPath );

				if( file_exists( $includePath ) ) //�t�@�C�������������ꍇ
					{ return $includePath; }

				//�擪�啶��
				$includePath = str_replace( '@' , ucfirst( $iClassName_ ) , $originPath );

				if( file_exists( $includePath ) ) //�t�@�C�������������ꍇ
					{ return $includePath; }

				//�擪������
				$includePath = str_replace( '@' , lcfirst( $iClassName_ ) , $originPath );

				if( file_exists( $includePath ) ) //�t�@�C�������������ꍇ
					{ return $includePath; }
			}

			return null;
		}

		//������ //

		/**
			@brief     Drawer�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Drawer( $iName_ )
			{ return self::GetComponent( 'Drawer' , $iName_ ); }

		/**
			@brief     Finder�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Finder( $iName_ )
			{ return self::GetComponent( 'Finder' , $iName_ ); }

		/**
			@brief     Info�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Info( $iName_ )
			{ return self::GetComponent( 'Info' , $iName_ ); }

		/**
			@brief     Logic�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Logic( $iName_ )
			{ return self::GetComponent( 'Logic' , $iName_ ); }

		/**
			@brief     Model�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Model( $iName_ )
			{ return self::GetComponent( 'Model' , $iName_ ); }

		/**
			@brief     System�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function System( $iName_ )
			{ return self::GetComponent( 'System' , $iName_ ); }

		/**
			@brief     Tester�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Tester( $iName_ )
			{ return self::GetComponent( 'Tester' , $iName_ ); }

		/**
			@brief     Util�R���|�[�l���g���擾����B
			@param[in] �R���|�[�l���g�̖��O�B
			@return    �R���|�[�l���g�I�u�W�F�N�g�B
		*/
		static function Util( $iName_ )
			{ return self::GetComponent( 'Util' , $iName_ ); }

		//���ϐ� //
		static private $ComponentCaches = Array(); ///<�����ς݂̃R���|�[�l���g�I�u�W�F�N�g�̃L���b�V���i�[�z��

		static private $ComponentPathConfigs = Array ///<�R���|�[�l���g�̎�ނƃC���N���[�h�p�X�ݒ�z��
		(
			'Drawer' => 'custom/drawer/@Drawer.php' ,
			'Finder' => 'custom/finder/@Finder.php' ,
			'Info'   => 'custom/info/@Info.php' ,
			'Logic'  => 'custom/logic/@Logic.php' ,
			'Model'  => 'custom/model/@Model.php' ,
			'System' => 'custom/system/@System.php' ,
			'Tester' => 'custom/tester/@Tester.php' ,
			'Util'   => 'include/util/@Util.php' ,
		);

		static private $ClassPathConfigs = Array ///<�N���X�̃C���N���[�h�p�X�ݒ�z��
		(
			'include/@.php' ,
			'include/base@.php' ,
			'include/extends@.php' ,
			'custom/@.php' ,
			'custom/api@.php' ,
			'custom/cron@.php' ,
			'module/@.inc' ,
			'module/command@.inc' ,
		);
	}
