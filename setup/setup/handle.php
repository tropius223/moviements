<?php

	class HandleSection
	{
		static private $Datas = Array();

		static function Parse( $_section )
		{
			List( $category , $target ) = $_section->getContentsWords();

			$childs = $_section->getChilds();

			foreach( $childs as $child )
			{
				$methods = HandleSection::GetMethods( $child , $target );

				foreach( $methods as $method => $users )
					foreach( $users as $user => $format )
						HandleSection::$Datas[ $target ][ $method ][ $user ] = $format;
			}
		}

		static private function GetMethods( $_section , $_target )
		{
			$result = Array();

			$contents                 = $_section->getContentsString();
			List( $methods , $users ) = preg_split( '/[\\t\\s]*=[\\t\\s]/' , $contents );
			$methods                  = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $methods );
			$users                    = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $users );

			$childs = $_section->getChilds();

			foreach( $methods as $method )
				foreach( $users as $user )
					foreach( $childs as $child )
						$result[ $method ][ $user ] = HandleSection::GetFormats( $child , $method , $_target );

			return $result;
		}

		static private function GetFormats( $_section , $_method , $_target )
		{
			$result = Array();

			$formats                 = $_section->getContentsString();
			List( $keys , $formats ) = preg_split( '/[\\t\\s]*=[\\t\\s]*/' , $formats );
			$keys                    = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $keys );
			$formats                 = preg_split( '/[\\t\\s]*,[\\t\\s]*/' , $formats );

			for( $i = 0 ; $i < count( $keys ) ; $i++ )
			{
				$temps = Preset::GetFormatKeys( $_method , $keys[ $i ] , $_target );

				foreach( $temps as $temp )
					$result[ $temp ] = ( count( $formats ) <= $i ? $formats[ count( $formats ) - 1 ] : $formats[ $i ] );
			}

			return $result;
		}

		static function GetTemplates()
		{
			$result = Array();

			foreach( HandleSection::$Datas as $target => $methods )
				foreach( $methods as $method => $users )
					foreach( $users as $user => $formats )
					{
						List( $user , $activate ) = explode( '/' , $user );

						foreach( $formats as $key => $format )
						{
							$file  = str_replace( '*' , Preset::GetFileName( $key , $target ) , $format );
							$file  = str_replace( '&' , ( 'nobody' == $user ? '' : ucfirst( $user ) ) , $file );
							$label = Preset::GetLabel( $key );

							if( FALSE === strpos( $file , '/' ) )
								$file = Preset::GetDirectory( $key , $target ) . $file;

							$data[ 'user' ]   = $user;
							$data[ 'target' ] = $target;
							$data[ 'file' ]   = $file . '.html';
							$data[ 'label' ]  = $label;
							$data[ 'owner' ]  = Preset::GetOwner( $method );

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
