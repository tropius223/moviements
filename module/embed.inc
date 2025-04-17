<?php

	class mod_embed extends command_base
	{
		private static $PreviousResult;		//直前のembedコマンドが成功したかどうか

		/*		検索結果を描画する		*/
		/*		p0    : テーブル名		*/
		/*		p1    : テンプレートファイルのラベル		*/
		/*		p2~pn : 書式により判断		*/
		/*		演算子を含む(column=search等) ... 検索条件		*/
		/*		num~num                       ... 表示する行範囲（無指定なら全て描画）		*/
		/*		column*desc                   ... ソート指定		*/
		/*		exec:any                      ... 関数実行(embed->any)		*/
		/*		それ以外                      ... partkeyの繰り返しパターン		*/
		/*		例：partkeyが a a b ならreadheadをa->a->b->a->a->b->...の順で繰り返す		*/
		/*		行頭が!のpartkeyはセパレータと判断する		*/
		function searchResult( &$_gm , $_rec , $_args )
		{
			$gm           = SystemUtil::getGMforType( array_shift( $_args ) );
			$db           = $gm->getDB();
			$table        = $db->getTable();
			$templateFile = Template::getLabelFile( array_shift( $_args ) );

			if( !$templateFile )
			{
				self::$PreviousResult = true;
				return;
			}

			/*		引数の解析		*/
			foreach( $_args as $param )
			{
				if( preg_match( '/(\w+)([=<>!]{1,2})(.*)/' , $param , $match ) )
					$table = $db->searchTable( $table , $match[ 1 ] , $match[ 2 ] , $match[ 3 ] );
				else if( preg_match( '/(\d*)~(\d*)/' , $param , $match ) )
				{
					$begin = $match[ 1 ] - 1;
					$end   = $match[ 2 ];
				}
				else if( preg_match( '/(\w+)\*(\w*)/' , $param , $match ) )
					$table = $db->sortTable( $table , $match[ 1 ] , $match[ 2 ] );
				else if( preg_match( '/exec:(.*)/' , $param , $match ) )
					$table = $match[ 1 ]( $gm , $rec , $table );
				else
					$partsList[] = $param;
			}

			$row = $db->getRow( $table );

			if( !$row )
			{
				self::$PreviousResult = false;
				return;
			}

			if( !$end || $row < $end )
				$end = $row;

			/*		リスト描画		*/
			for( $i = $begin ; $i < $end ; $i++ )
			{
				$gm->setVariable( 'i' , $i );
				$gm->setVariable( 'I' , $readCount );

				if( !$partsList[ $partsCursor ] )
					$partsCursor = 0;

				$parts = $partsList[ $partsCursor ];
				$partsCursor++;

				/*		セパレータ		*/
				if( 0 === strpos( $parts , '!' ) )
				{
					$string .= $gm->getString( $templateFile , null , $parts );

					$i--;
					continue;
				}

				if( $i >= $row )
				{
					$string .= $gm->getString( $templateFile , null , $parts . '_null' );

					if( !$string )
						$string .= $gm->getString( $templateFile , null , 'null' );
				}
				else
				{
					$rec = $db->getRecord( $table , $i );
					$string .= $gm->getString( $templateFile , $rec , $parts );
				}
			}

			if( $string )
			{
				$this->addBuffer( $gm->getString( $templateFile , $_rec , 'head' ) );
				$this->addBuffer( $string );
				$this->addBuffer( $gm->getString( $templateFile , $argRecord , 'foot' ) );
			}

			self::$PreviousResult = true;
		}

		/*		画像を描画		*/
		/*		p0 : パス		*/
		/*		p1 : 幅		*/
		/*		p2 : 高さ		*/
		function image( &$_gm , $_rec , $_args )
		{
			if( !file_exists( $_args[ 0 ] ) )
			{
				self::$PreviousResult = false;
				return;
			}

			$width  = ( $_args[ 1 ] ? 'width="' . $_args[ 1 ] . '"' : '');
			$height = ( $_args[ 2 ] ? 'height="' . $_args[ 2 ] . '"' : '');

			$result = '<img src="' . $_args[ 0 ] . '" ' . $width . ' ' . $height . ' />';
			$result = str_replace( ' ' , '&CODE001;' , $result );

			$this->addBuffer( $result );

			self::$PreviousResult = true;
		}

		/*		リンク付き画像を描画		*/
		/*		p0 : パス		*/
		/*		p0 : リンクパス		*/
		/*		p1 : 幅		*/
		/*		p2 : 高さ		*/
		function thumbnail( &$_gm , $_rec , $_args )
		{
			if( !file_exists( $_args[ 0 ] ) )
			{
				self::$PreviousResult = false;
				return;
			}

			$width  = ( $_args[ 2 ] ? 'width="' . $_args[ 1 ] . '"' : '');
			$height = ( $_args[ 3 ] ? 'height="' . $_args[ 2 ] . '"' : '');

			$result = '<a href="' . $_args[ 1 ] . '"><img src="' . $_args[ 0 ] . '" ' . $width . ' ' . $height . ' /></a>';
			$result = str_replace( ' ' , '&CODE001;' , $result );

			$this->addBuffer( $result );

			self::$PreviousResult = true;
		}

		/*		flashを描画		*/
		/*		p0 : パス		*/
		/*		p1 : 幅		*/
		/*		p2 : 高さ		*/
		/*		p3 : ループ再生		*/
		function flash( &$_gm , $_rec , $_args )
		{
			if( !file_exists( $_args[ 0 ] ) )
			{
				self::$PreviousResult = false;
				return;
			}

			$width  = ( $_args[ 1 ] ? 'width="' . $_args[ 1 ] . '"' : '');
			$height = ( $_args[ 2 ] ? 'height="' . $_args[ 2 ] . '"' : '');
			$loop   = ( $_args[ 3 ] ? 'roop="' . $_args[ 3 ] . '"' : '');

			$result = '<embed src="' . $_args[ 0 ] . '" ' . $width . ' ' . $height . ' ' . $loop . ' />';

			$result = str_replace( ' ' , '&CODE001;' , $result );

			$this->addBuffer( $result );

			self::$PreviousResult = true;
		}

		/*		失敗時の値を描画		*/
		/*		p0 ~ pn : 描画する文字列(結合)		*/
		function failedCase( &$_gm , $_rec , $_args )
		{
			if( self::$PreviousResult )
				return;

			$this->addBuffer( implode( ' ' , $_args ) );

			self::$PreviousResult = true;
		}
	}

?>