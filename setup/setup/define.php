<?php

	class DefineSection
	{
		static private $Confs   = Array();
		static private $Datas   = Array();

		static function GetDefines()
		{
			return DefineSection::$Datas;
		}

		static function Parse( $_section )
		{
			$contents             = $_section->getContentsWords( $contents );

			if( 'conf' == $contents[ 1 ] )
			{
				$childs = $_section->getChilds();

				foreach( $childs as $child )
				{
					$contents                        = $child->getContentsArray();

					foreach( $contents as $content )
						DefineSection::$Confs[] = $content;
				}

				return;
			}
			else if( 'const' == $contents[ 1 ] )
			{
				$constType  = true;
				$defineType = $contents[ 2 ];
				$defineName = $contents[ 3 ];
			}
			else
			{
				$constType  = false;
				$defineType = $contents[ 1 ];
				$defineName = $contents[ 2 ];
			}

			$data                 = DefineSection::$Datas[ $defineName ];
			$data[ 'constType' ]  = $constType;
			$data[ 'defineType' ] = $defineType;

			$childs = $_section->getChilds();

			foreach( $childs as $child )
			{
				$contents              = $child->getContentsString();
				List( $name , $value ) = preg_split( '/[\\t\\s]*=[\\t\\s]*/' , $contents , 2 );
				$data[ $name ]         = $value;
			}

			DefineSection::$Datas[ $defineName ] = $data;
		}

		static function GetConfString()
		{
			$writeString  = '<?php' . "\n\n";
			$writeString .= '/**********          Šî–{Ý’è          **********/' . "\n\n";

			foreach( DefineSection::$Confs as $value )
				$writeString .= "\t" . $value . "\n";

			$writeString .= "\n";

			foreach( DefineSection::$Datas as $name => $option )
			{
				$data[ '$EDIT_TYPE' ]    = "'" . $name . "'" . ';';
				$data[ '$TABLE_NAME[]' ] = '$EDIT_TYPE;';
				$data[ '$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ]' ] = ( 'user' == $option[ 'defineType' ] ? 'true' : 'false' ) . ';';
				$data[ '$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]' ]    = ( $option[ 'constType' ] ? 'true' : 'false' ) . ';';
				$data[ '$LOGIN_KEY_COLUM[ $EDIT_TYPE ]' ]        = "'" . ( $option[ 'loginKey' ] ? $option[ 'loginKey' ] : 'mail' ) . "'" . ';';
				$data[ '$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]' ]     = "'" . ( $option[ 'loginPass' ] ? $option[ 'loginPass' ] : 'pass1' ) . "'" . ';';
				$data[ '$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]' ]    = "'" . ( $option[ 'loginPass2' ] ? $option[ 'loginPass2' ] : 'pass2' ) . "'" . ';';
				$data[ '$LST[ $EDIT_TYPE ]' ]                    = "'" . ( $option[ 'lst' ] ? $option[ 'lst' ] :  './lst/' . strtolower( $name ) . '.csv' ) . "'" . ';';
				$data[ '$TDB[ $EDIT_TYPE ]' ]                    = "'" . ( $option[ 'tdb' ] ? $option[ 'tdb' ] : './tdb/' . strtolower( $name ) . '.csv' ) . "'" . ';';

				preg_match( '/(\w*)(\**)/' , $option[ 'id' ] , $match );
				$data[ '$ID_HEADER[ $EDIT_TYPE ]' ] = "'" . $match[ 1 ] . "'" . ';';

				$data[ '$ID_LENGTH[ $EDIT_TYPE ]' ] = strlen( $option[ 'id' ] ) . ';';

				$writeString .= '/**********          ' . $name . '‚Ì’è‹`          **********/' . "\n\n";

				foreach( $data as $key => $value )
				{
					$length = 37 - strlen( $key );

					$writeString .= "\t" . $key;

					for( $i = 0 ; $i < $length ; $i++ )
						$writeString .= ' ';

					$writeString .= ' = ' . $value . "\n";
				}

				$writeString .= "\n";
			}

			$writeString .= '?>';

			return $writeString;
		}
	}
?>
