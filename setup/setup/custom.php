<?php

	class CustomSection
	{
		static private $Datas = Array();

		static function Parse( $_section )
		{
			List( $category , $targets , $labels ) = $_section->getContentsWords();
			$targets = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $targets );
			$labels  = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $labels );
			$maxRow  = ( count( $targets ) > count( $labels ) ? count( $targets ) : count( $labels ) );

			for( $i = 0 ; $i < $maxRow ; $i++ )
			{
				$target = ( count( $targets ) <= $i ? $targets[ count( $targets ) - 1 ] : $targets[ $i ] );
				$label  = ( count( $labels ) <= $i ? $labels[ count( $labels ) - 1 ] : $labels[ $i ] );
				$result[ $target . '/' . $label ]++;
			}

			$childs = $_section->getChilds();

			foreach( $childs as $child )
			{
				$contents = $child->getContentsArray();

				foreach( $contents as $content )
				{
					List( $users , $formats ) = preg_split( '/[\\t\\s]*=[\\t\\s]*/' , $content );
					$users                    = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $users );
					$formats                  = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $formats );

					for( $i = 0 ; $i < count( $users ) ; $i++ )
						foreach( array_keys( $result ) as $target )
							CustomSection::$Datas[ $target ][ $users[ $i ] ] = ( count( $formats ) <= $i ? $formats[ count( $formats ) - 1 ] : $formats[ $i ] );
				}
			}
		}

		static function GetTemplates()
		{
			$result = Array();

			foreach( CustomSection::$Datas as $target => $users )
			{
				List( $target , $label ) = explode( '/' , $target );

				foreach( $users as $user => $format )
				{
					List( $user , $activate ) = explode( '/' , $user );

					$file  = str_replace( '*' , $target , $format );
					$file  = str_replace( '&' , ( 'nobody' == $user ? '' : ucfirst( $user ) ) , $file );

					if( FALSE == strpos( $file , '/' ) )
						$file = 'other/' . $file;

					$data[ 'user' ]   = $user;
					$data[ 'target' ] = $target;
					$data[ 'file' ]   = $file;
					$data[ 'label' ]  = $label;
					$data[ 'owner' ]  = 3;

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

			return $result;
		}
	}
?>
