<?php

	class LabelSection
	{
		static private $Datas = Array();

		static function Parse( $_section )
		{
			$childs = $_section->getChilds();

			foreach( $childs as $child )
			{
				$contents              = $child->getContentsArray();
				foreach( $contents as $content )
				{
					List( $label , $file ) = preg_split( '/[\\t\\s]*=[\\t\\s]*/' , $content );

					LabelSection::$Datas[ $label ] = $file;
				}
			}
		}

		static function GetTemplates()
		{
			$result = Array();

			foreach( LabelSection::$Datas as $label => $file )
			{
				if( FALSE === strpos( $file , '/' ) )
					$file = 'other/' . $file;

				if( !preg_match( '/.+\/.+\..+/' , $file ) )
					$file .= '.html';

				$data[ 'label' ]    = $label;
				$data[ 'file' ]     = $file;
				$data[ 'activate' ] = 15;
				$data[ 'owner' ]    = 3;
				$result[]           = $data;
			}

			return $result;
		}
	}
?>
