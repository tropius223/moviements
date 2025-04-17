<?PHP

	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * 入力内容チェッククラス
	 * 
	 * @author 丹羽一智
	 * @version 1.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/

class CheckData
{
	var $gm;
	var $error_design;
	var $check;
	var $error_name;
	var $error_msg;
	var $edit;
	var $_DEBUG	 = false;

	function __construct( &$gm, $edit, $loginUserType, $loginUserRank )
	{
		$this->gm			 = $gm;
		$this->error_design	 = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_ERROR_DESIGN' );
		$this->check		 = true;
		$this->error_name  	 = Array();
		$this->error_msg   	 = Array();
		$this->edit			 = $edit;
	}
	
	// 正規表現による汎用的な構文チェック
	function checkRegex()
	{
		global $THIS_TABLE_IS_STEP_PC;
		global $THIS_TABLE_IS_STEP_MOBILE;
		global $terminal_type;

		$useStep = false;

		if( 0 < $terminal_type ) //携帯端末の場合
			{ $useStep = $THIS_TABLE_IS_STEP_MOBILE[ $_GET[ 'type' ] ]; }
		else //PC端末の場合
			{ $useStep = $THIS_TABLE_IS_STEP_PC[ $_GET[ 'type' ] ]; }

		for($i=0; $i<count($this->gm[ $_GET['type'] ]->colName); $i++)
		{
			if( $useStep )
			{
				if($this->gm[ $_GET['type'] ]->maxStep >= 2 && $this->gm[ $_GET['type'] ]->colStep[$this->gm[ $_GET['type'] ]->colName[$i]] != $_POST['step'])
					continue;
			}

			$name		 = $this->gm[ $_GET['type'] ]->colName[$i];
			if( strlen( $this->gm[ $_GET['type'] ]->colRegex[ $name ] ) )
			{
				if( isset( $_POST[ $name ]) && $_POST[ $name ] != null )
				{
					if( !preg_match( $this->gm[ $_GET['type'] ]->colRegex[ $name ],$_POST[ $name ]) )
					{
						$this->addError( $name. '_REGEX' );
						$this->addValidate( $name , $name. '_REGEX' );
					}
				}
			}
		}
		return $this->check;
	}

	// 空欄チェック
	function checkNull($name,$args)
	{
		if(    !isset(   $_POST[ $name ]   ) || is_null($_POST[  $name  ]) || $_POST[  $name  ] === '' )
		{
			if( $_FILES[  $name  ]['name'] )	 { return $this->check; }
			$this->addError( $name );
			$this->addValidate( $name , $name );
		}
		return $this->check;
	}
	
	// 空欄チェック
	// Errorメッセージを指定したcolmと共通利用する
	function checkNullset($name,$args)
	{
    	if( $this->error_name[$args[0]] )
    		return $this->check;
		
		if( !isset( $_POST[ $name ] ) || $_POST[ $name ] == null )
		{
			if( $_FILES[ $name ]['name'] ){ return $this->check; }
			$this->addError( $args[0] );
			$this->addValidate( $name , $args[0] );
		}
		return $this->check;
	}
	
	// 特定のユーザーの場合に空欄チェック
	function checkNullAuthority($name,$args)
	{
		global $loginUserType;
		
		if(!count($args)){return;}
		
		if( array_search($loginUserType,$args) !== FALSE && ( !isset(   $_POST[ $name ]   ) || $_POST[  $name  ] == null ) )
		{
			if( $_FILES[  $name  ]['name'] )	 { return $this->check; }
			$this->addError( $name );
			$this->addValidate( $name , $name );
		}
		return $this->check;
	}

	// 引数で指定した条件を見たす場合にチェック(複数条件指定可能
	function checkNullFlag($name,$args)
	{
		if( !isset($args[0]) || !isset($args[1]) ){
			return $this->check;
		}else{
			for($i=0;isset($args[$i]);$i+=2)
			{
				if( !isset( $_POST[$args[$i]] ) || $_POST[$args[$i]] != $args[$i+1] )
					return $this->check;
			}
		}
		return $this->checkNull($name,$args);
	}

