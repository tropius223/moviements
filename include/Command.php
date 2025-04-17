<?php

	/**
	 * 基本命令クラス
	 * 
	 * @author 丹羽一智
	 * @version 1.0.0
	 * 
	 */
	class Command extends command_base
	{

		/**********************************************************************************************************
		 * システム用メソッド
		 **********************************************************************************************************/

		/**
		 * ログインIDを描画します。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 */
		function loginid( &$gm, $rec, $args ){
			global $LOGIN_ID;
			$this->addBuffer( $LOGIN_ID );
		}

		/**
		 * タイムスタンプを変換します。
		 * 指定が無い場合はシステムデフォルトの物が使用されます。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 * 		第一引数にUNIXタイムを渡します。
		 * 		第二引数にdateに渡すtimeformatを指定します(任意)
		 */
		function timestamp( &$gm, $rec, $args ){
			if(isset($args[1])){ $this->addBuffer(date( $args[1], $args[0] )); }
			else{ $this->addBuffer(date( $gm->timeFormat, $args[0] )); }
		}
		
		/**
		 * 現在の時間を取得します。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 */
		function now( &$gm, $rec, $args ){
			$kind	 = $args[0];
			$add	 = $args[1];
		
			switch( $kind ){
				case 'y':
				case 'year':
					$this->addBuffer( date('Y') + $add );
					break;
				case 'm':
				case 'month':
					$this->addBuffer( date('m') + $add );
					break;
				case 'd':
				case 'day':
					$this->addBuffer( date('d') + $add );
					break;
				case 'u':
				case 'unix':
					$this->addBuffer(time()+$add);
					break;
				default:
					$this->addBuffer( $this->addBuffer(date( $gm->timeFormat ) ) );
			}
		}
	
	
        //タイムスタンプカラム値の名前を受けて、そのタイムスタンプ値の経過年数を返す
        function getPassage( &$gm, $rec, $args ){
            			
			$db		 = $gm->getDB();
            $passage = localtime( $db->getData( $rec, $args[0] ) );
            $now = localtime( );
            
            $y = $now[5] - $passage[5];
            $m = $now[4] - $passage[4];
            
            if($m < 0 ){$y--;}
            
			$this->addBuffer( $y );
        }
        
        // 年　月　日を受け取って、年齢を描画
        function drawAgeByBirth( &$gm, $rec , $args ){
            if(!isset($args[1])){$args[1]=1;}
            if(!isset($args[2])){$args[2]=1;}
            $birth = sprintf("%4d%02d%02d",$args[0],$args[1],$args[2]);
            $now = date('Ymd');
            $this->addBuffer( (int)(($now - $birth)/10000) );
        }
		
		/**
		 * アクティベートコードを発行します。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。このメソッドでは利用しません。
		 */
		function activate( &$gm, $rec, $args )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $HOME;
			// **************************************************************************************
			
			$db		 = $gm->getDB();
			$this->addBuffer(   $HOME. 'activate.php?type='. $_GET['type'] .'&id='. $db->getData( $rec, 'id' ) .'&md5='. md5(  $db->getData( $rec, 'id' ). $db->getData( $rec, 'mail' )  )   );
		}
		
		function drawImage( &$gm, $rec, $args ){
		 	if(  file_exists( $args[0] )  ){
				// ファイルが存在する場合
				if(  isset( $args[1] ) && isset( $args[2] )  ){
					$this->addBuffer( '<img src="'. $args[0] .'" width="'. $args[1] .'" height="'. $args[2] .'" border="0"/>' );
				}else{
					$this->addBuffer( '<img src="'. $args[0] .'" border="0"/>' );
				}
			
			}else{
				// ファイルが存在しない場合
				$this->addBuffer( '<span>イメージは登録されていません</span>' );
			}
		 }

		/**
		 * データの件数を取得。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にテーブル名　第二引数にカラム名　第三引数に演算子　第四引数に値　をしています。
		 */
		function getRow( &$gm, $rec, $args ){
			$tgm	 = SystemUtil::getGMforType($args[0]);
			$db		 = $tgm->getDB();
            $table = $db->getTable();
            for($i=0;isset($args[1+$i]);$i+=3){
            	if($args[2+$i] == 'b'){
	    			$table = $db->searchTable( $table, $args[1+$i], $args[2+$i], $args[3+$i], $args[4+$i] );
	    			$i++;
            	}else{
	    			$table = $db->searchTable( $table, $args[1+$i], $args[2+$i], $args[3+$i] );
            	}
            }
            $this->addBuffer( $db->getRow( $table ) );
		}
		
		/**
		 * データの合計を取得。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にテーブル名　第二引数に集計カラム名　第三～五引数に検索カラム名、演算子、値　をしています。
		 */
		function getSum( &$gm, $rec, $args ){
			$tgm	 = SystemUtil::getGMforType($args[0]);
			$db		 = $tgm->getDB();
            $table = $db->getTable();
            for($i=0;isset($args[2+$i]);$i+=3){
            	if($args[3+$i] == 'b'){
	    			$table = $db->searchTable( $table, $args[2+$i], $args[3+$i], $args[4+$i], $args[5+$i] );
	    			$i++;
            	}else{
	    			$table = $db->searchTable( $table, $args[2+$i], $args[3+$i], $args[4+$i] );
            	}
            }
            
            $this->addBuffer( $db->getSum( $args[1], $table ) );
		}		

		/**********************************************************************************************************
		 * 拡張システム用メソッド
		 **********************************************************************************************************/

		/**
		 * ユーザ名取得。
		 * IDからユーザ名を検索し、該当する ユーザ名( ユーザID ) の形式で出力します。
		 * どのユーザ情報テーブルにユーザデータがあるのかわからないときなどに有効です。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。 第二引数にリンクするかを真偽値で渡します。
		 */
		function getName( &$gm, $rec, $args )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			// **************************************************************************************
			
			for( $i=0; $i<count($TABLE_NAME); $i++ )
			{
				if(  $THIS_TABLE_IS_USERDATA[ $TABLE_NAME[$i] ]  )
				{
					$tgm	 = SystemUtil::getGMforType( $TABLE_NAME[$i] );
					$db		 = $tgm->getDB();
					$table	 = $db->searchTable( $db->getTable(), 'id', '=', $args[0] );
					if(  $db->getRow( $table ) != 0  )
					{
						$rec	 = $db->getRecord( $table, 0 );
						if( $args[1] == 'true' || $args[1] == 'TRUE' )
						{
							$this->addBuffer(  
								'<a href="info.php?type='. $TABLE_NAME[$i] .'&id='. $db->getData( $rec, 'id' ) .'">'. 
								$db->getData( $rec, 'name' ). '( '. $db->getData( $rec, 'id' ). ' )'.
								'</a>'  );
						}else
						{
							$this->addBuffer(  $db->getData( $rec, 'name' ). '( '. $db->getData( $rec, 'id' ). ' )'  );
						}
					}
				}
			}
		}



		/**
		 * データ名を取得。
		 * IDからデータを検索し、該当する データ名( データID ) の形式で出力します。
		 * どのテーブルにデータがあるのかわからないときなどに有効です。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。　第一引数にIDを渡します。　第二引数に名前の格納されているカラム名を渡します。 第三引数にリンクするかを真偽値で渡します。
		 */
		function getDataName( &$gm, $rec, $args )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $TABLE_NAME;
            global $ID_LENGTH;
			// **************************************************************************************
			
			// 全てのテーブルのGUIManagerインスタンスを取得します。
			$tgm	 = SystemUtil::getGM();
			$flg	 = false;
			for( $i=0; $i<count($tgm); $i++ ){
                
                if( $ID_LENGTH[ $TABLE_NAME[$i] ] == 0)
                    continue;
            
				$db		 = $tgm[ $TABLE_NAME[$i] ]->getDB();
				$table	 = $db->searchTable( $db->getTable(), 'id', '=', $args[0] );
				if(  $db->getRow( $table ) != 0  )
				{
					$rec	 = $db->getRecord( $table, 0 );
					if( $args[2] == 'true' || $args[2] == 'TRUE' )
					{
						$this->addBuffer(  
							'<a href="info.php?type='. $TABLE_NAME[$i] .'&id='. $db->getData( $rec, 'id' ) .'">'. 
							$db->getData( $rec, $args[1] ). '( '. $db->getData( $rec, 'id' ). ' )'.
							'</a>'  );
					}
					else
					{
						$this->addBuffer(  $db->getData( $rec, $args[1] ). '( '. $db->getData( $rec, 'id' ). ' )'  );
					}
					$flg	 = true;
					break;
				}
			}
			
			if( !$flg )	{ $this->addBuffer( '該当データ無し' ); }
		}




		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 * サイトシステム用メソッド
		 **********************************************************************************************************/



         
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 *　拡張汎用メソッド
		 **********************************************************************************************************/

         
		/**
		 * 引数で渡した数字までを選択できるselectコントロールを表示。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
		 * 第一引数でnameを指定
		 * 第二引数で最後の数字を指定値(省略可)
		 * 第三引数で初期値(選択中の項目の数字を指定値)(省略可)
		 * 第四引数で開始値(省略可)
         * 第五引数で接頭項目の追加値(例：未選択) (省略可)
         * 第六引数でタグオプションを設定（省略可能）
		 */
        function num_option( &$gm , $rec , $args ){
        	
            $name = $args[0];
            
            $max = 1;
            if(strlen($args[1])){ $max = $args[1]; }
            
            $check = 0;
            if( strlen( $_POST[$args[0]] ) ){ $check = $_POST[$args[0]]; }
            else if(strlen($args[2])){ $check = $args[2]; }
			
            $start = 1;
            if(strlen($args[3])){ $start = $args[3]; }
            
            $option = "";
            if( strlen($args[5]) ){ $option = $args[5]; }


            if( strlen($name) ){
                $index = "";
                $value  = "";
                if( strlen($args[4]) ){
                    $index .= $args[4].'/';
                    $value  .= '/';
                }
                for($i=$start;$i<$max;$i++){
                    $index .= $i.'/';
                    $value  .= $i.'/';
                }
                $index .= $i;
                $value  .= $i;
                
                $this->addBuffer( $gm->getCCResult( $rec, '<!--# form option '.$name.' '.$check.' '.$value.' '.$index.' '.$option.' #-->' ) );
            }
            
        }
        
        /**
         * 引数で指定した文字と同数の*を出力する。
         *
         */
        function drawPassChar( &$gm , $rec , $args ){
            $PASS_CHAR = '*';
            $str = "";
            for($i=0;strlen($args[0]) > $i ;$i++){
                $str .= $PASS_CHAR;
            }
            $this->addBuffer( $str );
        }
        
		/**
		 * テーブルの全行から選択するselectBoxの表示
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         * 第一引数：name
         * 第二引数：table名
         * 第三引数：option名となるカラム名
         * 第四引数：valueとなるカラム名
         * 第五引数：初期値(省略可)
         * 第六引数：未選択項目値(省略可)
         * 第七引数：タグオプション要素(省略可)
         * 第八～引数：カラム名、演算子、値の3セットのループ。
		 */
        function tableSelectForm( &$gm , $rec , $args ){
            if(isset($args[4]) && strlen($args[4]))
                $check = $args[4];
            else
                $check = "";
                
            if(isset($args[6]) && strlen($args[6]))
                $option = ' '.$args[6];
            else
                $option = "";
            
            $tgm = SystemUtil::getGMforType( $args[1] );
            $db = $tgm->getDB();
            
            $table = $db->getTable();
            
            if(isset($args[7])){
            	for($i=0;isset($args[$i+7]);$i+=3){
            		$table = $db->searchTable( $table, $args[7+$i], $args[8+$i], $args[9+$i] );
            	}
            }
            
            $row = $db->getRow( $table );
            
            $index = "";
            $value  = "";
            
            if( isset($args[5]) && strlen($args[5]) ){
                $index .= $args[5];
                
                if($row){
                    $index  .= '/';
                    $value  .= '/';
                }
            }
            
            for($i=0;$i<$row-1;$i++){
                $rec = $db->getRecord( $table , $i );
                $index .= $db->getData( $rec , $args[2] )."/";
                $value .= $db->getData( $rec , $args[3] )."/";
            }
            $rec = $db->getRecord( $table , $i );
            $index .= $db->getData( $rec , $args[2] );
            $value .= $db->getData( $rec , $args[3] );
            
            $this->addBuffer( $gm->getCCResult( $rec, '<!--# form option '.$args[0].' '.$check.' '.$value.' '.$index.$option.' #-->' ) );
        }
         
		/**
		 * 親子関係のテーブルの全行から、親テーブルでグループ化した子テーブル選択のためのselectBoxの表示
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         * 第一引数：name
         * 第二引数：親table名
         * 第三引数：グループ名
         * 第四引数：子table名
         * 第五引数：option名となるカラム名
         * 第六引数：valueとなるカラム名
         * 第七引数：親のIDを示すカラム名
         * 第八引数：初期値(省略可)
         * 第九引数：未選択項目値(省略可)
		 */
        function groupTableSelectForm( &$gm , $rec , $args ){
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[7]))
                $check = $args[7];
            else
                $check = "";
        
            $pgm = SystemUtil::getGMforType( $args[1] );
            $cgm = SystemUtil::getGMforType( $args[3] );
            
            $pdb = $pgm->getDB();
            $cdb = $cgm->getDB();
            
            $ptable = $pdb->getTable();
            $prow = $pdb->getRow( $ptable );
            
            $str = '<select name="'.$args[0].'" >'."\n";
            
            if( isset($args[8]) ){
                $str .= '  <optgroup label="'.$args[8].'" >'."\n";
            
                $str .= '    <option value="" >'.$args[8]."\n";
                $str .= '  </optgroup>'."\n";
            }
            
            for($i=0;$i<$prow;$i++){
                $prec = $pdb->getRecord( $ptable , $i );
                
                $str .= '  <optgroup label="'.$pdb->getData( $prec , $args[2] ).'" >'."\n";
                
                $ctable = $cdb->searchTable( $cdb->getTable() , $args[6] , '=' , $pdb->getData( $prec , 'id' ) );
                $crow = $cdb->getRow( $ctable );
                
                for($j=0;$j<$crow;$j++){
                    $crec = $cdb->getRecord( $ctable , $j );
                    $option = $cdb->getData( $crec , $args[4] );
                    $value = $cdb->getData( $crec , $args[5] );
                    if( $check == $value )
                        $str .= '    <option value="'.$value.'" selected="selected">'.$option."\n";
                    else
                        $str .= '    <option value="'.$value.'" >'.$option."\n";
                }
                $str .= '  </optgroup>'."\n";
            }
            
            $str .= '</select>'."\n";
            
            $this->addBuffer( $str );
        }
        
		/**
		 * 多段階の親子関係のテーブルの全行を使ったGroupサーチ用のフォームを出力
         * valueは全てIDとします。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         *
         * 第一引数：name
         * 第二引数：初期値
         * 第三引数：未選択項目値
         * 第四引数：親table
         * 第五引数：親option
         * 第六引数：子table
         * 第七引数：子option
         * 第八引数：親のIDを示す子のカラム名
         *
         * 以下、六～八がループ
		 */
        function groupTableSelectFormMulti( &$gm , $rec , $args ){
        
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[1]))
                $check = $args[1];
            else
                $check = "";
        
            $tcount = ( count($args) - 5 ) / 3;
        
            $_gm = SystemUtil::getGM();
        
            $param = Array();
        
            $param[0]['db'] = $_gm[ $args[3] ]->getDB();    //最上位テーブルを取得
            $param[0]['name'] = $args[4];
            for($i=0;$i<$tcount;$i++){
                $param[$i+1]['db'] = $_gm[ $args[ 5 + $i*3 ] ]->getDB();
                $param[$i+1]['name'] = $args[ 6 + $i*3 ];
                $param[$i+1]['parent'] = $args[ 7 + $i*3 ];
            }
            
            $str = '<select name="'.$args[0].'" >'."\n";
            
            
            if( isset($args[2]) ){
                $str .= '    <option value="" >'.$args[2]."\n";
            }
            
            groupTableSelectFormMultiReflexive( $str, $param , $check );
            
            $str .= '</select>'."\n";
            
            $this->addBuffer( $str );
        }
        
		/**
		 * 親子関係のテーブルの全行を使ったGroupサーチ用のフォームを出力
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         * 第一引数：name
         * 第二引数：親table名
         * 第三引数：グループ名
         * 第四引数：子table名
         * 第五引数：option名となるカラム名
         * 第六引数：valueとなるカラム名
         * 第七引数：親のIDを示すカラム名
         * 第八引数：初期値(省略可)
         * 第九引数：未選択項目値(省略可)
		 */
        function searchGroupTableForm( &$gm , $rec , $args ){
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[7]))
                $check = $args[7];
            else
                $check = "";
        
            $pgm = SystemUtil::getGMforType( $args[1] );
            $cgm = SystemUtil::getGMforType( $args[3] );
            
            $pdb = $pgm->getDB();
            $cdb = $cgm->getDB();
            
            $ptable = $pdb->getTable();
            $prow = $pdb->getRow( $ptable );
            
            $str = '<select name="'.$args[0].'" >'."\n";
            
            if( isset($args[8]) ){
                $str .= '    <option value="" >'.$args[8]."\n";
            }
            
            for($i=0;$i<$prow;$i++){
                $prec = $pdb->getRecord( $ptable , $i );
                
                $pid = $pdb->getData( $prec , 'id' );
                $str .= '  <option value="'.$pid.'" >'.$pdb->getData( $prec , $args[2] )."\n";
                
                $ctable = $cdb->searchTable( $cdb->getTable() , $args[6] , '=' , $pid );
                $crow = $cdb->getRow( $ctable );
                
                for($j=0;$j<$crow;$j++){
                    $crec = $cdb->getRecord( $ctable , $j );
                    $option = "　".$cdb->getData( $crec , $args[4] );
                    $value = $cdb->getData( $crec , $args[5] );
                    if( $check == $value )
                        $str .= '    <option value="'.$value.'" selected="selected">'.$option."\n";
                    else
                        $str .= '    <option value="'.$value.'" >'.$option."\n";
                }
            }
            
            $str .= '</select>'."\n";
            
            $this->addBuffer( $str );
        }
         
		/**
		 * 多段階の親子関係のテーブルの全行を使ったGroupサーチ用のフォームを出力
         * valueは全てIDとします。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         *
         * 第一引数：name
         * 第二引数：初期値
         * 第三引数：未選択項目値
         * 第四引数：親table
         * 第五引数：親option
         * 第六引数：子table
         * 第七引数：子option
         * 第八引数：親のIDを示す子のカラム名
         *
         * 以下、六～八がループ
		 */
        function searchGroupTableFormMulti( &$gm , $rec , $args ){
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[1]))
                $check = $args[1];
            else
                $check = "";
        
            $tcount = ( count($args) - 5 ) / 3;
        
            $_gm = SystemUtil::getGM();
        
            $param = Array();
        
            $param[0]['db'] = $_gm[ $args[3] ]->getDB();    //最上位テーブルを取得
            $param[0]['name'] = $args[4];
            for($i=0;$i<$tcount;$i++){
                $param[$i+1]['db'] = $_gm[ $args[ 5 + $i*3 ] ]->getDB();
                $param[$i+1]['name'] = $args[ 6 + $i*3 ];
                $param[$i+1]['parent'] = $args[ 7 + $i*3 ];
            }
            
            $str = '<select name="'.$args[0].'" >'."\n";
            
            
            if( isset($args[2]) ){
                $str .= '    <option value="" >'.$args[2]."\n";
            }
            
            searchGroupTableFormMultiReflexive( $str, $param , $check );
            
            $str .= '</select>'."\n";
            
            $this->addBuffer( $str );
        }
        
        
		/**
		 * テーブルの全行から選択するcheckBoxの表示
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         * 第一引数：name
         * 第二引数：table名
         * 第三引数：表示名となるカラム名
         * 第四引数：valueとなるカラム名
         * 第五引数：区切り文字
         * 第六引数：初期値(省略可)
         * 第七引数：未選択項目値(省略可)
         * 第八引数：一列に表示する数(省略可)
		 */
        function tableCheckForm( &$gm , $rec , $args ){
            if(isset($args[5]) && strlen($args[5]))
                $check = $args[5];
            else
                $check = "";
            if(isset($args[7]) && strlen($args[7]))
                $option = ' '.$args[7];
            else
                $option = "";
            
            $tgm = SystemUtil::getGMforType( $args[1]);
            $db = $tgm->getDB();
            $table = $db->getTable();
            $row = $db->getRow( $table );
            
            $index = "";
            $value  = "";
            
            if( isset($args[6]) && strlen($args[6]) ){
                $index .= $args[6].'/';
                $value  .= '/';
            }
            
            for($i=0;$i<$row-1;$i++){
                $rec = $db->getRecord( $table , $i );
                $index .= $db->getData( $rec , $args[2] )."/";
                $value .= $db->getData( $rec , $args[3] )."/";
            }
            $rec = $db->getRecord( $table , $i );
            $index .= $db->getData( $rec , $args[2] );
            $value .= $db->getData( $rec , $args[3] );
            
            $this->addBuffer( $gm->getCCResult( $rec, '<!--# form checkbox '.$args[0].' '.$check.' '.$args[4].' '.$value.' '.$index.$option.'  '.$args[9].' #-->' ) );
        }
        
        
		/**
		 * テーブルの全行から選択するradioButtonの表示
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         * 第一引数：name
         * 第二引数：table名
         * 第三引数：表示名となるカラム名
         * 第四引数：valueとなるカラム名
         * 第五引数：区切り文字
         * 第六引数：初期値(省略可)
         * 第七引数：未選択項目値(省略可)
         * 第八引数：一列に表示する数(省略可)
		 */
        function tableRadioForm( &$gm , $rec , $args ){
            if(isset($args[5]) && strlen($args[5]))
                $check = $args[5];
            else
                $check = "";
            if(isset($args[7]) && strlen($args[7]))
                $option = ' '.$args[7];
            else
                $option = "";
            
            $tgm = SystemUtil::getGMforType( $args[1]);
            $db = $tgm->getDB();
            $table = $db->getTable();
            $row = $db->getRow( $table );
            
            $index = "";
            $value  = "";
            
            if( isset($args[6]) && strlen($args[6]) ){
                $index .= $args[6].'/';
                $value  .= '/';
            }
            
            for($i=0;$i<$row-1;$i++){
                $rec = $db->getRecord( $table , $i );
                $index .= $db->getData( $rec , $args[2] )."/";
                $value .= $db->getData( $rec , $args[3] )."/";
            }
            $rec = $db->getRecord( $table , $i );
            $index .= $db->getData( $rec , $args[2] );
            $value .= $db->getData( $rec , $args[3] );
            
            $this->addBuffer( $gm->getCCResult( $rec, '<!--# form radio '.$args[0].' '.$check.' '.$args[4].' '.$value.' '.$index.$option.'  '.$args[9].' #-->' ) );
        }
         
        /*
          注目の○○リストを表示
          つまりは、任意のテーブルの任意のフラグがtrueの項目を一覧として表示する。
        
        args
         0:テーブル名
         1:フラグカラム名
         2:表示数
        */
        function attentionListDraw( &$gm, $rec , $args ){
        global $loginUserType;
        global $loginUserRank;
            $HTML = Template::getTemplate( $loginUserType , $loginUserRank , $args[0] , 'ATTENTION_TEMPLATE' );
            
            if( !strlen( $HTML ) ){
                throw new Exception('dos not template');
            }
        
            $tgm = SystemUtil::getGMforType( $args[0] );
            $db = $tgm->getDB();
            $list = $db->searchTable( $db->getTable() , $args[1] , '=' , true );
            
            $row = $db->getRow( $list );
            
            $this->addBuffer( $tgm->getString( $HTML , null , 'head' ) );
            for( $i = 0 ; $i < $args[2] ; $i++ ){
                if( $i < $row ){
                    $lrec = $db->getRecord( $list , $i );
                    $this->addBuffer( $tgm->getString( $HTML , $lrec , 'element' ) );
                }else{
                    $this->addBuffer( $tgm->getString( $HTML , null , 'dummy' ) );
                }
            }
            $this->addBuffer( $tgm->getString( $HTML , null , 'foot' ) );
        }
        
        /*
          新着の○○リストを表示
          つまりは、任意のテーブルのregistが指定した期間以内の項目を一覧表示。
        
        args
         0:テーブル名
         1:新着とする期間(時間で)
         2:表示数
        */
        function newListDraw( &$gm, $rec , $args ){
        global $loginUserType;
        global $loginUserRank;
            $HTML = Template::getTemplate( $loginUserType , $loginUserRank , $args[0] , 'NEW_TEMPLATE' );
            
            if( !strlen( $HTML ) ){
                throw new Exception('dos not template');
            }
        
            $tgm = SystemUtil::getGMforType( $args[0] );
            $db = $tgm->getDB();
            $list = $db->searchTable( $db->getTable() , 'regist' , '>' , time() - ($args[1]*60*60) );
            $row = $db->getRow( $list );
            
            $this->addBuffer( $tgm->getString( $HTML , null , 'head' ) );
            for( $i = 0 ; $i < $args[2] ; $i++ ){
                if( $i < $row ){
                    $lrec = $db->getRecord( $list , $i );
                    $this->addBuffer( $tgm->getString( $HTML , $lrec , 'element' ) );
                }else{
                    $this->addBuffer( $tgm->getString( $HTML , null , 'dummy' ) );
                }
            }
            $this->addBuffer( $tgm->getString( $HTML , null , 'foot' ) );
        }
        
        /*
         * レコードに値が存在する場合リンクを表示する
         *
         * 0:レコード名
         * 1:URL
         * 2:リンクの表示文言
         * 3:リンクが無い場合の表示文言
         */
         function drawLinkByRec( &$gm, $rec, $args ){
             $db = $gm->getDB();
             $data = $db->getData( $rec , $args[0]);
             if( ! strlen($data) ){
                 $this->addBuffer( $args[3] );
             }else{
                 //Linkが空の時はrecのデータ
                 if( !strlen($args[1]) )
                     $url = $data;
                 else
                     $url = $args[1];
                 
                 $this->addBuffer( '<a href="'.$url.'">'.$args[2].'</a>' );
             }
         }
         
        /*
         * 引数が存在する場合リンクを表示する
         *
         * 0:URL
         * 1:リンクの頭に付ける文字（mailto:とか
         */
         function drawLink( &$gm, $rec, $args ){
             if( strlen($args[0]) )
                 $this->addBuffer( '<a href="'.$args[1].$args[0].'" target="_blank">'.$args[0].'</a>' );
         }

        
        function getReferer(&$gm , $rec , $args ){
            $this->addBuffer( $_SERVER['HTTP_REFERER'] );
        }
        
        /*
         * 複数ID指定に対応したリンク出力
         * レコードに値が存在する場合リンクを表示する
         *
         * 0:レコード名
         * 1:URL(末尾にIDを付与する形)
         * 2:リンクの表示文言
         * 3:リンクが無い場合の表示文言
         */
         function drawLinkMultiID( &$gm, $rec, $args ){
             $sep = '/';
         
             $db = $gm->getDB();
             $data = $db->getData( $rec , $args[0]);
             if( ! strlen($data) ){
                 $this->addBuffer( $args[3] );
             }else{
                 $array = explode( $sep , $data );
                 
                 $row = count( $array );
                 for($i=0; $i < $row-1 ; $i++){
                     $this->addBuffer( '<a href="'.$args[1].$array[$i].'">'.$args[2].'</a><br/>' );
                 }
                 
                 $this->addBuffer( '<a href="'.$args[1].$array[$i].'">'.$args[2].'</a>' );
             }
         }
         
         
         //1:全角かな 2:半角カナ 3:英字 4:数字。 
         function getInputMode( &$gm , $rec , $args ){
         global $terminal_type; // 1:docomo 2:au 3:softbank
             $e = Array( 
                     1 => Array( '1' => 'istyle="1" style="-wap-input-format:&quot;*&lt;ja:h&gt;&quot;"' ,
                                  '2' => 'istyle="2" style="-wap-input-format:&quot;*&lt;ja:hk&gt;&quot;"' ,
                                  '3' => 'istyle="3" style="-wap-input-format:&quot;*&lt;ja:en&gt;&quot;"' ,
                                  '4' => 'istyle="4" mode="numeric" style="-wap-input-format:&quot;*&lt;ja:n&gt;&quot;"' ) ,
                     2 => Array( '1' => 'format="*M"' , '2' => 'istyle="2"' , '3' => 'format="*x"' , '4' => 'format="*N"' ) ,
                     3 => Array( '1' => 'MODE="hiragana"' , '2' => 'MODE="hankakukana"' , '3' => 'MODE="alphabet"' , '4' => 'MODE="numeric"' ) );
             $this->addBuffer( $e[$terminal_type][$args[0]] );
         }
         
         //args[0]:「0」～「9」、「*」、「#」
         //args[1]: true 'NONUMBER' ,false ''
         function getAccesskey( &$gm , $rec , $args ){
         global $terminal_type;
//             $nonumber = '';
             // 1:docomo 2:au 3:softbank
             $elements = Array( 0 => 'accesskey' , 1 => 'accesskey', 2 => 'accesskey', 3 => 'DIRECTKEY' );
             
             $element = $elements[$terminal_type];
             
/*             if( $terminal_type == 3 ){
                 $nonumber = 'NONUMBER';
             }*/
//             $this->addBuffer( $element.'="'.$args[0].'"'.$nonumber );
             $this->addBuffer( $element.'="'.$args[0].'"' );
         }
         
         //$args[0] true:start false,null:ret num
         function getTabindex( &$gm , $rec , $args ){
             global $tub_count;
             if( isset($args[0]) && $args[0] === 'true' ){
                 $tub_count = 0;
             }
             $tub_count++;
             $this->addBuffer( 'tabindex="'.$tub_count.'"' );
         }
         
         
         /*
          *  続きを見る様に文字列を切り出すするメソッド
          *　（引数に文字列を持たす形にすると、実装時に半角スペースでのセパレートに泣く事になる可能性が高いので要考慮
          *
          * 0:切り出し対象の文字列
          * 1:切り出し文字列の長さ(省略可能、システムのデフォルトの文字長
          */
         function Continuation( &$gm , $rec , $args ){
             if( !isset($args[1]) || $args[1] <= 0 )
                $num = 32;
             else
             	$num = $args[1];
             
             $str = $args[0];
             	
             if(mb_strlen($str, 'SJIS') > $num ){
                 $this->addBuffer( str_replace( ' ' , '&CODE001;', mb_substr( str_replace( '&CODE001;', ' ' , $str ), 0 , $num )."…" ) );
             }else{
                 $this->addBuffer( $args[0] );
             }
         }
         
         /*
          * 基本システムの各種コードの引数に使うために、文字列内の半角スペースをEscapeして返す。
          *
          * 0:エスケープを行う文字列
          */
         function spaceEscape( &$gm , $rec , $args ){
             $this->addBuffer( join( '\ ' , $args) );
         }
         
         function urlencode( &$gm , $rec , $args ){
             $this->addBuffer( urlencode( $args[0] ) );
         }
         
		
		/**
		 * 条件分岐。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。第一引数と第二引数の内容が一致した場合は　第三引数を、一致しなかった場合は第四引数を表示します。
		 */
		function ifelse( &$gm, $rec, $args ){
			if( $args[0] == $args[1] ){
				$this->addBuffer( $args[2] );
			}else{
				$this->addBuffer( $args[3] );
			}
		}
		function is_set( &$gm, $rec, $args ){
			if( $args[0] != "" ){
				$this->addBuffer( $args[1] );
			}else if(isset($args[2])){
				$this->addBuffer( $args[2] );
			}
		}
		
		/*
		 * @param args 0 値
		 * @param args 1 正規表現
		 * @param args 2 true draw
		 * @param args 3 false draw
		 */
		function ifmatch( &$gm, $rec, $args ){
			
			if( mb_ereg( $args[1], $args[0] ) !== FALSE ){
				$this->addBuffer( $args[2] );
			}else{
				$this->addBuffer( $args[3] );
			}
		}
		
		
		/**
		 * ソートのためのURLを描画します。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。このメソッドでは利用しません。
		 */
		function sortLink( &$gm, $rec, $args ){
			$sort	 = $_GET['sort'];
			if( $args[0] != '' ) { $sort	 =  $args[0]; }
			
			$url	 = basename($_SERVER['SCRIPT_NAME']).'?'.SystemUtil::getUrlParm($_GET);
			$url	 = preg_replace("/&sort=\w+/", "",$url);
			$url	 = preg_replace("/&sort_PAL=\w+/", "",$url);
			$url	.= '&sort='.$sort.'&sort_PAL=';
            if( isset($args[1]) && strlen($args[1]) ){
                 $url	 .= $args[1];
            }else if( $sort == $_GET['sort'] )
			{// ソート条件が現在と同一の場合
				if( $_GET['sort_PAL'] == 'asc' ){ $url	 .= 'desc'; }
				else							{ $url	 .= 'asc'; }
			}else{ $url	 .= 'desc'; }
			
			$this->addBuffer( $url );
		}
		
		
		/**
		 * GETパラメータ文字列を再現します。
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。このメソッドでは利用しません。
		 */
		function getParam( &$gm, $rec, $args ){
			
				$param = $_GET;
			//除外するパラメータ
			if( isset($args[0]) ){
				unset($param[$args[0]]);
			}
			
			$this->addBuffer( SystemUtil::getUrlParm($param) );
		}
        
        //周期的に指定項目を出力する
        //1:cycle_id   1ページ内で複数の周期を仕様する際に、それぞれを区別するため
        //2:周期間隔 2～
        //3～:パターンの中身。  周期間隔の数だけ続く
        function drawPatternCycle( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                $CYCLE_PATTERN_STRUCT[$id]['cnt'] = 0;
                $CYCLE_PATTERN_STRUCT[$id]['interval'] = $args[1];
                $CYCLE_PATTERN_STRUCT[$id]['pattern'] = array_slice( $args , 2 );
            }
            
            $this->addBuffer( $CYCLE_PATTERN_STRUCT[$id]['pattern'][ $CYCLE_PATTERN_STRUCT[$id]['cnt'] ] );
            
            
            $CYCLE_PATTERN_STRUCT[$id]['cnt']++;
            if( $CYCLE_PATTERN_STRUCT[$id]['cnt'] >= $CYCLE_PATTERN_STRUCT[$id]['interval'] )
                $CYCLE_PATTERN_STRUCT[$id]['cnt'] = 0;
        }
        //drawPatternCycleの現在のデータをインクリメントを行なわず表示する
        function drawPatternNow( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                //drawPatternCycleが先に呼ばれていない場合はスルー
                return;
            }
            
            $this->addBuffer( $CYCLE_PATTERN_STRUCT[$id]['pattern'][ $CYCLE_PATTERN_STRUCT[$id]['cnt'] ] );
        }
        //drawPatternCycleの現在のデータをインクリメントを行なわず対応するデザインを表示する
        function drawPatternSet( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                //drawPatternCycleが先に呼ばれていない場合はスルー
                return;
            }
            
            $this->addBuffer( $args[ $CYCLE_PATTERN_STRUCT[$id]['cnt']+1 ] );
        }
        
		/**
		 * 数字にコンマをつけて出力します。
		 *
		 * @param gm GUIManagerオブジェクトです。このメソッドでは利用しません。
		 * @param rec 登録情報のレコードデータです。このメソッドでは利用しません。
		 * @param args コマンドコメント引数配列です。 このメソッドでは利用しません。
		 */
		function comma( &$gm, $rec, $args ){
            $this->addBuffer(number_format(floor($args[0])). strstr($args[0], '.'));
		}
        
		/*
		 * モジュールが存在するかどうかを確認します
		 *
		 * addBuffer:TRUE/FALSE
		 *
		 * @param gm GUIManagerオブジェクトです。このメソッドでは利用しません。
		 * @param rec 登録情報のレコードデータです。このメソッドでは利用しません。
		 * @param args コマンドコメント引数配列です。 第一引数にモジュール名を指定します。
		 */
		function mod_on( &$gm, $rec, $args ){
			if( class_exists( 'mod_'.$args[0] ) ){
				$this->addBuffer( 'TRUE' );
			}else{
				$this->addBuffer( 'FALSE' );
			}
		}

	}


