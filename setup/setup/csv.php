<?php

	class CSV
	{
		static function CreateDefine( $_defines )
		{
			foreach( $_defines as $name => $option )
			{
				$lstFile = $option[ 'lst' ];

				if( !$lstFile )
					$lstFile = '../lst/' . strtolower( $name ) . '.csv';
				else
					$lstFile = '../' . $lstFile;

				if( file_exists( $lstFile ) )
					continue;

				$writeString = '';

				$writeString .= 'id,string' . "\n";

				if( 'user' == $option[ 'defineType' ] )
				{
					$writeString .= ( $option[ 'loginKey' ] ? $option[ 'loginKey' ] : 'mail' ) . ',string,Null,Null' . "\n";
					$writeString .= ( $option[ 'loginPass1' ] ? $option[ 'loginPass1' ] : 'pass1' ) . ',string,Null,Null' . "\n";
					$writeString .= ( $option[ 'loginPass2' ] ? $option[ 'loginPass2' ] : 'pass2' ) . ',string,Null,Null' . "\n";
				}

				if( 'user' == $option[ 'defineType' ] )
					$writeString .= 'activate,int' . "\n";

				$writeString .= 'regist,timestamp' . "\n";

				if( 'user' == $option[ 'defineType' ] )
				{
					$writeString .= 'login,timestamp' . "\n";
					$writeString .= 'logout,timestamp' . "\n";
				}

				$file = fopen( $lstFile , 'wb' );
				fputs( $file , $writeString );
				fclose( $file );

				$tdbFile = $option[ 'tdb' ];

				if( !$tdbFile )
					$tdbFile = '../tdb/' . strtolower( $name ) . '.csv';
				else
					$tdbFile = '../' . $tdbFile;

				if( !file_exists( $tdbFile ) )
				{
					$file = fopen( $tdbFile , 'wb' );
					fclose( $file );
				}
			}
		}

		static function CreateTemplate( $_define , $_templates )
		{
			if( $_define[ 'template' ] )
				$tdbFile = $_define[ 'template' ][ 'tdb' ];

			if( !$tdbFile )
				$tdbFile = '../tdb/template.csv';
			else
				$tdbFile = '../' . $tdbFile;

			$file = fopen( $tdbFile , 'wb' );

			foreach( $_templates as $template )
			{
				$id++;

				$writeString  = $id . ',,T' . sprintf( '%04d' , $id ) . ',';
				$writeString .= '/' . $template[ 'user' ] . '/' . ',';
				$writeString .= $template[ 'target' ] . ',';
				$writeString .= $template[ 'activate' ] . ',';
				$writeString .= $template[ 'owner' ] . ',';
				$writeString .= $template[ 'label' ] . ',';
				$writeString .= $template[ 'file' ] . ',';
				$writeString .= 0;
				$writeString .= "\n";

				fputs( $file , $writeString );

				CSV::CreateTemplateFile( $template );
			}

			fclose( $file );
		}

		private function CreateTemplateFile( $_template )
		{
			$file    = $_template[ 'file' ];

			if( !file_exists( '../template/pc/' . $file ) )
			{
				$paths = explode( '/' , 'template/pc/' . $file );
				array_pop( $paths );

				foreach( $paths as $path )
				{
					$longPath .= $path . '/';

					if( !file_exists( '../' . $longPath ) )
						mkdir( '../' . $longPath , 0666 );
				}

				$handle = fopen( '../template/pc/' . $file , 'wb' );
				fputs( $handle , CSV::GetContentsToWrite( $_template ) );
				fclose( $handle );
			}

			$longPath = null;

			if( !file_exists( '../template/mobile/' . $file ) )
			{
				$paths = explode( '/' , 'template/mobile/' . $file );
				array_pop( $paths );

				foreach( $paths as $path )
				{
					$longPath .= $path . '/';

					if( !file_exists( '../' . $longPath ) )
						mkdir( '../' . $longPath , 0666 );
				}

				$handle = fopen( '../template/mobile/' . $file , 'wb' );
				fputs( $handle , CSV::GetContentsToWrite( $_template ) );
				fclose( $handle );
			}
		}

		private function GetContentsToWrite( $_template )
		{
			$defines = DefineSection::GetDefines();
			$define  = $defines[ $_template[ 'target' ] ];
			$label   = $_template[ 'label' ];
			$columns = CSV::getColumns( $define );

			switch( $label )
			{
				case 'REGIST_FORM_PAGE_DESIGN' :
				case 'EDIT_FORM_PAGE_DESIGN' :

					foreach( $columns as $column )
						$result .= '<!--# form text ' . $column . ' 32 128 #-->' . "\n";

					break;
			}

			return $result;
		}

		private function GetColumns( $_define )
		{
			$result = Array();

			$file = fopen( $_define[ 'lst' ] , 'rb' );

			if( $file )
			{
				while( $read = rtrim( fgets( $file ) ) )
				{
					List( $column , $temp ) = implode( ',' , $read , 2 );
					$result[]               = $column;
				}

				fclose( $file );
			}

			return $result;
		}
	}
?>
