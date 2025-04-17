<?php

	include_once './include/extends/MobileUtil.php';

	//�@�g�ѕ���
	if( $mobile_flag )	
	{
		// include_once './custom/Mobile.php';
		$terminal_type = MobileUtil::getTerminal(); 
	}

	switch($terminal_type){
		case MobileUtil::$TYPE_NUM_DOCOMO:
		case MobileUtil::$TYPE_NUM_MOBILE_CRAELER:
			header("Content-type: application/xhtml+xml;charset=Shift_JIS");
			include_once "./include/extends/mobile/EmojiDocomo.php";
			break;
		case MobileUtil::$TYPE_NUM_AU:
			include_once "./include/extends/mobile/EmojiAU.php";
			break;
		case MobileUtil::$TYPE_NUM_SOFTBANK:
			include_once "./include/extends/mobile/EmojiSoftbank.php";
			break;
		case MobileUtil::$TYPE_NUM_IPHONE:
			include_once "./include/extends/mobile/EmojiIphone.php";
			break;
		case MobileUtil::$TYPE_NUM_ANDROID:
			include_once "./include/extends/mobile/EmojiAndroid.php";
			break;
		default:
			include_once "./include/extends/mobile/EmojiPc.php";
			break;
	}

    if( $terminal_type ) //PC�ȊO�̒[���̏ꍇ
	{
		switch( $terminal_type ) //�[���̎�ނŕ���
		{
			case MobileUtil::$TYPE_NUM_IPHONE : //iphone
			{
				if( $mobile_flag ) //�g�ѐ؂�ւ����L���ȏꍇ
					{ $template_path = "./template/iphone/"; }

				break;
			}

			case MobileUtil::$TYPE_NUM_ANDROID : //android
			{
				if( $mobile_flag ) //�g�ѐ؂�ւ����L���ȏꍇ
					{ $template_path = "./template/iphone/"; }

				break;
			}

			default : //���̑��̒[��
			{
				if( $mobile_flag ) //�g�ѐ؂�ւ����L���ȏꍇ
					{ $template_path = "./template/mobile/"; }

		        ini_set( 'session.use_trans_sid' , '1' );

				if( ini_get( 'session.use_trans_sid' ) != '1' ) //�ݒ肪�ύX�ł��Ȃ��ꍇ
					{ output_add_rewrite_var( 'PHPSESSID' , htmlspecialchars( SID ) ); }

				break;
			}
		}
    }

?>