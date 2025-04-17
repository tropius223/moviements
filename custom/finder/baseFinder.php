<?php

	//���N���X //

	abstract class  BaseFinder //
	{
		//������ //

		/**
			@brief     �N�G���p�����[�^���g���ă��R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B�ȗ����͋�̃e�[�u���B
			@param[in] $iQuery_    �N�G���p�����[�^�B�ȗ�����GET�z��B
			@param[in] $iUsertype_ ���[�U�[��ʁB�ȗ����͌��݂̃��[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B�ȗ����͌��݂̃��[�U�[ID�B
			@return    ������̃e�[�u���B
		*/
		function searchQueryTable( $iTable_ = null , $iQuery_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //�e�[�u�����w�肳��Ă��Ȃ��ꍇ
				{ $iTable_ = $this->db->getTable(); }

			if( !$iQuery_ ) //�N�G���p�����[�^���w�肳��Ă��Ȃ��ꍇ
				{ $iQuery_ = $_GET; }

			if( !$iUserType_ ) //���[�U�[��ʂ��w�肳��Ă��Ȃ��ꍇ
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //���[�U�[ID���w�肳��Ă��Ȃ��ꍇ
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			$addTable = $this->db->getTable();
			$addTable = $this->searchQueryTableProc( $addTable , $iQuery_ , $iUserType_ , $iUserID_ );

			$iTable_ = $this->db->andTable( $iTable_ , $addTable );

			foreach( $iQuery_ as $name => $value ) //�S�ẴN�G���p�����[�^�������B
			{
				if( '_' != substr( $name , 0 , 1 ) ) //�p�����[�^���̊J�n�������n�C�t���ł͂Ȃ��ꍇ
					{ continue; }

				$extraMethodName = 'extraSearch' . ucfirst( substr( $name , 1 ) );

				if( method_exists( $this , $extraMethodName ) ) //�Ή����郁�\�b�h������ꍇ
				{
					$addTable = $this->db->getTable();
					$addTable = $this->{$extraMethodName}( $addTable , $value , $iQuery_ );

					$iTable_ = $this->db->andTable( $iTable_ , $addTable );
				}
			}

			return $iTable_;
		}

		/**
			@brief     �Q�Ɖ\�ȃ��R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B�ȗ����͋�̃e�[�u���B
			@param[in] $iUsertype_ ���[�U�[��ʁB�ȗ����͌��݂̃��[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B�ȗ����͌��݂̃��[�U�[ID�B
			@return    ������̃e�[�u���B
		*/
		function searchReadableTable( $iTable_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //�e�[�u�����w�肳��Ă��Ȃ��ꍇ
				{ $iTable_ = $this->db->getTable(); }

			if( !$iUserType_ ) //���[�U�[��ʂ��w�肳��Ă��Ȃ��ꍇ
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //���[�U�[ID���w�肳��Ă��Ȃ��ꍇ
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			$addTable = $this->db->getTable();
			$addTable = $this->searchReadableTableProc( $addTable , $iUserType_ , $iUserID_ );

			$iTable_ = $this->db->andTable( $iTable_ , $addTable );

			return $iTable_;
		}

		/**
			@brief     �e�[�u�����\�[�g����B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iQuery_    �N�G���p�����[�^�B�ȗ�����GET�z��B
			@param[in] $iUsertype_ ���[�U�[��ʁB�ȗ����͌��݂̃��[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B�ȗ����͌��݂̃��[�U�[ID�B
			@return    �\�[�g��̃e�[�u���B
		*/
		function sortTable( $iTable_ , $iQuery_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //�e�[�u�����w�肳��Ă��Ȃ��ꍇ
				{ $iTable_ = $this->db->getTable(); }

			if( !$iQuery_ ) //�N�G���p�����[�^���w�肳��Ă��Ȃ��ꍇ
				{ $iQuery_ = $_GET; }

			if( !$iUserType_ ) //���[�U�[��ʂ��w�肳��Ă��Ȃ��ꍇ
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //���[�U�[ID���w�肳��Ă��Ȃ��ꍇ
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			return $this->sortTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ );
		}

		/**
			@brief     ���L���̂��郌�R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B�ȗ����͋�̃e�[�u���B
			@param[in] $iUsertype_ ���[�U�[��ʁB�ȗ����͌��݂̃��[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B�ȗ����͌��݂̃��[�U�[ID�B
			@return    ������̃e�[�u���B
		*/
		function searchMineTable( $iTable_ = null , $iUserType_ = null , $iUserID_ = null ) //
		{
			if( !$iTable_ ) //�e�[�u�����w�肳��Ă��Ȃ��ꍇ
				{ $iTable_ = $this->db->getTable(); }

			if( !$iUserType_ ) //���[�U�[��ʂ��w�肳��Ă��Ȃ��ꍇ
				{ $iUserType_ = WS::Info( 'User' )->getType(); }

			if( !$iUserID_ ) //���[�U�[ID���w�肳��Ă��Ȃ��ꍇ
				{ $iUserID_ = WS::Info( 'User' )->getID(); }

			switch( $iUserType_ ) //���[�U�[��ʂŕ���
			{
				case 'admin' : //�Ǘ���
					{ return $iTable_; }

				default : //���̑�
				{
					$ownerMarks = WS::Info( 'table' )->GetOwnerMarks( $this->getType() );

					if( !array_key_exists( $iUserType_ , $ownerMarks ) ) //���L�҃J�����ݒ肪�Ȃ��ꍇ
						{ return $this->getEmptyTable(); }

					$iTable_ = $this->db->searchTable( $iTable_ , $ownerMarks[ $iUserType_ ] , '=' , $iUserID_ );

					return $iTable_;
				}
			}
		}

		/**
			@brief     �^�C���X�^���v���烌�R�[�h����������B
			@param[in] $iTable_  �����x�[�X�Ƃ���e�[�u���B�ȗ����͋�̃e�[�u���B
			@param[in] $iColumn_ ��������J�������B
			@param[in] $iBegins_ �J�n�����̃^�C���X�^���v�܂��͓��t�̔z��(y/m/d/h/i/s)
			@param[in] $iEnds_   �I�������̃^�C���X�^���v�܂��͓��t�̔z��(y/m/d/h/i/s)
			@return    ������̃e�[�u���B
		*/
		function searchPeriodTable( $iTable_ = null , $iColumn_ , $iBegins_ , $iEnds_ ) //
		{
			if( !$iTable_ ) //�e�[�u�����w�肳��Ă��Ȃ��ꍇ
				{ $iTable_ = $this->db->getTable(); }

			$hasBegin = ( is_array( $iBegins_ ) ? $iBegins_[ 0 ] : $iBegins_ );
			$hasEnd   = ( is_array( $iEnds_ )   ? $iEnds_[ 0 ]   : $iEnds_ );

			if( !$hasBegin && !$hasEnd ) //�J�n�����ƏI�����������ɖ����̏ꍇ
				{ return $iTable_; }

			if( !$iColumn_ ) //�J����������̏ꍇ
				{ return $iTable_; }

			if( is_array( $iBegins_ ) ) //�J�n�������z��Ŏw�肳��Ă���ꍇ
			{
				List( $year , $month , $day , $hour , $min , $sec ) = $iBegins_;

				$beginTime = mktime( $hour , $min , $sec , $month , $day , $year );
			}
			else //�J�n�������^�C���X�^���v�Ŏw�肳��Ă���ꍇ
				{ $beginTime = $iBegins_; }

			if( is_array( $iEnds_ ) ) //�I���������z��Ŏw�肳��Ă���ꍇ
			{
				List( $year , $month , $day , $hour , $min , $sec ) = $iEnds_;

				$endTime = mktime( $hour , $min , $sec , $month , $day + 1 , $year );
			}
			else //�I���������^�C���X�^���v�Ŏw�肳��Ă���ꍇ
				{ $endTime = $iEnds_; }

			$addTable = $this->db->getTable();

			if( $beginTime ) //�J�n�������L���ȏꍇ
			{
				if( $endTime ) //�I���������L���ȏꍇ
					{ $addTable = $this->db->searchTable( $addTable , $iColumn_ , 'b' , $beginTime , $endTime ); }
				else //�I�������������ȏꍇ
					{ $addTable = $this->db->searchTable( $addTable , $iColumn_ , '>' , $beginTime ); }
			}
			else //�J�n�����������ȏꍇ
				{ $addTable = $this->db->searchTable( $addTable , $iColumn_ , '<' , $endTime ); }

			$iTable_ = $this->db->andTable( $iTable_ , $addTable );

			return $iTable_;
		}

		/**
			@brief     �ł��邾���d�����Ȃ��悤��or��������B
			@param[in] $iTables_ ��������e�[�u���z��B
			@return    ������̃e�[�u���B
		*/
		function joinTableOr( $iTables_ ) //
		{
			if( !is_array( $iTables_ ) ) //�z��ł͂Ȃ��ꍇ
				{ return null; }

			$arraySize = count( $iTables_ );

			switch( $arraySize ) //�z��T�C�Y�ŕ���
			{
				case 0 : //��
					{ return null; }

				case 1 : //1����
					{ return $iTables_[ 0 ]; }

				default : //���̑�
				{
					$splitPosition = ( int )( count( $iTables_ ) / 2 );

					$lhs = array_slice( $iTables_ , 0 , $splitPosition );
					$rhs = array_slice( $iTables_ , $splitPosition );

					$leftTable  = $this->joinTableOr( $lhs );
					$rightTable = $this->joinTableOr( $rhs );
					$table      = $this->db->orTable( $leftTable , $rightTable );

					return $table;
				}
			}
		}

		//���f�[�^�擾 //

		/**
			@brief  ���̃N���X����������e�[�u�������擾����B
			@return �e�[�u�����B
		*/
		function getType() //
			{ return $this->tableName; }

		/**
			@brief  �q�b�g���Ȃ��������������e�[�u�����擾����B
			@return 0���̃e�[�u���B
		*/
		function getEmptyTable() //
		{
			$table = $this->db->getTable();
			$table = $this->db->searchTable( $table , 'shadow_id' , '<' , '0' );

			return $table;
		}

		/**
			@brief     Search�N���X�ɂ�錟���ς݂̃e�[�u�����擾����B
			@param[in] $iQuery_ �N�G���p�����[�^�B�ȗ�����GET�z��B
		*/
		function getSearcherTable( $iQuery_ = null )
		{
			if( !$iQuery_ ) //�N�G���p�����[�^���w�肳��Ă��Ȃ��ꍇ
				{ $iQuery_ = $_GET; }

			$searcher = new Search( $this->gm , $this->getType() );

			if( ini_get( 'magic_quotes_gpc' ) ) //magic_quotes���L���ȏꍇ
				{ $searcher->setParamertorSet( $iQuery_ ); }
			else //magic_quotes�������ȏꍇ
				{ $searcher->setParamertorSet( addslashes_deep( $iQuery_ ) ); }

			return $searcher->getResult();
		}

		//�����z //

		/**
			@brief     �N�G���p�����[�^���g���ă��R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iQuery_    �N�G���p�����[�^�B
			@param[in] $iUsertype_ ���[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B
			@return    ������̃e�[�u���B
		*/
		abstract function searchQueryTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ); //

		/**
			@brief     �Q�Ɖ\�ȃ��R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iUsertype_ ���[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B
			@return    ������̃e�[�u���B
		*/
		abstract function searchReadableTableProc( $iTable_ , $iUserType_ , $iUserID_ ); //

		/**
			@brief     �e�[�u�����\�[�g����B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iQuery_    �N�G���p�����[�^�B
			@param[in] $iUsertype_ ���[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B
			@return    �\�[�g��̃e�[�u���B
		*/
		abstract function sortTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ); //

		//���R���X�g���N�^�E�f�X�g���N�^ //

		/**
			@brief �R���X�g���N�^�B
		*/
		function __construct()
		{
			global $LST;

			$className = get_class( $this );
			$isMatch   = preg_match( '/(.*)Finder$/' , $className , $matches );

			if( !$isMatch ) //�p�^�[���Ƀ}�b�`���Ȃ������ꍇ
				{ throw new LogicException( '�R���X�g���N�^�������ł��܂���[' . $className . ']' ); }

			$this->tableName = $matches[ 1 ];

			if( !array_key_exists( $this->tableName , $LST ) ) //�J�����ݒ�t�@�C����������Ȃ��ꍇ
				{ $this->tableName[ 0 ] = strtoupper( $this->tableName[ 0 ] ); }

			if( !array_key_exists( $this->tableName , $LST ) ) //�J�����ݒ�t�@�C����������Ȃ��ꍇ
				{ $this->tableName[ 0 ] = strtolower( $this->tableName[ 0 ] ); }

			if( !array_key_exists( $this->tableName , $LST ) ) //�J�����ݒ�t�@�C����������Ȃ��ꍇ
				{ throw new LogicException( '�R���X�g���N�^�������ł��܂���[' . $className . ']' ); }

			$this->gm = GMList::getGM( $this->tableName );
			$this->db = $this->gm->getDB();
		}

		//���ϐ� //
		private   $tableName = '';   ///<���̃N���X����������e�[�u�����B
		protected $gm        = null; ///<�e�[�u����GUIManager�I�u�W�F�N�g�B
		protected $db        = null; ///<�e�[�u����Database�I�u�W�F�N�g�B
	}