	function checkNullCarrier( $name , $args )
	{
		if( 'TRUE' != $_POST[ 'use_carrier_url' ] )
			{ return $this->check; }

		foreach( Array( 'url_docomo' , 'url_au' , 'url_softbank' , 'url_iphone' , 'url_android' ) as $col )
		{
			if( $_POST[ $col ] )
				{ return $this->check; }
		}

		$this->addError( 'url_carriers' );
		$this->addValidate( $name , 'url_carriers' );
	}

	function checkSize($name,$args){
		if( strlen( $_POST[ $name ] ) > $args[0] )
		{
			$this->addError( $name.'_size' );
			$this->addValidate( $name , $name.'_size' );
		}
		return $this->check;
	}

	// 引数で指定した条件を見たす場合にチェック(複数条件指定可能
	function checkFlag($name,$args)
	{
		if( !isset($args[0]) || !isset($args[1]) ){
			return $this->check;
		}else{
			if( !isset( $_POST[$args[0]] ) || $_POST[$args[0]] != $args[1] )
				return $this->check;
		}
		return call_user_func(array($this,'check'.$args[2]), $name, array_slice($args,3) );
	}
	
	// 任意のtableのidとして存在している
	function checkIntable($name,$args)
	{
		$type = $args[0];
		
		if( isset( $_POST[ $name ] ) && $_POST[ $name ] != null )
		{
			$db = $this->gm[ $type ]->getDB();
			if(! $db->getRow( $db->searchTable( $db->getTable(), 'id' , '=' , $_POST[ $name ] ) ) )
			{
				$this->addError( $args[0].'_in_table' );
				$this->addValidate( $name , $args[0].'_in_table' );
			}
		}
		return $this->check;
	}

	/**
		@brief   編集禁止カラムチェック。
		@details POSTデータがレコードの元の値と異なる場合、エラー情報を追加します。
	*/
	function checkConst( $name , $args )
	{
		if( !isset( $_POST[ $name ] ) ) //POSTされてないならチェック不要
			return $this->check;

		//オリジナルデータを取得
		$db     = SystemUtil::getGMforType( $_GET[ 'type' ] )->getDB();
		$rec    = $db->selectRecord( $_GET[ 'id' ] );
		$origin = $db->getData( $rec , $name );

		if( $origin != $_POST[ $name ] )
		{
			//個別メッセージ用エラーパート
			$this->addError( $name . '_isConst' );
			$this->addValidate( $name , $name . '_isConst' );

			//単一メッセージ用エラーパート
			if( !$this->error_name[ 'Const' ] )
				$this->addError( 'Const' );
		}

		return $this->check;
	}

	/**
		@brief   管理者データチェック。
		@details 管理者以外のユーザーが編集しようとした場合、エラー情報を追加します。
	*/
	function checkAdminData( $name , $args )
	{
		global $loginUserType;

		if( 'admin' == $loginUserType ) //管理者はパス
			return $this->check;

		if( !isset( $_POST[ $name ] ) ) //POSTされてないならチェック不要
			return $this->check;

		//オリジナルデータを取得
		$db     = SystemUtil::getGMforType( $_GET[ 'type' ] )->getDB();
		$rec    = $db->selectRecord( $_GET[ 'id' ] );
		$origin = $db->getData( $rec , $name );

		if( $origin != $_POST[ $name ] )
		{
			//個別メッセージ用エラーパート
			$this->addError( $name . '_isAdminData' );
			$this->addValidate( $name , $name . '_isAdminData' );

			//単一メッセージ用エラーパート
			if( !$this->error_name[ 'AdminData' ] )
				$this->addError( 'AdminData' );
		}

		return $this->check;
	}

