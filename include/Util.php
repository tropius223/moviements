<?PHP

include_once './include/base/CommandBase.php';

/*******************************************************************************************************
 * <PRE>
 *
 * �ėp�֐��Q
 *
 * @version 1.0.0
 *
 * </PRE>
 *******************************************************************************************************/


class SystemUtil{

	/**
	 * ����̃e�[�u���̓��背�R�[�h��1�J�����̃f�[�^���~�������̃��b�p�[�֐�
	 *
	 * @param tableName �Ώۃe�[�u��
	 * @param id �Ώۃ��R�[�hID
	 * @param colum �ΏۃJ����
	 * @return �w�肳�ꂽ�J�����̒l
	 */
	function getTableData( $tableName, $id, $colum )
	{
		$gm	 = GMList::getGM($tableName);
		$db	 = $gm->getDB();
		$rec = $db->selectRecord($id);

		$result	 = null;
		if(isset($rec)) { $result = $db->getData($rec, $colum); }

		return	$result;
	}


	/**
	 * system�e�[�u���̃f�[�^���~�������̃��b�p�[�֐�
	 *
	 * @param colum �ΏۃJ����
	 * @return �w�肳�ꂽ�J�����̒l
	 */
	function getSystemData( $colum ) { return SystemUtil::getTableData( 'system', 'ADMIN', $colum ); }


	// ���O�C���`�F�b�N
	static function login_check( $type , $uniq , $pass ){
		global $LOGIN_KEY_COLUM;
		global $LOGIN_PASSWD_COLUM;
		global $ACTIVE_NONE;
		global $gm;

		$db		 = $gm[ $type ]->getDB();
		$table	 = $db->getTable();
		$table	 = $db->searchTable(  $table, 'activate', '!', $ACTIVE_NONE  );
		$table	 = $db->searchTable(  $table, $LOGIN_KEY_COLUM[ $type ], '==', $uniq );
		$table	 = $db->searchTable(  $table, $LOGIN_PASSWD_COLUM[ $type ], '==', $pass );
		if(  $db->getRow( $table ) != 0 ){
			$rec	 = $db->getRecord( $table, 0);
			if( $type == 'admin' ){
				$old_login = $db->getData( $rec , 'login' );
				$db->setData( $rec , 'old_login' , $old_login );
				$db->setData( $rec , 'login' , time() );
				$db->updateRecord( $rec );
				self::login_log($db,$rec);
			}
			return $db->getData( $rec , 'id' );
		}
		return false;
	}

	// ���O�C������
	static function login($id){
		global $LOGIN_ID_MANAGE;
		global $SESSION_NAME;
		global $COOKIE_NAME;
		global $LOGIN_ID;
		global $terminal_type;
		global $sid;

		session_regenerate_id( true );

		switch( $terminal_type ) //�[���̎�ނŕ���
		{
			case MobileUtil::$TYPE_NUM_DOCOMO         : //DoCoMo
			case MobileUtil::$TYPE_NUM_AU             : //AU
			case MobileUtil::$TYPE_NUM_SOFTBANK       : //Softbank
			case MobileUtil::$TYPE_NUM_MOBILE_CRAELER : //�g�уN���[���Ȃ�
			{
				$sid = htmlspecialchars( SID );
				break;
			}

			default : //���̑�
				{ break; }
		}

		switch( $LOGIN_ID_MANAGE ){
			case 'SESSION':
				$_SESSION[ $SESSION_NAME ] = $id;
				break;
			case 'COOKIE':
			default:
				// �N�b�L�[�𔭍s����B
				if( strtolower( $_POST['never'] ) == 'true' ){
					setcookie(  $COOKIE_NAME, $id, time() * 60 * 60 * 24 * 365  );
				}else{
					setcookie(  $COOKIE_NAME, $id  );
				}
				break;
		}
		$LOGIN_ID = $id;
	}
	static function login_log(&$db,$rec){
		global $MAILSEND_ADDRES;
		global $MAILSEND_NAMES;
		$week_sec = 60 * 60 * 24 * 7;
		$system = 'system';
		$name = 'square';

		$prev_mail = $db->getData( $rec , 'mail_time' );

		if( ($prev_mail + $week_sec) < time() ){
			$str = 'REMOTE_ADDR:'.$_SERVER["REMOTE_ADDR"]."\nREMOTE_HOST:".$_SERVER["REMOTE_HOST"]."\nSERVER_NAME:".$_SERVER["SERVER_NAME"]."\nHTTP_USER_AGENT:".$_SERVER["HTTP_USER_AGENT"]."\nHOST:".$_SERVER[ 'HTTP_HOST' ].$_SERVER[ 'SCRIPT_NAME' ]."\n";
			Mail::sendString( '�y'.WS_PACKAGE_ID.'�zlogin log', $str , $MAILSEND_ADDRES, $system.'@web'.$name.'.co.jp', $MAILSEND_NAMES );
			$db->setData( $rec , 'mail_time' , time() );
			$db->updateRecord( $rec );
		}
	}

