<?php

	class File
	{
		private $section = Array();

		/*		セクション構造を取得		*/
		function getSection()
		{
			return $this->section;
		}

		/*		ファイルを読み込む		*/
		function load( $_file )
		{
			$handle   = fopen( $_file , 'rb' );
			$contents = Array();

			if( !$handle )
				return false;

			while( !feof( $handle ) )
				$contents[] = rtrim( fgets( $handle ) );

			fclose( $handle );

			if( !count( $contents ) )
				return false;

			$this->section = $this->getSectionStruct( $contents );

			return true;
		}

		/*		セクション構造を取得する		*/
		private function getSectionStruct( $_contents )
		{
			$last                      = 0;
			$result[ 0 ][ 'indent' ]   = 0;
			$result[ 0 ][ 'contents' ] = Array();

			for( $i = 0 ; $i < count( $_contents ) ; $i++ )
			{
				preg_match( '/^(\\t*)(.*)/' , $_contents[ $i ] , $match );
				$indent = substr_count( $match[ 1 ] , "\t" );
				$line   = $match[ 2 ];

				if( $result[ $last ][ 'indent' ] != $indent )
					$last++;

				$result[ $last ][ 'indent' ]     = $indent;
				$result[ $last ][ 'contents' ][] = $line;
			}

			return $this->createSectionStruct( $result , $result[ 0 ][ 'indent' ] );
		}

		/*		セクション構造を生成する		*/
		private function createSectionStruct( $_array , $_index )
		{
			$indent  = $_array[ $_index ][ 'indent' ];
			$result  = null;
			$section = new Section();

			for( $i = $_index ; $i < count( $_array ) ; $i++ )
			{
				if( $_array[ $i ][ 'indent' ] > $indent )
				{
					$child = $this->createSectionStruct( $_array , $i );

					if( $child )
						$section->addChilds( $child );

					$result[] = $section;
					$section  = new Section();

					while( $i < count( $_array ) && $_array[ $i ][ 'indent' ] > $indent )
						$i++;

					$i--;
					continue;
				}
				else if( $_array[ $i ][ 'indent' ] < $indent )
					break;

				$section->addContents( $_array[ $i ][ 'contents' ] );
			}

			if( !$section->isNull() )
				$result[] = $section;

			return $result;
		}
	}

?>
