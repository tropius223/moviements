<?PHP

	try
	{
	//get or post �œn���ꂽ�f�[�^�Ƀ��[���𑗂邾����PHP�v���O����
	//Javascript����̌Ăяo���ŗ��p����

	//���M���s�������[������\������B

		include "custom/head_main.php";

		print ' { '; //json start
		if( count($_POST) > 0 ){

			ini_set("mbstring.internal_encoding","UTF-8"); // ���������G���R�[�f�B���O��UTF-8�ɐݒ肵�܂��B  

			//UTF-8�̏�Ԃ�stripslashes�����s����B
			$_POST['main'] = stripslashes($_POST['main']);
			$_POST['sub']  = stripslashes($_POST['sub']);

			ini_set("mbstring.internal_encoding","SJIS"); // ���������G���R�[�f�B���O��SJIS�ɐݒ肵�܂��B  

			//UTF-8�@���@SJIS
			$main = mb_convert_encoding( ($_POST['main']) ,'SJIS' , 'UTF-8');
			$sub =  mb_convert_encoding( ($_POST['sub']) ,'SJIS' , 'UTF-8');

			//SJIS�Ƃ���addslashes
			$main = addslashes($main);
			$sub  = addslashes($sub);

			$send	 = explode(  '/', $_POST['send_id'] );
			$db = $gm[ 'multimail' ]->getDB();
			$rec = $db->getNewRecord();
			$db->setData( $rec , 'sub' , $sub );
			$cnt=0;
			for( $i=0; $i<count($send); $i++ ){
				if( strlen( $send[$i] ) <= 0)
					break;
				// ���[�����M�ʒm�̑��M
				for($j=0; $j<count($TABLE_NAME); $j++){
					if(  $THIS_TABLE_IS_USERDATA[ $TABLE_NAME[$j] ]  ){
						$udb		 = $gm[ $TABLE_NAME[$j] ]->getDB();
						$utable		 = $udb->searchTable(  $udb->getTable(), 'id', '=', $send[$i] );
						if( $udb->getRow($utable) != 0 ){
							$urec	 = $udb->getRecord($utable, 0);
							Mail::sendString( $sub , $main , $MAILSEND_ADDRES, $db->getData($urec, 'mail'), $MAILSEND_NAMES );
							$cnt++;
							break;
						}
					}
				}
			}
			print ' "count" : '.$cnt." ,";
			print ' "success" : true ';
		}else{
			if($multimail_send_user[$loginUserType]){
				//���M����������܂���B
				print ' "error" : "001" ,';
			}else if(count($_POST) > 0){
				print ' "error" : "002" ,';
			}else{
				print ' "error" : "000" ,';
			}
			print ' "success" : false ';
		}

		print ' } '; //json end
	}
	catch( Exception $e_ )
	{
		//�G���[���b�Z�[�W�����O�ɏo��
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );
	}
?>
