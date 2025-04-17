<?php

	/**
		@brief   �p�b�P�[�W�ݒ�Ǘ��N���X�B
		@details �p�b�P�[�W���L�̐ݒ�����Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup PackageInformation
	*/
	class PackageInfo
	{
		/**
			@brief  �ۋ��ݒ���擾����B
			@return �ۋ��ݒ�B
		*/
		function getFeeStyle()
			{ return WS::Info( 'system' )->getParam( 'fee_style' ); }

		/**
			@brief  ��z���̗��p�������擾����B
			@return ���p�����B
		*/
		function getLimitIntervalMonth()
			{ return WS::Info( 'system' )->getParam( 'limit_interval_month' ); }

		/**
			@brief  ���������̉{���R�X�g���擾����B
			@return ���������̉{���R�X�g�B
		*/
		function getRequestViewCost()
			{ return WS::Info( 'system' )->getParam( 'request_view_cost' ); }
	}

?>
