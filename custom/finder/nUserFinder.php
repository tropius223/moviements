<?php

	class  nUserFinder extends baseFinder //
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
			{ return $iTable_; }

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

		function extraSearchThisMonth( $iTable_ , $iValue_ , $iQuery_ )
		{
			if( 'true' == $iValue_ )
				{ $iTable_ = $this->db->searchTable( $iTable_ , 'regist' , '>=' , mktime( 0 , 0 , 0 , date( 'n' ) , 1 , date( 'Y' ) ) ); }

			return $iTable_;
		}

		function extraSearchPreviousMonth( $iTable_ , $iValue_ , $iQuery_ )
		{
			if( 'true' == $iValue_ )
			{
				$iTable_ = $this->db->searchTable( $iTable_ , 'regist' , '>=' , mktime( 0 , 0 , 0 , date( 'n' ) - 1 , 1 , date( 'Y' ) ) );
				$iTable_ = $this->db->searchTable( $iTable_ , 'regist' , '<' , mktime( 0 , 0 , 0 , date( 'n' ) , 1 , date( 'Y' ) ) );
			}

			return $iTable_;
		}
	}
