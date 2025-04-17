<?php

	includeDirectory( 'setup/' );

	$Files[] = 'base.txt';

	$dir = opendir( 'custom' );

	while( $entry = readdir( $dir ) )
	{
		if( '.' == $entry || '..' == $entry )
			continue;

		$Files[] = 'custom/' . $entry;
	}

	closedir( $dir );

	foreach( $Files as $file )
	{
		$File = new File();
		$File->load( $file );

		$Section = $File->getSection();

		for( $i = 0 ; $i < count( $Section ) ; $i++ )
		{
			$words = $Section[ $i ]->getContentsWords();

			switch( $words[ 0 ] )
			{
				case 'handle' :
					HandleSection::Parse( $Section[ $i ] );
					break;

				case 'use' :
					UseSection::Parse( $Section[ $i ] );
					break;

				case 'label' :
					LabelSection::Parse( $Section[ $i ] );
					break;

				case 'custom' :
					CustomSection::Parse( $Section[ $i ] );
					break;

				case 'define' :
					DefineSection::Parse( $Section[ $i ] );
					break;

				default :
					print 'unknown section : ' . $words[ 0 ] . '<br>';
					break;
			}
		}
	}

	$conf = fopen( '../custom/Conf.php' , 'wb' );
	fputs( $conf , DefineSection::GetConfString() );
	fclose( $conf );

	$conf =DefineSection::GetDefines();

	$templates[] = HandleSection::GetTemplates();
	$templates[] = UseSection::GetTemplates();
	$templates[] = LabelSection::GetTemplates();
	$templates[] = CustomSection::GetTemplates();
	$templates   = Templater::Marge( $templates );

	CSV::CreateDefine( $conf );
	CSV::CreateTemplate( $conf , $templates );

	function includeDirectory( $_dir )
	{
		$dir = opendir( $_dir );

		while( $read = readdir( $dir ) )
		{
			if( '.' == $read || '..' == $read )
				continue;

			include_once $_dir . $read;
		}
	}

	function drawTemplates( $_templates )
	{
		foreach( $_templates as $template )
		{
			print '/' . $template[ 'user' ] . '/' . ',';
			print $template[ 'target' ] . ',';
			print $template[ 'activate' ] . ',';
			print $template[ 'owner' ] . ',';
			print $template[ 'label' ] . ',';
			print $template[ 'file' ] . ',';
			print 0;
			print '<br>';
		}
	}

	function drawSection( $_section , $_indent )
	{
		if( !is_array( $_section ) )
			return;

		for( $i = 0 ; $i < count( $_section ) ; $i++ )
		{
			for( $j = 0 ; $j < $_indent ; $j++ )
				print '@-@';

			print $_section[ $i ]->getContentsString() . '<br>';
			drawSection( $_section[ $i ]->getChilds() , $_indent + 1 );
		}
	}

?>
