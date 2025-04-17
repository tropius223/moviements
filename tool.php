<?php

	if( isset( $_GET[ 'phpinfo' ] ) && $_GET[ 'phpinfo' ] )
	{
		phpinfo();
		exit();
	}

	set_time_limit( 0 );

	include_once "./custom/extends/debugConf.php";
	include 'custom/conf.php';
	include './module/module.inc';

	$PEAR_ZIP      = false;  //zip���k��PEAR::File_Archive���g��
	$charcode_flag = true;

	if( $charcode_flag )
	{
		ini_set( 'output_buffering'              , 'Off');               // �o�̓o�b�t�@�����O���w�肵�܂�
		ini_set( 'default_charset'               , 'Shift_JIS');         // �f�t�H���g�̕����R�[�h���w�肵�܂�
		ini_set( 'extension'                     , 'php_mbstring.dll' ); // �}���`�o�C�g�������L���ɂ��܂��B
		ini_set( 'mbstring.language'             , 'Japanese' );         // �f�t�H���g����{��ɐݒ肵�܂��B
		ini_set( 'mbstring.internal_encoding'    , 'SJIS' );             // ���������G���R�[�f�B���O��SJIS�ɐݒ肵�܂��B
		ini_set( 'mbstring.http_input'           , 'auto' );             // HTTP���͕����G���R�[�f�B���O�ϊ���auto�ɐݒ肵�܂��B
		ini_set( 'mbstring.http_output'          , 'SJIS' );             // HTTP�o�͕����G���R�[�f�B���O�ϊ���SJIS�ɐݒ肵�܂��B
		ini_set( 'mbstring.encoding_translation' , 'On'   );             // ���������G���R�[�f�B���O�ւ̕ϊ���L���ɂ��܂��B
		ini_set( 'mbstring.detect_order'         , 'auto' );             // �����R�[�h���o��auto�ɐݒ肵�܂��B
		ini_set( 'mbstring.substitute_character' , 'none' );             // �����ȕ������o�͂��Ȃ��B
		mb_http_output( 'SJIS' );
		mb_internal_encoding( 'SJIS' );
	}

	session_start();

	/*		DBMS�ɐڑ�		*/
	$SQL = SQLConnect::Create( $SQL_MASTER , $SQL_ID , $SQL_PASS , $DB_NAME , $SQL_SERVER , $SQL_PORT );

	if( !$SQL->connect )
	{
		header( 'Content-Type: text/html;charset=shift_jis' );
		Draw::Head( $SQL_MASTER );
		Draw::SQLConnectError();
		return;
	}

	/*		�쐬�ς݂̃e�[�u���ꗗ���擾���Ă���		*/
	$EXISTS = Array();

	if( 'SQLiteDatabase' == $SQL_MASTER )
		$result = $SQL->run( 'SELECT name FROM sqlite_master WHERE type="table" UNION ALL SELECT name FROM sqlite_temp_master WHERE type = "table" ORDER BY name' );
	else if( 'PostgreSQLDatabase' == $SQL_MASTER )
		$result = $SQL->run( 'SELECT tablename FROM pg_tables ORDER BY tablename' );
	else
		$result = $SQL->run( 'show tables' );

	if( $result )
	{
		while( $name = $SQL->fetch( $result ) )
		{
			if( preg_match( '/(.*)_backup(\d*)/' , $name[ 0 ] , $match )  )
			{
				$EXISTS[] = $match[ 1 ] . '_backup';
				$BACKUPS[ $match[ 1 ] ] = $match[ 2 ];
			}
			else
			$EXISTS[] = $name[ 0 ];
		}
	}

	/*		���O�C��		*/
	if( !in_array( 'tool_admin_password' , $EXISTS ) )
	{
		$SQL->run( 'CREATE TABLE tool_admin_password (password ' . $SQL->colAlias[ 'string' ] .' )' );
		$SQL->run( 'INSERT INTO tool_admin_password (password ) VALUES ( "' . md5( admin ) . '" )' );
	}

	$result = $SQL->run( 'SELECT password FROM tool_admin_password' );
	$result = $SQL->fetch( $result );
	$TOOL_PASS = $result[ 0 ];

	if( $TOOL_PASS != $_SESSION[ 'tool_login' ] )
	{
		header( 'Content-Type: text/html;charset=shift_jis' );
		if( $TOOL_PASS != md5( $_POST[ 'password' ] ) )
		{
			if( strlen( $_POST[ 'password' ] ) )
				Draw::loginError();

			Draw::loginForm();
			return;
		}
		else
			$_SESSION[ 'tool_login' ] = $TOOL_PASS;
	}

	/*		�_�E�����[�h�n�̏���		*/
	switch( $_GET[ 'method' ] )
	{
		/*		�_�E�����[�h		*/
		case 'download' :
			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Pragma: no-cache' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . strtolower( $_GET[ 'table' ] ) . '.csv"' );
			header( 'Content-Length: ' . filesize( $TDB[ $_GET[ 'table' ] ] ) );
			readfile( $TDB[ $_GET[ 'table' ] ] );
			return;

		/*		�ꊇ�_�E�����[�h		*/
		case 'download_all' :

			/*		��PEAR��File_Archive���ʓr�K�v�ł�		*/
			if( $PEAR_ZIP )
			{
				$path = ini_get( 'include_path' );
				ini_set( 'include_path' , $path . ':./pear' );
				include './pear/File/Archive.php';
				File_Archive::extract( File_Archive::read( './tdb/' ) , File_Archive::toArchive( 'tdb.zip' , File_Archive::toOutput() ) );
			}
			else
			{
				$zip = new ZipArchive();
				$zip->open( './tdb/tdb.zip' , ZIPARCHIVE::OVERWRITE );

				foreach( $TABLE_NAME as $name )
					$zip->addFile( $TDB[ $name ] , $name . '.csv' );

				$zip->close();

				header( 'Cache-Control: no-cache, must-revalidate' );
				header( 'Pragma: no-cache' );
				header( 'Content-Type: application/octet-stream' );
				header( 'Content-Disposition: attachment; filename="tdb.zip"' );
				header( 'Content-Length: ' . filesize( './tdb/tdb.zip' ) );
				readfile( './tdb/tdb.zip' );
			}

			return;

		default :
			break;
	}

	header( 'Content-Type: text/html;charset=shift_jis' );
	Draw::Head( $SQL_MASTER );

	/*		�����̐U�蕪��		*/
	switch( $_GET[ 'method' ] )
	{
		/*		�C���|�[�g		*/
		case 'import' :

			if( 'true' != $_GET[ 'run' ] )
				Draw::ImportCheck( $_GET[ 'table' ] );
			else
				importProc( $_GET[ 'table' ] , $LST[ $_GET[ 'table' ] ] , $TDB[ $_GET[ 'table' ] ] , $SQL , $BACKUPS , $EXISTS );

			break;

		/*		TDB�C���|�[�g		*/
		case 'import_all' :

			if( 'true' != $_GET[ 'run' ] )
				Draw::ImportAllCheck();
			else
			{
				foreach( $TABLE_NAME as $name )
					importProc( $name , $LST[ $name ] , $TDB[ $name ] , $SQL , $BACKUPS , $EXISTS );
			}

			break;

		/*		���S�C���|�[�g		*/
		case 'import_complete' :

			if( 'true' != $_GET[ 'run' ] )
				Draw::ImportCompleteCheck();
			else
			{
				$SQL->run( 'ALTER DATABASE '.$DB_NAME.' DEFAULT CHARACTER SET sjis COLLATE sjis_japanese_ci ' );
				foreach( $TABLE_NAME as $name )
					importProc( $name , $LST[ $name ] , $TDB[ $name ] , $SQL , $BACKUPS , $EXISTS );

				$moduleDir = opendir( './module/added_template/' );

				while( $fileName = readdir( $moduleDir ) )
				{
					if( false !== strpos( $fileName , '.csv' ) )
					{
						$fileName = str_replace( '.csv' , '' , $fileName );
						addTemplateProc( $fileName , 'template' , $LST[ 'template' ] , $SQL );
					}
				}
			}
			break;

		/*		�G�N�X�|�[�g		*/
		case 'export' :
			exportProc( $_GET[ 'table' ] , $TDB[ $_GET[ 'table' ] ] , $SQL );
			break;

		/*		�ꊇ�G�N�X�|�[�g		*/
		case 'export_all' :

			foreach( $TABLE_NAME as $name )
				exportProc( $name , $TDB[ $name ] , $SQL );

			break;

		/*		�o�b�N�A�b�v		*/
		case 'backup' :
			backupProc( $_GET[ 'table' ] , $SQL , $BACKUPS );
			break;

		/*		�ꊇ�o�b�N�A�b�v		*/
		case 'backup_all' :

			foreach( $TABLE_NAME as $name )
				backupProc( $name , $SQL , $BACKUPS );

			break;

		/*		����		*/
		case 'restore' :

			if( 'true' != $_GET[ 'run' ] )
				Draw::RestoreCheck( $_GET[ 'table' ] );
			else
				RestoreProc( $_GET[ 'table' ] , $SQL , $BACKUPS );

			break;

		/*		�ꊇ����		*/
		case 'restore_all' :

			if( 'true' != $_GET[ 'run' ] )
				Draw::RestoreAllCheck();
			else
			{
				foreach( $TABLE_NAME as $name )
					RestoreProc( $name , $SQL , $BACKUPS );
			}

			break;

		/*		�ĕҐ�		*/
		case 'struct' :
			if( 'true' != $_GET[ 'run' ] )
				Draw::StructCheck( $_GET[ 'table' ] );
			else
				structProc( $_GET[ 'table' ] , $LST[ $_GET[ 'table' ] ] , $TDB[ $_GET[ 'table' ] ] , $SQL , $BACKUPS , $EXISTS );

			break;

		/*		�ꊇ�ĕҐ�		*/
		case 'struct_all' :

			if( 'true' != $_GET[ 'run' ] )
				Draw::StructAllCheck();
			else
			{
				foreach( $TABLE_NAME as $name )
					structProc( $name , $LST[ $name ] , $TDB[ $name ] , $SQL , $BACKUPS , $EXISTS );
			}

			break;

		/*		CSV��DB�̔�r		*/
		case 'marge' :

			if( !$_GET[ 'run' ] )
			{
				Draw::MargeSelect( $_GET[ 'table' ] , $LST[ $_GET[ 'table' ] ] , $TDB[ $_GET[ 'table' ] ] );
				break;
			}

			margeProc( $_GET[ 'table' ] , $LST[ $_GET[ 'table' ] ] , $TDB[ $_GET[ 'table' ] ] , $SQL );

			break;

		/*		�e���v���[�g�ǉ�		*/
		case 'add_template' :
			addTemplateProc( $_GET[ 'name' ] , 'template' , $LST[ 'template' ] , $SQL );
			break;

		/*		�ꊇ�e���v���[�g�ǉ�		*/
		case 'add_template_all' :

			$moduleDir = opendir( './module/added_template/' );

			while( $fileName = readdir( $moduleDir ) )
			{
				if( false !== strpos( $fileName , '.csv' ) )
				{
					$fileName = str_replace( '.csv' , '' , $fileName );
					addTemplateProc( $fileName , 'template' , $LST[ 'template' ] , $SQL );
				}
			}

			break;

		/*		�e���v���[�g�����\�z		*/
		case 'add_template_auto' :

			importProc( 'template' , $LST[ 'template' ] , $TDB[ 'template' ] , $SQL , $BACKUPS , $EXISTS );

			$moduleDir = opendir( './module/added_template/' );

			while( $fileName = readdir( $moduleDir ) )
			{
				if( false !== strpos( $fileName , '.csv' ) )
				{
					$fileName = str_replace( '.csv' , '' , $fileName );
					addTemplateProc( $fileName , 'template' , $LST[ 'template' ] , $SQL );
				}
			}

			break;

		/*		�f�U�C���e���v���[�g�ꗗ�\��		*/
		case 'template_map' :
			templateMapProc( $SQL );
			break;

		/*		�p�X���[�h�ύX		*/
		case 'password' :
			if( $_POST[ 'password' ] )
				passwordProc( $_POST[ 'password' ] , $SQL );
			else
				Draw::PasswordChange();

			break;

		default :
			Draw::TableSelect( $TABLE_NAME , $LST , $TDB , $EXISTS , $BACKUPS );
			break;
	}

	Draw::Foot();

	/*		�C���|�[�g����		*/
	function importProc( $_tableName , $_lstFile , $_tdbFile , $_sqlObject , $_backups , $_exists )
	{
		global $SQL_MASTER;

		$csv = new CSV( $_tableName , $_lstFile , $_tdbFile );

		if( !$csv->readable() )
		{
			Draw::ImportError( $_tableName );
			return;
		}

		if( !in_array( strtolower( $_tableName ) , $_exists ) )
			$result = $_sqlObject->createTable( $_tableName , $csv );

		/*		���ؗp�e�[�u�����쐬		*/
		if( in_array( 'import_verifi' , $_exists ) )
		$result = $_sqlObject->dropTable( 'import_verifi' );
		$result = $_sqlObject->createTable( 'import_verifi' , $csv );

		/*		�����s�}���̂ق��������ɏ����ł���		*/
		if( 'MySQLDatabase' == $SQL_MASTER )
		{
			$count = 0;

			while( $result && $record = $csv->readRecord() )
			{
				/*		�s���ȍs����̓X�J���l���߂�		*/
				if( is_array( $record ) )
				$_sqlObject->insertScheduling( 'import_verifi' , $csv , $record );

				if( ++$count > 256 )
				{
					$result = $_sqlObject->insertComplete( 'import_verifi' , $csv );
					$count = 0;
				}
			}

			if( $_sqlObject->ifInsertScheduling() )
				$result = $_sqlObject->insertComplete( 'import_verifi' , $csv );
		}
		else
		{
			while( $result && $record = $csv->readRecord() )
			{
				if( is_array( $record ) )
				$result = $_sqlObject->insertRecord( 'import_verifi' , $csv , $record );
			}
		}

		/*		���e�[�u�����o�b�N�A�b�v		*/
		if( $result )
			$result = $_sqlObject->renameTable( $_tableName , $_tableName . '_backup' );

		/*		���ؗp�e�[�u�������e�[�u���Ƀ��l�[��		*/
		if( $result )
			$result = $_sqlObject->renameTable( 'import_verifi' , $_tableName );

		if( !$result )
		{
			Draw::SQLError( $_sqlObject );
			Draw::ImportError( $_tableName );
			$_sqlObject->renameTable( $_GET[ 'table' ] . '_backup' , $_GET[ 'table' ] );
			return;
		}

		if( $_backups[ $_tableName ] )
			$_sqlObject->dropTable( $_tableName . '_backup' . $_backups[ $_tableName ] );

		$_sqlObject->renameTable( $_tableName . '_backup' , $_tableName . '_backup' . time() );
		Draw::ImportComplete( $_tableName );
		return $result;
	}

	/*		�G�N�X�|�[�g����		*/
	function exportProc( $_tableName , $_outputFile , $_sqlObject )
	{
		$result = $_sqlObject->run( 'SELECT * FROM ' . strtolower( $_tableName ) );

		if( !$result )
		{
			Draw::SQLError( $_sqlObject );
			Draw::ExportError( $_tableName );
			return;
		}

		$file = fopen( $_outputFile , 'wb' );

		if( !$file )
		{
			Draw::ExportError( $_tableName );
			return;
		}

		while( $data = $_sqlObject->fetch( $result ) )
		{
			/*		PHP5.1.0�ȏ�		*/
//			fputcsv( $file , $data );

			/*		PHP5.1.0����		*/
			for( $i = 0 ; $i < count( $data ) ; $i++ )
				if( FALSE !== strstr( $data[ $i ] , "\n" ) || FALSE !== strstr( $data[ $i ] , '"' ) || FALSE !== strstr( $data[ $i ] , ',' ) )
					$data[ $i ] = '"' . str_replace( '"' , '""' , $data[ $i ] ) . '"';

			fputs( $file , implode( ',' , $data ) . "\n" );
		}

		Draw::ExportComplete( $_tableName );
	}

	/*		�o�b�N�A�b�v����		*/
	function backupProc( $_tableName , $_sqlObject , $_backups )
	{
		$result = $_sqlObject->copyTable( $_tableName ,  $_tableName . '_backup' );

		if( !$result )
		{
			Draw::SQLError( $_sqlObject );
			Draw::BackupError( $_tableName );
		}
		else
		{
			$result = $_sqlObject->dropTable( $_tableName . '_backup' . $_backups[ strtolower( $_tableName ) ] );
			$_sqlObject->renameTable( $_tableName . '_backup' , $_tableName . '_backup' . time() );
			Draw::BackupComplete( $_tableName );
		}
	}

	/*		��������		*/
	function RestoreProc( $_tableName , $_sqlObject , $_backups )
	{
		$result = $_sqlObject->dropTable( $_tableName . '_swap' );
		$result = $_sqlObject->renameTable( $_tableName , $_tableName . '_swap' );

		if( $result )
			$result = $_sqlObject->copyTable( $_tableName . '_backup' . $_backups[ strtolower( $_tableName ) ] ,  $_tableName );

		if( !$result )
		{
			Draw::SQLError( $_sqlObject );
			$_sqlObject->renameTable( $_tableName . '_swap' , $_tableName );
			Draw::RestoreError( $_tableName );
		}
		else
			Draw::RestoreComplete( $_tableName );
	}

	/*		�ĕҐ�����		*/
	function structProc( $_tableName , $_lstFile , $_tdbFile , $_sqlObject , $_backups , $_exists )
	{
		global $SQL_MASTER;
		global $CONFIG_SQL_PASSWORD_KEY;

		$csv = new CSV( $_tableName , $_lstFile , $_tdbFile );

		if( !$csv->readable() )
		{
			Draw::StructError( $_tableName );
			return;
		}

		if( !in_array( strtolower( $_tableName ) , $_exists ) )
		{
			Draw::StructError( $_tableName );
			return;
		}

		/*		���ؗp�e�[�u�����쐬		*/
		if( in_array( 'struct_verifi' , $_exists ) )
		$result = $_sqlObject->dropTable( 'struct_verifi' );
		$result = $_sqlObject->createTable( 'struct_verifi' , $csv );

		if( !$result )
		{
			Draw::SQLError( $_sqlObject );
			Draw::StructError( $_tableName );
			return;
		}

		/*		�p�X���[�h�̌���		*/
		if( $SQL_MASTER == 'MySQLDatabase' )
		{
			/*		DB����J�����ꗗ���擾		*/
			$result = $_sqlObject->run( 'SHOW COLUMNS FROM ' . strtolower( $_tableName ) );

			while( $line = $_sqlObject->fetch( $result , 'assoc' ) )
				$dbColumns[] = $line[ 'Field' ];

			foreach( $dbColumns as $key => $colName )
			{
				/*		�J�����̌^�𒲂ׂ�		*/
				$index = array_search( $colName , $csv->colNames );

				if( FALSE !== $index )
					$type = $csv->colTypes[ $index ];
				else
					$type = 'unknown';

				/*		�p�X���[�h�Ȃ�f�R�[�h���Ď擾		*/
				if( 'password' == $type )
					$cols[ $key ] = "AES_DECRYPT($colName,'$CONFIG_SQL_PASSWORD_KEY') as " . $colName;
				else
					$cols[ $key ] = $colName;
			}

			$sql = 'SELECT '.join( ',' , $cols ) . ' FROM ' . strtolower( $_tableName );
		}
		else
			$sql = 'SELECT * FROM ' . strtolower( $_tableName );

		$table = $_sqlObject->run( $sql );

		if( !$table )
		{
			Draw::SQLError( $_sqlObject );
			Draw::StructError( $_tableName );
			return;
		}

		while( $read = $_sqlObject->fetch( $table , 'assoc' ) )
		{
			$data = Array();

			foreach( $csv->colNames as $key )
				$data[ $key ] = $read[ $key ];

			$result = $_sqlObject->insertRecord( 'struct_verifi' , $csv , $data );

			if( !$result )
			{
				Draw::SQLError( $_sqlObject );
				Draw::StructError( $_tableName );
				return;
			}
		}

		/*		���e�[�u�����o�b�N�A�b�v		*/
		if( $result )
			$result = $_sqlObject->renameTable( $_tableName , $_tableName . '_backup' );

		/*		���ؗp�e�[�u�������e�[�u���Ƀ��l�[��		*/
		if( $result )
			$result = $_sqlObject->renameTable( 'struct_verifi' , $_tableName );

		if( !$result )
		{
			Draw::SQLError( $_sqlObject );
			Draw::StructError( $_tableName );
			$_sqlObject->renameTable( $_tableName . '_backup' , $_tableName );
			return;
		}

		if( $_backups[ $_tableName ] )
			$_sqlObject->dropTable( $_tableName . '_backup' . $_backups[ $_tableName ] );

		$_sqlObject->renameTable( $_tableName . '_backup' , $_tableName . '_backup' . time() );

		Draw::StructComplete( $_tableName );
	}

	/*		�}�[�W����		*/
	function margeProc( $_tableName , $_lstFile , $_tdbFile , $_sqlObject )
	{
		$csv = new CSV( $_tableName , $_lstFile , $_tdbFile );

		if( !$csv->readable() )
			return;

		/*		�J�������̏�������肷��		*/
		for( $i = 0 ; $i < count( $csv->colNames ) ; $i++ )
		{
			$name = $csv->colNames[ $i ];

			if( is_array($_GET[ 'col_' . $name ] ) )
			{
				$operation[ $name ] = implode( ',' , $_GET[ 'col_' . $name ] );
				$columns[]          = $name;

				if( 'int' == $csv->colTypes[ $i ] || 'double' == $csv->colTypes[ $i ] )
					$nums[] = $name;

				if( 'boolean' == $csv->colTypes[ $i ] )
					$booleans[] = $name;
			}
		}

		if( !$columns )
			return;

		Draw::MargeHead( $columns );

		/*		CSV��1�s���ǂݍ���Ŕ�r		*/
		while( $record = $csv->readRecord() )
		{
			$result = $_sqlObject->run( 'SELECT ' . implode( ',' , $columns ) . ' FROM ' . strtolower( $_GET[ 'table' ] ) . ' WHERE shadow_id = ' . $record[ 'shadow_id' ] );

			if( !$result )
				continue;

			$read   = $_sqlObject->fetch( $result , 'assoc' );

			if( !is_array( $read ) )
				continue;

			$replacedColumns = Array();

			/*		�u������J���������		*/
			foreach( $columns as $column )
			{
				if( false !== strstr( $operation[ $column ] , 'rep' ) )
				{
					if( $record[ $column ] != $read[ $column ] )
						$replacedColumns[] = $column;
				}
			}

			/*		�^�ɍ��킹�ĕ\�L��ύX		*/
			foreach( $nums as $name )
			{
				$record[ $name ] = ( !$record[ $name ] ? '0' : $record[ $name ] );
				$read[ $name ]   = ( !$read[ $name ]  ? '0' : $read[ $name ] );
			}

			foreach( $booleans as $name )
			{
				$record[ $name ] = ( !$record[ $name ] || '0' == $record[ $name ] || 'false' == strtolower( $record[ $name ] ) || 'f' == strtolower( $record[ $name ] ) ? 'FALSE' : 'TRUE' );
				$read[ $name ]   = ( !$read[ $name ] || '0' == $record[ $name ] || 'f' == strtolower( $read[ $name ] ) || 'false' == strtolower( $read[ $name ] ) ? 'FALSE' : 'TRUE' );
			}

			/*		�u�����s		*/
			if( count( $replacedColumns ) )
			{
				$result = $_sqlObject->updateRecord( $_tableName , $csv , $record , $replacedColumns );

				if( !$result )
				{
					Draw::SQLError( $_sqlObject );
					$replacedColumns = Array();
				}
			}

			/*		���ʂ�`��		*/
			Draw::MargeResult( $record , $read , $replacedColumns );
		}

		Draw::MargeFoot();
	}

	/*		���W���[���e���v���[�g�ǉ�����		*/
	/*		p0 : ���W���[����		*/
	/*		p1 : �e�[�u����		*/
	/*		p2 : LST�t�@�C����		*/
	/*		p3 : TDB�t�@�C����		*/
	/*		p3 : SQLConnect�I�u�W�F�N�g		*/
	function addTemplateProc( $_templateName , $_tableName , $_lstFileName , $_sqlObject )
	{
		global $SQL_MASTER;

		$addFile     = './module/added_template/' . $_templateName . '.csv';
		$csv         = new CSV( $_tableName , $_lstFileName , $addFile );
		$resultCount = 0;
		if( !$csv->readable() )
			return;

		while( $record = $csv->readRecord() )
		{
			/*		ID����		*/
			/*		MySQL���͈ꊇ�}���̂��߂Ɏ��O�ŘA�Ԍv�Z		*/
			if( !isset( $MasterID ) || 'MySQLDatabase' != $SQL_MASTER )
			{
				$result    = $_sqlObject->run( 'SELECT shadow_id FROM ' . strtolower( $_tableName ) . ' ORDER BY shadow_id desc' );
				$result    = $_sqlObject->fetch( $result );
				$shadow_id = $result[ 0 ];
				$MasterID  = $shadow_id;
			}
			else
				$shadow_id = ++$MasterID;

			if( !isset( $MasterNum ) || 'MySQLDatabase' != $SQL_MASTER )
			{
				$result = $_sqlObject->run( 'SELECT id FROM ' . strtolower( $_tableName ) . ' WHERE shadow_id = ' . $shadow_id );
				$result = $_sqlObject->fetch( $result );
				$id     = $result[ 0 ];

				preg_match( '/([^\\d]*)(\d*)/' , $id , $match );
				$oldLength = strlen( $match[ 2 ] );
				$newNum    = sprintf( '%04d' , ++$match[ 2 ] );
				$MasterNum = $match[ 2 ];
			}
			else
				$newNum = sprintf( '%04d' , ++$MasterNum );

			$record[ 'shadow_id' ] = $shadow_id + 1;
			$record[ 'id' ]        = $match[ 1 ] . $newNum;

			/*		�d���`�F�b�N		*/
			$query = 'SELECT shadow_id FROM ' . strtolower( $_tableName ) . ' WHERE ';
			$terms = Array();

			for( $i = 0 ; $i < count( $csv->colNames ) ; $i++ )
			{
				if( 'shadow_id' == $csv->colNames[ $i ] || 'id' == $csv->colNames[ $i ] || 'regist' == $csv->colNames[ $i ] )
					continue;

				$terms[] = $csv->colNames[ $i ] . ' = ' . $_sqlObject->escape( $record[ $csv->colNames[ $i ] ] , $csv->colTypes[ $i ] );
			}

			$query .= implode( ' AND ' , $terms );
			$result = $_sqlObject->run( $query );
			$result = $_sqlObject->fetch( $result );

			if( $result[ 0 ] )
				continue;

			if( 'MySQLDatabase' == $SQL_MASTER )
			{
				$result = $_sqlObject->insertScheduling( $_tableName , $csv , $record );
				$resultCount++;
			}
			else
			{
				$result = $_sqlObject->insertRecord( $_tableName , $csv , $record );

				if( $result )
					$resultCount++;
			}
		}

		if( 'MySQLDatabase' == $SQL_MASTER )
		{
			if( $_sqlObject->ifInsertScheduling() )
				$result = $_sqlObject->insertComplete( $_tableName , $csv );

			if( $result )
				print '<p>' . $_templateName . ' import : ' . $resultCount . '</p>';
			else
				print '<p>' . $_templateName . ' import : error</p>';
		}
		else
			print '<p>' . $_templateName . ' import : ' . $resultCount . '</p>';
	}

	/*		�f�U�C���e���v���[�g�`�揈��		*/
	function templateMapProc( $_sql )
	{
		$map = new TemplateMap( $_sql );

		$userTypes = $map->userTypes();

		foreach( $userTypes as $userType )
			print '<a href="#' . ( $userType ? $userType : 'label' ) . '">�y' . ( $userType ? $userType : 'label' ) . '�z</a>�@';

		foreach( $userTypes as $userType )
		{
			$targets = $map->targets( $userType );
			Draw::TemplateMap( $userType , $targets , $map );
		}
	}

	/*		�p�X���[�h�ύX		*/
	function passwordProc( $_password , $_sql )
	{
		$_sql->run( 'UPDATE tool_admin_password SET password = "' . md5( $_password ) . '"' );
		$_SESSION[ 'tool_login' ] = '';

		print '<p>�ύX���܂���</p>';
	}

	class SQLConnect
	{
		/*		�R���X�g���N�^		*/
		function __construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port )
		{
			$this->server   = $_dbmsName;
			$this->user     = $_user;
			$this->password = $_password;
			$this->dbName   = $_dbName;
			$this->server   = $_server;
			$this->port     = $_port;
			$this->schedule = array();
		}

		/*		�eDBMS�p�̔h���N���X��Ԃ�		*/
		/*		p0 : DBMS��		*/
		/*		rt : �I�u�W�F�N�g		*/
		static function Create( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port )
		{
			switch( $_dbmsName )
			{
				case 'MySQLDatabase' :
					return new MySQLConnect( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );
				case 'PostgreSQLDatabase' :
					return new PostgreSQLConnect( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );
				case 'SQLiteDatabase' :
					return new SQLiteConnect( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );
				default :
					return new SQLConnect( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );
			}
		}

		/*		SQL�������s		*/
		/*		p0 : SQL��		*/
		function run( $_query )
		{
			$this->lastQuery = $_query;
			print '<p><strong style="color:#f00">DBMS��������̂��ߎ��s�ł��܂��� : </strong>' . $_query . '</p>';
		}

		/*		�G���[��������擾		*/
		/*		��^���� : �����͂���܂���		*/
		function error()
		{
			return 'no error';
		}

		/*		���ʂ��p�[�X����		*/
		/*		p0 : ���\�[�XID		*/
		/*		p0 : �p�[�X���@(row/array/)		*/
		function fetch( $_resource , $_fetchType = 'row' )
		{
			return null;
		}

		/*		SQL���ɑg�ݍ��ނ��߂ɁA�^�ɍ��킹�ēK�؂ɃG�X�P�[�v����		*/
		/*		p0 : �l		*/
		/*		p1 : lst�ł̌^��`		*/
		function escape( $_value , $_type )
		{
			switch( $_type )
			{
				case 'string' :
				case 'image' :
				case 'varchar' :
				case 'char' :
					return "'" . str_replace( "'" , "\\'" , $_value ) . "'";

				case 'boolean' :
					return ( !$_value || 'false' == strtolower( $_value ) ? 'FALSE' : 'TRUE' );

				default :
					return ( !$_value ? '0' : $_value );
			}
		}

		/*		�e�[�u�����폜����		*/
		/*		p0 : �e�[�u����		*/
		function dropTable( $_tableName )
		{
			return $this->run( 'DROP TABLE ' . strtolower( $_tableName ) );
		}

		/*		�e�[�u�����쐬����		*/
		/*		p0 : �e�[�u����		*/
		/*		p1 : CSV�I�u�W�F�N�g		*/
		function createTable( $_tableName , $_csvObject )
		{
			$size_type = Array( 'varchar' => 'true', 'char' => 'true' );
			
			for( $i = 0 ; $i < count( $_csvObject->colNames ) ; $i++ )
				$columns[] = implode( ' ' , Array( $_csvObject->colNames[ $i ] , $this->colAlias[ $_csvObject->colTypes[ $i ] ] . ( isset( $size_type[$_csvObject->colTypes[ $i ]] ) ? '('.$_csvObject->colSizes[ $i ].')' : ''  ) , ( 'shadow_id' == $_csvObject->colNames[ $i ] ? 'primary key' : '' ) ) );

			return $this->run( 'CREATE TABLE ' . strtolower( $_tableName ) . ' (' . implode( ',' , $columns ) . ')' );
		}

		/*		���R�[�h��ǉ�����		*/
		/*		p0 : �e�[�u����		*/
		/*		p1 : CSV�I�u�W�F�N�g		*/
		/*		p2 : CSV->readRecord�̒l		*/
		function insertRecord( $_tableName , $_csvObject , $_record )
		{
			for( $i = 0 ; $i < count( $_csvObject->colNames ) ; $i++ )
			{
				$name  = $_csvObject->colNames[ $i ];
				$value = $this->escape( $_record[ $name ] , $_csvObject->colTypes[ $i ] );

				$columns[] = $name;
				$values[]  = $value;
			}

			return $this->run( 'INSERT INTO ' . strtolower( $_tableName ) . ' (' . implode( ',' , $columns ) . ') VALUES (' . implode( ',' , $values ) . ')' );
		}

		/*		���R�[�h��ǉ�����		*/
		/*		p0 : �e�[�u����		*/
		/*		p1 : CSV�I�u�W�F�N�g		*/
		/*		p2 : CSV->readRecord�̒l		*/
		function insertScheduling( $_tableName , $_csvObject , $_record )
		{
			for( $i = 0 ; $i < count( $_csvObject->colNames ) ; $i++ )
			{
				$name  = $_csvObject->colNames[ $i ];
				$value = $this->escape( $_record[ $name ] , $_csvObject->colTypes[ $i ] );

				$columns[] = $name;
				$values[]  = $value;
			}

			$this->schedule[] = '(' . implode( ',' , $values ) . ')';
		}

		/*		���R�[�h�̒ǉ��\�肪����Ȃ�TRUE		*/
		function ifInsertScheduling()
		{
			return ( count( $this->schedule ) ? TRUE : FALSE );
		}

		/*		���R�[�h�̒ǉ�����������		*/
		/*		p0 : �e�[�u����		*/
		/*		p1 : CSV�I�u�W�F�N�g		*/
		function insertComplete( $_tableName , $_csvObject )
		{
			if( !count( $this->schedule ) )
				return false;

			for( $i = 0 ; $i < count( $_csvObject->colNames ) ; $i++ )
				$columns[] = $_csvObject->colNames[ $i ];

			$result = $this->run( 'INSERT INTO ' . strtolower( $_tableName ) . ' (' . implode( ',' , $columns ) . ') VALUES ' . implode( ',' , $this->schedule ) );
			$this->schedule = array();

			return $result;
		}

		/*		���R�[�h���X�V����		*/
		/*		p0 : �e�[�u����		*/
		/*		p1 : CSV�I�u�W�F�N�g		*/
		/*		p2 : �X�V���郌�R�[�h		*/
		/*		p3 : �X�V����J����		*/
		function updateRecord( $_tableName , $_csvObject , $_record , $_column )
		{
			$query = 'UPDATE ' . strtolower( $_tableName ) . ' SET ';

			for( $i = 0 ; $i < count( $_csvObject->colNames ) ; $i++ )
			{
				$name  = $_csvObject->colNames[ $i ];
				$value = $this->escape( $_record[ $name ] , $_csvObject->colTypes[ $i ] );

				if( !in_array( $name , $_column ) )
					continue;

				$updates[] = $name . ' = ' . $value;
			}

			if( !$updates )
				return;

			$query .= implode( ',' , $updates );
			$query .= ' WHERE shadow_id = ' . $_record[ 'shadow_id' ];

			return $this->run( $query );
		}

		/*		�e�[�u�������l�[������		*/
		/*		p0 : ���e�[�u����		*/
		/*		p1 : �V�e�[�u����		*/
		function renameTable( $_originName , $_newName )
		{
			return $this->run( 'ALTER TABLE ' . strtolower( $_originName ) . ' RENAME TO ' . strtolower( $_newName ) );
		}

		/*		�e�[�u���𕡐�����		*/
		/*		p0 : ���e�[�u����		*/
		/*		p1 : �V�e�[�u����		*/
		function copyTable( $_originName , $_newName )
		{
			return $this->run( 'CREATE TABLE ' . strtolower( $_newName ) . ' AS SELECT * FROM ' . strtolower( $_originName ) );
		}
	}

	class MySQLConnect extends SQLConnect
	{
		/*		�R���X�g���N�^		*/
		function __construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port )
		{
			parent::__construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );

			$this->colAlias = Array( 'int' => 'int' , 'double' => 'double' , 'string' => 'text' , 'image' => 'text' , 'boolean' => 'boolean' , 'timestamp' => 'int', 'char' => 'char', 'varchar' => 'varchar' );

			if($_port != "")
				$this->connect  = mysqli_connect( $this->server . ':' . $this->port , $this->user , $this->password );
			else
				$this->connect  = mysqli_connect( $this->server , $this->user , $this->password );

			if( $this->connect )
			{
				mysqli_select_db( $this->connect , $this->dbName );
				mysqli_query( $this->connect , 'set names sjis' );
			}
		}

		/*		SQL�������s		*/
		/*		p0 : SQL��		*/
		function run( $_query )
		{
			$this->lastQuery = $_query;
			return ( $this->connect ? mysqli_query( $this->connect , $_query ) : false );
		}

		/*		�G���[��������擾		*/
		/*		��^���� : �����͂���܂���		*/
		function error()
		{
			return mysqli_error( $this->connect );
		}

		/*		���ʂ��p�[�X����		*/
		/*		p0 : ���\�[�XID		*/
		/*		p0 : �p�[�X���@(row/array/)		*/
		function fetch( $_resource , $_fetchType = 'row' )
		{
			switch( $_fetchType )
			{
				case 'row' :
					return mysqli_fetch_row( $_resource );

				case 'assoc' :
					return mysqli_fetch_assoc( $_resource );

				default :
					return mysqli_fetch_array( $_resource );
			}
		}
	}

	class PostgreSQLConnect extends SQLConnect
	{
		/*		�R���X�g���N�^		*/
		function __construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port )
		{
			parent::__construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );

			$this->colAlias = Array( 'int' => 'int4' , 'double' => 'float8' , 'string' => 'text' , 'image' => 'text' , 'boolean' => 'boolean' , 'timestamp' => 'int4', 'char' => 'char', 'varchar' => 'varchar' );

			if($_port != "")
				$this->connect  = pg_pconnect( 'host=' . $this->server . ' port=' . $this->port . ' dbname=' . $this->dbName . ' user=' . $this->user . ' password=' . $this->password );
			else
				$this->connect  = pg_pconnect( 'host=' . $this->server . ' dbname=' . $this->dbName . ' user=' . $this->user . ' password=' . $this->password );

			pg_set_client_encoding('SJIS');

		}

		/*		SQL�������s		*/
		/*		p0 : SQL��		*/
		function run( $_query )
		{
			$this->lastQuery = $_query;
			return ( $this->connect ? pg_exec( $this->connect , $_query ) : false );
		}

		/*		�G���[��������擾		*/
		/*		��^���� : �����͂���܂���		*/
		function error()
		{
			return pg_last_error();
		}

		/*		���ʂ��p�[�X����		*/
		/*		p0 : ���\�[�XID		*/
		/*		p0 : �p�[�X���@(row/array/)		*/
		function fetch( $_resource , $_fetchType = 'row' )
		{
			switch( $_fetchType )
			{
				case 'row' :
					return pg_fetch_row( $_resource );

				case 'assoc' :
					return pg_fetch_assoc( $_resource );

				default :
					return pg_fetch_array( $_resource );
			}
		}

		/*		SQL���ɑg�ݍ��ނ��߂ɁA�^�ɍ��킹�ēK�؂ɃG�X�P�[�v����		*/
		/*		p0 : �l		*/
		/*		p1 : lst�ł̌^��`		*/
		function escape( $_value , $_type )
		{
			return parent::escape( mb_convert_encoding( $_value , pg_client_encoding() , 'SJIS' ) , $_type );
		}

		/*		�e�[�u�����쐬����		*/
		/*		p0 : �e�[�u����		*/
		/*		p1 : CSV�I�u�W�F�N�g		*/
		function createTable( $_tableName , $_csvObject )
		{
			$result = parent::createTable( $_tableName , $_csvObject );

			if( $result )
				$result = $this->run( 'GRANT ALL PRIVILEGES ON ' . strtolower( $_tableName ) . ' TO PUBLIC' );

			return $result;
		}

		/*		�e�[�u�������l�[������		*/
		/*		��Postgres�̓��l�[�����Q�ƂŎ������Ă���ۂ��H�̂ŃR�s�[�Ŏ���		*/
		/*		p0 : ���e�[�u����		*/
		/*		p1 : �V�e�[�u����		*/
		function renameTable( $_originName , $_newName )
		{
			$result = $this->copyTable( $_originName , $_newName );

			if( $result )
			{
				$result = $this->dropTable( $_originName );

				if( !$result )
				{
					$this->copyTable( $_newName , $_originName );
					$this->dropTable( $_newName );
				}
			}

			return $result;
		}
	}

	class SQLiteConnect extends SQLConnect
	{
		/*		�R���X�g���N�^		*/
		function __construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port )
		{
			parent::__construct( $_dbmsName , $_user , $_password , $_dbName , $_server , $_port );

			$this->colAlias = Array( 'int' => 'integer' , 'double' => 'real' , 'string' => 'text' , 'image' => 'text' , 'boolean' => 'boolean' , 'timestamp' => 'integer', 'char' => 'char', 'varchar' => 'varchar' );
			$this->connect  = sqlite_open( './tdb/' . $this->dbName . '.db' , 0666 , $error );

			//�g�����U�N�V�������J�n����
			$this->run( 'BEGIN' );
		}

		/*		�f�X�g���N�^		*/
		function __destruct()
		{
			//�g�����U�N�V�������I������
			$this->run( 'END' );
		}

		/*		SQL�������s		*/
		/*		p0 : SQL��		*/
		function run( $_query )
		{
			$this->lastQuery = $_query;
			return ( $this->connect ? @sqlite_query( mb_convert_encoding($_query, 'UTF-8', mb_internal_encoding() ) , $this->connect ) : false );
		}

		/*		�G���[��������擾		*/
		/*		��^���� : �����͂���܂���		*/
		function error()
		{
			return sqlite_error_string( sqlite_last_error( $this->connect ) );
		}

		/*		���ʂ��p�[�X����		*/
		/*		p0 : ���\�[�XID		*/
		/*		p0 : �p�[�X���@(row/array/)		*/
		function fetch( $_resource , $_fetchType = 'row' )
		{
			switch( $_fetchType )
			{
				case 'row' :
					$array = sqlite_fetch_array( $_resource , SQLITE_NUM );
					break;
				case 'assoc' :
					$array = sqlite_fetch_array( $_resource , SQLITE_ASSOC );
					break;
				default :
					$array = sqlite_fetch_array( $_resource );
			}
			if($array){
				$array = array_map( create_function( '$x', 'return mb_convert_encoding($x, mb_internal_encoding() , \'UTF-8\' );' ) , $array );
			}

			return $array;
		}

		/*		SQL���ɑg�ݍ��ނ��߂ɁA�^�ɍ��킹�ēK�؂ɃG�X�P�[�v����		*/
		/*		p0 : �l		*/
		/*		p1 : lst�ł̌^��`		*/
		function escape( $_value , $_type )
		{
			if( 'boolean' == $_type )
				return ( !$_value || 'false' == strtolower( $_value ) ? '\'\'' : '1' );
			else
				return parent::escape( sqlite_escape_string( $_value ) , $_type );
		}

		/*		�e�[�u�������l�[������		*/
		/*		���W�����ڂ�SQLite�ł�ALTER���g���Ȃ��̂ŁA�R�s�[�Ŏ���		*/
		/*		p0 : ���e�[�u����		*/
		/*		p1 : �V�e�[�u����		*/
		function renameTable( $_originName , $_newName )
		{
			$result = $this->copyTable( $_originName , $_newName );

			if( $result )
			{
				$result = $this->dropTable( $_originName );

				if( !$result )
				{
					$this->copyTable( $_newName , $_originName );
					$this->dropTable( $_newName );
				}
			}

			return $result;
		}
	}

	class CSV
	{
		/*		�R���X�g���N�^		*/
		/*		p0 : �e�[�u����		*/
		/*		p0 : lst�t�@�C���̃p�X		*/
		/*		p0 : tdb�t�@�C���̃p�X		*/
		function __construct( $_tableName , $_lstFileName , $_tdbFileName )
		{
			global $ADD_LST; 
			
			$this->tableName   = $_tableName;
			$this->lstFileName = $_lstFileName;
			$this->tdbFileName = $_tdbFileName;
			$this->addColumnRow = 0;

			if( !file_exists( $this->lstFileName ) )
				return;

			$handle = fopen( $this->lstFileName , 'rb' );

			if( !$handle )
				return;

			$this->colNames[0] = 'shadow_id';
			$this->colTypes[0] = 'int';
			$this->colSizes[0] = '';

			$this->colNames[1] = 'delete_key';
			$this->colTypes[1] = 'boolean';
			$this->colSizes[1] = '';

			while( $readLine = rtrim( fgets( $handle ) ) )
			{
				$readLine = explode( ',' , $readLine );
				$this->colNames[] = $readLine[ 0 ];
				$this->colTypes[] = $readLine[ 1 ];
				$this->colSizes[] = $readLine[ 2 ];
			}

			fclose( $handle );
			
            if( isset($ADD_LST[$this->tableName]) && is_array($ADD_LST[$this->tableName]) && count($ADD_LST[$this->tableName]) ){
            	foreach( $ADD_LST[ $this->tableName ] as $add_lst ){
            		
					if( !file_exists( $add_lst ) ){ continue;}
					if( !($handle = fopen( $add_lst , 'rb' )) ){ continue; }

					while( $readLine = rtrim( fgets( $handle ) ) )
					{
						$readLine = explode( ',' , $readLine );
						$this->colNames[] = $readLine[ 0 ];
						$this->colTypes[] = $readLine[ 1 ];
						$this->colSizes[] = $readLine[ 2 ];
						$this->addColumnRow++;
					}
					fclose( $handle );
            	}
            }
		}

		/*		csv���ǂݍ��݉\�����ׂ�		*/
		function readable()
		{
			if( !file_exists( $this->lstFileName ) || !file_exists( $this->tdbFileName ) )
				return false;

			if( !$this->tdbReadHandle )
				$this->tdbReadHandle = fopen( $this->tdbFileName , 'rb' );

			return ( $this->tdbReadHandle ? true : false );
		}

		/*		csv��1�s���ǂ�ŘA�z�z��ɂ��ĕԂ�		*/
		/*		��^���� : �����͂���܂���		*/
		/*		rt : �A�z�z�񖔂�null		*/
		function readRecord()
		{
			if( !$this->readable() )
				return null;

			$readLine = CSV::fget( $this->tdbReadHandle );

			if( !$readLine )
				return null;

			for( $i = 0 ; $i < count( $readLine ) ; $i++ )
				$result[ $this->colNames[ $i ] ] = $readLine[ $i ];

			if($this->addColumnRow){
				for( $i ; $i < count( $readLine ) + $this->addColumnRow ; $i++ )
				$result[ $this->colNames[ $i ] ] = "";
			}
				
			if( !strlen( $result[ 'shadow_id' ] ) )
				return 'skip';

			return $result;
		}

		/*		�t�@�C���|�C���^����s���擾���ACSV�t�B�[���h����������		*/
		/*		�Q�lURL : http://yossy.iimp.jp/wp/?p=56		*/
		/*		p0 : �t�@�C���n���h��		*/
		/*		p1 : �ǂݍ��݃T�C�Y		*/
		/*		p2 : ��؂蕶��		*/
		/*		p3 : �͂ݕ���		*/
		/*		rt : �z�񖔂�false		*/
		static function fget ( &$handle , $length = null , $d = ',' , $e = '"' )
		{
			$d = preg_quote( $d );
			$e = preg_quote( $e );

			$_line = '';

			while ( $eof != true )
			{
				$_line   .= ( empty( $length ) ? fgets( $handle ) : fgets( $handle , $length ) );
				$itemcnt  = preg_match_all('/' . $e . '/' , $_line , $dummy );

				if( $itemcnt % 2 == 0 )
					$eof = true;
			}

			$_csv_line    = preg_replace( '/(?:\r\n|[\r\n])?$/' , $d , trim( $_line ) );
			$_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';

			preg_match_all( $_csv_pattern , $_csv_line , $_csv_matches );

			$_csv_data = $_csv_matches[ 1 ];

			for($_csv_i = 0 ; $_csv_i < count($_csv_data) ; $_csv_i++ )
			{
				$_csv_data[ $_csv_i ] = preg_replace('/^' . $e . '(.*)' . $e . '$/s' , '$1' , $_csv_data[ $_csv_i ] );
				$_csv_data[ $_csv_i ] = str_replace( $e . $e , $e , $_csv_data[ $_csv_i ] );
			}

			return ( empty( $_line ) ? false : $_csv_data );
		}

	}

	class Draw
	{
		/*		�w�b�_�`��		*/
		/*		p0 : DBMS��		*/
		static function Head( $_dbmsName )
		{
			print 'DBMS : ' . $_dbmsName . '<br>';
			print '<hr>';
		}

		/*		���O�C���t�H�[���`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function loginForm()
		{
			print '<form method="post" action="tool.php">';
			print '<input type="password" name="password" value="">';
			print '<input type="submit" value="���O�C��">';
			print '</form>';
		}

		/*		���O�C�����s��ʕ`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function loginError()
		{
			print '<p>���O�C���ł��܂���</p>';
		}

		/*		�p�X���[�h�ύX��ʕ`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function PasswordChange()
		{
			print '<form method="post" action="tool.php?method=password">';
			print '<input type="password" name="password" value="">';
			print '<input type="submit" value="�ύX">';
			print '</form>';
		}

		/*		�t�b�^�`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function Foot()
		{
			print '<hr><table cellpadding="5" style="border:solid 1px #000">';
			print '<tr style="background-color:#cdc">';
			print '<td><a href="tool.php">�g�b�v</a></td>';
			print '<td><a href="tool.php?method=import_all">TDB�C���|�[�g</a></td>';
			print '<td><a href="tool.php?method=import_complete">���S�C���|�[�g</a></td>';
			print '<td><a href="tool.php?method=export_all">�ꊇ�G�N�X�|�[�g</a></td>';
			print '<td><a href="tool.php?method=backup_all">�ꊇ�o�b�N�A�b�v</a></td>';
			print '<td><a href="tool.php?phpinfo=true">phpinfo��\��</a></td>';
			print '</tr><tr style="background-color:#cdc">';
			print '<td><a href="tool.php?method=restore_all">�ꊇ����</a></td>';
			print '<td><a href="tool.php?method=struct_all">�ꊇ�ĕҐ�</a></td>';
			print '<td><a href="tool.php?method=download_all">�ꊇ�_�E�����[�h</a></td>';
			print '<td><a href="tool.php?method=template_map">�f�U�C���e���v���[�g�̊m�F</a></td>';
			print '<td><a href="tool.php?method=password">�p�X���[�h�ύX</a></td>';
			print '<td>&nbsp;</td>';
			print '</tr></table>';
		}

		/*		�e�[�u���ꗗ�`��		*/
		/*		p0 : �e�[�u�����ꗗ		*/
		/*		p1 : lst�ꗗ		*/
		/*		p2 : tdb�ꗗ		*/
		/*		p3 : �����̃e�[�u���ꗗ		*/
		/*		p4 : �o�b�N�A�b�v����		*/
		static function TableSelect( $_tables , $_lsts , $_tdbs , $_exists , $_backups )
		{
			print '<table cellpadding="5" style="border:solid 1px #000">';

			print '<tr>';
			print '<th>�e�[�u����</th>';
			print '<th>�C���|�[�g<br>(CSV����DB��)</th>';
			print '<th>�G�N�X�|�[�g<br>(DB����CSV��)</th>';
			print '<th>�ĕҐ�</th>';
			print '<th>�o�b�N�A�b�v</th>';
			print '<th>����</th>';
			print '<th>DB��CSV���r</th>';
			print '<th>TDB���_�E�����[�h</th>';
			print '</tr>';

			foreach( $_tables as $name )
			{
				print '<tr id="row' . ++$row . '" onmouseover="document.getElementById(\'row' . $row . '\').style.backgroundColor=\'#cdc\'" onmouseout="document.getElementById(\'row' . $row . '\').style.backgroundColor=\'#fff\'">';
				print '<td>' . $name . '</td>';

				/*		�C���|�[�g		*/
				if( file_exists( $_lsts[ $name ] ) && file_exists( $_tdbs[ $name ] ) )
					print '<td><a href="tool.php?method=import&table=' . $name . '">�C���|�[�g</a></td>';
				else
					print '<td style="background-color:#333;color:#fff;">csv not found</td>';

				/*		�G�N�X�|�[�g/�ĕҐ�/�o�b�N�A�b�v		*/
				if( in_array( strtolower( $name ) , $_exists ) )
				{
					print '<td><a href="tool.php?method=export&table=' . $name . '">�G�N�X�|�[�g</a></td>';
					print '<td><a href="tool.php?method=struct&table=' . $name . '">�ĕҐ�</a></td>';
					print '<td><a href="tool.php?method=backup&table=' . $name . '">�o�b�N�A�b�v</a></td>';
				}
				else
					print '<td colspan="3" style="background-color:#333;color:#fff;">table not found</td>';

				/*		����		*/
				if( $_backups[ strtolower( $name ) ] )
					print '<td><a href="tool.php?method=restore&table=' . $name . '">' . date( 'Y/n/j G:i:s' , $_backups[ strtolower( $name ) ] ) . '</a></td>';
				else
					print '<td style="background-color:#333;color:#fff;">backup not found</td>';

				/*		��r		*/
				if( !in_array( strtolower( $name ) , $_exists ) )
					print '<td style="background-color:#333;color:#fff;">table not found</td>';
				else if( !file_exists( $_lsts[ $name ] ) || !file_exists( $_tdbs[ $name ] ) )
					print '<td style="background-color:#333;color:#fff;">csv not found</td>';
				else
					print '<td><a href="tool.php?method=marge&table=' . $name . '">��r</a></td>';

				/*		�_�E�����[�h		*/
				if( file_exists( $_tdbs[ $name ] ) )
					print '<td><a href="tool.php?method=download&table=' . $name . '">�_�E�����[�h</a></td>';
				else
					print '<td style="background-color:#333;color:#fff;">csv not found</td>';

				print '</tr>';
			}

			print '</table>';

			if( !file_exists( './module/added_template/' ) )
				return;

			print '<hr><table cellpadding="5" style="border:solid 1px #000"><tr>';
			print '<th>add template</th>';

			$moduleDir = opendir( './module/added_template/' );

			while( $fileName = readdir( $moduleDir ) )
			{
				if( false !== strpos( $fileName , '.csv' ) )
				{
					$fileName = str_replace( '.csv' , '' , $fileName );
					print '<td><a href="tool.php?method=add_template&name=' . $fileName . '">' . $fileName . '</a></td>';
				}
			}

			print '<td><a href="tool.php?method=add_template_all">�ꊇ</a></td>';
			print '<td><a href="tool.php?method=add_template_auto">�����\�z</a></td>';
			print '<tr></table>';
		}

		/*		�}�[�W���ʉ�ʂ̃w�b�_��`��		*/
		/*		p0 : ��������J�����ꗗ		*/
		static function MargeHead( $_columns )
		{
			print '<p>�͑��ፀ�ځA�Ԃ͒u���ΏۂƂȂ������ڂł��B</p>';
			print '<table cellpadding="5" style="border:solid 1px #000;margin:10px 0px;">';
			print '<tr><th>&nbsp;</th>';

			if( !in_array( 'shadow_id' , $_columns ) )
				array_unshift( $_columns , 'shadow_id' );

			foreach( $_columns as $name )
				print '<th style="background-color:#' . ( $count++ % 2 ? 'eee' : 'cdc' ) . '">' . $name . '</th>';

			print '</tr>';
		}

		/*		�}�[�W���ʉ�ʂ̃t�b�^��`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function MargeFoot()
		{
			print '</table>';
		}

		/*		�}�[�W���ڑI����ʕ`��		*/
		/*		p0 : �e�[�u�����ꗗ		*/
		/*		p1 : lst		*/
		/*		p2 : tdb		*/
		static function MargeSelect( $_tableName , $_lstFile , $_tdbFile )
		{
			$csv = new CSV( $_tableName , $_lstFile , $_tdbFile );

			print '<form method="get" action="tool.php">';
			print '<input type="hidden" name="method" value="marge">';
			print '<input type="hidden" name="table" value="' . $_tableName . '">';
			print '<input type="hidden" name="run" value="true">';
			print '<table cellpadding="5" style="border:solid 1px #000">';

			print '<tr>';
			print '<th>�J������</th>';
			print '<th>����s�̕\��</th>';
			print '<th>����s��u��<br>(CSV����DB��)</th>';
			print '</tr>';

			foreach( $csv->colNames as $name )
			{
				if( 'shadow_id' == $name )
					continue;

				print '<tr style="background-color:#' . ( $count++ % 2 ? 'fff' : 'cdc' ) . '">';
				print '<td>' . $name . '</td>';
				print '<td><input type="checkbox" checked name="col_' . $name . '[]" value="view_diff">�\��</td>';
				print '<td><input type="checkbox" name="col_' . $name . '[]" value="rep_diff">�u��</td>';
				print '</tr>';
			}

			print '</table><br><input type="submit" value="���s"></form>';
		}

		/*		�e���v���[�g�}�b�v��`��		*/
		static function TemplateMap( $_userType , $_targets , $_map )
		{
			print '<table cellpadding="5" width="100%" style="border:solid 1px #000;margin:30px 0px"><tr style="background-color:#9cc"><th colspan="6"><a name="' . ( $_userType ? $_userType : 'label' ) . '">�y' . ( $_userType ? $_userType : 'label' ) . '�z</a></th></tr>';
			print '<tr><th>target</th><th>label</th><td style="background-color:#ffc">ACTIVE_NONE(1)</td><td style="background-color:#fc9">ACTIVE_ACTIVATE(2)</td><td style="background-color:#fc3">ACTIVE_ACCEPT(4)</td><td style="background-color:#f90">ACTIVE_DENY(8)</td></tr>';

			foreach( $_targets as $target )
			{
				$targetBG = ( $targetRow++ % 2 ? '#ccd' : '#fff' );
				$labels = $_map->labels( $_userType , $target );

				foreach( $labels as $label )
				{
					/*		��F����		*/
//					print '<tr style="background-color:' . ( $row++ % 2 ? '#fff' : '#cdc' ) . '"><td style="background-color:' . $targetBG . '">' . $target . '</td>';

					/*		�F����		*/
					if( FALSE != strstr( $label , 'REGIST' ) )
						$color = '#f93';
					else if( FALSE != strstr( $label , 'EDIT' ) )
						$color = '#fc6';
					else if( FALSE != strstr( $label , 'DELETE' ) )
						$color = '#f66';
					else if( FALSE != strstr( $label , 'SEARCH' ) )
						$color = '#9cf';
					else if( FALSE != strstr( $label , 'INFO' ) )
						$color = '#afa';
					else if( FALSE != strstr( $label , 'OTHER' ) || FALSE != strstr( $label , 'INCLUDE' ) )
						$color = '#999';
					else
						$color = '#fff';

					print '<tr style="background-color:' . $color . '"><td style="background-color:' . $targetBG . '">' . $target . '</td>';
					print '<td>' . $label . '</td>';

					$files = $_map->files( $_userType , $target , $label );

					$drawer = new Drawer();

					for( $i = 0 ; $i < 4 ; $i++ )
					{
						$duplicate = $_map->duplicate( $_userType , $target , $label , pow( 2 , $i ) );
						$drawer->push( $files[ $i ] , $duplicate );
					}

					print $drawer->string();
					print '</tr>';
				}
			}

			print '</table>';
		}

		/*		�C���|�[�g�m�F��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function ImportCheck( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . ' ���C���|�[�g���悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>�C���|�[�g�����������ꍇ�A���݂̃e�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=import&table=' . $_tableName . '&run=true">' . $_tableName . '�̃C���|�[�g�����s����</a></p>';
		}

		/*		TDB�C���|�[�g�m�F��ʕ`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function ImportAllCheck()
		{
			print '<p><strong style="color:#f00">TDB�C���|�[�g�����s���悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>�S�Ẵe�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=import_all&run=true">TDB�C���|�[�g�����s����</a></p>';
		}

		/*		���S�C���|�[�g�m�F��ʕ`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function ImportCompleteCheck()
		{
			print '<p><strong style="color:#f00">���S�C���|�[�g�����s���悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>�S�Ẵe�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=import_complete&run=true">���S�C���|�[�g�����s����</a></p>';
		}

		/*		�C���|�[�g������ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function ImportComplete( $_tableName )
		{
			print '<p>' . $_tableName . '���C���|�[�g���܂����B���̃e�[�u���̓o�b�N�A�b�v����܂��B</p>';
		}

		/*		�C���|�[�g���s��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function ImportError( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . '���C���|�[�g�ł��܂���ł����B</strong><br>�i' . $_tableName . '�̌��݂̃f�[�^�͕ύX����Ă��܂���B�j</p>';
		}

		/*		�G�N�X�|�[�g������ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function ExportComplete( $_tableName )
		{
			print '<p>' . $_tableName . '���G�N�X�|�[�g���܂����B</p>';
		}

		/*		�G�N�X�|�[�g���s��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function ExportError( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . '���G�N�X�|�[�g�ł��܂���ł����B</strong></p>';
		}

		/*		�o�b�N�A�b�v������ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function BackupComplete( $_tableName )
		{
			print '<p>' . $_tableName . '���o�b�N�A�b�v���܂����B</p>';
		}

		/*		�o�b�N�A�b�v���s��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function BackupError( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . '���o�b�N�A�b�v�ł��܂���ł����B</strong></p>';
		}

		/*		�����m�F��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function RestoreCheck( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . ' �𕜌����悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>���������������ꍇ�A���݂̃e�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=restore&table=' . $_tableName . '&run=true">' . $_tableName . '�̕��������s����</a></p>';
		}

		/*		�ꊇ�����m�F��ʕ`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function RestoreAllCheck()
		{
			print '<p><strong style="color:#f00">�ꊇ���������s���悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>�S�Ẵe�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=restore_all&run=true">�ꊇ���������s����</a></p>';
		}

		/*		����������ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function RestoreComplete( $_tableName )
		{
			print '<p>' . $_tableName . '�𕜌����܂����B</p>';
		}

		/*		�������s��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function RestoreError( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . '�𕜌��ł��܂���ł����B</strong><br>�i' . $_tableName . '�̌��݂̃f�[�^�͕ύX����Ă��܂���B�j</p>';
		}

		/*		�ĕҐ��m�F��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function StructCheck( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . ' ���ĕҐ����悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>DB��̃e�[�u���̃J������CSV�ɍ��킹�ĕύX���܂��B<br/>�ύX�����������ꍇ�A���݂̃e�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=struct&table=' . $_tableName . '&run=true">' . $_tableName . '�̍ĕҐ������s����</a></p>';
		}

		/*		�ꊇ�ĕҐ��m�F��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function StructAllCheck()
		{
			print '<p><strong style="color:#f00">�ꊇ�ĕҐ������s���悤�Ƃ��Ă��܂��I</strong></p>';
			print '<p>�S�Ẵe�[�u�����u���������܂��B<br>��肪�Ȃ����Ƃ��m�F������Ŏ��s���Ă��������B</p>';
			print '<p><a href="tool.php?method=struct_all&run=true">�ꊇ�ĕҐ������s����</a></p>';
		}

		/*		�ĕҐ����s��ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function StructError( $_tableName )
		{
			print '<p><strong style="color:#f00">' . $_tableName . '�̍ĕҐ��Ɏ��s���܂����B</strong><br>�i' . $_tableName . '�̌��݂̃f�[�^�͕ύX����Ă��܂���B�j</p>';
		}

		/*		�ĕҐ�������ʕ`��		*/
		/*		p0 : �e�[�u����		*/
		static function StructComplete( $_tableName )
		{
			print '<p>' . $_tableName . '���ĕҐ����܂����B</p>';
		}

		/*		�}�[�W�������e��`��		*/
		/*		p0 : CSV�f�[�^		*/
		/*		p1 : DB�f�[�^		*/
		/*		p2 : �u�����ꂽ�J�����ꗗ		*/
		static function MargeResult( $_csvRecord , $_dbRecord , $_replacedColumns )
		{
			$differColumns = Array();

			foreach( $_dbRecord as $key => $value )
			{
				if( $value != $_csvRecord[ $key ] )
					$differColumns[] = $key;
			}

			if( !$differColumns )
				return;

			print '<tr>';
			print '<th style="background-color:#cdc">CSV</th>';
			print '<td rowspan="2" style="background-color:#333;color=#fff;text-align:center">' . $_csvRecord[ 'shadow_id' ] . '</td>';

			$columns = array_keys( $_dbRecord );

			foreach( $columns as $name )
			{
				if( 'shadow_id' == $name )
					continue;

				$count++;

				if( in_array( $name , $_replacedColumns ) )
					print '<td style="background-color:#f66">' . $_csvRecord[ $name ] . '</td>';
				else if( in_array( $name , $differColumns ) )
					print '<td style="background-color:#3cf">' . $_csvRecord[ $name ] . '</td>';
				else
					print '<td rowspan="2" style="background-color:#' . ( $count % 2 ? 'eee' : 'cdc' ) . '">' . $_csvRecord[ $name ] . '</td>';
			}

			print '</tr><tr>';
			print '<th style="background-color:#9a9">DB</th>';
			$count = 0;

			foreach( $columns as $name )
			{
				if( 'shadow_id' == $name )
					continue;

				if( in_array( $name , $_replacedColumns ) )
					print '<td style="background-color:#c66">' . $_dbRecord[ $name ] . '</td>';
				else if( in_array( $name , $differColumns ) )
					print '<td style="background-color:#09f">' . $_dbRecord[ $name ] . '</td>';
				else
					continue;
			}

			print '</tr>';
		}

		/*		DBMS�ڑ��G���[�`��		*/
		/*		��^���� : �����͂���܂���		*/
		static function SQLConnectError()
		{
			print '<p>DBMS�ɐڑ��ł��܂���</p>';
		}

		/*		SQL���s���s���̃G���[���b�Z�[�W�`��		*/
		/*		p0 : SqlConnect�I�u�W�F�N�g		*/
		static function SQLError( $_sqlObject )
		{
			print '<div style="background-color:#ffc;padding:5px;border:solid 1px #333">';
			print '<p><strong style="color:#f00">����SQL���̎��s�ŃG���[���������܂����B</strong><br>' . $_sqlObject->lastQuery . '</p>';
			print '<p><strong>SQL �G���[ ���b�Z�[�W : </strong><br>' . $_sqlObject->error() . '</p>';
			print '</div>';
		}
	}

	class templateMap
	{
		/*		�R���X�g���N�^		*/
		/*		p0 : SQLConnect�I�u�W�F�N�g		*/
		function __construct( $_sqlObject )
		{
			$this->sql = $_sqlObject;
		}

		/*		���[�U�[�^�C�v�ꗗ���擾		*/
		/*		��^���� : �����͂���܂���		*/
		function userTypes()
		{
			$resource = $this->sql->run( 'SELECT user_type FROM template' );

			while( $result = $this->sql->fetch( $resource ) )
			{
				foreach( explode( '/' , $result[ 0 ] ) as $value )
					$userTypes[ $value ] = 1;
			}

			return array_keys( $userTypes );
		}

		/*		�^�[�Q�b�g�ꗗ���擾		*/
		/*		p0 : ���[�U�[�^�C�v		*/
		function targets( $_userType )
		{
			$resource = $this->sql->run( 'SELECT target_type FROM template WHERE user_type LIKE "%/' . $_userType . '/%"' );

			while( $result = $this->sql->fetch( $resource ) )
				$targets[ $result[ 0 ] ] = 1;

			return array_keys( $targets );
		}

		/*		���x���ꗗ���擾		*/
		/*		p0 : ���[�U�[�^�C�v		*/
		/*		p1 : �^�[�Q�b�g		*/
		function labels( $_userType , $_target )
		{
			$resource = $this->sql->run( 'SELECT label FROM template WHERE user_type LIKE "%/' . $_userType . '/%" AND target_type = "' . $_target . '" ORDER BY target_type asc , label asc' );

			while( $result = $this->sql->fetch( $resource ) )
				$labels[ $result[ 0 ] ] = 1;

			return array_keys( $labels );
		}

		/*		�t�@�C���ꗗ���擾		*/
		/*		p0 : ���[�U�[�^�C�v		*/
		/*		p1 : �^�[�Q�b�g		*/
		/*		p2 : ���x��		*/
		function files( $_userType , $_target , $_label )
		{
			$resource = $this->sql->run( 'SELECT file , activate FROM template WHERE user_type LIKE "%/' . $_userType . '/%" AND target_type = "' . $_target . '" AND label = "' . $_label . '" ORDER BY target_type asc , label asc' );

			while( $result = $this->sql->fetch( $resource , 'assoc' ) )
			{
				if( 1 & $result[ 'activate' ] )
					$files[ 0 ][] = $result[ 'file' ];
				if( 2 & $result[ 'activate' ] )
					$files[ 1 ][] = $result[ 'file' ];
				if( 4 & $result[ 'activate' ] )
					$files[ 2 ][] = $result[ 'file' ];
				if( 8 & $result[ 'activate' ] )
					$files[ 3 ][] = $result[ 'file' ];
			}

			return $files;
		}

		function duplicate( $_userType , $_target , $_label , $_activate )
		{
			$resource = $this->sql->run( 'SELECT owner , activate FROM template WHERE user_type LIKE "%/' . $_userType . '/%" AND target_type = "' . $_target . '" AND label = "' . $_label . '" AND activate & ' . $_activate . ' = ' . $_activate );

			while( $result = $this->sql->fetch( $resource ) )
			{
				if( isset( $owner[ $result[ 0 ] ] ) )
					return true;

				$owner[ $result[ 0 ] ] = 1;
			}

			return false;
		}
	}

	class drawer
	{
		function push( $_files , $_duplicate )
		{
			if( !$this->length )
			{
				$this->files  = $_files;
				$this->dup    = $_duplicate;
				$this->length = 1;
				return;
			}
			else if( $this->dup == $_duplicate )
			{
				if( is_array( $this->files ) && is_array( $_files ) )
				{
					if( !count( array_diff( $this->files , $_files ) ) && !count( array_diff( $_files , $this->files ) ) )
					{
						$this->length++;
						return;
					}
				}
				else
				{
					if( !$this->files && !$_files )
					{
						$this->length++;
						return;
					}
				}
			}

			$this->rfiles[]  = $this->files;
			$this->rdup[]    = $this->dup;
			$this->rlength[] = $this->length;

			$this->files  = $_files;
			$this->dup    = $_duplicate;
			$this->length = 1;
		}

		function string()
		{
			$this->rfiles[]  = $this->files;
			$this->rdup[]    = $this->dup;
			$this->rlength[] = $this->length;

			$color = Array( '#ffc' , '#fc9' , '#fc3' , '#f90' );
			$index = 0;

			for( $i = 0 ; $i < count( $this->rfiles ) ; $i++ )
			{
				if( is_array( $this->rfiles[ $i ] ) )
				{
					if( $this->rdup[ $i ] )
						print '<td colspan="' . $this->rlength[ $i ] . '" style="border:solid 1px #666;background-color:#f00;color:#fff;font-weight:bold;">' . implode( '<br>' , $this->fileCheck( $this->rfiles[ $i ] ) ) . '</td>';
					else
						print '<td colspan="' . $this->rlength[ $i ] . '" style="border:solid 1px #666;background-color:' . $color[ $index ] . '">' . implode( '<br>' , $this->fileCheck( $this->rfiles[ $i ] ) ) . '</td>';
				}
				else
					print '<td colspan="' . $this->rlength[ $i ] . '" style="background-color:#fff">&nbsp;</td>';

				$index += $this->rlength[ $i ];
			}
		}

		function fileCheck( $_files )
		{
			global $template_path;

			for ( $i = 0 ; $i < count( $_files ) ; $i++ )
			{
				if( !file_exists( $template_path . $_files[ $i ] ) )
					$_files[ $i ] .= '�@<span style="background-color:#000;color:#fff;font-weight:bold;padding:2px;">No File!</span>';
				else if( 0 === filesize( $template_path . $_files[ $i ] ) )
					$_files[ $i ] .= '�@<span style="background-color:#f00;color:#fff;font-weight:bold;padding:2px;">Empty File!</span>';
			}

			return $_files;
		}
	}
?>
