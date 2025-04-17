<?php

	class Templater
	{
		static function Marge( $_templates )
		{
			foreach( $_templates as $template )
				foreach( $template as $element )
					$result[] = $element;

			$result = Templater::MargeActivate( $result );

			return $result;
		}

		static private function MargeActivate( $_templates )
		{
			$result = Array();

			foreach( $_templates as $template )
				$temp[ $template[ 'user' ] ][ $template[ 'target' ] ][ $template[ 'label' ] ][ $template[ 'owner' ] ][ $template[ 'file' ] ] |= $template[ 'activate' ];

			ksort( $temp );

			foreach( $temp as $user => $targets )
			{
				ksort( $targets );

				foreach( $targets as $target => $labels )
				{
					ksort( $labels );

					foreach( $labels as $label => $owners )
					{

						ksort( $owners );

						foreach( $owners as $owner => $files )
						{

							ksort( $files );

							foreach( $files as $file => $activate )
							{
								$data[ 'user' ]     = $user;
								$data[ 'target' ]   = $target;
								$data[ 'label' ]    = $label;
								$data[ 'file' ]     = $file;
								$data[ 'owner' ]    = $owner;
								$data[ 'activate' ] = $activate;
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
