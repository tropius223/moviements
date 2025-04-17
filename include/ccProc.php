<?php

	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * ccProc�N���X�B
	 * 
	 * @author �O�H��q
	 * @version 3.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/
	class ccProc
	{
		// �֐��̊���U��
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
		
		// �e���v���[�g�Ɋ֘A�t����ꂽ���R�[�h�̈����Ŏw�肳�ꂽ�J�����̓��e���o�͂���B
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

		// �ݒ肳�ꂽ���R�[�h���J�������Ō������A�}�b�`�������ڂ𕡐��̕����񂩂猟���A�Ή����镶�����\������B
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

		// �������œ��͂���������𕡐��̕����񂩂猟���A�Ή����镶�����\������B
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

		// �e���v���[�g�Ɋ֘A�Â���ꂽ���R�[�h�̎w�肳�ꂽ�J������ʃe�[�u���̎w��J�������L�[�Ɍ������s�Ȃ��u�����s�Ȃ��B 
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

		// �w�肵���������ʃe�[�u���̎w��J�������L�[�Ɍ������s�Ȃ��u�����s�Ȃ��B 
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

		// �w�肵���J�����ɓ������e�������ʃe�[�u���̎w��J�������L�[�Ɍ������s�Ȃ��u�����s�Ȃ��A���̈ꗗ��Ԃ��B
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

		// /�Ō��������u����̕�����z���Ԃ��B 
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
		
		// �e���v���[�g�Ɋ֘A�Â������R�[�h����w�肵���J�����̒l�𔲂������A���݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������B
		function object($gm, $rec, $cc)
		{
			global $IMAGE_NOT_FOUND;
			$ret			 = "";
			switch( $cc[1] )
			{
				case 'image':
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������B
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
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������Bwidth��height��ݒ�\�B
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else												{ $ret	 .= '<img'."\t".'src="'. $gm->db->getData($rec, $cc[2]). '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>'; }
					break;
				case 'imageStr':
					// �\������摜�̃p�X�𕶎���w��œn���B
					if(  strlen($cc[2]) <= 0  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else												{ $ret	 .= '<img'."\t".'src="'. $cc[2] . '"'."\t".'border="0"/>'; }
					break;
				case 'imageSizeStr':
					// �\������摜�̃p�X�𕶎���w��œn���Bwidth��height��ݒ�\�B
					if(  strlen($cc[2]) <= 0  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else												{ $ret	 .= '<img'."\t".'src="'. $cc[2] . '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>'; }
					break;
				case 'linkImage':
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������B�摜�ɂ͉摜�ւ̃����N��t�^����B 
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else{
						$url = $gm->db->getData($rec, $cc[2]);
						$ret    .= '<a href="'. $url .'" target="_blank" ><img'."\t".'src="'. $url . '"'."\t".'border="0"/></a>';
					}
					break;
					
				case 'linkImageSize':
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������B�摜�ɂ͉摜�ւ̃����N��t�^����Bwidth��height��ݒ�\�B 
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $IMAGE_NOT_FOUND; }
					else{
						$url = $gm->db->getData($rec, $cc[2]);
						$ret    .= '<a href="'. $url .'" target="_blank" >';
						$ret    .= '<img'."\t".'src="'. $url . '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>';
						$ret    .= '</a>';
					}
					break;
				case 'imageSizeNotfound':
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������Bwidth��height��ݒ�\�B
					// ���݂��Ȃ��ꍇ�͈����ɗ^����ꂽ�������\������B
					if(  $gm->db->getData( $rec, $cc[2] ) == null  )	{ $ret	 .= $cc[5]; }
					else												{ $ret	 .= '<img'."\t".'src="'. $gm->db->getData($rec, $cc[2]). '"'."\t".'width="'. $cc[3] .'"'."\t".'height="'. $cc[4] .'"'."\t".'border="0"/>'; }
					break;
			}
			return $ret;
		}
		
		// form���o�͂���B
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
					// text��input�^�O���o�́B
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
					// password��input�^�O���o�́B
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
					// textarea�^�O���o�́B
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
					// radio��input�^�O��z��̐������o��
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
					// checkbox��input�^�O��z��̐������o��
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
					// �v���_�E��(select-option�^�O�̃Z�b�g)���o��
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
					// �t�@�C������(type=file��input�^�O)���o��
					$option		 = "";
					if(  isset(  $cc[3]  )  ){ $option	 = $cc[3]; }
					$ret		 .= '<input name="'. $cc[2] .'" type="file" '. $option .'>'. "\n";
					
					if( isset(  $_POST[ $cc[2] ] ) && strlen( $_POST[ $cc[2] ] ) ){
						$ret	 .= '<input name="'. $cc[2] .'_filetmp" type="hidden" value="'.$_POST[ $cc[2] ].'">'. "\n";
						if( !isset($cc[4]) || !strlen($cc[4]) ){ $cc[4] = '�폜'; }
						$ret	 .= '<label><input type="checkbox" name="'.$cc[2].'_DELETE[]" value="true"  />'.$cc[4].'</label>';
					}
					
					break;
					
				case 'hidden':
					// �s������(type=hidden��input�^�O)���o��
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

		
		// �e���v���[�g�̕\���Ɏg�p�����GUIManager�̃C���X�^���X�ɐݒ肵���l���o�͏o����B 
		function variable($gm, $rec, $cc)
		{
			$ret			 = "";
			if(  is_null( $gm->variable[ $cc[1] ] )  )	{ throw new Exception( 'CommandComment Null Pointer Error -> variable : '. $cc[1] ); }
			$ret	 .= $gm->variable[ $cc[1] ];
			
			return $ret;
		}
		// �e���v���[�g�̕\���Ɏg�p�����GUIManager�̃C���X�^���X�ɐݒ肵���l���o�͏o����B 
		// ���ݒ�ł��G���[�o�͂�����Ȃ��B
		function safeVariable($gm, $rec, $cc)
		{
			$ret			 = "";
			if( ! is_null( $gm->variable[ $cc[1] ] )  )	{ $ret	 .= $gm->variable[ $cc[1] ]; }
			
			return $ret;
		}

		
		// �e���v���[�g��\�����悤�Ƃ��Ă���y�[�W�ւ̃��N�G�X�g�œn���ꂽGET�p�����[�^��\���o����B
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

		
		// �e���v���[�g��\�����悤�Ƃ��Ă���y�[�W�ւ̃��N�G�X�g�œn���ꂽPOST�p�����[�^��\���o����B
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

		// $_REQUEST�̒l���o��
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

		// value��timestamp��\������ꍇ�Ɏg�p����format���w��o����B
		function setTimeFormat($gm, $rec, $cc)
		{
			$ret			 = "";
			$gm->setTimeFormat(  str_replace(  Array( "&CODE000;","&CODE001;"), Array("/"," ") , $cc[1]) );
			
			return $ret;
		}

		// Command.php�Œ�`����Ă���R�����g�R�}���h���Ăяo�������o����B
		function code($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,2);
			$e		 = new Command();
			$e->{$cc[1]}( $gm, $rec, $args );
			$ret	 .= $e->getBuffer();
			
			return $ret;
		}

		// Extension.php�Œ�`����Ă���R�����g�R�}���h���Ăяo�������o����B
		function ecode($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,2);
			$e		 = new Extension();
			$e->{$cc[1]}( $gm, $rec, $args );
			$ret	 .= $e->getBuffer();
			
			return $ret;
		}

		
		// System.php����System�N���X�Œ�`����Ă���R�����g�R�}���h���Ăяo�������o����B
		function syscode($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,2);

			$sys	 = SystemUtil::getSystem( $_GET["type"] );
			
			$sys->{$cc[1]}( $gm, $rec, $args );
			$ret	 .= $sys->getBuffer();
			
			return $ret;
		}

		// ./module/�ȉ��ɐݒu����./module/module.php�ɂ��include���ꂽ���W���[���t�@�C�����Œ�`���ꂽ���W���[���N���X���̃��\�b�h���Ăяo�������\�B
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
		
		// �����ɗ^����ꂽ�������v�Z���Ƃ��ĉ��߂��A�v�Z���ʂ�Ԃ��B
		function calc($gm, $rec, $cc)
		{
			$ret			 = "";
			$args = array_slice($cc,1);
			eval( '$ret = '.join('',$args).';' );
			
			return $ret;
		}

		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l�Ɋ܂܂�锼�p�X�y�[�X���G�X�P�[�v�������ʂ�Ԃ��B
		function escp($gm, $rec, $cc)
		{
			$ret = "";
			$cc  = array_slice($cc,1);
			$ret = str_replace( '&CODE001;' , '&CODE101;', ccProc::controller($gm, $rec, $cc) );
			return $ret;
		}
		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l��int�^��cast���ĕԂ��B
		function int($gm, $rec, $cc)
		{
			$ret			 = "";
			$cc = array_slice($cc,1);
			$ret = (int)ccProc::controller($gm, $rec, $cc);
			
			return $ret;
		}
		
		// $cc �̓��e��A�����ďo��
		function join($gm, $rec, $cc)
		{
			$ret			 = "";
			$cc = array_slice($cc,1);
			$ret = join( '' , $cc );
			
			return $ret;
		}

		// �����ɗ^����ꂽ������ϐ��Ƃ��ĉ��߂��A���g��Ԃ��B
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
			
		// ���̃R�}���h�͑���template��template���ɓW�J���鎖���o����B
		// �������Atemplate�e�[�u���ɁuINCLUDE_DESIGN�v���x����ݒ肳�ꂽ���̂Ɍ���B
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
		
		//�����ϊ��e�[�u���ɏ]���ĊG�������o�͂���
		function emoji($gm, $rec, $cc){
			global $EMOJI_CHARSET_MAP;
			global $terminal_type;
			
			if(!$terminal_type){ return ""; }
			
			eval( '$ret = '. $EMOJI_CHARSET_MAP[ $cc[1] ].";" );
			return $ret;
		}
        
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    //�o�͂ł͂Ȃ��V�X�e�����ɍ�p�������ȃR�����g�R�}���h
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
        //template�̏����t���p�[�T�[
        //C����̏����t���R���p�C��(#ifdef)�݂����Ȃ���
        //@return boolean(true/false)
        function ifbegin($gm, $rec, $cc)
		{
            switch( $cc[1] ){
                case 'not':
                case '!':
                    //�����̔��]
                    return ! ccProc::ifbegin($gm, $rec, array_slice($cc,1));
                case 'alias':
                	$db = SystemUtil::getGMforType( $cc[2] )->getDB();
                	$_rec = $db->selectRecord( $cc[3] );
                	
                    return ccProc::ifbegin($gm, $_rec, array_slice($cc,3));
                case 'bool':
                case 'boolean':
                    $db = $gm->getDB();
                    return (boolean)$db->getData( $rec , $cc[2] );
                case 'intime'://�w��J�������w����ԓ����ǂ���
                    $db = $gm->getDB();
                    $time = $db->getData( $rec , $cc[2] );
                    $period = time() - $cc[3]*3600;
                    return $time > $period;
                case 'val_intime'://�w��J�������w����ԓ����ǂ���
                    $period = time() - $cc[3]*3600;
                    return $cc[2] > $period;
                case 'isget':
                    //get�ɂ��̈��������݂��邩�ǂ����B
                    return isset($_GET[$cc[2]]);

                case 'ispost':
                    //post�ɂ��̈��������݂��邩�ǂ����B
                    return isset($_POST[$cc[2]]);

                case 'issession':
                    //get�ɂ��̈��������݂��邩�ǂ����B
                    return isset($_SESSION[$cc[2]]);
                case 'session':
                    //get�ɂ��̈��������݂��邩�ǂ����B���݂����ꍇ��bool��
                    return isset($_SESSION[$cc[2]]) ? SystemUtil::convertBool($_SESSION[$cc[2]]) : false;
                case 'nullcheck':
                    $db = $gm->getDB();
                    //�������Ɏw�肳�ꂽ�J�������ݒ肳��Ă��邩�ǂ���
                    $cols = explode( '/', $cc[2]);
                    foreach( $cols as $col ){
                        if( !strlen( $db->getData( $rec, $col) ) ){
                            return false;
                        }
                    }
                    return true;
                    break;
                case 'zerocheck'://int�^�ł�nullcheck
                    $db = $gm->getDB();
                    //�������Ɏw�肳�ꂽ�J�������ݒ肳��Ă��邩�ǂ���
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
                    //�������̃J�������Ƃ������R�[�h�̒l�ƁA��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    $db = $gm->getDB();
                    return ($db->getData( $rec , $cc[2] ) == $cc[3]);
                case 'val_equal':
                case 'val_eq':
                case 'val=':
                    //���A��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    return ($cc[2] == $cc[3]);
                case 'in':
                    //�������̃J�������Ƃ������R�[�h�̒l���A"/"�ŕ������ꂽ��O�����̕����Q�Ɋ܂܂�Ă��邩�ǂ����B
                    $db = $gm->getDB();
                    $val = $db->getData( $rec , $cc[2] );
                    $array = explode( '/', $cc[3] );
                    foreach( $array as $data ){
                    	if(($val == $data) ){return true;}
                    }
                    return false;
                case 'get_equal':
                    //��������GET�����̘A�z�z�񖼂Ƃ����l�ƁA��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    return ($_GET[$cc[2]] == $cc[3]);
                case 'post_equal':
                    //��������GET�����̘A�z�z�񖼂Ƃ����l�ƁA��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    return ($_POST[$cc[2]] == $cc[3]);
                case 'eval':
                    //�������Ɏw�肳�ꂽ����]����������
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