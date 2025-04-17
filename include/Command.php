<?php

	/**
	 * ��{���߃N���X
	 * 
	 * @author �O�H��q
	 * @version 1.0.0
	 * 
	 */
	class Command extends command_base
	{

		/**********************************************************************************************************
		 * �V�X�e���p���\�b�h
		 **********************************************************************************************************/

		/**
		 * ���O�C��ID��`�悵�܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 */
		function loginid( &$gm, $rec, $args ){
			global $LOGIN_ID;
			$this->addBuffer( $LOGIN_ID );
		}

		/**
		 * �^�C���X�^���v��ϊ����܂��B
		 * �w�肪�����ꍇ�̓V�X�e���f�t�H���g�̕����g�p����܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 * 		��������UNIX�^�C����n���܂��B
		 * 		��������date�ɓn��timeformat���w�肵�܂�(�C��)
		 */
		function timestamp( &$gm, $rec, $args ){
			if(isset($args[1])){ $this->addBuffer(date( $args[1], $args[0] )); }
			else{ $this->addBuffer(date( $gm->timeFormat, $args[0] )); }
		}
		
		/**
		 * ���݂̎��Ԃ��擾���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
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
	
	
        //�^�C���X�^���v�J�����l�̖��O���󂯂āA���̃^�C���X�^���v�l�̌o�ߔN����Ԃ�
        function getPassage( &$gm, $rec, $args ){
            			
			$db		 = $gm->getDB();
            $passage = localtime( $db->getData( $rec, $args[0] ) );
            $now = localtime( );
            
            $y = $now[5] - $passage[5];
            $m = $now[4] - $passage[4];
            
            if($m < 0 ){$y--;}
            
			$this->addBuffer( $y );
        }
        
        // �N�@���@�����󂯎���āA�N���`��
        function drawAgeByBirth( &$gm, $rec , $args ){
            if(!isset($args[1])){$args[1]=1;}
            if(!isset($args[2])){$args[2]=1;}
            $birth = sprintf("%4d%02d%02d",$args[0],$args[1],$args[2]);
            $now = date('Ymd');
            $this->addBuffer( (int)(($now - $birth)/10000) );
        }
		
		/**
		 * �A�N�e�B�x�[�g�R�[�h�𔭍s���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B���̃��\�b�h�ł͗��p���܂���B
		 */
		function activate( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $HOME;
			// **************************************************************************************
			
			$db		 = $gm->getDB();
			$this->addBuffer(   $HOME. 'activate.php?type='. $_GET['type'] .'&id='. $db->getData( $rec, 'id' ) .'&md5='. md5(  $db->getData( $rec, 'id' ). $db->getData( $rec, 'mail' )  )   );
		}
		
		function drawImage( &$gm, $rec, $args ){
		 	if(  file_exists( $args[0] )  ){
				// �t�@�C�������݂���ꍇ
				if(  isset( $args[1] ) && isset( $args[2] )  ){
					$this->addBuffer( '<img src="'. $args[0] .'" width="'. $args[1] .'" height="'. $args[2] .'" border="0"/>' );
				}else{
					$this->addBuffer( '<img src="'. $args[0] .'" border="0"/>' );
				}
			
			}else{
				// �t�@�C�������݂��Ȃ��ꍇ
				$this->addBuffer( '<span>�C���[�W�͓o�^����Ă��܂���</span>' );
			}
		 }

		/**
		 * �f�[�^�̌������擾�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɃJ�������@��O�����ɉ��Z�q�@��l�����ɒl�@�����Ă��܂��B
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
		 * �f�[�^�̍��v���擾�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɏW�v�J�������@��O�`�܈����Ɍ����J�������A���Z�q�A�l�@�����Ă��܂��B
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
		 * �g���V�X�e���p���\�b�h
		 **********************************************************************************************************/

		/**
		 * ���[�U���擾�B
		 * ID���烆�[�U�����������A�Y������ ���[�U��( ���[�UID ) �̌`���ŏo�͂��܂��B
		 * �ǂ̃��[�U���e�[�u���Ƀ��[�U�f�[�^������̂��킩��Ȃ��Ƃ��ȂǂɗL���ł��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
		 */
		function getName( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
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
		 * �f�[�^�����擾�B
		 * ID����f�[�^���������A�Y������ �f�[�^��( �f�[�^ID ) �̌`���ŏo�͂��܂��B
		 * �ǂ̃e�[�u���Ƀf�[�^������̂��킩��Ȃ��Ƃ��ȂǂɗL���ł��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B�@�������ɖ��O�̊i�[����Ă���J��������n���܂��B ��O�����Ƀ����N���邩��^�U�l�œn���܂��B
		 */
		function getDataName( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $TABLE_NAME;
            global $ID_LENGTH;
			// **************************************************************************************
			
			// �S�Ẵe�[�u����GUIManager�C���X�^���X���擾���܂��B
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
			
			if( !$flg )	{ $this->addBuffer( '�Y���f�[�^����' ); }
		}




		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 * �T�C�g�V�X�e���p���\�b�h
		 **********************************************************************************************************/



         
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 *�@�g���ėp���\�b�h
		 **********************************************************************************************************/

         
		/**
		 * �����œn���������܂ł�I���ł���select�R���g���[����\���B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
		 * ��������name���w��
		 * �������ōŌ�̐������w��l(�ȗ���)
		 * ��O�����ŏ����l(�I�𒆂̍��ڂ̐������w��l)(�ȗ���)
		 * ��l�����ŊJ�n�l(�ȗ���)
         * ��܈����Őړ����ڂ̒ǉ��l(��F���I��) (�ȗ���)
         * ��Z�����Ń^�O�I�v�V������ݒ�i�ȗ��\�j
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
         * �����Ŏw�肵�������Ɠ�����*���o�͂���B
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
		 * �e�[�u���̑S�s����I������selectBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����Foption���ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F�����l(�ȗ���)
         * ��Z�����F���I�����ڒl(�ȗ���)
         * �掵�����F�^�O�I�v�V�����v�f(�ȗ���)
         * �攪�`�����F�J�������A���Z�q�A�l��3�Z�b�g�̃��[�v�B
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
		 * �e�q�֌W�̃e�[�u���̑S�s����A�e�e�[�u���ŃO���[�v�������q�e�[�u���I���̂��߂�selectBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������F�etable��
         * ��O�����F�O���[�v��
         * ��l�����F�qtable��
         * ��܈����Foption���ƂȂ�J������
         * ��Z�����Fvalue�ƂȂ�J������
         * �掵�����F�e��ID�������J������
         * �攪�����F�����l(�ȗ���)
         * �������F���I�����ڒl(�ȗ���)
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
		 * ���i�K�̐e�q�֌W�̃e�[�u���̑S�s���g����Group�T�[�`�p�̃t�H�[�����o��
         * value�͑S��ID�Ƃ��܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         *
         * �������Fname
         * �������F�����l
         * ��O�����F���I�����ڒl
         * ��l�����F�etable
         * ��܈����F�eoption
         * ��Z�����F�qtable
         * �掵�����F�qoption
         * �攪�����F�e��ID�������q�̃J������
         *
         * �ȉ��A�Z�`�������[�v
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
        
            $param[0]['db'] = $_gm[ $args[3] ]->getDB();    //�ŏ�ʃe�[�u�����擾
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
		 * �e�q�֌W�̃e�[�u���̑S�s���g����Group�T�[�`�p�̃t�H�[�����o��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������F�etable��
         * ��O�����F�O���[�v��
         * ��l�����F�qtable��
         * ��܈����Foption���ƂȂ�J������
         * ��Z�����Fvalue�ƂȂ�J������
         * �掵�����F�e��ID�������J������
         * �攪�����F�����l(�ȗ���)
         * �������F���I�����ڒl(�ȗ���)
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
                    $option = "�@".$cdb->getData( $crec , $args[4] );
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
		 * ���i�K�̐e�q�֌W�̃e�[�u���̑S�s���g����Group�T�[�`�p�̃t�H�[�����o��
         * value�͑S��ID�Ƃ��܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         *
         * �������Fname
         * �������F�����l
         * ��O�����F���I�����ڒl
         * ��l�����F�etable
         * ��܈����F�eoption
         * ��Z�����F�qtable
         * �掵�����F�qoption
         * �攪�����F�e��ID�������q�̃J������
         *
         * �ȉ��A�Z�`�������[�v
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
        
            $param[0]['db'] = $_gm[ $args[3] ]->getDB();    //�ŏ�ʃe�[�u�����擾
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
		 * �e�[�u���̑S�s����I������checkBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����F�\�����ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F��؂蕶��
         * ��Z�����F�����l(�ȗ���)
         * �掵�����F���I�����ڒl(�ȗ���)
         * �攪�����F���ɕ\�����鐔(�ȗ���)
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
		 * �e�[�u���̑S�s����I������radioButton�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����F�\�����ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F��؂蕶��
         * ��Z�����F�����l(�ȗ���)
         * �掵�����F���I�����ڒl(�ȗ���)
         * �攪�����F���ɕ\�����鐔(�ȗ���)
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
          ���ڂ́������X�g��\��
          �܂�́A�C�ӂ̃e�[�u���̔C�ӂ̃t���O��true�̍��ڂ��ꗗ�Ƃ��ĕ\������B
        
        args
         0:�e�[�u����
         1:�t���O�J������
         2:�\����
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
          �V���́������X�g��\��
          �܂�́A�C�ӂ̃e�[�u����regist���w�肵�����Ԉȓ��̍��ڂ��ꗗ�\���B
        
        args
         0:�e�[�u����
         1:�V���Ƃ������(���Ԃ�)
         2:�\����
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
         * ���R�[�h�ɒl�����݂���ꍇ�����N��\������
         *
         * 0:���R�[�h��
         * 1:URL
         * 2:�����N�̕\������
         * 3:�����N�������ꍇ�̕\������
         */
         function drawLinkByRec( &$gm, $rec, $args ){
             $db = $gm->getDB();
             $data = $db->getData( $rec , $args[0]);
             if( ! strlen($data) ){
                 $this->addBuffer( $args[3] );
             }else{
                 //Link����̎���rec�̃f�[�^
                 if( !strlen($args[1]) )
                     $url = $data;
                 else
                     $url = $args[1];
                 
                 $this->addBuffer( '<a href="'.$url.'">'.$args[2].'</a>' );
             }
         }
         
        /*
         * ���������݂���ꍇ�����N��\������
         *
         * 0:URL
         * 1:�����N�̓��ɕt���镶���imailto:�Ƃ�
         */
         function drawLink( &$gm, $rec, $args ){
             if( strlen($args[0]) )
                 $this->addBuffer( '<a href="'.$args[1].$args[0].'" target="_blank">'.$args[0].'</a>' );
         }

        
        function getReferer(&$gm , $rec , $args ){
            $this->addBuffer( $_SERVER['HTTP_REFERER'] );
        }
        
        /*
         * ����ID�w��ɑΉ����������N�o��
         * ���R�[�h�ɒl�����݂���ꍇ�����N��\������
         *
         * 0:���R�[�h��
         * 1:URL(������ID��t�^����`)
         * 2:�����N�̕\������
         * 3:�����N�������ꍇ�̕\������
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
         
         
         //1:�S�p���� 2:���p�J�i 3:�p�� 4:�����B 
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
         
         //args[0]:�u0�v�`�u9�v�A�u*�v�A�u#�v
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
          *  ����������l�ɕ������؂�o�����郁�\�b�h
          *�@�i�����ɕ�������������`�ɂ���ƁA�������ɔ��p�X�y�[�X�ł̃Z�p���[�g�ɋ������ɂȂ�\���������̂ŗv�l��
          *
          * 0:�؂�o���Ώۂ̕�����
          * 1:�؂�o��������̒���(�ȗ��\�A�V�X�e���̃f�t�H���g�̕�����
          */
         function Continuation( &$gm , $rec , $args ){
             if( !isset($args[1]) || $args[1] <= 0 )
                $num = 32;
             else
             	$num = $args[1];
             
             $str = $args[0];
             	
             if(mb_strlen($str, 'SJIS') > $num ){
                 $this->addBuffer( str_replace( ' ' , '&CODE001;', mb_substr( str_replace( '&CODE001;', ' ' , $str ), 0 , $num )."�c" ) );
             }else{
                 $this->addBuffer( $args[0] );
             }
         }
         
         /*
          * ��{�V�X�e���̊e��R�[�h�̈����Ɏg�����߂ɁA��������̔��p�X�y�[�X��Escape���ĕԂ��B
          *
          * 0:�G�X�P�[�v���s��������
          */
         function spaceEscape( &$gm , $rec , $args ){
             $this->addBuffer( join( '\ ' , $args) );
         }
         
         function urlencode( &$gm , $rec , $args ){
             $this->addBuffer( urlencode( $args[0] ) );
         }
         
		
		/**
		 * ��������B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�������Ƒ������̓��e����v�����ꍇ�́@��O�������A��v���Ȃ������ꍇ�͑�l������\�����܂��B
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
		 * @param args 0 �l
		 * @param args 1 ���K�\��
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
		 * �\�[�g�̂��߂�URL��`�悵�܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B���̃��\�b�h�ł͗��p���܂���B
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
			{// �\�[�g���������݂Ɠ���̏ꍇ
				if( $_GET['sort_PAL'] == 'asc' ){ $url	 .= 'desc'; }
				else							{ $url	 .= 'asc'; }
			}else{ $url	 .= 'desc'; }
			
			$this->addBuffer( $url );
		}
		
		
		/**
		 * GET�p�����[�^��������Č����܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B���̃��\�b�h�ł͗��p���܂���B
		 */
		function getParam( &$gm, $rec, $args ){
			
				$param = $_GET;
			//���O����p�����[�^
			if( isset($args[0]) ){
				unset($param[$args[0]]);
			}
			
			$this->addBuffer( SystemUtil::getUrlParm($param) );
		}
        
        //�����I�Ɏw�荀�ڂ��o�͂���
        //1:cycle_id   1�y�[�W���ŕ����̎������d�l����ۂɁA���ꂼ�����ʂ��邽��
        //2:�����Ԋu 2�`
        //3�`:�p�^�[���̒��g�B  �����Ԋu�̐���������
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
        //drawPatternCycle�̌��݂̃f�[�^���C���N�������g���s�Ȃ킸�\������
        function drawPatternNow( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                //drawPatternCycle����ɌĂ΂�Ă��Ȃ��ꍇ�̓X���[
                return;
            }
            
            $this->addBuffer( $CYCLE_PATTERN_STRUCT[$id]['pattern'][ $CYCLE_PATTERN_STRUCT[$id]['cnt'] ] );
        }
        //drawPatternCycle�̌��݂̃f�[�^���C���N�������g���s�Ȃ킸�Ή�����f�U�C����\������
        function drawPatternSet( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                //drawPatternCycle����ɌĂ΂�Ă��Ȃ��ꍇ�̓X���[
                return;
            }
            
            $this->addBuffer( $args[ $CYCLE_PATTERN_STRUCT[$id]['cnt']+1 ] );
        }
        
		/**
		 * �����ɃR���}�����ďo�͂��܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B ���̃��\�b�h�ł͗��p���܂���B
		 */
		function comma( &$gm, $rec, $args ){
            $this->addBuffer(number_format(floor($args[0])). strstr($args[0], '.'));
		}
        
		/*
		 * ���W���[�������݂��邩�ǂ������m�F���܂�
		 *
		 * addBuffer:TRUE/FALSE
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B �������Ƀ��W���[�������w�肵�܂��B
		 */
		function mod_on( &$gm, $rec, $args ){
			if( class_exists( 'mod_'.$args[0] ) ){
				$this->addBuffer( 'TRUE' );
			}else{
				$this->addBuffer( 'FALSE' );
			}
		}

	}


//$db_a database�̔z��
//$d ���݂̐[��
function groupTableSelectFormMultiReflexive( &$str , $param , $check , $d = 0 , $id = null ){

    $db = $param[$d]['db'];
    if( $id == null ){
        $table = $db->getTable();
    }else{
        $table = $db->searchTable( $db->getTable() , $param[$d]['parent'] , '=' , $id );
    }
    $row = $db->getRow( $table );
    
    $pad = putCnt($d,'�@');
    
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
    
    $pad = putCnt($d,'�@');
    
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

//�w�肵���������A�w�肵��������Ԃ�
function putCnt( $num , $char ){
    $str = "";
    for($i=0;$i<$num;$i++){
        $str .= $char;
    }
    return $str;
}

?>