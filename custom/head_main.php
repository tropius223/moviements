<?PHP

	$mobile_flag = true;
	$charcode_flag = true;
	$magic_quotes_gpc = ini_get('magic_quotes_gpc');
	//$magic_quotes_gpc = false;
    if( $charcode_flag )
	{
		$TRUTH_INTERNAL_ENCODING = mb_internal_encoding();
        ini_set("output_buffering","Off"); // �o�̓o�b�t�@�����O���w�肵�܂� 
        ini_set("default_charset","Shift_JIS"); // �f�t�H���g�̕����R�[�h���w�肵�܂� 
        ini_set("extension","php_mbstring.dll"); // �}���`�o�C�g�������L���ɂ��܂��B 
        ini_set("mbstring.language","Japanese"); // �f�t�H���g����{��ɐݒ肵�܂��B 
        ini_set("mbstring.encoding_translation","Off");//�����G���R�[�f�B���O�𖳌��ɏo����ꍇ�͖����ɂ��� 
        ini_set("mbstring.detect_order","auto"); // �����R�[�h���o��auto�ɐݒ肵�܂��B 
        ini_set("mbstring.substitute_character","none"); // �����ȕ������o�͂��Ȃ��B 
        mb_internal_encoding('SJIS');

		// �P�[�^�C�̊G�������\������Ȃ��ꍇ�͈ȉ���2�s���R�����g�A�E�g
        mb_http_output('SJIS');
		
    }

	include_once "./custom/extends/debugConf.php";
    include_once "./include/Util.php";
	include_once "./custom/conf.php";
    
	session_start();

	switch( $terminal_type ) //�[���̎�ނŕ���
	{
		case MobileUtil::$TYPE_NUM_DOCOMO         : //DoCoMo
		case MobileUtil::$TYPE_NUM_AU             : //AU
		case MobileUtil::$TYPE_NUM_SOFTBANK       : //Softbank
		case MobileUtil::$TYPE_NUM_MOBILE_CRAELER : //�g�уN���[���Ȃ�
		{
			$sid = htmlspecialchars( SID , ENT_COMPAT | ENT_HTML401 , 'SJIS' );

			break;
		}

		default : //���̑�
		{
			if( session_id() == $_GET[ ini_get( 'session.name' ) ] || session_id() == $_POST[ ini_get( 'session.name' ) ] ) //GET/POST����̐ݒ�͈�x�����ύX����
				{ session_regenerate_id(); }

			break;
		}
	}
/*
//euc-jp��$charcode_flag=true��post,get������������ꍇ�ɗL���ɂ���
    if( $charcode_flag ){
    	mb_convert_variables("SJIS",$TRUTH_INTERNAL_ENCODING, $_POST);
    	mb_convert_variables("SJIS",$TRUTH_INTERNAL_ENCODING, $_GET);
    }
*/
    if ($magic_quotes_gpc) {
        $_GET = stripslashes_deep($_GET);
        $_POST = stripslashes_deep($_POST);
        $_COOKIE = stripslashes_deep($_COOKIE);
        $_FILES = stripslashes_deep( $_FILES);
    }

	include_once "./include/autoload.php";
	include_once "./include/ws.php";
	include_once "./include/ccProc.php";
	include_once "./include/IncludeObject.php";
	include_once "./include/GUIManager.php";
	include_once "./include/Search.php";
	include_once "./include/Mail.php";
	include_once "./include/Template.php";
	include_once "./include/Command.php";
	include_once "./include/GMList.php";
	include_once "./custom/checkData.php";
	include_once "./custom/extension.php";
	include_once "./custom/global.php";
    include_once "./module/module.inc";
    include_once $system_path."System.php";
   	
	CleanGlobal::action();

	// �f�[�^�x�[�X���[�h
	$gm		 = SystemUtil::getGM();

    //sytem data set
    $tdb = $gm['system']->getDB();
    $trec = $tdb->getRecord( $tdb->getTable() , 0 );
    
    //global�ϐ��̒�`
	$HOME				= $tdb->getData( $trec , 'home' );
	$MAILSEND_ADDRES	= $tdb->getData( $trec , 'mail_address' );
	$MAILSEND_NAMES 	= $tdb->getData( $trec , 'mail_name' );
	$LOGIN_ID_MANAGE	= $tdb->getData( $trec , 'login_id_manage' );
	$css_name			= $tdb->getData( $trec , 'main_css' );

	// ���[�UID�����
	switch( $LOGIN_ID_MANAGE )
	{
		case 'SESSION':
			$LOGIN_ID	 = $_SESSION[ $SESSION_NAME ]; break;
		case 'COOKIE':	
		default:
			$LOGIN_ID	 = $_COOKIE[ $COOKIE_NAME ]; break;
	}
	
	// ���O�C�����Ă��郆�[�U�̃��[�U�^�C�v���Ƃ��̌����̎擾
	$loginUserType = $NOT_LOGIN_USER_TYPE;
	$loginUserRank = $ACTIVE_ACTIVATE;
	if(  isset( $LOGIN_ID ) &&  $LOGIN_ID != '' )
	{
		for($i=0; $i<count($TABLE_NAME); $i++)
		{
			if(  $THIS_TABLE_IS_USERDATA[ $TABLE_NAME[$i] ]  )
			{
				$db		 = $gm[ $TABLE_NAME[$i] ]->getDB();
				$table	 = $db->searchTable( $db->getTable(), 'id', '=', $LOGIN_ID );
				if( $db->getRow($table) != 0 )
				{
					$rec			 = $db->getRecord( $table, 0 );
					$loginUserType	 = $TABLE_NAME[$i];
					$loginUserRank	 = $db->getData( $rec, 'activate' );
					break;
				}
			}
		}
	}
?>
