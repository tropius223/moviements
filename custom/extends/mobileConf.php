<?php

	include_once './include/extends/MobileUtil.php';

	//　携帯分岐
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

    if( $terminal_type ) //PC以外の端末の場合
	{
		switch( $terminal_type ) //端末の種類で分岐
		{
			case MobileUtil::$TYPE_NUM_IPHONE : //iphone
			{
				if( $mobile_flag ) //携帯切り替えが有効な場合
					{ $template_path = "./template/iphone/"; }

				break;
			}

			case MobileUtil::$TYPE_NUM_ANDROID : //android
			{
				if( $mobile_flag ) //携帯切り替えが有効な場合
					{ $template_path = "./template/iphone/"; }

				break;
			}

			default : //その他の端末
			{
				if( $mobile_flag ) //携帯切り替えが有効な場合
					{ $template_path = "./template/mobile/"; }

		        ini_set( 'session.use_trans_sid' , '1' );

				if( ini_get( 'session.use_trans_sid' ) != '1' ) //設定が変更できない場合
					{ output_add_rewrite_var( 'PHPSESSID' , htmlspecialchars( SID ) ); }

				break;
			}
		}
    }

?>