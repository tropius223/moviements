<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class nUserSystem extends System
	{
		function registProc( &$iGM_ , &$iRec_ , $iUserType_ , $iUserRank_  , $iCheckPhase_ = false )
		{
			if( $_SESSION[ 'friend' ] ) //親情報がある場合
			{
				$db = GMList::getDB( 'nUser' );
				$db->setData( $iRec_ , 'parent_id' , $_SESSION[ 'friend' ] );
			}

			return parent::registProc( $iGM_ , $iRec_ , $iUsertype_ , $iUserRank_ , $iCheckPhase_ );
		}

		function registComp( &$iGM_ , &$iRec_ , $iUserType_ , $iUserRank_ )
		{
			$_SESSION[ 'friend' ] = null;

			$db      = GMList::getDB( 'nUser' );
			$nUserID = $db->getData( $iRec_ , 'id' );

			$clickLogDB  = GMList::getDB( 'click_log' );
			$clickLogRec = $clickLogDB->getNewRecord();

			$clickLogDB->setData( $clickLogRec , 'id' , SystemUtil::getNewId( $clickLogDB , 'click_log' ) );
			$clickLogDB->setData( $clickLogRec , 'nuser_id' , $nUserID );
			$clickLogDB->setData( $clickLogRec , 'regist' , time() );

			$clickLogDB->addRecord( $clickLogRec );

			return parent::registComp( $iGM_ , $iRec_ , $iUsertype_ , $iUserRank_ );
		}

		/**
		 * 検索処理。
		 * フォーム入力以外の方法で検索条件を設定したい場合に利用します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param table フォームのからの入力内容に一致するレコードを格納したテーブルデータ。
		 */
		function searchProc( &$gm, &$table, $loginUserType, $loginUserRank )
		{
			$table = WS::Finder( 'nUser' )->searchQueryTable( $table );
			$table = WS::Finder( 'nUser' )->sortTable( $table );

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );
		}
	}

?>