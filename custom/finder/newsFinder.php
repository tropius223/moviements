<?php

	//���N���X //

	class newsFinder extends baseFinder //
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
		{
			$iTable_ = $this->db->sortTable( $iTable_ , 'open_time' , 'desc' , true );
			$iTable_ = $this->db->sortTable( $iTable_ , 'regist' , 'desc' , true );

			return $iTable_;
		}

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
			global $ACTIVE_ACCEPT;

			switch( $iUserType_ ) //���[�U�[��ʂŕ���
			{
				case 'admin' : //�Ǘ���
					{ return $iTable_; }

				default : //���̑�
				{
					$iTable_ = $this->db->searchTable( $iTable_ , 'open_time' , '<' , time() );
					$iTable_ = $this->db->searchTable( $iTable_ , 'activate' , '=' , $ACTIVE_ACCEPT );

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
			$iTable_ = $this->db->sortTable( $iTable_ , 'open_time' , 'desc' );
			$iTable_ = $this->db->sortTable( $iTable_ , 'regist' , 'desc' , true );

			return $iTable_;
		}
	}
