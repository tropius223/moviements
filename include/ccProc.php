<?php

	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * ccProcクラス。
	 * 
	 * @author 丹羽一智
	 * @version 3.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/
	class ccProc
	{
		// 関数の割り振り
		function controller($gm, $rec, $cc)
		{
			switch($cc[0])
			{
			case 'readhead':
			case 'readend':
            case 'ifbegin':
            case 'elseif':
            case 'else':
            case 'endif':
				return;
			case 'include':
				return ccProc::drawDesign($gm, $rec, $cc);
            case '//':
                return;
			default:
				return ccProc::{$cc[0]}($gm, $rec, $cc);
			}
		}
		
		// テンプレートに関連付けられたレコードの引数で指定されたカラムの内容を出力する。
		function value($gm, $rec, $cc)
		{
			$ret			 = "";
			if(  !isset( $gm )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via value )' ); }
			if(   isset(  $_GET[ $cc[1] ]  )   ){
				if(  strpos( $_GET[ $cc[1] ], '%u'  ) !== false  )	{ $_POST[ $cc[1] ]	 = escuni2sjis( $_GET[ $cc[1] ] ); }
				else												{ $_POST[ $cc[1] ]	 = $_GET[ $cc[1] ]; }
			}
			
			$db		 = $gm->getDB();
			if( $gm->colType[ $cc[1] ] == 'timestamp' )		
			{ 
				$time	  = $db->getData( $rec, $cc[1] );
				if( $time > 0 ) { $ret	 .= date(  $gm->timeFormat, $time  ); }
			}
			else if( $gm->colType[ $cc[1] ] == 'boolean' )
			{
				if( $db->getData( $rec, $cc[1] ) )	{ $ret	 .= 'TRUE'; }
				else								{ $ret	 .= 'FALSE'; }
			}
			else
			{
				if( is_null( $rec ) && isset( $_POST[ $cc[1] ] ) )	
				{ 
					if(  isset( $cc[2] ) && strtolower($cc[2]) == 'true'  )		{ $ret	 .= brChange($_POST[ $cc[1] ]); }
					else																	{ $ret	 .= ($_POST[ $cc[1] ]); }
				}
				else
				{
					if(  isset( $cc[2] ) && strtolower($cc[2]) == 'false'  )	{ $ret	 .= $db->getData( $rec, $cc[1] ); }
					else																	{ $ret	 .= $db->getData( $rec, $cc[1], true ); }
				}
			}
			
			return $ret;
		}

		// 設定されたレコードをカラム名で検索し、マッチした項目を複数の文字列から検索、対応する文字列を表示する。
		function valueReplace($gm, $rec, $cc)
		{
			$ret			 = "";
			if(  !isset( $gm )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via valueReplace )' ); }
			$db		 = $gm->getDB();
			$data	 = $db->getData( $rec, $cc[1] );
			if( is_bool($data) )
			{
				if( $data )	{ $data	 = 'TRUE'; }
				else		{ $data	 = 'FALSE'; }
				$cc[2]	 = strtoupper($cc[2]);
			}
			$befor	 = explode( '/', $cc[2] );
			$after	 = explode( '/', $cc[3] );
			for($i=0; $i<count($befor); $i++)
			{
				if( $data == $befor[$i] )	{ $ret	 .= $after[$i]; break; }
			}
			return $ret;
		}

		// 第一引数で入力した文字列を複数の文字列から検索、対応する文字列を表示する。
		function valueValueReplace($gm, $rec, $cc)
		{
			$ret			 = "";
			if(  !isset( $gm )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via valueValueReplace )' ); }
			$data	 = $cc[1];
			if( is_bool($data) )
			{
				if( $data )	{ $data	 = 'TRUE'; }
				else		{ $data	 = 'FALSE'; }
				$cc[2]	 = strtoupper($cc[2]);
			}
			$befor	 = explode( '/', $cc[2] );
			$after	 = explode( '/', $cc[3] );
			for($i=0; $i<count($befor); $i++)
			{
				if( $data == $befor[$i] )	{ $ret	 .= $after[$i]; break; }
			}
			
			return $ret;
		}
        
        function arrayReplace($gm, $rec, $cc)
        {
			$ret			 = "";
			if( !isset($gm) )						{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via arrayReplace )' ); }
			$db		 = $gm->getDB();
            
			$array       = explode( '/' , $db->getData( $rec, $cc[1] ) );
			$befor	 = array_flip(explode( '/', $cc[3] ));
			$after	 = explode( '/', $cc[4] );
                
			foreach( $array as $data ){
                if( is_bool($data) )
                {
                    if( $data )	{ $data	 = 'TRUE'; }
                    else		{ $data	 = 'FALSE'; }
                    $cc[3]	 = strtoupper($cc[3]);
                }
                
                if(strlen($ret)){ $ret .= $cc[2];}
                $ret .= $after[ $befor[$data] ];
			}
			
			return $ret;
        }

		// テンプレートに関連づけられたレコードの指定されたカラムを別テーブルの指定カラムをキーに検索を行ない置換を行なう。 
		function alias($gm, $rec, $cc)
		{
			$ret			 = "";
			if( !isset($gm) )						{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via alias )' ); }
			if( !isset( $gm->aliasDB[ $cc[1] ] ) )	{ $gm->addAlias( $cc[1] ); }
			
			$db = $gm->aliasDB[ $cc[1] ];
			$table		 = $db->getTable($gm->table_type);
			$value		 = $gm->db->getData( $rec, $cc[2] );
			
			if( $value != '' )
			{
				$table		 = $db->searchTable(  $table, $cc[3], '=', $value );
				
				$rec		 = $db->getRecord( $table, 0 );
				
				if(  isset( $cc[2] ) && ( $cc[2] == 'FALSE' || $cc[2] == 'false' )  )	{ $ret		 .= $db->getData( $rec, $cc[4] ); }
				else																	{ $ret		 .= $db->getData( $rec, $cc[4], true ); }
			}	
			
			return $ret;
		}

		// 指定した文字列を別テーブルの指定カラムをキーに検索を行ない置換を行なう。 
		function valueAlias($gm, $rec, $cc)
		{
			$ret			 = "";
			if( !isset($gm) )						{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via valueAlias )' ); }
		if( !isset( $gm->aliasDB[ $cc[1] ] ) )	{ $gm->addAlias( $cc[1] ); }
			
			$db = $gm->aliasDB[ $cc[1] ];
			$table		 = $db->getTable($gm->table_type);
			$table		 = $db->searchTable( $table, $cc[3], '=', $cc[2] );
			$rec		 = $db->getRecord( $table, 0 );
			
			if(  isset( $cc[2] ) && ( $cc[2] == 'FALSE' || $cc[2] == 'false' )  )	{ $ret		 .= $db->getData( $rec, $cc[4] ); }
			else																	{ $ret		 .= $db->getData( $rec, $cc[4], true ); }
			
			
			return $ret;
		}

		// 指定したカラムに入った各文字列を別テーブルの指定カラムをキーに検索を行ない置換を行ない、その一覧を返す。
		function arrayAlias($gm, $rec, $cc)
		{
			$ret			 = "";
			if( !isset($gm) )						{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via arrayAlias )' ); }
			if( !isset( $gm->aliasDB[ $cc[1] ] ) )	{ $gm->addAlias( $cc[1] ); }
			
			$db			 = $gm->aliasDB[ $cc[1] ];
			$table		 = $db->getTable($gm->table_type);
			
			$array       = explode( '/' , $gm->db->getData( $rec, $cc[2] ) );
			foreach( $array as $key ){
				$stable		 = $db->searchTable(  $table, $cc[3], '=',  $key );
				$arec		 = $db->getRecord( $stable, 0 );
				if(  isset( $cc[2] ) && ( $cc[2] == 'FALSE' || $cc[2] == 'false' )  )	{ 
					if( isset( $cc[5]) ) $ret .= $db->getData( $arec, $cc[4] ).$cc[5];
					else $ret .= $db->getData( $arec, $cc[4] ).'/';
				}
				else
				{
					if( isset( $cc[5]) ) $ret .= $db->getData( $arec, $cc[4], true ).$cc[5];
					else $ret .= $db->getData( $arec, $cc[4], true ).'/';
				}
			}
			if( count($array) ){
				if( isset( $cc[5]) ) $ret = substr( $ret, 0, strlen($ret)-strlen($cc[5]));
				else $ret = substr( $ret, 0, strlen($ret)-1);
			}
			
			
			return $ret;
		}

		// /で結合した置換後の文字列配列を返す。 
		function arrayValueAlias($gm, $rec, $cc)
		{
			$ret			 = "";
			if( !isset($gm) )						{ throw new Exception( 'CommandComment Null Pointer Error -> $gm ( via arrayValueAlias )' ); }
			if( !isset( $gm->aliasDB[ $cc[1] ] ) )	{ $gm->addAlias( $cc[1] ); }
			
			$db			 = $gm->aliasDB[ $cc[1] ];
			$table		 = $db->getTable();
			
			$array       = explode( '/' , $cc[2] );
			foreach( $array as $key ){
				$stable		 = $db->searchTable(  $table, $cc[3], '=',  $key );
				$arec		 = $db->getRecord( $stable, 0 );
				if(  isset( $cc[2] ) && ( $cc[2] == 'FALSE' || $cc[2] == 'false' )  )	{ 
					if( isset( $cc[5]) ) $ret .= $db->getData( $arec, $cc[4] ).$cc[5];
					else $ret .= $db->getData( $arec, $cc[4] ).'/';
				}
				else
				{
					if( isset( $cc[5]) ) $ret .= $db->getData( $arec, $cc[4], true ).$cc[5];
					else $ret .= $db->getData( $arec, $cc[4], true ).'/';
				}
			}
			if( count($array) ){
				if( isset( $cc[5]) ) $ret = substr( $ret, 0, strlen($ret)-strlen($cc[5]));
				else $ret = substr( $ret, 0, strlen($ret)-1);
			}
			
			return $ret;
		}
		
		// テンプレートに関連づいたレコードから指定したカラムの値を抜きだし、存在する場合はそれを画像のパスとしてimgタグに受け渡し表示する。
		function object($gm, $rec, $cc)
		{
			global $IMAGE_NOT_FOUND;
			$ret			 = "";
			switch( $cc[1] )
			{
				case 'image':
					// 画像が存在する場合はそれを画像のパスとしてimgタグに受け渡し表示する。
					if(  $gm->db->getData( $rec, $cc[2] ) == null  ){
                                            $ret	 .= $IMAGE_NOT_FOUND; 
                                        }
					else{
                                            if(isset($cc[3])){
                                                if($cc[3]=='option'){
                                                    $option = ' '.$cc[4];
                                                }else{
                                                    $option = '';
                                                }
                                            }
                                            $ret	 .= '<img'."\t".'src="'. $gm->db->getData($rec, $cc[2]). '"'."\t".'border="0"'.$option.'/>'; 
                                        }
					break;
					
				case 'imageSize':
					// 画像が存在する場合はそれを画像のパスとしてimgタグに受け渡し表示する。widthとheightを設定可能。
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else												{ $ret	 .= '<img'."\t".'src="'. $gm->db->getData($rec, $cc[2]). '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>'; }
					break;
				case 'imageStr':
					// 表示する画像のパスを文字列指定で渡す。
					if(  strlen($cc[2]) <= 0  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else												{ $ret	 .= '<img'."\t".'src="'. $cc[2] . '"'."\t".'border="0"/>'; }
					break;
				case 'imageSizeStr':
					// 表示する画像のパスを文字列指定で渡す。widthとheightを設定可能。
					if(  strlen($cc[2]) <= 0  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else												{ $ret	 .= '<img'."\t".'src="'. $cc[2] . '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>'; }
					break;
				case 'linkImage':
					// 画像が存在する場合はそれを画像のパスとしてimgタグに受け渡し表示する。画像には画像へのリンクを付与する。 
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else{
						$url = $gm->db->getData($rec, $cc[2]);
						$ret    .= '<a href="'. $url .'" target="_blank" ><img'."\t".'src="'. $url . '"'."\t".'border="0"/></a>';
					}
					break;
					
				case 'linkImageSize':
					// 画像が存在する場合はそれを画像のパスとしてimgタグに受け渡し表示する。画像には画像へのリンクを付与する。widthとheightを設定可能。 
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else{
						$url = $gm->db->getData($rec, $cc[2]);
						$ret    .= '<a href="'. $url .'" target="_blank" >';
						$ret    .= '<img'."\t".'src="'. $url . '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>';
						$ret    .= '</a>';
					}
					break;
				case 'imageSizeNotfound':
					// 画像が存在する場合はそれを画像のパスとしてimgタグに受け渡し表示する。widthとheightを設定可能。
					// 存在しない場合は引数に与えられた文字列を表示する。
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $cc[5]; }
					else												{ $ret	 .= '<img'."\t".'src="'. $gm->db->getData($rec, $cc[2]). '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>'; }
					break;
			}
			return $ret;
		}
		
		// formを出力する。
		function form($gm, $rec, $cc)
		{
			$ret			 = "";
			if(   isset(  $_GET[ $cc[2] ]  )   )
			{
				if( is_array( $_GET[ $cc[ 2 ] ] ) )
				{
					$result = Array();

					foreach( $_GET[ $cc[ 2 ] ] as $value )
					{
						if( false !== strpos( $value , '%u' ) )
							{ $result[] = escuni2sjis( $value ); }
						else
							{ $result[] = $value; }
					}

					$_POST[ $cc[ 2 ] ] = $result;
				}
				else
				{
					if(  strpos( $_GET[ $cc[2] ], '%u'  ) !== false  )	{ $_POST[ $cc[2] ]	 = escuni2sjis( $_GET[ $cc[2] ] ); }
					else												{ $_POST[ $cc[2] ]	 = $_GET[ $cc[2] ]; }
				}
			}
			
			switch( $cc[1] )
			{
				case 'text':
					// textのinputタグを出力。
					$option		 = "";
					if(  isset(  $cc[6]  )  )	{ $option	 = $cc[6]; }
					if(   isset(  $_POST[ $cc[2] ]  )   )	{ $option	 .= ' value="'. htmlspecialchars($_POST[ $cc[2] ] , ENT_COMPAT | ENT_HTML401 , 'SJIS') .'"';	}
					else									{ $option	 .= ' value="'. htmlspecialchars($cc[5] , ENT_COMPAT | ENT_HTML401 , 'SJIS') .'"'; }
					$ret	 .= '<input type="text" name="'. $cc[2] .'" ';
                    if( isset($cc[3]) && strlen($cc[3]) )
                        $ret     .= 'size="'. $cc[3] .'" ';
                    if( isset($cc[4]) && strlen($cc[4]) )
                        $ret     .= 'maxlength="'. $cc[4] .'" ';
                    $ret     .= $option .'/>'. "\n";
					break;
					
				case 'password':
					// passwordのinputタグを出力。
					$option		 = "";
					if(  isset(  $cc[5]  )  )	{ $option	 = $cc[5]; }
					$ret	 .= '<input type="password" name="'. $cc[2] .'" ';
                    if( isset($cc[3]) && strlen($cc[3]) )
                        $ret     .= 'size="'. $cc[3] .'" ';
                    if( isset($cc[4]) && strlen($cc[4]) )
                        $ret     .= 'maxlength="'. $cc[4] .'" ';
                    $ret     .= $option .'>'. "\n";
					break;
					
				case 'textarea':
					// textareaタグを出力。
					$option		 = "";
					if( isset(  $cc[6]  ) )	{ $option = $cc[6]; }
					if( isset( $_POST[ $cc[2] ] ) )
					{
						$value	 = str_replace(  '<br/>', "\n", ($_POST[ $cc[2] ])  );
					}
					else
					{
						$cc[5]	 = str_replace( '<br/>', "\n", $cc[5] );
						$value	 = $cc[5];
					}
					$ret	 .= '<textarea name="'. $cc[2] .'" cols="'. $cc[3] .'" rows="'. $cc[4] .'" '. $option .'>'. htmlspecialchars ( $value , ENT_COMPAT | ENT_HTML401 , 'SJIS' ) .'</textarea>'. "\n";
					break;
					
				case 'radio':
					// radioのinputタグを配列の数だけ出力
					$value	 = explode( '/', $cc[5] );
					$index	 = explode( '/', $cc[6] );
							
					$option		 = "";
					if(  isset(  $cc[7]  )  )	{ $option	 = $cc[7]; }
					
					if(   isset(  $_POST[ $cc[2] ]  ) && !is_array( $_POST[ $cc[ 2 ] ] )   )
					{
						for($i=0; $i<count($value); $i++)
						{
							if( $value[$i] == $_POST[ $cc[2] ] )	{ $ret	 .= '<label><input type="radio" name="'. $cc[2] .'" value="'. $value[$i] .'" '. $option .' checked="true" />'. $index[$i]. $cc[4]. "</label>\n"; }
							else									{ $ret	 .= '<label><input type="radio" name="'. $cc[2] .'" value="'. $value[$i] .'" '. $option .' />'. $index[$i]. $cc[4]. "</label>\n"; }
						}
					}
					else
					{
						for($i=0; $i<count($value); $i++)
						{
							if(  $value[$i] == $cc[3]  )	{ $ret	 .= '<label><input type="radio" name="'. $cc[2] .'" value="'. $value[$i] .'" '. $option .' checked="true" />'. $index[$i]. $cc[4]. "</label>\n"; }
							else							{ $ret	 .= '<label><input type="radio" name="'. $cc[2] .'" value="'. $value[$i] .'" '. $option .'/>'. $index[$i]. $cc[4]. "</label>\n"; }
						}
					}
					break;
					
				case 'checkbox':
					// checkboxのinputタグを配列の数だけ出力
					$init	 = explode( '/', $cc[3] );
					$value	 = explode( '/', $cc[5] );
					$index	 = explode( '/', $cc[6] );
							
					$option		 = "";
					if(  isset(  $cc[7]  )  )	{ $option	 = $cc[7]; }
					
					if(   isset(  $_POST[ $cc[2] ]  )   )
					{
						if(  is_array( $_POST[ $cc[2] ] )  )
						{
							for($i=0; $i<count($_POST[ $cc[2] ]); $i++){ $init[$i]	 = $_POST[ $cc[2] ][ $i ]; }
						}
						else	{ $init				 = explode(  '/', $_POST[ $cc[2] ]  ); }
					}
					for($i=0; $i<count($value); $i++)
					{
						$flg	 = false;
						for($j=0; $j<count($init); $j++)
						{
							if( $value[$i] == $init[$j] )
							{
								$ret	 .= '<label><input type="checkbox" name="'. $cc[2] .'[]" value="'. $value[$i] .'" '. $option .' checked="true" />'. $index[$i]. $cc[4]. "</label>\n";
								$flg	 = true;
								break;
							}
						}
					
						if( !$flg )	{ $ret	 .= '<label><input type="checkbox" name="'. $cc[2] .'[]" value="'. $value[$i] .'" '. $option .' />'. $index[$i]. $cc[4]. "</label>\n"; }
					}
                    if(!isset($cc[8]))
    					$ret	 .= '<label><input type="hidden" name="'. $cc[2] .'_CHECKBOX" value="" /></label>'."\n";
					break;
					
				case 'option':
					// プルダウン(select-optionタグのセット)を出力
					$value	 = explode( '/', $cc[4] );
					$index	 = explode( '/', $cc[5] );
						
					$option		 = "";
					if(  isset(  $cc[6]  )  )	{ $option	 = $cc[6]; }
					
					$ret	 .= '<select name="'. $cc[2] .'" '. $option .'>'. "\n";
					if(   isset(  $_POST[ $cc[2] ]  )   )
					{
						for($i=0; $i<count($value); $i++)
						{
							if(  $value[$i] == $_POST[ $cc[2] ]  )	{ $ret	 .= '<option value="'. $value[$i] .'" selected="selected" >'. $index[$i] .'</option>'. "\n"; }
							else									{ $ret	 .= '<option value="'. $value[$i] .'">'. $index[$i] .'</option>'. "\n"; }
						}
					}
					else
					{
						for($i=0; $i<count($value); $i++)
						{
							if(  $value[$i] == $cc[3]  )	{ $ret	 .= '<option value="'. $value[$i] .'" selected="selected" >'. $index[$i] .'</option>'. "\n"; }
							else							{ $ret	 .= '<option value="'. $value[$i] .'">'. $index[$i] .'</option>'. "\n"; }
						}
					}
					$ret	 .= '</select>'. "\n";
					
					break;
				case 'image':
					if( isset(  $_POST[ $cc[2] ] ) && strlen( $_POST[ $cc[2] ] ) ){
						$ret	 .= '<img src="'. $_POST[ $cc[2] ] . '" width="'. $cc[5] .'" height="'. $cc[6] .'" border="0"/><br />'."\n";
					}
				case 'file':
					// ファイル入力(type=fileのinputタグ)を出力
					$option		 = "";
					if(  isset(  $cc[3]  )  ){ $option	 = $cc[3]; }
					$ret		 .= '<input name="'. $cc[2] .'" type="file" '. $option .'>'. "\n";
					
					if( isset(  $_POST[ $cc[2] ] ) && strlen( $_POST[ $cc[2] ] ) ){
						$ret	 .= '<input name="'. $cc[2] .'_filetmp" type="hidden" value="'.$_POST[ $cc[2] ].'">'. "\n";
						if( !isset($cc[4]) || !strlen($cc[4]) ){ $cc[4] = '削除'; }
						$ret	 .= '<label><input type="checkbox" name="'.$cc[2].'_DELETE[]" value="true"  />'.$cc[4].'</label>';
					}
					
					break;
					
				case 'hidden':
					// 不可視入力(type=hiddenのinputタグ)を出力
					$option		 = "";
					if(  isset(  $cc[4]  )  )	{ $option	 = $cc[4]; }
					if( isset(  $_POST[ $cc[2] ]  ) )	{
						if( is_array( $_POST[ $cc[2] ] ) ){
							foreach( $_POST[ $cc[2] ] as $val ){
								$ret		 .= '<input name="'. $cc[2] .'[]" type="hidden" value="'. htmlspecialchars($val , ENT_COMPAT | ENT_HTML401 , 'SJIS') .'" '. $option .'/>'. "\n";
							}
						}
						else{
							$ret		 .= '<input name="'. $cc[2] .'" type="hidden" value="'. htmlspecialchars(($_POST[ $cc[2] ]) , ENT_COMPAT | ENT_HTML401 , 'SJIS') .'" '. $option .'/>'. "\n";
						} 
					}
					else								{ $ret		 .= '<input name="'. $cc[2] .'" type="hidden" value="'. htmlspecialchars($cc[3] , ENT_COMPAT | ENT_HTML401 , 'SJIS') .'" '. $option .'/>'. "\n"; }
					break;
			}
		
			return $ret;
		}

		
		// テンプレートの表示に使用されるGUIManagerのインスタンスに設定した値を出力出来る。 
		function variable($gm, $rec, $cc)
		{
			$ret			 = "";
			if(  is_null( $gm->variable[ $cc[1] ] )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> variable : '. $cc[1] ); }
			$ret	 .= $gm->variable[ $cc[1] ];
			
			return $ret;
		}
		// テンプレートの表示に使用されるGUIManagerのインスタンスに設定した値を出力出来る。 
		// 未設定でもエラー出力がされない。
		function safeVariable($gm, $rec, $cc)
		{
			$ret			 = "";
			if( ! is_null( $gm->variable[ $cc[1] ] )  )	{ $ret	 .= $gm->variable[ $cc[1] ]; }
			
			return $ret;
		}

		
		// テンプレートを表示しようとしているページへのリクエストで渡されたGETパラメータを表示出来る。
		function get($gm, $rec, $cc)
		{
			$ret			 = "";
			if(   is_array(  $_GET[ $cc[1] ]  )   )
			{
				if(  !isset( $cc[2] )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> get array index' ); }
				$ret	 .= $_GET[ $cc[1] ][ $cc[2] ];
			}
			else	{ $ret	 .= $_GET[ $cc[1] ]; }
			
			return $ret;
		}

		
		// テンプレートを表示しようとしているページへのリクエストで渡されたPOSTパラメータを表示出来る。
		function post($gm, $rec, $cc)
		{
			$ret			 = "";
			if(   is_array(  $_POST[ $cc[1] ]  )   )
			{
				if(  !isset( $cc[2] )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> post array index' ); }
				$ret	 .= $_POST[ $cc[1] ][ $cc[2] ];
			}
			else	{ $ret	 .= $_POST[ $cc[1] ]; }
			
			return $ret;
		}

		// $_REQUESTの値を出力
		function request($gm, $rec, $cc)
		{
			if(   is_array(  $_REQUEST[ $cc[1] ]  )   )
			{
				if(  !isset( $cc[2] )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> request array index' ); }
				$ret	 .= $_REQUEST[ $cc[1] ][ $cc[2] ];
			}
			else	{ $ret	 .= $_REQUEST[ $cc[1] ]; }
			
			return $ret;
		}

		// valueでtimestampを表示する場合に使用するformatを指定出来る。
		function setTimeFormat($gm, $rec, $cc)
		{
			$ret			 = "";
			$gm->setTimeFormat(  str_replace(  Array( "&CODE000;","&CODE001;"), Array("/"," ") , $cc[1]) );
			
			return $ret;
		}

		// Command.phpで定義されているコメントコマンドを呼び出す事が出来る。
		function code($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,2);
			$e		 = new Command();
			$e->{$cc[1]}( $gm, $rec, $args );
			$ret	 .= $e->getBuffer();
			
			return $ret;
		}

		// Extension.phpで定義されているコメントコマンドを呼び出す事が出来る。
		function ecode($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,2);
			$e		 = new Extension();
			$e->{$cc[1]}( $gm, $rec, $args );
			$ret	 .= $e->getBuffer();
			
			return $ret;
		}

		
		// System.php内のSystemクラスで定義されているコメントコマンドを呼び出す事が出来る。
		function syscode($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,2);

			$sys	 = SystemUtil::getSystem( $_GET["type"] );
			
			$sys->{$cc[1]}( $gm, $rec, $args );
			$ret	 .= $sys->getBuffer();
			
			return $ret;
		}

		// ./module/以下に設置され./module/module.phpによりincludeされたモジュールファイル内で定義されたモジュールクラス内のメソッドを呼び出す事が可能。
		function mod($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,3);
			
			$class_name = 'mod_'.$cc[1];
			if( !class_exists( $class_name ) ){
				return $ret;
			}
			
			$sys = new $class_name();
			
			$sys->{$cc[2]}( $gm, $rec, $args );
			$ret	 .= $sys->getBuffer();
			
			return $ret;
		}
		
		// 引数に与えられた文字を計算式として解釈し、計算結果を返す。
		function calc($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,1);
			eval( '$ret = '.join('',$args).';' );
			
			return $ret;
		}

		// このコマンドは他のコマンドの前に付ける形で利用する事により、戻り値に含まれる半角スペースをエスケープした結果を返す。
		function escp($gm, $rec, $cc)
		{
			$ret = "";
			$cc  = array_slice($cc,1);
			$ret = str_replace( '&CODE001;' , '&CODE101;', ccProc::controller($gm, $rec, $cc) );
			return $ret;
		}
		// このコマンドは他のコマンドの前に付ける形で利用する事により、戻り値をint型にcastして返す。
		function int($gm, $rec, $cc)
		{
			$ret			 = "";
			$cc = array_slice($cc,1);
			$ret = (int)ccProc::controller($gm, $rec, $cc);
			
			return $ret;
		}
		
		// $cc の内容を連結して出力
		function join($gm, $rec, $cc)
		{
			$ret			 = "";
			$cc = array_slice($cc,1);
			$ret = join( '' , $cc );
			
			return $ret;
		}

		// 引数に与えられた文字を変数として解釈し、中身を返す。
		function val($gm, $rec, $cc)
		{
			$ret			 = "";
			eval( 'global $'.$cc[1].'; $ret = $'.$cc[1].';' );
			
            if(is_bool($ret)){
    			if( $ret )	{ $ret	 = 'TRUE'; }
	    		else		{ $ret	 = 'FALSE'; }
            }
            
			return $ret;
		}
			
		// このコマンドは他のtemplateをtemplate内に展開する事が出来る。
		// ただし、templateテーブルに「INCLUDE_DESIGN」ラベルを設定されたものに限る。
		function drawDesign($gm, $rec, $cc)
		{
			global $loginUserType;
			global $NOT_LOGIN_USER_TYPE;
			global $loginUserRank;
			
            $file = Template::getTemplate( $loginUserType , $loginUserRank , $cc[1] , 'INCLUDE_DESIGN' );

            if( ! strlen($file) ){
                $ret = "<br/><br/><br/>!include error! -> ".$cc[1]."<br/><br/><br/>";
            }else if( is_null($gm) ){
            	if( $loginUserType == $NOT_LOGIN_USER_TYPE ){
            		$ret = SystemUtil::getGMforType('system')->getString( $file , $rec , null );
            	}else{
            		$ret = SystemUtil::getGMforType($loginUserType)->getString( $file , $rec , null );
            	}
                
            }else{
                $ret = $gm->getString( $file , $rec , null );
            }

			return $ret;
		}
		
		//内部変換テーブルに従って絵文字を出力する
		function emoji($gm, $rec, $cc){
			global $EMOJI_CHARSET_MAP;
			global $terminal_type;
			
			if(!$terminal_type){ return ""; }
			
			eval( '$ret = '. $EMOJI_CHARSET_MAP[ $cc[1] ].";" );
			return $ret;
		}
        
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    //出力ではなくシステム側に作用する特殊なコメントコマンド
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
        //templateの条件付きパーサー
        //C言語の条件付きコンパイル(#ifdef)みたいなもの
        //@return boolean(true/false)
        function ifbegin($gm, $rec, $cc)
		{
            switch( $cc[1] ){
                case 'not':
                case '!':
                    //条件の反転
                    return ! ccProc::ifbegin($gm, $rec, array_slice($cc,1));
                case 'alias':
                	$db = SystemUtil::getGMforType( $cc[2] )->getDB();
                	$_rec = $db->selectRecord( $cc[3] );
                	
                    return ccProc::ifbegin($gm, $_rec, array_slice($cc,3));
                case 'bool':
                case 'boolean':
                    $db = $gm->getDB();
                    return (boolean)$db->getData( $rec , $cc[2] );
                case 'intime'://指定カラムが指定期間内かどうか
                    $db = $gm->getDB();
                    $time = $db->getData( $rec , $cc[2] );
                    $period = time() - $cc[3]*3600;
                    return $time > $period;
                case 'val_intime'://指定カラムが指定期間内かどうか
                    $period = time() - $cc[3]*3600;
                    return $cc[2] > $period;
                case 'isget':
                    //getにその引数が存在するかどうか。
                    return isset($_GET[$cc[2]]);

                case 'ispost':
                    //postにその引数が存在するかどうか。
                    return isset($_POST[$cc[2]]);

                case 'issession':
                    //getにその引数が存在するかどうか。
                    return isset($_SESSION[$cc[2]]);
                case 'session':
                    //getにその引数が存在するかどうか。存在した場合はboolで
                    return isset($_SESSION[$cc[2]]) ? SystemUtil::convertBool($_SESSION[$cc[2]]) : false;
                case 'nullcheck':
                    $db = $gm->getDB();
                    //第二引数に指定されたカラムが設定されているかどうか
                    $cols = explode( '/', $cc[2]);
                    foreach( $cols as $col ){
                        if( !strlen( $db->getData( $rec, $col) ) ){
                            return false;
                        }
                    }
                    return true;
                    break;
                case 'zerocheck'://int型版のnullcheck
                    $db = $gm->getDB();
                    //第二引数に指定されたカラムが設定されているかどうか
                    $cols = explode( '/', $cc[2]);
                    foreach( $cols as $col ){
                        if(  $db->getData( $rec, $col) == 0 ){
                            return false;
                        }
                    }
                    return true;
                    break;
                case 'eq':
                case 'equal':
                case '=':
                    //第二引数のカラム名としたレコードの値と、第三引数に指定された値が一致するかどうか。
                    $db = $gm->getDB();
                    return ($db->getData( $rec , $cc[2] ) == $cc[3]);
                case 'val_equal':
                case 'val_eq':
                case 'val=':
                    //第二、第三引数に指定された値が一致するかどうか。
                    return ($cc[2] == $cc[3]);
                case 'in':
                    //第二引数のカラム名としたレコードの値が、"/"で分割された第三引数の文字群に含まれているかどうか。
                    $db = $gm->getDB();
                    $val = $db->getData( $rec , $cc[2] );
                    $array = explode( '/', $cc[3] );
                    foreach( $array as $data ){
                    	if(($val == $data) ){return true;}
                    }
                    return false;
                case 'get_equal':
                    //第二引数をGET引数の連想配列名とした値と、第三引数に指定された値が一致するかどうか。
                    return ($_GET[$cc[2]] == $cc[3]);
                case 'post_equal':
                    //第二引数をGET引数の連想配列名とした値と、第三引数に指定された値が一致するかどうか。
                    return ($_POST[$cc[2]] == $cc[3]);
                case 'eval':
                    //第二引数に指定された式を評価した結果
                    if( eval( '$ret = '.$cc[2].';' ) !== FALSE ){
                        return $ret;
                    }
                    break;
                case 'val>':
                    return ($cc[2] > $cc[3]);
                    break;
                case 'val<':
                    return ($cc[2] < $cc[3]);
                    break;
                case 'mod_on':
                	return class_exists('mod_'.$cc[2]);
                case 'mod_off':
                	return !class_exists('mod_'.$cc[2]);
                case 'match':
                	return mb_ereg( $cc[3], $cc[2] ) !== FALSE;
                case 'login':
                	global $loginUserType;
                	return $loginUserType == $cc[2];

                case 'isCarrier':
					global $terminal_type;

					$db = GMList::getDB( 'adwares' );

					if( $db->getData( $rec , 'url' ) )
						{ return true; }

					switch( $terminal_type )
					{
						case MobileUtil::$TYPE_NUM_DOCOMO:
							{ return ( $db->getData( $rec , 'url_docomo' ) ? true : false ); }

						case MobileUtil::$TYPE_NUM_AU:
							{ return ( $db->getData( $rec , 'url_au' ) ? true : false ); }

						case MobileUtil::$TYPE_NUM_SOFTBANK:
							{ return ( $db->getData( $rec , 'url_softbank' ) ? true : false ); }

						case MobileUtil::$TYPE_NUM_IPHONE:
							{ return ( $db->getData( $rec , 'url_iphone' ) ? true : false ); }

						case MobileUtil::$TYPE_NUM_ANDROID:
							{ return ( $db->getData( $rec , 'url_android' ) ? true : false ); }
					}
            }
            return false;
        }
                
	}
?>