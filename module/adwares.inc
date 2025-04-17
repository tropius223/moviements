<?php

	class mod_adwares extends command_base
	{
		function drawIndexList( $iGM_ , $iRec_ , $iArgs_ )
		{
			global $loginUserType;
			global $loginUserRank;

			$maxNum = array_shift( $iArgs_ );

			if( !$maxNum )
				{ $maxNum = 12; }

			$gm    = GMList::getGM( self::$type );
			$db    = $gm->getDB();
			$table = $db->getTable();
			$table = WS::Finder( self::$type )->SearchReadableTable( $table );
			$table = WS::Finder( self::$type )->SortTable( $table );
			$table = $db->limitOffset( $table , 0 , $maxNum );
			$row   = $db->getRow( $table );

			$templatePath = Template::getTemplate( $loginUserType , $loginUserRank , 'adwares' , 'EX_INDEX_LIST_DESIGN' );

			$this->addBuffer( $gm->getString( $templatePath , null , 'head' ) );

			for( $i = 0 ; $row > $i ; ++$i )
			{
				$rec = $db->getRecord( $table , $i );

				$this->addBuffer( $gm->getString( $templatePath , $rec , 'element' ) );
			}

			$this->addBuffer( $gm->getString( $templatePath , null , 'foot' ) );
		}

		function drawNewestAdwares( $iGM_ , $iRec_ , $iArgs_ )
		{
			global $loginUserType;
			global $loginUserRank;

			$gm    = GMList::getGM( self::$type );
			$db    = $gm->getDB();
			$table = $db->getTable();
			$table = $db->sortTable( $table , 'regist' , 'desc' );
			$table = $db->limitOffset( $table , 0 , 1 );
			$row   = $db->getRow( $table );

			$templatePath = Template::getTemplate( $loginUserType , $loginUserRank , 'adwares' , 'EX_NEWEST_ADWARES_DESIGN' );

			if( $row )
			{
				$rec = $db->getRecord( $table , $i );

				$this->addBuffer( $gm->getString( $templatePath , $rec , 'element' ) );
			}
			else
				{ $this->addBuffer( $gm->getString( $templatePath , $rec , 'empty' ) ); }
		}

		private static $type = 'adwares';
	}
