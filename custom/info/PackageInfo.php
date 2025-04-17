<?php

	/**
		@brief   パッケージ設定管理クラス。
		@details パッケージ特有の設定情報を管理します。
		@author  松木 昌平
		@version 1.0
		@ingroup PackageInformation
	*/
	class PackageInfo
	{
		/**
			@brief  課金設定を取得する。
			@return 課金設定。
		*/
		function getFeeStyle()
			{ return WS::Info( 'system' )->getParam( 'fee_style' ); }

		/**
			@brief  定額制の利用月数を取得する。
			@return 利用月数。
		*/
		function getLimitIntervalMonth()
			{ return WS::Info( 'system' )->getParam( 'limit_interval_month' ); }

		/**
			@brief  資料請求の閲覧コストを取得する。
			@return 資料請求の閲覧コスト。
		*/
		function getRequestViewCost()
			{ return WS::Info( 'system' )->getParam( 'request_view_cost' ); }
	}

?>