//$db_a databaseの配列
//$d 現在の深さ
function groupTableSelectFormMultiReflexive( &$str , $param , $check , $d = 0 , $id = null ){

    $db = $param[$d]['db'];
    if( $id == null ){
        $table = $db->getTable();
    }else{
        $table = $db->searchTable( $db->getTable() , $param[$d]['parent'] , '=' , $id );
    }
    $row = $db->getRow( $table );
    
    $pad = putCnt($d,'　');
    
    for($i=0;$i<$row;$i++){
        $rec = $db->getRecord( $table , $i );
        if( isset( $param[ $d+1 ] ) ){
            $cid = $db->getData( $rec , 'id' );
            $str .= '<option value="" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            groupTableSelectFormMultiReflexive($str,$param,$check,$d+1,$cid);
        }else{
            $cid = $db->getData( $rec , 'id' );
            if( $cid == $check )
                $str .= '<option value="'.$cid.'" selected="selected">'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            else
                $str .= '<option value="'.$cid.'" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
        }
    }
}
function searchGroupTableFormMultiReflexive( &$str , $param , $check , $d = 0 , $id = null ){
    $db = $param[$d]['db'];
    if( $id == null ){
        $table = $db->getTable();
    }else{
        $table = $db->searchTable( $db->getTable() , $param[$d]['parent'] , '=' , $id );
    }
    $row = $db->getRow( $table );
    
    $pad = putCnt($d,'　');
    
    for($i=0;$i<$row;$i++){
        $rec = $db->getRecord( $table , $i );
        if( isset( $param[ $d+1 ] ) ){
            $cid = $db->getData( $rec , 'id' );
            if( $cid == $check )
                $str .= '<option value="'.$cid.'" selected="selected">'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            else
                $str .= '<option value="'.$cid.'" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            searchGroupTableFormMultiReflexive($str,$param,$check,$d+1,$cid);
        }else{
            $cid = $db->getData( $rec , 'id' );
            if( $cid == $check )
                $str .= '<option value="'.$cid.'" selected="selected">'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            else
                $str .= '<option value="'.$cid.'" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
        }
    }
}

//指定した数だけ、指定した文字を返す
function putCnt( $num , $char ){
    $str = "";
    for($i=0;$i<$num;$i++){
        $str .= $char;
    }
    return $str;
}

?>