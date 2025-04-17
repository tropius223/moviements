<?php

	//テーブル定義
	$EDIT_TYPE                            = 'news';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = null;
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = null;
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = null;
	$LST[ $EDIT_TYPE ]                    = './lst/module/news.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/module/news.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'N';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;

    $js_file_paths['all']['mod_news'] = './common/js/module/news/news.js';

	class mod_news extends command_base
	{
		/**
			@brief 最新お知らせリストを描画する。
			@param $a_gm   現在のGUIManagerインスタンス。
			@param $a_rec  現在のレコードデータ。
			@param $a_args コマンドコメント引数。以下の順で指定。
			               @li 0 表示するカテゴリ。
			               @li 1 最大表示数。
			               @li 2 パーツ名の接尾子。
		*/
		function drawHeadline( &$a_gm , $a_rec , $a_args )
		{
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACCEPT;

			//引数取得
			$category = array_shift( $a_args );
			$maxRow   = array_shift( $a_args );
			$suffix   = array_shift( $a_args );

			//テーブル取得
			$gm    = SystemUtil::getGMforType( 'news' );
			$db    = $gm->getDB();
			$table = $db->getTable();
			$table = $db->searchTable( $table , 'category' , '=' , $category );

			//管理者以外は非公開
			if( 'admin' != $loginUserType )
			{
				$table = $db->searchTable( $table , 'open_time' , '<' , time() );
				$table = $db->searchTable( $table , 'activate' , '=' , $ACTIVE_ACCEPT );
			}

			$table = WS::Finder( 'news' )->sortTable( $table );

			$row   = $db->getRow( $table );

			//テンプレート取得
			$templateFile = Template::getTemplate( $loginUserType , $loginUserRank , 'news' , 'HEADLINE_PARTS' );

			//描画開始
			$this->addBuffer( $gm->getString( $templateFile , $a_rec , 'head' . $suffix ) );

			//リスト描画
			for( $i = 0 ; $i < $maxRow ; $i++ )
			{
				//テーブル範囲を超えたらダミーで埋める
				if( $row <= $i )
				{
					$this->addBuffer( $gm->getString( $templateFile , $a_rec , 'dummy' . $suffix ) );
					continue;
				}

				$rec = $db->getRecord( $table , $i );
				$this->addBuffer( $gm->getString( $templateFile , $rec , 'element' . $suffix ) );
			}

			$this->addBuffer( $gm->getString( $templateFile , $a_rec , 'foot' . $suffix ) );
		}

		/**
			@brief  お知らせの詳細設定を取得する。
			@param  $a_gm   現在のGUIManagerインスタンス。
			@param  $a_rec  現在のレコードデータ。
			@param  $a_args コマンドコメント引数。以下の順で指定。
			                @li 0 レコードID。省略時は引数 $a_rec を使用する。
			@return 次の文字列のいずれか。
					@li none            リンクしない。
			        @li messageToDetail リンクメッセージを使って詳細ページにリンク。
					@li topicsToDetail  見出しを使って詳細ページにリンク。
			        @li messageToURI    リンクメッセージを使って外部URLにリンク。
					@li topicsToURI     見出しを使って外部URLにリンク。
		*/
		function getHeadLineType( &$a_gm , $a_rec , $a_args )
		{
			$id = array_shift( $a_args );
			$db = SystemUtil::getGMforType( 'news' )->getDB();

			if( $id )
				$rec = $db->selectRecord( $id );

			if( !$rec )
				$rec = $a_rec;

			if( !$rec )
			{
				$this->addBuffer( 'none' );
				return;
			}

			$detailMode = $db->getData( $rec , 'detail_mode' );
			$useMessage = $db->getData( $rec , 'use_link_message' );

			switch( $detailMode )
			{
				//リンク無し
				case 0 :
					$this->addBuffer( 'none' );
					break;

				//本文ページ
				case 1 :
					if( $useMessage )
						$this->addBuffer( 'messageToDetail' );
					else
						$this->addBuffer( 'topicsToDetail' );

					break;

				//任意のURI
				case 2 :
					if( $useMessage )
						$this->addBuffer( 'messageToURI' );
					else
						$this->addBuffer( 'topicsToURI' );

					break;
			}
		}
	};
?>
