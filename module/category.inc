<?php

	class mod_category extends command_base
	{
		function drawCategoryList( $iGM_ , $iRec_ , $iArgs_ )
		{
			global $loginUserType;
			global $loginUserRank;

			$gm    = GMList::getGM( self::$type );
			$db    = $gm->getDB();
			$table = $db->getTable();
			$row   = $db->getRow( $table );

			$templatePath = Template::getTemplate( $loginUserType , $loginUserRank , 'category' , 'EX_CATEGORY_LIST_DESIGN' );

			$this->addBuffer( $gm->getString( $templatePath , null , 'head' ) );

			for( $i = 0 ; $row > $i ; ++$i )
			{
				$rec = $db->getRecord( $table , $i );

				$this->addBuffer( $gm->getString( $templatePath , $rec , 'list' ) );
			}

			$this->addBuffer( $gm->getString( $templatePath , null , 'foot' ) );
		}

		private static $type = 'category';
	}