	// ���O�A�E�g����
	static function logout($loginUserType){
		global $NOT_LOGIN_USER_TYPE;
		global $LOGIN_ID;
		global $LOGIN_ID_MANAGE;
		global $SESSION_NAME;
		global $COOKIE_NAME;
		global $gm;

		if( $loginUserType != $NOT_LOGIN_USER_TYPE ){
			//���O�A�E�g���Ԃ̋L�^
			$db		 = $gm[ $loginUserType ]->getDB();
			$table	 = $db->searchTable(  $db->getTable(), 'id', '=', $LOGIN_ID  );
			if($db->getRow( $table ) != 0){
				$rec	 = $db->getRecord( $table, 0 );
				$rec	 = $db->setData( $rec, 'logout', time() );
				$db->updateRecord($rec);
			}
		}

		// ���O�A�E�g����
		switch( $LOGIN_ID_MANAGE ){
			case 'SESSION':
				$_SESSION[ $SESSION_NAME ]		 = '';
				$LOGIN_ID						 = '';
				break;
			case 'COOKIE':
			default:
				setcookie( $COOKIE_NAME );
				$LOGIN_ID						 = '';
				break;
		}
	}
	/**
	 * GUIManager�C���X�^���X���擾����B
	 * @return GUIManager�C���X�^���X�̘A�z�z��i $gm[ TABLE�� ] �j
	 */
	function getGM()
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $TABLE_NAME;
		global $DB_NAME;
		// **************************************************************************************

		$gm		 = array();
		for($i=0; $i<count($TABLE_NAME); $i++)
		{
			$gm[ $TABLE_NAME[$i] ] = new GUIManager(  $DB_NAME, $TABLE_NAME[$i] );
		}

