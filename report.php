<?php

/*
・report作成用モジュール
 $_GET['type']が存在する場合は、そのDBのtdb内容をそのまま取得して返す。
 $_GET['case']が存在する場合は、/module/Report.incで定義された形式でreportを出力する。

*/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		if( !function_exists( 'fputcsv' ) )
		{
			function fputcsv( $iHandle_ , $iArray_ )
			{
				$results = Array();

				foreach( $iArray_ as $value )
				{
					if( FALSE !== strpos( $value , '"' ) || FALSE !== strpos( $value , "\n" ) )
					{
						$value = str_replace( '"' , '""' , $value );
						$value = '"' . $value . '"';
					}

					$results[] = $value;
				}

				fputs( $iHandle_ , implode( ',' , $results ) . "\n" );
			}
		}

		$rp = new mod_report();
		$cnf = report_conf::getInstance();
		if( ! $rp->user_type_check($loginUserType) ){
			include_once 'head.php';
			Template::drawTemplate( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'TOP_PAGE_DESIGN' );
			include_once 'foot.php';
		}else if( (isset($_POST["run"]) && isset($_POST['case']) ) || (isset($_GET["run"]) && isset($_GET['case']) ) ){
			if(isset($_POST["run"]))
				$name = $_POST['case'];
			else
				$name = $_GET['case'];

			if( !isset( $cnf->report_list[$loginUserType]['report'][$name] ) ){
				include_once 'head.php';
				Template::drawTemplate( $gm[ $loginUserType ], null, 'report', $loginUserRank, '', 'REPORT_CASE_NOT_FOUND' );
				include_once 'foot.php';
				exit();
			}

			$array  = $cnf->report_list[$loginUserType]['report'][$name];
			$db = $gm[ $array['table_name'] ]->getDB();
			$table = $db->getTable();

			if( isset($_POST['m']) && isset($_POST['y']) && strlen($_POST['m']) && strlen($_POST['y']) )//期間データ使って、searchした結果をCVSに出力
				$table	 = $db->searchTable( $table , 'regist', 'b', mktime( 0, 0, 0, $_POST['m'], 1, $_POST['y'] ), mktime( 0, 0, -1, $_POST['m'] + 1, 1, $_POST['y'] )  );

			//        $table	 = $db->searchTable( $table , "state" , "=" , $ACTIVE_ACTIVATE );

			$haed = "";
			$colum_functions = Array();
			$colum_variable = Array();
			foreach( $array['head_name'] as $key => $head_name ){
				$head .= $head_name.",";

				$discrimination = explode( ':' , $array['colum_name'][$key] );

				switch($discrimination[0]){
					case 'f'://function
						switch($discrimination[1]){
							case 'date':
//								rp_date_init();
								$colum_variable[] = $discrimination[2];
								$colum_functions[] = 'date';
								break;
							case 'no':
								$rp->no_init($colum_variable[]);
								$colum_functions[] = 'no';
								break;
						}
						break;
					case 'r'://relational
						$colum_variable[] = $rp->relational_init(array_slice($discrimination,1));
						$colum_functions[] = 'relational';
						break;
					case 'rm'://multiple relational
						$colum_variable[] = $rp->relational_init(array_slice($discrimination,1));
						$colum_functions[] = 'mrelational';
						break;
					default:
						$colum_functions[] = 'nomal';
						$colum_variable[] = $discrimination[0];
						break;
				}
			}

			////////////////////

			$contents = $head."\n";

			$row	 = $db->getRow( $table );
			$sum	 = 0;

			for($i=0; $i<$row; $i++){
				$rec	 = $db->getRecord( $table, $i );

				foreach( $colum_functions as $key => $function ){
					$contents .= $rp->{$function}( $db , $rec , $colum_variable[$key] ).",";
				}
				$contents .="\n";
			}

			//ファイル出力
			$filename = "./report/".$name.".csv";
			$handle = fopen($filename, 'w');
			if(fwrite($handle, $contents) === FALSE){
			   echo "Cannot write to file ($filename)";
			   exit;
			}
			fclose($handle);

			//ロケーション。
			header( "Content-Type: application/octet-stream" );
			header("Location: " .$HOME. "$filename");

		}else if(isset($_GET["type"]) && isset($_GET["run"])){


			if( isset($cnf->report_list[$loginUserType]['table'][$_GET['type']]) ){

				$db = $gm[ $_GET["type"] ]->getDB();
				$clmList = $db->getClumnNameList();
				$table = $db->getTable();

				if( isset($_POST['m']) && isset($_POST['y']) )//期間データ使って、searchした結果をCVSに出力
					$table	 = $db->searchTable( $table , 'regist', 'b', mktime( 0, 0, 0, $_POST['m'], 1, $_POST['y'] ), mktime( 0, 0, -1, $_POST['m'] + 1, 1, $_POST['y'] )  );

				$contents = Array();

				$row = $db->getRow( $table );
				for( $i = 0 ; $i < $row ; $i++ ){
					$rec = $db->getRecord( $table , $i );
					$line = Array();
					foreach( $clmList as $clm ){
						$line[] = $db->getData( $rec , $clm );
					}
					$contents[] = $line;
				}

				//ファイル出力
				header('Content-Disposition: attachment; filename="'.$_GET["type"].'.csv"');
				header('Content-type: application/x-octet-stream; name="'.$_GET["type"].'.csv"; charset=Shift_JIS');

				//ファイル出力
				$handle = fopen('php://output', 'w');

				foreach( $contents as $line ){
					if( fputcsv($handle, $line) === FALSE){
					   echo "Cannot write to file (".$_GET["type"].")";
					   exit;
					}
				}
				fclose($handle);

			}else{
				//error
				header("Location: ".$HOME."index.php");
//				d("error");
			}
		}else{
			include_once 'head.php';
			Template::drawTemplate( $gm[ $loginUserType ], null, 'report', $loginUserRank, '', 'REPORT_DESIGN' );
			include_once 'foot.php';
		}
	}
	catch( Exception $e_ )
	{
		ob_end_clean();

		//エラーメッセージをログに出力
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );

		//例外に応じてエラーページを出力
		$className = get_class( $e_ );
		ExceptionUtil::DrawErrorPage( $className );
	}

	ob_end_flush();
?>