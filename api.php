<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * api.php - メソッドアクセス用
	 * JavaScriptからデータを取得したり変更したりする際や
	 * 情報変更のフォームをinfoやindexに埋め込む場合等に使用。
	 *
	 * ※パスの関係上、アクセス用にファイルを置き、中身の記述は「custom/api_core.php」で行います。
	 *
	 * </PRE>
	 *******************************************************************************************************/

	try
	{
		// クラス名はc,メソッド名はmにセットして渡してください。

		include_once "custom/head_main.php";
		include_once "custom/extends/api.inc";

		header("Content-Type: text/html;charset=shift_jis");

		$param	 = $_POST;
		if( isset($_GET['get_p']) ) { $param = $_GET; } // get_pがセットされている場合GETパラメータを使用

		$class_name	 = $param['c'];
		if( strlen($class_name) && class_exists('mod_'.$class_name) )	 
		{ 
			$class_name = 'mod_'.$class_name;
			$api	 = new $class_name();
			$method	 = $param['m']; 
		}
		else
		{ 
			$api	 = new Api_core();
			$method	 = $param['post']; 
			$param['info_change_flg'] = false;
			if( strlen($param['js']) || strlen($param['jump']) ) { $param['info_change_flg'] = true; }
		}

		if( method_exists($api, $method) ) { $api->{$method}( $param ); }
	}
	catch( Exception $e_ )
	{
		//エラーメッセージをログに出力
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );
	}
?>