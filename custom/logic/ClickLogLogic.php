<?php

	class click_logLogic //
	{
		//������ //

		/**
			@brief         �L���̃N���b�N������ǉ����܂��B
			@param[in,out] $ioRec_      �N���b�N���O�̃��R�[�h�f�[�^�B
			@param[in]     $iAdwaresID_ �ǉ�����L����ID�B
			@retval        true  ���R�[�h�f�[�^���ύX���ꂽ�ꍇ�B
			@retval        false ���R�[�h�f�[�^�ɕύX���Ȃ��ꍇ�B
		*/
		function setClickAdwaresLog( &$ioRec_ , $iAdwaresID_ ) //
		{
			$db = GMList::getDB( 'click_log' );

			$clickIDs = $this->db->getData( $ioRec_ , 'click_adwares_id_list' );
			$clickIDs = explode( '/' , $clickIDs );

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //ID���܂܂�Ă��Ȃ��ꍇ
			{
				$clickIDs[] = $iAdwaresID_;
				$clickIDs   = implode( '/' , $clickIDs );

				$db->setData( $ioRec_ , 'click_adwares_id_list' , $clickIDs );

				return true;
			}

			return false;
		}

		/**
			@brief         �L���̐��ʔ���������ǉ����܂��B
			@param[in,out] $ioRec_      �N���b�N���O�̃��R�[�h�f�[�^�B
			@param[in]     $iAdwaresID_ �ǉ�����L����ID�B
			@retval        true  ���R�[�h�f�[�^���ύX���ꂽ�ꍇ�B
			@retval        false ���R�[�h�f�[�^�ɕύX���Ȃ��ꍇ�B
		*/
		function setPayAdwaresLog( &$ioRec_ , $iAdwaresID_ ) //
		{
			$db = GMList::getDB( 'click_log' );

			$clickIDs = $this->db->getData( $ioRec_ , 'pay_adwares_id_list' );
			$clickIDs = explode( '/' , $clickIDs );

			if( !in_array( $iAdwaresID_ , $clickIDs ) ) //ID���܂܂�Ă��Ȃ��ꍇ
			{
				$clickIDs[] = $iAdwaresID_;
				$clickIDs   = implode( '/' , $clickIDs );

				$db->setData( $ioRec_ , 'pay_adwares_id_list' , $clickIDs );

				return true;
			}

			return false;
		}
	}
