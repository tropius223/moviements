<?php
/*****************************************************************************
 *
 * 定数宣言
 *
 ****************************************************************************/

	define( "WS_PACKAGE_ID", "pointget" );				//セッションキーprefixなどに使う、パッケージを識別する為のID

	$NOT_LOGIN_USER_TYPE							 = 'nobody';							// ログインしていない状態のユーザ種別名
	$NOT_HEADER_FOOTER_USER_TYPE					 = 'nothf';								// ヘッダー・フッターを表示していない状態のユーザ種別名

	$LOGIN_KEY_FORM_NAME							 = 'mail';								// ログインフォームのキーを入力するフォーム名
	$LOGIN_PASSWD_FORM_NAME							 = 'passwd';							// ログインフォームのパスワードを入力するフォーム名

	$ADD_LOG										 = true;								// DBの新規追加情報を記録するか
	$UPDATE_LOG										 = true;								// DBの更新情報を記録するか
	$DELETE_LOG										 = true;								// DBの削除情報を記録するか

	$SESSION_NAME									 = WS_PACKAGE_ID.'loginid';							// ログイン情報を管理するSESSION の名前
	$COOKIE_NAME									 = WS_PACKAGE_ID.'loginid';							// ログイン情報を管理するCOOKIE の名前

	$ACTIVE_NONE									 = 1;									// アクティベートされていない状態を表す定数
	$ACTIVE_ACTIVATE	 							 = 2;									// アクティベートされている状態を表す定数
	$ACTIVE_ACCEPT		 							 = 4;									// 許可されている状態を表す定数
	$ACTIVE_DENY		 							 = 8;									// 拒否されている状態を表す定数
	$ACTIVE_ALL	 									 = 15;

	$IMAGE_NOT_FOUND								= '';

	$terminal_type = 0;
	$sid = "";

    $template_path                                   = "./template/pc/";
	$system_path                          	         = "./custom/system/";
	$page_path										 = "./file/page/";

	$FORM_TAG_DRAW_FLAG	 							 = 'variable';					//  buffer/variable

	$DB_LOG_FILE									 = "./logs/dbaccess.log";				// データベースアクセスログファイル
	$COOKIE_PATH 									 = '/';

	$MAX_FILE_SIZE = 512000;

	//kickbackの状態
	$KICKBACK_STATE_OFF	= 1;
	$KICKBACK_STATE_ON	= 2;
	$KICKBACK_STATE_DEF	= $KICKBACK_STATE_ON;

/***************************
 ** 設定ファイルの読み込み**
 ***************************/

	include_once "./custom/extends/sqlConf.php";
	include_once "./custom/extends/mobileConf.php";
	include_once "./custom/extends/tableConf.php";
	include_once "./custom/extends/exceptionConf.php";
	include_once "./custom/extends/modelConf.php";
	include_once "./custom/extends/logicConf.php";

/*************************
 *  拡張クラスの読み込み *
 *************************/

	//include_once "./include/extends/";
	//include_once "./include/extends/MobileUtil.php";


/***************************
 ** LINK&JS IMPORT関連 **
 ****************************/

    $css_file_paths['nobody']['import'] = './common/css/style.css';
    $css_file_paths['nUser']['import']  = './common/css/style.css';
    $css_file_paths['cUser']['import']  = './common/css/style.css';
    $css_file_paths['admin']['import']  = './common/css/system/admin.css';

    $js_file_paths['all']['jquery']        = './common/js/jquery-1.6.4.min.js';
    $js_file_paths['all']['wsAutoBind']    = './common/js/ws.autoBind.js';
    $js_file_paths['all']['wsBindMethods'] = './common/js/ws.bindMethods.js';
    $js_file_paths['nobody']['textbox']    = './common/js/textbox_focus.js';
    $js_file_paths['nobody']['biggerlink'] = './common/js/jquery.biggerlink.js';
    $js_file_paths['nUser']['textbox']     = './common/js/textbox_focus.js';
    $js_file_paths['cUser']['textbox']     = './common/js/textbox_focus.js';
    $js_file_paths['nUser']['biggerlink']  = './common/js/jquery.biggerlink.js';
    $js_file_paths['cUser']['biggerlink']  = './common/js/jquery.biggerlink.js';
    $js_file_paths['admin']['droppy']      = './common/js/jquery.droppy.js';
    $js_file_paths['admin']['odd']         = './common/js/odd.js';

?>