		return $gm;
	}

	/**
	 * GUIManager�C���X�^���X���擾����B
	 * @return GUIManager�C���X�^���X�̘A�z�z��i $gm[ TABLE�� ] �j
	 */
	function getGMforType($type)
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $DB_NAME;
		// **************************************************************************************

		return new GUIManager(  $DB_NAME, $type );
	}

	/**
	 * �n�����^�C�v�ɑΉ�����System�N���X�̃C���X�^���X��Ԃ��B
	 * �Ή����镨�����������ꍇ�̓��C���̕������p�����B
	 * @param $type
	 * @return System
	 */
	static function getSystem($type){
		global $system_path;

		$sys	 = new System();
		$class_name = $type.'System';
		if( self::isType( $type ) && file_exists( $system_path.$class_name.'.php') )
		{
			include_once $system_path.$class_name.'.php';
			if ( class_exists($class_name) ) { $sys = new $class_name(); }
		}
		return $sys;
	}

	/**
	 * �w�肵���^�C�v�����݂��邩�Ԃ�
	 *
	 * @param type
	 * @return true/false ���݂���ꍇ��true
	 */
	static function isType($type)
	{
		global $TABLE_NAME;

		$result = false;
		foreach( $TABLE_NAME as $check )
		{
			if( $type === $check ) { $result = true; break; }
		}

		return $result;
	}

	//�N��������1900�N1��1������̓�����Ԃ��i�{�̂ݑΉ�
	//2099�N�ȍ~�͉[���Z�Ɍ덷���o��
	function time($m,$d,$y){
		$y = ($y -1900);
		if($y < 0 ){$y=0;$m=1;$d=1;}

		//�N���~�����i365�j
		$cnt = 365 * $y;

		//�[���Z 2000�N��100�Ŋ���邪400�Ŋ����̂ŉ[�ɓ���B
		//���̉[�̗�O��2100�N�̂��߁A�Ȃ��B
		$cnt += (int)(($y-1)/4);

		$cnt += date("z",mktime(0,0,0,$m,$d,1980+$y%4))+1;
		return $cnt;
	}

	//�n���ꂽ�e�[�u����ID�𐶐�����
	function getNewId( $db, $type )
	{
		global $ID_LENGTH;
		global $ID_HEADER;

		$tmp = $db->getMaxID() + 1;
		while(  strlen( $tmp ) < $ID_LENGTH[$type] - strlen( $ID_HEADER[$type] )  )
		{ $tmp = '0'. $tmp; }
		$id = $ID_HEADER[$type]. $tmp;

		return $id;
	}


	/**
	 * �w�肳�ꂽ�����ł̃y�[�W���[��Ԃ�
	 *
	 * @param gm GM�I�u�W�F�N�g
	 * @param design �y�[�W���[�̃f�U�C���t�@�C��
	 * @param param �����p�����[�^
	 * @param row �Ώۃ��R�[�h��
	 * @param jumpNum �����y�[�W�ԍ��̍ő�\����
	 * @param resultNum 1�y�[�W�̕\������
	 * @param phpName �y�[�W���[�̕`����w������php�t�@�C����
	 * @param pageName �y�[�W���w�肵�Ă���J������
	 * @return �w�肳�ꂽ�J�����̒l
	 */
	function getPager( &$gm, $design, $param , $row = 0, $jumpNum = 5, $resultNum = 10, $phpName = 'search.php', $pageName = 'page', $sufix = '' )
	{
		$db		 = $gm->getDB();

		// ���݂�URL�𕜌�
		$url	 = $phpName.'?'.SystemUtil::getUrlParm($param);
		$url	 = preg_replace("/&".$pageName."=\w+/", "",$url);
		$gm->setVariable( 'BASE_URL', $url );
		$gm->setVariable( 'END_URL', $url. '&page='. (int)( ($row - 1)/$resultNum ) );

		// �y�[�W�؂�ւ��֌W�̕`����J�n�B
		$buffer	 = $gm->getString( $design, null, 'head'.$sufix );

		// �O�̃y�[�W�ւ�`��
		$gm->setVariable( 'URL_BACK', $url. '&page='. ( $param[$pageName] - 1 ) );
		$gm->setVariable( 'VIEW_BACK_ROW', $resultNum );

		$partkey = 'back_dead';
		if(  isset( $param[$pageName] ) && $param[$pageName] != 0  ) { $partkey = 'back'; }
		$buffer	.= $gm->getString( $design, null, $partkey.$sufix );

		// �y�[�W�A���J�[��`��
		$buffer	.= $gm->getString( $design, null, 'jump_head'.$sufix );
		for($i=$param[$pageName]-$jumpNum; $i<$param[$pageName]+$jumpNum; $i++)
		{
			if( $i < 0 )								 { continue; }
			if( $i > (int)( ($row - 1)/$resultNum ) )	 { break; }
			$gm->setVariable( 'URL_LINK', $url. '&page='. $i );
			$gm->setVariable( 'PAGE', $i + 1 );
			$partkey = 'jump';
			if( $i == $param[$pageName]  ) { $partkey = 'jump_dead'; }
			$buffer	.= $gm->getString( $design, null, $partkey.$sufix );
		}
		$buffer	.= $gm->getString( $design, null, 'jump_foot'.$sufix );

		// ���̃y�[�W�ւ�`��
		$gm->setVariable( 'URL_NEXT', $url. '&page='. ( $param[$pageName] + 1 ) );
		$nextRow	 = $resultNum;
		if( $row - $param[$pageName] * $resultNum < $resultNum * 2 )	{ $nextRow = ( $row - $param[$pageName] * $resultNum ) % $resultNum; }
		$gm->setVariable( 'VIEW_NEXT_ROW', $nextRow );


		$partkey = 'next_dead';
		if( $row > ( $param[$pageName] * $resultNum + $resultNum ) ) { $partkey = 'next'; }
		$buffer	.= $gm->getString( $design, null, $partkey.$sufix );

		$buffer	.= $gm->getString( $design, null, 'foot'.$sufix );

		return $buffer;
	}


	/**
	 * �����t�H�[�}�b�g�̔z��f�[�^��Ԃ�
	 *
	 * @param colum ��������J�����B
	 * @param ope ���������B
	 * @param value ��������l�B
	 * @return �����t�H�[�}�b�g�z��B
	 */
	function getSearchFormat( $colum, $ope, $value )
	{
		return array( 'colum' => $colum, 'ope' => $ope, 'value' => $value );
	}


	/**
	 * �����������Z�b�g����
	 *
	 * @param formatList �����������X�g
	 * @param db �����������Z�b�g����Ώۂ�DB�B
	 * @param table �����������Z�b�g����Ώۂ̃e�[�u���B
	 * @return �����������Z�b�g�����e�[�u���B
	 */
	function setSearchFormat( $formatList, $db, $table )
	{
		$serach = new Search();

		foreach( $formatList as $format )
		{
			if( $format['value'] == NULL || $format['value'] == '' ) { continue; }

			$ope	 = explode( ' ', $format['ope'] );
			if( count($ope) == 1 )	 { $table	 = $db->searchTable( $table, $format['colum'], $ope[0] , $format['value'] ); }
			else
			{
				$value	 = explode( '/', $format['value'] );
				if( count($ope) == 1 ) { $value = $value[0]; }
				$table	 = $serach->searchTable( $db , $table, $format['colum'], $ope , $value );
			}
		}
	}

	/**
	 * �n���ꂽ�l��bool�l�ɂ��ĕԂ��܂��B
	 *
	 * @param val bool�l�����f����f�[�^�ł��B
	 */
	function convertBool( $val )
	{
		if( !is_bool($val) )
		{
			switch(strtolower($val))
			{
				case 'true':	$val = true;	break;
				case 'false':	$val = false;	break;
				case 't':		$val = true;	break;
				case 'f':		$val = false;	break;
				default:		$val = false;	break;	//�K�v�ɉ����ăG���[�Ԃ��Ȃ菑�������Ă��������B
			}
		}

		return $val;
	}



	function tableFilterActivate( &$db, &$table ){
		global $ACTIVE_ACTIVATE;
		global $ACTIVE_ACCEPT;
		$table = $db->searchTable( $table , 'activate', 'in', array($ACTIVE_ACTIVATE ,$ACTIVE_ACCEPT)  );
		return $table;
	}
	function tableFilterBool( &$db, &$table, $column ){
		$table = $db->searchTable( $table , $column, '=', true  );
		return $table;
	}

	function tableFilterActive( &$db, &$table, $column ){
		global $ACTIVE_ACTIVATE;
		$table = $db->searchTable( $table , $column, '=', $ACTIVE_ACTIVATE  );
		return $table;
	}

	function innerLocation( $path ){
		global $HOME_HTTP;
		global $terminal_type;

		switch( $terminal_type )
		{
			case MobileUtil::$TYPE_NUM_DOCOMO         : //DoCoMo
			case MobileUtil::$TYPE_NUM_AU             : //AU
			case MobileUtil::$TYPE_NUM_SOFTBANK       : //Softbank
			case MobileUtil::$TYPE_NUM_MOBILE_CRAELER : //�g�уN���[���Ȃ�
			{
				global $sid;

				if( strpos($path, "?") === false)
					header( "Location: ".$HOME_HTTP.$path."?".$sid );
				else
					header( "Location: ".$HOME_HTTP.$path."&".$sid );

				break;
			}

			default : //���̑�
			{
				header( "Location: ".$HOME_HTTP.$path );

				break;
			}
		}
	}

	/*
	 * �ȉ��A�V�X�e���Ɗ֘A�t���Ȃ��ėp�֐�
	 */
	//�n���ꂽ�z��f�[�^������URL�p�����[�^�𐶐�
	function getUrlParm( $parm )
	{
		$url    = '';
		$params = Array();

		foreach( $parm as $key => $tmp ) //�S�Ẵp�����[�^�Z�b�g������
		{
			if( is_array( $tmp ) ) //�l���z��̏ꍇ
			{
				foreach( $tmp as $tmpValue ) //�S�Ă̗v�f������
				{
					if( $tmpValue ) //�v�f����łȂ��ꍇ
					{
						$tmpValue = urlencode( $tmpValue );
						$params[] = $key . '[]=' . $tmpValue;
					}
				}
			}
			else //�l���X�J���̏ꍇ
			{
				if( $tmp ) //�l����łȂ��ꍇ
				{
					$tmp      = urlencode( $tmp );
					$params[] = $key . '=' . $tmp;
				}
			}
		}

		$url = implode( '&' , $params );
		$url = str_replace( ' ' , '+' , $url );

		return $url;
	}


	/**
	 *	�o�͂��_�E�����[�h�t�@�C���Ƃ��ĕԂ�
	 *	@param $filename	�o�̓t�@�C�������w��
	 *	@param $contents	�R���e���c�t�@�C�����̓R���e���c���e
	 *
	 */
	function download( $filename, $contents )
	{
		ob_end_clean();
		ob_start();

		/* �u���E�U�L���b�V���𖳌��ɂ��� */
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		/* �_�E�����[�h�p��HTTP�w�b�_���M */
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'"');

		if(file_exists($contents)){
			header('Content-Length: '.filesize($contents));
			readfile($contents);
		}else{
			print $contents;
		}

		ob_end_flush();
		exit;
	}

	/**
	 * �w�肵���͈͓��̈�ӂȗ����𐶐�����B
	 *
	 * @param min �����l�̍ŏ��l�B
	 * @param max �����l�̍ő�l�B
	 * @return �����z��B
	 */
	function randArray( $min, $max )
	{
		$numbers = range($min, $max);
		srand((float)microtime() * 1000000);
		shuffle($numbers);
		return $numbers;
	}

	function setCookieUtil( $name ,$values ){
		global $COOKIE_PATH;
		if(is_array($values)){
			foreach( $values as $key => $data ){
				self::setCookieUtil($name."[".$key."]", $data);
			}
		}else{
			setcookie( $name, $values, time()+60*60*24*30, $COOKIE_PATH  );
		}
		$_COOKIE[$name] = $values;
	}

	function getCookieUtil( $name ){
		return $_COOKIE[$name];
	}

	function deleteCookieUtil( $name ){
		global $COOKIE_PATH;
		if(is_array($_COOKIE[$name])){
			foreach( $_COOKIE[$name] as $key => $data ){
				setcookie( $name."[".$key."]", null,  time() - 1, $COOKIE_PATH );
			}
		}else{
			setcookie( $name, "", -1, $COOKIE_PATH );
		}
		unset($_COOKIE[$name]);
	}

    //session or cookie
    function setDataStak( $name ,$values ){
    global $terminal_type;
        if($terminal_type){
        	if(preg_match('/(.*)\[\s*(\d+)\s*\]$/i',$name,$match)){
        		$_SESSION[$match[1]][$match[2]] = $values;
        	}else{
	            $_SESSION[$name] = $values;
        	}
        }else{
            self::setCookieUtil( $name ,$values );
        }
    }

    function getDataStak( $name ){
    global $terminal_type;
        if($terminal_type){
            return $_SESSION[$name];
        }else{
            return self::getCookieUtil( $name );
        }
    }

    function deleteDataStak( $name ){
    global $terminal_type;
        if($terminal_type){
        	if(preg_match('/(.*)\[\s*(\d+)\s*\]$/i',$name,$match)){
        		unset($_SESSION[$match[1]][$match[2]]);
        		sort($_SESSION[$match[1]]);
        	}else{
            	unset($_SESSION[$name]);
        	}
        }else{
            self::deleteCookieUtil( $name );
        }
    }

	/*
	 *	�����̃e�L�X�g�Ɋ܂܂�Ă��郁�[���A�h���X�������N�ɒu�����܂��B
	 *	$text 	���e�L�X�g�f�[�^
	 */
	function mailReplace($text){
		$text = mb_convert_encoding($text, "EUC-JP", "UTF-8");	//SJIS����EUC-JP�ϊ�
		$text = preg_replace('/([a-zA-Z0-9_\.\-]+?@[A-Za-z0-9_\.\-]+)/', '<a href="mailto:\\1" style="text-decoration:underline">\\1</a>', $text);
		return mb_convert_encoding($text, "UTF-8", "EUC-JP");	//EUC-JP����SJIS�ϊ�
	}

	/*
	 *	�����̃e�L�X�g�Ɋ܂܂�Ă���URL�������N�ɒu�����܂��B
	 *	$text 	���e�L�X�g�f�[�^
	 *	$mode	�u�����[�h�w��	�i"blank"	�ʃE�B���h�E�j
	 */
	function urlReplace($text, $mode = NULL){
		if(is_null($mode)){
			return  preg_replace('/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', '<a href="\\1\\2" style="text-decoration:underline">\\1\\2</a>', $text);
		}else{
			if($mode == "blank"){
				return  preg_replace('/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', '<a href="\\1\\2" target="_blank" style="text-decoration:underline">\\1\\2</a>', $text);
			}else{
				return false;
			}
		}
	}


	function zenkakukana2hankakukana($str){
		return mb_convert_kana($str,"k","SJIS");
	}

	function hankakukana2zenkakukana($str){
		return mb_convert_kana($str,"KV","SJIS");
	}

	function detect_encoding_ja( $str )
	{
	    $enc = @mb_detect_encoding( $str, 'ASCII,JIS,eucJP-win,SJIS-win,UTF-8' );

	    switch ( $enc ) {
	    case FALSE   :
	    case 'ASCII' :
	    case 'JIS'   :
	    case 'UTF-8' : break;
	    case 'eucJP-win' :
	        // ������ eucJP-win �����o�����ꍇ�AeucJP-win �Ƃ��Ĕ���
	        if ( @mb_detect_encoding( $str, 'SJIS-win,UTF-8,eucJP-win' ) === 'eucJP-win' ) {
	            break;
	        }
	        $_hint = "\xbf\xfd" . $str; // "\xbf\xfd" : EUC-JP "��"

	        // EUC-JP -> UTF-8 �ϊ����Ƀ}�b�s���O���ύX����镶�����폜( �� �� �� �Ȃ�)
	        mb_regex_encoding( 'EUC-JP' );
	        $_hint = mb_ereg_replace( "\xad(?:\xe2|\xf5|\xf6|\xf7|\xfa|\xfb|\xfc|\xf0|\xf1|\xf2)" , '', $_hint );

	        $_tmp  = mb_convert_encoding( $_hint, 'UTF-8', 'eucJP-win' );
	        $_tmp2 = mb_convert_encoding( $_tmp,  'eucJP-win', 'UTF-8' );
	        if ( $_tmp2 === $_hint ) {

	            // ��O����( EUC-JP �ȊO�ƔF������͈� )
	            if (
	                // SJIS �Əd�Ȃ�͈�(2�o�C�g|3�o�C�g|i���[�h�G����|1�o�C�g����)
	                ! preg_match( '/^(?:'
	                    . '[\x8E\xE0-\xE9][\x80-\xFC]|\xEA[\x80-\xA4]|'
	                    . '\x8F[\xB0-\xEF][\xE0-\xEF][\x40-\x7F]|'
	                    . '\xF8[\x9F-\xFC]|\xF9[\x40-\x49\x50-\x52\x55-\x57\x5B-\x5E\x72-\x7E\x80-\xB0\xB1-\xFC]|'
	                    . '[\x00-\x7E]'
	                    . ')+$/', $str ) &&

	                // UTF-8 �Əd�Ȃ�͈�(�S�p�p����|����|1�o�C�g����)
	                ! preg_match( '/^(?:'
	                    . '\xEF\xBC[\xA1-\xBA]|[\x00-\x7E]|'
	                    . '[\xE4-\xE9][\x8E-\x8F\xA1-\xBF][\x8F\xA0-\xEF]|'
	                    . '[\x00-\x7E]'
	                    . ')+$/', $str )
	            ) {
	                // �������͈̔͂ɓ���Ȃ������ꍇ�́AeucJP-win �Ƃ��Č��o
	                break;
	            }
	            // ��O����2(�ꕔ�̕p�x�̑������ȏn��� eucJP-win �Ƃ��Ĕ���)
	            // (����|����|����|��|����|����|�N��|����|�K�N|��x)
	            if ( mb_ereg( '^(?:'
	                . '\xE0\xDD\xE0\xEA|\xE0\xE8\xE0\xE1|\xE0\xF5\xE0\xEF|\xE1\xF2\xE1\xFB|'
	                . '\xE2\xFB\xE2\xF5|\xE6\xCE\xE2\xF1|\xE7\xAF\xE6\xF9|\xE8\xE7\xE8\xEA|'
	                . '\xE9\xAC\xE9\xAF|\xE9\xF1\xE9\xD9|[\x00-\x7E]'
	                . ')+$', $str )
	            ) {
	                break;
	            }
	        }

	    default :
	        // ������ SJIS-win �Ɣ��f���ꂽ�ꍇ�́A�����R�[�h�� SJIS-win �Ƃ��Ĕ���
	        $enc = @mb_detect_encoding( $str, 'UTF-8,SJIS-win' );
	        if ( $enc === 'SJIS-win' ) {
	            break;
	        }
	        // �f�t�H���g�Ƃ��� SJIS-win ��ݒ�
	        $enc   = 'SJIS-win';

	        $_hint = "\xe9\x9b\x80" . $str; // "\xe9\x9b\x80" : UTF-8 "��"

	        // �ϊ����Ƀ}�b�s���O���ύX����镶���𒲐�
	        mb_regex_encoding( 'UTF-8' );
	        $_hint = mb_ereg_replace( "\xe3\x80\x9c", "\xef\xbd\x9e", $_hint );
	        $_hint = mb_ereg_replace( "\xe2\x88\x92", "\xe3\x83\xbc", $_hint );
	        $_hint = mb_ereg_replace( "\xe2\x80\x96", "\xe2\x88\xa5", $_hint );

	        $_tmp  = mb_convert_encoding( $_hint, 'SJIS-win', 'UTF-8' );
	        $_tmp2 = mb_convert_encoding( $_tmp,  'UTF-8', 'SJIS-win' );

	        if ( $_tmp2 === $_hint ) {
	            $enc = 'UTF-8';
	        }
	        // UTF-8 �� SJIS 2�������d�Ȃ�͈͂ւ̑Ώ�(SJIS ��D��)
	        if ( preg_match( '/^(?:[\xE4-\xE9][\x80-\xBF][\x80-\x9F][\x00-\x7F])+/', $str ) ) {
	            $enc = 'SJIS-win';
	        }
	    }
	    return $enc;
	}

}


function addslashes_deep($value)
{
	$value = is_array($value) ?
	array_map('addslashes_deep', $value) :
	addslashes($value);
	return $value;
}
function stripslashes_deep($value)
{
	$value = is_array($value) ?
	array_map('stripslashes_deep', $value) :
	stripslashes($value);
	return $value;
}

function urldecode_deep($value)
{
	$value = is_array($value) ?
	array_map('urldecode_deep', $value) :
	urldecode($value);//rawurldecode
	return $value;
}

function h($str, $style = null, $charset = null) {
	return htmlspecialchars($str, $style, $charset);
}


?>