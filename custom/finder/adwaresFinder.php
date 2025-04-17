<?php

	//���N���X //

	class adwaresFinder extends baseFinder //
	{
		//������ //

		/**
			@brief     �N�G���p�����[�^���g���ă��R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iQuery_    �N�G���p�����[�^�B
			@param[in] $iUserType_ ���[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B
			@retval    ������̃e�[�u�� ��������������ɍs��ꂽ�ꍇ�B
			@retval    null             ��������������ɍs���Ȃ������ꍇ�B
		*/
		function searchQueryTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ) //
			{ return $iTable_; }

		/**
			@brief     �Q�Ɖ\�ȃ��R�[�h����������B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iUsertype_ ���[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B
			@retval    ������̃e�[�u�� ��������������ɍs��ꂽ�ꍇ�B
			@retval    null             ��������������ɍs���Ȃ������ꍇ�B
		*/
		function searchReadableTableProc( $iTable_ , $iUserType_ , $iUserID_ ) //
		{
			switch( $iUserType_ ) //���[�U�[��ʂŕ���
			{
				case 'admin' : //�Ǘ���
					{ return $iTable_; }

				default : //���̑�
				{
					$tableA = $this->db->searchTable( $iTable_ , 'use_limit_time' , '=' , true );
					$tableA = $this->db->searchTable( $tableA , 'limit_time' , '>' , time() );

					$tableB = $this->db->searchTable( $iTable_ , 'use_limit_time' , '=' , false );

					$iTable_ = $this->db->orTable( $tableA , $tableB );

					$iTable_ = $this->db->searchTable( $iTable_ , 'open' , '=' , true );

					return $iTable_;
				}
			}
		}

		/**
			@brief     �e�[�u�����\�[�g����B
			@param[in] $iTable_    �����x�[�X�Ƃ���e�[�u���B
			@param[in] $iQuery_    �N�G���p�����[�^�B
			@param[in] $iUsertype_ ���[�U�[��ʁB
			@param[in] $iUserID_   ���[�U�[ID�B
			@return    �\�[�g��̃e�[�u���B
		*/
		function sortTableProc( $iTable_ , $iQuery_ , $iUserType_ , $iUserID_ ) //
		{
			$iTable_ = $this->db->sortTable( $iTable_ , 'regist' , 'desc' , true );

			return $iTable_;
		}

		function searchPCTable( $iTable_ ) //
		{
			$table = $this->db->searchTable( $iTable_ , 'use_carrier_url' , '=' , false );

			return $table;
		}

		function extraSearchUseable( $iTable_ , $iValue_ , $iQuery_ )
		{
			global $LOGIN_ID;

			if( is_array( $iValue_ ) && 'true' == $iValue_[ 0 ] )
			{
				$clickLogDB    = GMList::getDB( 'click_log' );
				$clickLogTable = $clickLogDB->getTable();
				$clickLogTable = $clickLogDB->searchTable( $clickLogTable , 'nuser_id' , '=' , $LOGIN_ID );
				$clickLogTable = $clickLogDB->limitOffset( $clickLogTable , 0 , 1 );
				$clickLogRec   = $clickLogDB->getRecord( $clickLogTable , 0 );
				$payAdwaresID  = $clickLogDB->getData( $clickLogRec , 'pay_adwares_id_list' );
				$payAdwaresIDs = explode( '/' , $payAdwaresID );

				$iTable_ = $this->db->searchTable( $iTable_ , 'id' , 'not in' , $payAdwaresIDs );
			}

			return $iTable_;
		}
	}