    function is_uri($text,$level = 1){
        switch($level){
            case 0: default:
            //接頭と使用文字の一致
                if (!preg_match("/https?:\/\/[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,%#]+/", $text)){ return FALSE; }
                break;
            case 1:
            //http URL の正規表現
                $re = "/\b(?:https?|shttp):\/\/(?:(?:[-_.!~*'()a-zA-Z0-9;:&=+$,]|%[0-9A-Fa-f" .
                      "][0-9A-Fa-f])*@)?(?:(?:[a-zA-Z0-9](?:[-a-zA-Z0-9]*[a-zA-Z0-9])?\.)" .
                      "*[a-zA-Z](?:[-a-zA-Z0-9]*[a-zA-Z0-9])?\.?|[0-9]+\.[0-9]+\.[0-9]+\." .
                      "[0-9]+)(?::[0-9]*)?(?:\/(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f]" .
                      "[0-9A-Fa-f])*(?:;(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-" .
                      "Fa-f])*)*(?:\/(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f" .
                      "])*(?:;(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)*)" .
                      "*)?(?:\?(?:[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])" .
                      "*)?(?:#(?:[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)?/";
                
                if (!preg_match($re, $text)) { return FALSE; }
                break;
        }
        return TRUE;
    }
    
    function checkUri($name,$args){
    	if( $this->error_name[$name] || !strlen( $_POST[$name] ) )
    		return $this->check;
    
    	if(count($args))
    		$level=$args[0];
    	else
    		$level=1;
    	
        if(!$this->is_uri($_POST[$name],$level)){
			$this->addError($name. '_URI');
			$this->addValidate($name , $name. '_URI');
        }
        return $this->check;
    }
    
	function is_mail($text,$level = 3,$dns_check = false) 
	{
        switch($level){
            case 0: default:
                if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $text)){ return FALSE; }
                break;
            case 1:
            //http://www.tt.rim.or.jp/~canada/comp/cgi/tech/mailaddrmatch/
            //「なるべく」おかしなアドレスを弾く正規表現
                if (!preg_match("/^[\x01-\x7F]+@(([-a-z0-9]+\.)*[a-z]+|\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])/", $text)){ return FALSE; }
                break;
            case 2:
            //PEAR::Mail_RFC822
                if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=??:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i", $text)){ return FALSE; }
                break;
            case 3:
            //CakePHP
                if (!preg_match("/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i", $text)){ return FALSE; }
                break;
            case 4:
            //symfony
                if (!preg_match("/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i", $text)){ return FALSE; }
                break;
            case 5:
            //Cal Henderson: http://iamcal.com/publish/articles/php/parsing_email/pdf/
            //Parsing Email Adresses in PHP
                $re = '/^([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-'
                   .'\\x5d\\x7f-\\xff]+|\\x22([^\\x0d\\x22\\x5c\\x80-\\xff]|\\x5c\\x00-'
                   .'\\x7f)*\\x22)(\\x2e([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-'
                   .'\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x22([^\\x0d\\x22\\x5c\\x80'
                   .'-\\xff]|\\x5c\\x00-\\x7f)*\\x22))*\\x40([^\\x00-\\x20\\x22\\x28\\x29'
                   .'\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x5b([^'
                   .'\\x0d\\x5b-\\x5d\\x80-\\xff]|\\x5c\\x00-\\x7f)*\\x5d)(\\x2e([^\\x00-'
                   .'\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-'
                   .'\\xff]+|\\x5b([^\\x0d\\x5b-\\x5d\\x80-\\xff]|\\x5c\\x00-\\x7f)*'
                   .'\\x5d))*$/';
                
