<?php

	class UseSection
	{
		static private $Datas = Array();

		static function Parse( $_section )
		{
			List( $category , $methods , $target ) = $_section->getContentsWords();
			$methods                               = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $methods );

			$childs = $_section->getChilds();

			foreach( $childs as $child )
			{
				$users = UseSection::GetUsers( $child );

				foreach( $users as $user => $format )
					foreach( $methods as $method )
						UseSection::$Datas[ $method ][ $target ][ $user ] = $format;
			}
		}

		static private function GetUsers( $_section )
		{
			$result   = Array();
			$contents = $_section->getContentsArray();

			foreach( $contents as $content )
			{
				List( $users , $format ) = preg_split( '/[\\t\\s]*=[\\t\\s]*/' , $content );
				$users                   = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $users );

				foreach( $users as $user )
					$result[ $user ] = $format;
			}

			return $result;
		}

		static function GetTemplates()
		{
			$result = Array();

			foreach( UseSection::$Datas as $method => $targets )
			{
				foreach( $targets as $target => $users )
				{
					foreach( $users as $user => $format )
					{
						if( '*' == $user )
							List( $user , $activate ) = Array( '' , 15 );
						else
							List( $user , $activate ) = explode( '/' , $user );

						$file  = str_replace( '*' , Preset::GetFileName( $method , $target ) , $format );
						$file  = str_replace( '&' , ( 'nobody' == $user ? '' : ucfirst( $user ) ) , $file );
						$label = Preset::GetLabel( $method );

						if( FALSE === strpos( $file , '/' ) )
							$file = Preset::GetDirectory( $method , $user ) . $file;

						$data[ 'user' ]     = $user;
						$data[ 'target' ]   = $target;
						$data[ 'owner' ]    = 3;
						$data[ 'label' ]    = $label;
						$data[ 'file' ]     = $file . '.html';

						foreach( Array( 1 , 2 , 4 , 8 ) as $act )
						{
							if( $act & $activate )
							{
								$data[ 'activate' ] = $act;
								$result[]           = $data;
							}
						}
					}
				}
			}

			return $result;
		}
	}
?>