                if (!preg_match($re, $text)) { return FALSE; }
                break;
        }
        
        //存在するメールアドレスかどうかを確認するためdnsのチェック
        if($dns_check){
             if (function_exists('checkdnsrr')) {
                 $tokens = explode('@', $text);
                 if (!checkdnsrr($tokens[1], 'MX') && !checkdnsrr($tokens[1], 'A')) 
                 {
                     return FALSE;
                 }
             }
        }
        return TRUE;
    }
    
    function checkMail($name,$args){
    	if( $this->error_name[$name] || !strlen( $_POST[$name] ) )
    		return $this->check;
    
    	if(isset($args[0]) && strlen($args[0]) ){
    		$level=$args[0];
    	}else{ $level=3; }
    	
    	if(isset($args[1]) && strlen($args[1]) ){
    		$dns_check=(boolean)$args[1];
    	}else{ $dns_check=false; }
    	
        if( !$this->is_mail($_POST[$name]) ){
			$this->addError($name. '_MAIL');
			$this->addValidate($name , $name. '_MAIL');
        }
        return $this->check;
    }
    
	// 重複チェック処理
	function checkDuplication( $name ,$args){
		// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
		// **************************************************************************************
		if(  isset( $_POST[$name] )  )
		{
            $db		 = $this->gm[ $_GET['type'] ]->getDB();
            $table	 = $db->getTable();
            if( isset( $_POST['id'] ) ) { $table	 = $db->searchTable($table, 'id', '!', $_POST['id']); }
            $table	 = $db->searchTable($table, $name, '=', $_POST[$name]);
            $row	 = $db->getRow($table);
            if( $row > 0 )
            {
				$this->addError($name.'_dup');
				$this->addValidate($name , $name.'_dup');
            }
		}
		return $this->check;
    }
        
	// メールの重複チェック処理
	function checkMailDup($name,$args)
	{
		// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
		   global $THIS_TABLE_IS_USERDATA;
		   global $TABLE_NAME;
		// **************************************************************************************

		if( isset( $_POST[$name] ) )
		{// メールアドレス重複チェック

			$cnt	 = 0;
			$max	 = count($TABLE_NAME);
			for($i=0; $i<$max; $i++)
			{
				if(  $THIS_TABLE_IS_USERDATA[ $TABLE_NAME[$i] ]  )
				{
					$db		 = $this->gm[ $TABLE_NAME[$i] ]->getDB();
					$table	 = $db->getTable();
					if( isset( $_POST['id'] ) ) { $table	 = $db->searchTable($table, 'id', '!', $_POST['id']); }
					$table	 = $db->searchTable($table, 'mail', '=', $_POST[$name]);
					$cnt	 += $db->getRow($table);
					if( $cnt > 0 )
					{
						$this->addError('mail_dup');
						$this->addValidate($name , 'mail_dup');
						break;
					}
				}
			}
		}
		return $this->check;
	}

	//日付の整合チェック
	function checkDate($name,$args)
	{
		$y = $name;
		$m = $args[0];
		$d = $args[1];
		if( strlen($_POST[$y]) && strlen($_POST[$m]) && strlen($_POST[$d]) ){
			if( ! checkdate($_POST[$m],$_POST[$d] , $_POST[$y]) ){
				$this->addError('date_format');
				$this->addValidate($name , 'date_format');
			}
		}
		
		return $this->check;
	}
	
	//日付が現在時刻より過去かどうか(checkDataとセットで使う
	function checkOlddate($name,$args)
	{
		
		$y = $name;
		$m = $args[0];
		$d = $args[1];
		if(isset($args[2]))
			$h = $args[2];
		if(isset($args[3]))
			$min = $args[3];
		if( strlen($_POST[$y]) && strlen($_POST[$m]) && strlen($_POST[$d]) ){
			if(!$this->error_name[ 'date_format' ]){
				
				if( mktime( $h?$_POST[$h]:0 , $min?$_POST[$min]:0 ,0,$_POST[$m],$_POST[$d] , $_POST[$y]) < time() ){
					$this->addError('old_date');
					$this->addValidate($name,'old_date');
				}	
			}
		}

		return $this->check;
	}
	
	// パスワード一致チェック
	function checkPass($name,$args)
	{
		$pass1 = $name;
		$pass2 = $args[0];

		if(  isset( $_POST[ $pass1 ] ) && isset( $_POST[ $pass2 ] ) && $_POST[ $pass1 ] != $_POST[ $pass2 ]  )
		{
			$this->addError('pass_error');
			$this->addValidate($name,'pass_error');
		}
		
		return $this->check;
	}

	//指定tableの指定columnに自信のidが存在するかどうか
	function checkChild($id, $type, $column ){
		
		$cdb = $this->gm[ $type ]->getDB();
		$ctable = $cdb->searchTable( $cdb->getTable(), $column, '=', $id );
		
		if( $cdb->getRow( $ctable ) ){
			$this->addError($type.'_CHILD');
			$this->addValidate($name,$type.'_CHILD');
		}
		return $this->check;
	}

	// 生年月日チェック
	function checkBirth( $name , $args )
	{
		if( $_POST[ $name ] && $_POST[ $args[ 0 ] ] && $_POST[ $args[ 1 ] ] )
			return $this->check;

		$this->addError( 'birth' );
		$this->addValidate( $name,'birth' );
		return $this->check;
	}

	// 汎用チェック処理を一括で行う
	function generalCheck($edit)
	{
		global $THIS_TABLE_IS_STEP_PC;
		global $THIS_TABLE_IS_STEP_MOBILE;

		$this->checkRegex();

		global $terminal_type;

		$useStep = false;

		if( 0 < $terminal_type ) //携帯端末の場合
			{ $useStep = $THIS_TABLE_IS_STEP_MOBILE[ $_GET[ 'type' ] ]; }
		else //PC端末の場合
			{ $useStep = $THIS_TABLE_IS_STEP_PC[ $_GET[ 'type' ] ]; }

		$row = count($this->gm[ $_GET['type'] ]->colName);
		for($i=0; $i<$row; $i++)
		{
			if( $useStep )
			{
				if($this->gm[ $_GET['type'] ]->maxStep >= 2 && $this->gm[ $_GET['type'] ]->colStep[$this->gm[ $_GET['type'] ]->colName[$i]] != $_POST['step'])
					continue;
			}
			
			$faled		 = false;
			$name		 = $this->gm[ $_GET['type'] ]->colName[$i];
			
			//Null,Uri,Mail,Duplication,MailDup,Pass,Birth,
			
			if( !$edit )	{	$pal = $this->gm[ $_GET['type'] ]->colRegist[ $name ];	}
			else			{	$pal = $this->gm[ $_GET['type'] ]->colEdit[ $name ];	}
			
			if( strlen($pal) ){
				$checks = explode('/', $pal );
				
				foreach( $checks as $check ){
					if( strpos($check,':') === FALSE ){
						call_user_func(array($this,'check'.$check), $name, Array() );
					}
					else{
						$val = explode(':', $check );
						call_user_func(array($this,'check'.$val[0]), $name, array_slice($val,1));
					}
					if( $this->_DEBUG ){ d('generalCheck: column('.$name.')  check('.$checks.')');}
				}
			}
		}
		return $this->check;
	}

	function checkShort( $name , $args )
	{
		$min = $args[ 0 ];

		$len = strlen( $_POST[ $name ] );

		if( $len == 0 )
			{ return $this->check; }
		else if( $len < $min )
		{
			$this->addError( $name . '_short' );
			$this->addValidate( $name , $name . '_short' );
		}

		return $this->check;
	}

	// エラー内容を取得
	function getError( $label = null )
	{
		$tmp = '';
		
		if( !$this->check )
		{// エラー内容がある場合$
			
			if(is_null($label)){
				$error	.= join($this->error_msg,"\n");
			}else if($this->error_name[ $label ] || $label == 'is_error' ){
				$error	.= $this->error_msg[ $label ];
			}

			if( strlen($error) )
			{
				$tmp	.= $this->gm[ $_GET['type'] ]->partGetString( $this->error_design , 'head');
				$tmp	.= $error;
				$tmp	.= $this->gm[ $_GET['type'] ]->partGetString( $this->error_design , 'foot');
			}
		}
		
		return $tmp;
	}

	// エラー内容を取得
	function isError( $label = null, $data )
	{
		$tmp = '';
		if( !strlen($data) ) { $data = 'validate'; }
		if( $this->error_name[ $label ] ) { $tmp = $data;  }
		
		return $tmp;
	}
	
	//指定カラムが現在のstepのものかどうかを返す
	function checkStep( $name ){
		if($this->gm[ $_GET['type'] ]->maxStep >= 2 && $this->gm[ $_GET['type'] ]->colStep[$this->gm[ $_GET['type'] ]->colName[$i]] != $_POST['step'])
			return;
	}
    
    function getCheck(){
        return $this->check;
    }
    
    function addError($part){
        $this->error_msg[ $part ] .= $this->gm[ $_GET['type'] ]->partGetString(  $this->error_design , $part );
		$this->error_name[ $part ] = true;
        if($this->check) { $this->error_msg[ 'is_error' ] .= $this->gm[ $_GET['type'] ]->partGetString(  $this->error_design , 'is_error' ); }
		$this->check = false;
		if( $this->_DEBUG ){ d('addError:'.$part);}
	}
	
	function addErrorString($str){
        $this->error_msg[ 'string' ] .= $str;
        if($this->check) { $this->error_msg[ 'is_error' ] .= $this->gm[ $_GET['type'] ]->partGetString(  $this->error_design , 'is_error' ); }
		$this->check = false;
		if( $this->_DEBUG ){ d('addError:'.$part);}
	}

	function checkMin( $iName_ , $iArgs_ )
	{
		$minValue = array_shift( $iArgs_ );

		if( $minValue > $_POST[ $iName_ ] )
		{
			$this->addError( $iName_ . '_min' );
			$this->addValidate( $iName_ , $iName_ . '_min' );
		}

		return $this->check;
	}

	function checkMinimum($name,$args){
		global $loginUserType;
		global $LOGIN_ID;

		$min = SystemUtil::getTableData( 'system' , 'ADMIN' , 'minimum_payment' );

		if( $_POST[ $name ] < $min )
		{
			$this->addError($name. '_Minimum');
			$this->addValidate( $name , $name . '_Minimum' );
		}

		return $this->check;
	}

	function checkMaxOver($name,$args){
		global $loginUserType;
		global $LOGIN_ID;

		$rate = SystemUtil::getTableData( 'system' , 'ADMIN' , 'point_to_yen_rate' );

		$gm = SystemUtil::getGMforType( $_GET[ 'type' ] );

		if( 'admin' == $loginUserType ){
			$point = SystemUtil::getTableData('nUser',$_POST[ $args[ 0 ] ],'point');
		}else{
			$point = SystemUtil::getTableData('nUser',$LOGIN_ID,'point');
		}

		$point *= $rate;

		if( $_POST[ $name ] > $point )
		{
			$this->addError($name. '_MaxOver');
			$this->addValidate( $name , $name . '_MaxOver' );
		}

		return $this->check;
	}
	//debugフラグ操作用
	function onDebug(){ $this->_DEBUG = true; }
	function offDebug(){ $this->_DEBUG = false; }

	/**
		@brief     カラムのエラーメッセージを追加する。
		@param[in] $iColumn_ エラー得メッセージを追加するカラム名。
		@param[in] $iPart_   追加するエラーメッセージのパーツ名。
	*/
	function addValidate( $iColumn_ , $iPart_ )
	{
		$gm           = SystemUtil::getGMforType( $_GET[ 'type' ] );
		$errorMessage = $gm->partGetString( $this->error_design , $iPart_ );

		$this->validate_msg[ $iColumn_ ][ $iPart_ ] = $errorMessage;

		if( $this->_DEBUG ) //デバッグ出力が有効な場合
			{ d( 'addValidate:' . $iColumn_ ); }
	}

	/**
		@brief     カラムのエラーメッセージを取得する。
		@param[in] $iColumn_ エラー得メッセージを追加するカラム名。
		@return    エラーメッセージ。
	*/
	function getValidate( $iColumn_ )
	{
		$result = '';

		if( !is_array( $this->validate_msg ) ) //エラーメッセージのストレージが存在しない場合
			{ return; }

		if( array_key_exists( $iColumn_ , $this->validate_msg ) ) //エラーメッセージが存在する場合
		{
			$messages = $this->validate_msg[ $iColumn_ ];

			if( count( $messages ) ) //エラーメッセージが1つ以上存在する場合
			{
				$gm = SystemUtil::getGMforType( $_GET[ 'type' ] );

				$result .= $gm->partGetString( $this->error_design , 'head' );
				$result .= implode( '' , $messages );
				$result .= $gm->partGetString( $this->error_design , 'foot' );
			}
		}

		return $result;
	}

	/**
		@brief     カラムにエラーメッセージが設定されているか確認する。
		@param[in] $iColumn_ エラーメッセージの有無を確認するカラム名。
		@retval    true  カラムにエラーメッセージが設定されている場合。
		@retval    false カラムにエラーメッセージが設定されていない場合。
	*/
	function isErrorEx( $iColumn_ )
		{ return ( $this->validate_msg[ $iColumn_ ] ? true : false ); }
}

?>