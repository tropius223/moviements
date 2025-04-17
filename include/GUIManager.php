<?php

	if( $SQL ){
		include_once "./include/extends/".$SQL_MASTER.".php";
	}else{
		include_once "./include/extends/SQLiteDatabase.php";
	}

	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * GUI�}�l�[�W���N���X�B
	 * 
	 * @author �O�H��q
	 * @version 3.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/

	class GUIManager
	{
		var $colName;								// �J������
		var $colType;								// �J�����̌^
		var $colRegist;								// Regist���ɓK�p����o���f�[�^�֐��̗�
		var $colEdit;								// Edit���ɓK�p����o���f�[�^�֐��̗�
		var $colRegex;								// ���K�\���ɂ��`�F�b�N
		var $colStep;								// ���i�K�o�^�̃X�e�b�v
		var $maxStep;								// �ő�X�e�b�v
		var $db;									// Database �I�u�W�F�N�g
		var $design_tmp;							// �f�U�C���t�@�C���e���|����
		var $table_type;							// �e�[�u���^�C�va(all)/n(nomal)/d(delete)
		var $timeFormat = "Y/m/d";					// timestamp�o�̓t�H�[�}�b�g
			//"Y/m/d(D) G:i:s"
		var $dateFormat = Array('y'=>'�N ','m'=>'��','d'=>'��');					// date�o�̓t�H�[�}�b�g
		
		static $CC_HEAD	 = '!--# ';
		static $CC_FOOT	 = ' #--';
		
		static $CC_OR = '|OR|';
		static $CC_AND = '&AND&';
		
		/**
		* �t�H�[���o�͕��@���w��
		* �E�����I�ɏo��(�W��)
		*   buffer
		*   b
		* �E�o�͉ӏ���CC�Ŏw�� <!--# variable form_begin #-->,<!--# variable form_end #-->
		*   variable
		*   v
		*/
		var $form_flg = 'v';					
		
		/**
		 * �R���X�g���N�^(SQLDatabase)�B
		 * @param a DB��
		 * @param b �e�[�u���̖��O
		 * @param c �e�[�u����`�t�@�C��
		 */
		function __construct($db_name, $table_name )
		{
			global $FORM_TAG_DRAW_FLAG;
			global $LST;
			global $ADD_LST;
			
			$LST_CLM_NAME		= 0;
			$LST_CLM_TYPE		= 1;
			$LST_CLM_SIZE		= 2;
			$LST_CLM_REGIST		= 3;
			$LST_CLM_EDIT		= 4;
			$LST_CLM_REGEX		= 5;
			$LST_CLM_STEP		= 6;
			
			$lst_file = $LST[ $table_name ];
			
			if( !file_exists( $lst_file ) )	{ throw new Exception(  'DB��`�t�@�C�����J���܂���B->'. $lst_file  ); }
			
			$fps[0] = fopen ($lst_file, 'r');
            if($fps[0] ==  FALSE ){ throw new Exception('DB��`�t�@�C���̃I�[�v���Ɏ��s���܂����B->'. $db_name); }
		
            if( isset($ADD_LST[$table_name]) && is_array($ADD_LST[$table_name]) && count($ADD_LST[$table_name]) ){
            	foreach( $ADD_LST[$table_name] as $add ){
					$fp = fopen ($add, 'r');
            		if($fp !=  FALSE ){ $fps[] = $fp; }	
            	}
            }
            foreach( $fps as $fp ){
				while(!feof($fp))
				{
					if( function_exists( 'fgetcsv' ) )
						{ $tmp = fgetcsv( $fp , 20480 , ',' , '"' ); }
					else
					{
						$buffer	 = fgets( $fp , 20480 );
						$tmp	 = explode( "," , $buffer );
					}

					if(count((Array)($tmp)) == 1)	{ continue; }
					else
					{
						$this->colName[]						 			= trim( $tmp[$LST_CLM_NAME] );
						$this->colType[  trim( $tmp[$LST_CLM_NAME] )  ]		= trim( $tmp[$LST_CLM_TYPE] );
						$this->colSize[ trim( $tmp[$LST_CLM_NAME] ) ]		= trim( $tmp[$LST_CLM_SIZE] );
						
						if(  isset( $tmp[$LST_CLM_REGIST] )  )	{ $this->colRegist[  trim( $tmp[$LST_CLM_NAME] )  ]	 = trim( $tmp[$LST_CLM_REGIST] ); }
						else						{ $this->colRegist[  trim( $tmp[$LST_CLM_NAME] )  ]	 = ""; }
						
						if(  isset( $tmp[$LST_CLM_EDIT] )  )	{ $this->colEdit[  trim( $tmp[$LST_CLM_NAME] )  ]	 = trim( $tmp[$LST_CLM_EDIT] ); }
						else						{ $this->colEdit[  trim( $tmp[$LST_CLM_NAME] )  ]	 = ""; }
						
						if(  isset( $tmp[$LST_CLM_REGEX] )  )
						{
							$tmp[$LST_CLM_REGEX] = str_replace( '<>' , ',' , $tmp[$LST_CLM_REGEX] );
							$tmp[$LST_CLM_REGEX] = str_replace( '\\<' , '<' , $tmp[$LST_CLM_REGEX] );
							$tmp[$LST_CLM_REGEX] = str_replace( '\\>' , '>' , $tmp[$LST_CLM_REGEX] );
							$this->colRegex[  trim( $tmp[$LST_CLM_NAME] )  ]	 = trim( $tmp[$LST_CLM_REGEX] );
						}
						else{ $this->colRegex[  trim( $tmp[$LST_CLM_NAME] )  ]	 = ""; }
	
						if( isset( $tmp[$LST_CLM_STEP] ) )	{ $this->colStep[  trim( $tmp[$LST_CLM_NAME] )  ]	 = trim( $tmp[$LST_CLM_STEP] ); }
						else					{ $this->colStep[  trim( $tmp[$LST_CLM_NAME] )  ]	 = 0; }
	
						if($this->maxStep < $this->colStep[  trim( $tmp[$LST_CLM_NAME] )  ])
							$this->maxStep = $this->colStep[  trim( $tmp[$LST_CLM_NAME] )  ];
					}
				}
				fclose($fp);
            }
			
			$this->db = new SQLDatabase($db_name, $table_name, $this->colName, $this->colType, $this->colSize);
			
			$this->form_flg = $FORM_TAG_DRAW_FLAG; 
		}
		
		/**
		 * �f�[�^�x�[�X���擾�B
		 * @return �f�[�^�x�[�X
		 */
		function getDB()	{	$this->db->cashReset();	return $this->db; }
		
		/**
		 * ���R�[�h�̓��e��POST�œ������Ă������Ƃɂ��܂��B
		 * @param $rec ���R�[�h�f�[�^
		 */
		function setForm($rec)
		{
			for($i=0; $i<count($this->colName); $i++)
			{
				$data	 = $this->db->getData( $rec, $this->colName[$i] );

				if(  isset( $data )  ){
					if( $this->colType[ $this->colName[$i] ] == 'boolean' )
					{
						if($data)	{ $_POST[ $this->colName[$i] ] = 'TRUE'; }
						else		{ $_POST[ $this->colName[$i] ] = 'FALSE'; }
					}
					else
					{
						$_POST[ $this->colName[$i] ] = ($data);
					}
				}
			}
		}
		
		/**
		 * GET�̓��e��POST�œ������Ă������Ƃɂ��܂��B
		 * @param $rec ���R�[�h�f�[�^
		 */
		function setFormGET($rec)
		{
			for($i=0; $i<count($this->colName); $i++)
			{
				$data	 = $this->db->getData( $rec, $this->colName[$i] );
				if( is_bool($data) )
				{
					if($data)	{ $_POST[ $this->colName[$i] ] = 'TRUE'; }
					else		{ $_POST[ $this->colName[$i] ] = 'FALSE'; }
				}
				else
				{
					if(  isset( $data )  ){ $_POST[ $this->colName[$i] ] = $data; }
				}
			}
		}
		
		var $aliasDB;
		
		/**
		 * �G�C���A�X�ŗp����GUIManager��ǉ��B
		 * $name �Ƃ������O��table��alias�\���p�e�[�u���Ƃ��Đ����A�L������B
		 * �R�}���h�R�����g�̃G�C���A�X�R�}���h�ɂ��`�悪�v�����ꂽ�ۂɂ�
		 * ����GUIManager��p���ĕ`�揈�����s���B
		 * @param $name ���O
		 * @param $gm GUIManager �I�u�W�F�N�g
		 */
		function addAlias($name)	{	global $gm;		$this->aliasDB[$name]	 = $gm[ $name ]->db; }
		
		/**
		 * timestamp�^�J�����̏o�͏����ݒ�B
		 * �R�}���h�R�����g �l�̏o�͂�
		 * timestamp�^�̒l���o�͂��悤�Ƃ����ۂɕ`�悳��鎞�Ԃ̕\���t�H�[�}�b�g���w�肵�܂��B
		 * �w����@��PHP��date() ���\�b�h�ɏ����܂��B
		 * @param $str ���Ԃ̕\���t�H�[�}�b�g
		 */
		function setTimeFormat($str)	{ $this->timeFormat = $str; }
		
		var $variable;
		/**
		 * variable ���߂ŌĂяo���ϐ����Z�b�g���܂��B
		 * @param name �ϐ���
		 * @param value �l
		 */
		 function setVariable($name, $value){ $this->variable[$name] = $value; }
		/**
		 * variable ���߂ŌĂяo���ϐ����Q�b�g���܂��B
		 * @param name �ϐ���
		 */
		 function getVariable($name){ return $this->variable[$name]; }
		 
		/**
		 * variable �����Z�b�g����
		 */
		 function clearVariable(){ $this->variable = Array(); }
		
		/**
		 * ���R�[�h�f�[�^����s���t�H�[���𐶐����܂��B
		 * @param name �ϐ���
		 * @param value �l
		 */
		 function setHiddenFormRecord( $rec )
		 {
			for($i=0; $i<count($this->colName); $i++)	{ $this->addHiddenForm(  $this->colName[$i], $this->db->getData( $rec, $this->colName[$i] )  ); }
		 }
		/**
		 * ���R�[�h�f�[�^����s���t�H�[���𐶐����܂��B
		 * @param name �ϐ���
		 * @param value �l
		 */
		 function setHiddenFormRecordEdit( $rec )
		 {
			for($i=0; $i<count($this->colName); $i++){
                $this->addHiddenForm(  $this->colName[$i], $this->db->getData( $rec, $this->colName[$i] )  );
                if( isset($_POST[$this->colName[$i].'_DELETE']) ){
                    $this->addHiddenForm(  $this->colName[$i].'_DELETE[]', $_POST[$this->colName[$i].'_DELETE'][0]);
                }
            }
		 }
		
		/**
		 * �t�H�[����HTML��`�悵�܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $rec=null ���R�[�h�f�[�^
		 * @param $jump=null submit�Ŕ�Ԑ�
		 * @param $partkey=null �����L�[
		 */
		function drawForm( $html, $rec = null, $jump = null, $partkey = null, $form_flg = null )
		{
			print $this->getFormString( $html, $rec, $jump, $partkey, $form_flg);
		}
		
		/**
		 * �t�H�[����HTML�f�[�^���擾���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $rec=null ���R�[�h�f�[�^
		 * @param $jump=null submit�Ŕ�Ԑ�
		 * @param $partkey=null �����L�[
		 */
		function getFormString( $html, $rec = null, $jump = null, $partkey = null, $form_flg = null )
		{
			if( !isset($form_flg) ) { $form_flg = $this->form_flg; }
			switch($form_flg)
			{
			case 'variable':
			case 'v':
				return $this->getFormStringSetVariable( $html, $rec, $jump, $partkey, $form_flg );
				break;
			case 'buffer':
			case 'b':
			default:
				return $this->getFormStringSetBuffer( $html, $rec, $jump, $partkey, $form_flg );
				break;
			}
		}
		
		/**
		 * �t�H�[����HTML�f�[�^���擾���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $rec=null ���R�[�h�f�[�^
		 * @param $jump=null submit�Ŕ�Ԑ�
		 * @param $partkey=null �����L�[
		 */
		function getFormStringSetBuffer( $html, $rec = null, $jump = null, $partkey = null )
		{
			global $terminal_type;
			
			$enctype	 = "";
			if(!$terminal_type) { $enctype = 'enctype="multipart/form-data"'; }
			
			$buffer	 = "";
			if(  isset( $jump )  )	{ $buffer	 .= '<form name="sys_form" method="post" action="'. $jump .'" '. $enctype .' style="margin: 0px 0px;">'. "\n"; }
			$buffer	 .= $this->addForm;
			$this->addForm = "";
			$buffer	 .= $this->getString($html, $rec, $partkey);
			if(  isset( $jump )  )	{ $buffer	 .= '</form>'. "\n"; }
			return $buffer;
		}

		/**
		 * �t�H�[����`�悵�܂��B
         *
         *  form�^�O����variable�ɃZ�b�g���ăe���v���[�g�ɓn���܂��B
         *  header������form���g���Ă��āAgetFormString����form���l�X�g���Ă��܂����ɂ��g�����������B
         *
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $rec=null ���R�[�h�f�[�^
		 * @param $jump=null submit�Ŕ�Ԑ�
		 * @param $partkey=null �����L�[
		 */
		function getFormStringSetVariable( $html,  $rec = null, $jump = null, $partkey = null )
		{
			global $terminal_type;
			
			$enctype	 = "";
			if(!$terminal_type) { $enctype = 'enctype="multipart/form-data"'; }
			
			if(  isset( $jump )  )	{ $this->setVariable('form_begin','<form name="sys_form" method="post" action="'. $jump .'" '. $enctype .' style="margin: 0px 0px;">'.$this->addForm); }
            else{ $this->setVariable('form_begin',$this->addForm); }
            $this->addForm = "";
            
			if(  isset( $jump )  )	{ $this->setVariable('form_end','</form>'); }
            else{ $this->setVariable(''); }
            
			$buffer	 = $this->getString($html, $rec, $partkey);
			return $buffer;
		}
		
		var $addForm;
		
		/**
		 * �s���t�H�[���̒ǉ��B
		 * @param $name INPUT��
		 * @param $val INPUT�̒l
		 */
		function addHiddenForm( $name, $val )
		{
			if( is_bool($val) )
			{
				if( $val )	{ $this->addForm .= '<input name="'. $name .'" type="hidden" value="TRUE" />'. "\n"; }
				else		{ $this->addForm .= '<input name="'. $name .'" type="hidden" value="FALSE" />'. "\n"; }
			}
			else	{ $this->addForm .= '<input name="'. $name .'" type="hidden" value="'. htmlspecialchars( $val , ENT_COMPAT | ENT_HTML401 , 'SJIS' ) .'" />'. "\n"; }
		}
		
		/**
		 * HTML��`�悵�܂��B
		 * HTML�Ɋ܂܂��R�}���h�R�����g�ɂ� $rec �œn�������R�[�h�f�[�^�̓��e�𔽉f���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $rec=null ���R�[�h�f�[�^
		 * @param $partkey=null �����L�[
		 */
		function draw($html, $rec = null, $partkey = null) { print $this->getString($html, $rec, $partkey); }
		
		/**
		 * �����`������s���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $partkey �����L�[
		 */
		function partRead($html, $partkey)	{ print GUIManager::partGetString( $html, $partkey ); }
		
		/**
		 * �����f�[�^�擾�����s���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $partkey �����L�[
		 */
		function partGetString($html, $partkey)
		{
			if(  !file_exists( $html )  )	{ throw new Exception( 'HTML�t�@�C�����J���܂���B->'. $html ); }
			
			if(  isset( $partkey )  )	{ $state = Array('draw'=>0,'if'=>false); }
			else						{ throw new Exception( "GUIManager Error -> partRead() or partGetString() -> Not Set PartKey" ); }
			
			$str	 = "";
            $c_part = null;
			
			if( !isset($this->design_tmp[$html]) ) { $this->getFile($html); }
            
            $row = count($this->design_tmp[$html]);
            for($i=0;$row>$i;$i++){
				$str .= GUIManager::commandComment( $this->design_tmp[$html][$i], $this, null, $state , $c_part , $partkey );
            }
			
			$str	 = str_replace( array("&CODE001;","&CODE101;","&CODE000;","&CODE002;","&CODE005;","&CODE006;"), array(" ", " ", "/", "\\",'!--# ',' #--') , $str );
			return $str;
		}
		
		/**
		 * �e�[�u���̓��e�����X�g��`�悵�܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $table �e�[�u���f�[�^
		 * @param $partkey=null �����L�[
		 */
		function drawList($html, $table, $partkey = null)	{ print $this->getListString( $html, $table, $partkey ); }
		
		/**
		 * �e�[�u���̓��e�̃��X�g�`�挋�ʂ�HTML���擾���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $table �e�[�u���f�[�^
		 * @param $partkey=null �����L�[
		 */
		function getListString($html, $table, $partkey = null)
		{
			$buffer	 = "";
			$this->db->cashReset();
			$row	 = $this->db->getRow( $table );
			for($i=0; $i<$row; $i++)
			{
				$rec = $this->db->getRecord( $table, $i );
				$buffer	 .= $this->getString( $html, $rec, $partkey );
			}
			
			return $buffer;
		}
		/**
		 * �e�[�u���̓��e�̃��X�g�`�挋�ʂ�HTML���擾���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $table �e�[�u���f�[�^
		 * @param $partkey=null �����L�[
		 */
		function getListNumString($html, $table, $partkey = null,$start)
		{
			$buffer	 = "";
			$row	 = $this->db->getRow( $table );
			for($i=0; $i<$row; $i++)
			{
				$rec = $this->db->getRecord( $table, $i );
				$this->setVariable('num',$start+$i);
				$buffer	 .= $this->getString( $html, $rec, $partkey );
			}
			
			return $buffer;
		}
		
		/**
		 * �e�[�u���̓��e���t�H�[���`���Ń��X�g�`�悵�܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $table �e�[�u���f�[�^
		 * @param $jump=null submit�Ŕ�Ԑ�
		 * @param $partkey=null �����L�[
		 */
		function drawFormList($html, $table, $jump, $partkey = null)
		{
			$row	 = $this->db->getRow( $table );
			for($i=0; $i<$row; $i++)
			{
				$rec = $this->db->getRecord( $table, $i );
				$this->drawForm( $html, $rec, $jump, $partkey );
			}
			
		}
		
		/**
		 * HTML���L���b�V�����܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 */		
		function getFile( $html )
		{
			$fp = fopen ( $html, 'r' );
			
			$str = Array();
			while(  !feof( $fp )  )
			{
				$buffer	 = fgets( $fp, 20480 );
				
				$buffer = mb_convert_encoding( $buffer,'UTF-8','SJIS');
				
				$buffer		 = str_replace( "\\\\", "&CODE002;", $buffer );
				$buffer		 = str_replace( "\/", "&CODE000;", $buffer );
				$buffer		 = str_replace( "\ ", "&CODE001;", $buffer );
				
				$buffer = mb_convert_encoding( $buffer,'SJIS','UTF-8');
				
				$str[] = $buffer;
			}
			fclose( $fp );
			$this->design_tmp[$html] = $str;
		}
		
		/**
		 * HTML���擾���܂��B
		 * HTML�Ɋ܂܂��R�}���h�R�����g�ɂ� $rec �œn�������R�[�h�f�[�^�̓��e�𔽉f���܂��B
		 * @param $html �f�U�C��HTML�t�@�C��
		 * @param $rec=null ���R�[�h�f�[�^
		 * @param $partkey=null �����L�[
		 */
		function getString($html, $rec = null, $partkey = null)
		{
			if( !file_exists( $html ) )	{ throw new Exception( 'HTML�t�@�C�����J���܂���B->'. $html ); }
			
		    $state = $this->getDefState( $partkey == null );
			$c_part = null;
			
			if( !isset($this->design_tmp[$html]) ) { $this->getFile($html); }
			
            $row = count($this->design_tmp[$html]);
            $str = "";
            for($i=0;$row>$i;$i++){
				$str .= GUIManager::commandComment( $this->design_tmp[$html][$i], $this, $rec, $state , $c_part , $partkey );
            }
			
			$str	 = str_replace( array("&CODE001;","&CODE101;","&CODE000;","&CODE002;","&CODE005;","&CODE006;"), array(" ", " ", "/", "\\",'!--# ',' #--') , $str );
			
			return $str;
		}
		
		function getCCResult($rec, $command)
		{
			$command = str_replace( "\\\\", "&CODE002;", $command );//\\�Ƀ}�b�`
			$command = str_replace( "\/", "&CODE000;", $command );
			$command = str_replace( "\ ", "&CODE001;", $command );
            
		    $state = $this->getDefState( true );
			$str	 = trim(  GUIManager::commandComment( $command. " ", $this, $rec, $state , $c_part = null )  );
			
			$str	 = str_replace( array("&CODE001;","&CODE101;","&CODE000;","&CODE002;","&CODE005;","&CODE006;"), array(" ", " ", "/", "\\",'!--# ',' #--') , $str );

                                    $stack[ $j ]		 = str_replace( array($CC_HEAD,$CC_FOOT), array(), $stack[ $j ] );
			return $str;
		}
		
        //$gm���s�p�ӂɏ����������鎖�ɂ��V�X�e���S�̂Ɏx����������ʈׁA$gm�͎Q�Ƃœn���Ȃ��B
		function commandComment($buffer, $gm, $rec, &$state , &$current_part , $partkey = null)
		{
			if( $state['draw'] <= 0){
				$syntax_list = array( 'read', 'endif', 'else', 'ifbegin', 'case', 'switch', 'endswitch', 'default' ); //��'break'�͊܂܂Ȃ��B
				$syntax_f = false;
				foreach( $syntax_list as $syntax ){
					if( strpos( $buffer, '!--# '.$syntax) !== false ){ $syntax_f = true; }
				}
                if( !$syntax_f ){ return ""; }
            }
			$buffer		 = str_replace(  Array(  "#--)","#-->","(!--#","<!--#"), Array( "#--", "#--", "!--#", "!--#" ), $buffer );

			$ret			 = "";
		
			// �܂��A�R�}���h�R�����g��������Ȃ��ꍇ�͂��̂܂ܕԂ��B
			if(  strpos( $buffer, self::$CC_HEAD ) === false  )	{ return $buffer; }
			
			// �R�}���h�R�����g�̃w�b�_������̂ɁA�t�b�^��������Ȃ��ꍇ�͍\���G���[
			if(  strpos( $buffer, self::$CC_FOOT ) === false  )	{ exit(   "CommandComment Syntax Error [". htmlspecialchars(  trim( $buffer ) , ENT_COMPAT | ENT_HTML401 , 'SJIS'  ) ."]"   ); }

			// �\����͊J�n
			$stack		 = array();
			$zStack		 = array();
			$counter	 = 0;
			$z			 = 0;
			$zMax		 = 0;
            $head_length = strlen( self::$CC_HEAD );
            $foot_length = strlen( self::$CC_FOOT );

            
            $stack[ $counter ]="";
			// �R�}���h�R�����g���w�b�_��t�b�^�ŕ������A�K�w�\���ɂ���B
			for( $pointer=0; $pointer<strlen( $buffer )+1; $pointer++ )
			{
				if( $foot_length <= $pointer &&  substr(  $buffer, $pointer - $foot_length, $foot_length  ) == self::$CC_FOOT   )
				{
					$zStack[ $counter ]	 = $z;
					$counter++;
					$z--;
                    $stack[ $counter ]="";
				}
                
				if(   substr(  $buffer, $pointer, $head_length  ) == self::$CC_HEAD   )
				{
					$zStack[ $counter ]	 = $z;
					$counter++;
					$z++;
					if( $zMax < $z )	{ $zMax	 = $z; }
                    $stack[ $counter ]="";
				}
				$stack[ $counter ]	  .= substr( $buffer, $pointer, 1 );
			}
			$zStack[ $counter ]	 = $z;
            
            //$part_off = false;
			// �ł��K�w�̐[���Ƃ��납��R�}���h�R�����g�����s���Ă����B
			for( $i=$zMax; $i>=0; $i-- )
			{
				for( $j=0; $j<count($stack); $j++ )
				{
					if(  $zStack[ $j ] == $i  )
					{
						if( $stack[ $j ] !== "\0" && strlen( $stack[ $j ] ) > 0  )
						{
							if(   strpos(  $stack[ $j ], self::$CC_HEAD  ) !== false && strpos(  $stack[ $j ], self::$CC_FOOT  ) !== false   )
							{ $command	 = substr(  $stack[ $j ], strlen( self::$CC_HEAD ), strlen( $stack[$j] ) - strlen( self::$CC_HEAD ) - strlen( self::$CC_FOOT )  ); }
							else if(   strpos(  $stack[ $j ], self::$CC_HEAD  ) !== false   )	{ $command	 = substr(  $stack[ $j ], strlen( self::$CC_HEAD )  ); }
							else if(   strpos(  $stack[ $j ], self::$CC_FOOT  ) !== false   )	{ $command	 = substr(  $stack[ $j ], 0, strlen( $stack[$j] ) - strlen( self::$CC_FOOT )  ); }
							else														{ $command	 = $stack[ $j ]; }
							$cc		 = explode( " ", $command );
                            
							if( !$state['break'] ){
							switch( $cc[0] ){
                                case 'ifbegin':
	                                    if($state['no']){$state['draw']--;}//��\���Ȉ׊K�w��������
	                                    else if(!self::ifbegin( $gm, $rec, $cc )){//���ʂ��U�ȈׁA��\���ɁB
	                                    	$state['draw']--;
	                                    	$state['no']=true;
	                                    }
                                    break;
                                case 'elseif':
	                                	if($state['in']){break;}//���ɂ���if�O���[�v�͗L���Ȍo�H��\���ς�
	                                    if( $state['no'] && $state['draw'] === 0 ){//��\����Ԃł���A���݂̊K�w��elseif�ł���
	                                    	if(self::ifbegin( $gm, $rec, $cc )){
		                                    	if( ++$state['draw'] > 0 ){ $state['no']=false; }
                                    	}
	                                    }else if( ! $state['no'] ){ //�\���u���b�N���甲����
                                    	$state['draw']--;
	                                    	$state['no']=true;
                                    	$state['in']=true;
	                                    } //�K�w���قȂ�
                                	break;
                                case 'else':
	                                	if($state['in']){break;}//���ɂ���if�O���[�v�͗L���Ȍo�H��\���ς�
	                                    if( $state['no'] && $state['draw'] === 0 ){//��\����Ԃł���A���݂̊K�w��elseif�ł���
	                                    	if( ++$state['draw'] > 0 ){ $state['no']=false; }
	                                    }else if( ! $state['no'] ){ //�\���u���b�N���甲����
                                    	$state['draw']--;
	                                    	$state['no']=true;
                                    	$state['in']=true;
	                                    } //�K�w���قȂ�
	                                	break;
	                                case 'switch':
	                                    if($state['no']){$state['draw']--;}//��\���Ȉ׊K�w��������
	                                    else{//����ɓ���A��[��\��
	                                    	array_push( $state['switch'], $cc[1] );
	                                    	$state['draw']--;
	                                    	$state['no']=true;
	                                    }
	                                	break;
	                                case 'default':
	                                    if( $state['no'] && $state['draw'] === 0 ){
	                                    	//���ݔ�\���ł���switch�̊K�w�ł���A����break���łȂ�
	                                    	if( ++$state['draw'] > 0 ){ $state['no']=false; }
	                                    }
	                                case 'case':
	                                    if( $state['no'] && $state['draw'] === 0 ){
	                                    	//���ݔ�\���ł���switch�̊K�w�ł���A����break���łȂ�
	                                    	if( $cc[1] == $state['switch'][0] ){
	                                    		//�l����v����ו\��
		                                    	if( ++$state['draw'] > 0 ){ $state['no']=false; }
	                                    	}
                                    } 
                                    break;
                                case 'readhead':
	                                    if( $partkey != null && $partkey == $cc[1] ){//partkey�����ݒ�łȂ��A��v���Ă���ו\��
	                                    	if( ++$state['draw'] > 0 ){ $state['no']=false; }
	                                    }
	                                    else{ $state['draw']--; }//��\��
                                    $current_part = $cc[1];
                                    break;
                            }
							}
                            
                            if( $state['draw'] > 0 ){
								if(  strpos( $stack[ $j ], self::$CC_HEAD ) !== false  )	{
                                    $stack[ $j ]		 = ccProc::controller( $gm, $rec, $cc );
									$stack[ $j ]	 = str_replace( array("!CODE001;","!CODE000;",self::$CC_HEAD,self::$CC_FOOT), array(" ", "/", "!CODE005;","!CODE006;") , $stack[ $j ] );
                                }
							}
                            else{ $stack[ $j ] = "";}
							if( $zStack[ $j ] != 0 )							{ $zStack[ $j ]--; }
                            
                            switch( $cc[0] ){
                                case 'endif':
                                    if( $state['no'] && !$state['break'] ){	//��\���ł���Abreak���łȂ�
                                    	$state['draw']++;	//�K�w���グ��
                                    	if($state['draw']>0){	//�S�Ă̊K�w�𔲂���
                                    		$state['no']=false;
                                    		$state['in']=false;
                                    	}
                                    }
                                    break;
                                case 'endswitch':
                                    if($state['no']){	//��\���ł���
                                    	$state['draw']++;	//�K�w���グ��
                                    	if($state['draw']>0){	//�S�Ă̊K�w�𔲂���
                                    		$state['no']=false;
                                    	}
                                    }
                                    array_pop( $state['switch'] );
                                    $state['break']=false;
                                    break;
                                case 'break':
                                    if( !$state['no'] && $state['draw'] > 0 && !$state['break'] ){
                                    	//�\�����ł���switch�̊K�w�ł���A����break���łȂ�
                                    	$state['draw']--;
                                    	$state['no']=true;
                                    	$state['break']=true;
                                    }
                                    break;
                                case 'readend':
                                    if( $partkey != null && $partkey == $current_part ){
	                                    $state['no']=true;
                                    	$state['draw']--; /*$part_off = true;*/
                                    }
                                    else{
                                    	if( ++$state['draw'] > 0 ){ $state['no']=false; }
                                    }
                                    $current_part = null;
                                    break;
                            }
						}
					}
				}
                //1�X�^�b�N�ɂ܂Ƃ߂�
                $z	 = -1;
                for( $k=0; $k<count($stack); $k++ )
                {
                    if(   trim(  $stack[ $k ], "\n\r"  ) == "\0" )	{ continue; }
                        
                    if( $z == $zStack[ $k ] )
                    {
                        $stack[ $k - 1 ]		 .= $stack[ $k ];
                        for( $l=$k; $l<count($stack); $l++ )
                        {
                            $stack[ $l ]		 = isset($stack[ $l + 1 ]) ? $stack[ $l + 1 ] : '';
                            $zStack[ $l ]		 = isset($zStack[ $l + 1 ]) ? $zStack[ $l + 1 ]: '';
                        }
                        $stack[ count($stack) - 1 ]		 = "\0";
                        $zStack[ count($stack) - 1 ]	 = 0;
                        $k--;
                    }
                    $z	 = $zStack[ $k ];
                }
			}

			if( $state['draw'] > 0 ){ $ret	 = $stack[ 0 ];}
			
			return $ret;
		}
		
		function ccProc(&$gm, $rec, $cc)
		{
			return ccProc::controller($gm, $rec, $cc);
		}
        
        //alias���Ŏg��getTable�ŎQ�Ƃ���table�̃^�C�v���w�肷��B(n/d/a)
        function setTableType($type){
            $this->table_type = $type;
        }
        
        /**
         * �w��J������step��Ԃ�
         * 
         * @param $column �J������
         * @return step��
         * 
         */
        function getStep( $column ){
        	return $this->colStep[ $column ];
        }

        /**
         * �f�[�^���󂯎��p�����[�^�Ŏw�肳�ꂽ�u�����s�Ȃ�
         * @param $str �ϊ����s�Ȃ�������
         * @param $param �ϊ��p�����[�^
         * @return �ϊ���̕�����
         */
        static function replaceString( $str, $param ){
        	$before = Array('<','>','"',"'");
        	$after = Array('��','��','�h',"�f");
        	
        	switch($param){
        		default:
        			if( strlen($param) == 1 ){
	        			$before[] = $param;
    	    			$after[]  =  mb_convert_kana( $param, A, 'shift_jis' );
        			}
        		case '':
        			//html�^�O��F�߂Ȃ�
        			$tmp = str_replace( $before, $after, $str );
        			break;
        		case 'html':
        			$tmp = $str;
        			break;
        	}
        	return $tmp;
        }

        /**
         * 
         */
        static function getDefState( $draw ){
        	return Array( 
        		'draw'=>$draw?1:0	//1�ȏ�ŕ\���A0�ȉ��͔�\���A�}�C�i�X�̏ꍇ���K�w�ɐ����Ă���
        		,'no'=>!$draw		//����Ŕ�\���Ƃ���Ă��鎞��true
        		,'in'=>false		//ifbegin�̕���Ŋ��ɂ��̊K�w��if�ŗL���Ȍo�H��ʉ߂�������true�Aendif��false
        		,'break'=>false		//switch�̕���Ŋ��ɂ��̊K�w��break�����s���ꂽ����true�Aendswitch��false
        		,'switch'=>array()		//switch�̏�������Ɏg�p����l�B �l�X�g�ׂ̈�stack
        	);
        }
        
        static function ifbegin( $gm, $rec, $cc )
        {
			self::$CC_OR;
			self::$CC_AND;
			
			while( true ){
				if( ($and_key = array_search(self::$CC_AND,$cc) )  !== FALSE ){
					$and_cc = array_splice( $cc,0,$and_key );
					$cc[0] = 'ifbegin';
				}else{
					$and_cc = $cc;
				}
				
				$result = false;
				
				while( true ){
					if( ( $or_key = $key = array_search( self::$CC_OR,$and_cc ) ) !== FALSE ){
						$or_cc = array_splice( $and_cc,0,$or_key );
						$and_cc[0] = 'ifbegin';
					}else{
						$or_cc = $and_cc;
					}
					$result = ccProc::ifbegin( $gm, $rec, $or_cc );
					if( $result || $or_key === FALSE ){ break; }
				}
				
				if( !$result ){
					//���݂�and�u���b�N��1��true���Ȃ�����
					return false;
				}
				
				//�Ō��and�u���b�N�A�������͗B��̃u���b�N�������B
        		if( $and_key === FALSE ){
        			return true;
        		}
			}
        }
	}

	/*******************************************************************************************************/
	
	function uni2utf8($uniescape)
	{
		$c = "";
		
		$n = intval(substr($uniescape, -4), 16);
		if ($n < 0x7F)  { $c .= chr($n); }
		elseif ($n < 0x800) 
		{
			$c .= chr(0xC0 | ($n / 64));
			$c .= chr(0x80 | ($n % 64));
		} 
		else 
		{
			$c .= chr(0xE0 | (($n / 64) / 64));
			$c .= chr(0x80 | (($n / 64) % 64));
			$c .= chr(0x80 | ($n % 64));
		}
		return $c;
	}
	
	function escuni2sjis($escunistr)
	{
		$eucstr = "";
		
//		while(eregi("(.*)(%u[0-9A-F][0-9A-F][0-9A-F][0-9A-F])(.*)$", $escunistr, $fragment)) {
		while( preg_match('/(.*)(%u[0-9A-F][0-9A-F][0-9A-F][0-9A-F])(.*)$/i', $escunistr, $fragment) ){
			$eucstr = mb_convert_encoding(uni2utf8($fragment[2]).$fragment[3], 'SHIFT-JIS', 'UTF-8').$eucstr;
			$escunistr = $fragment[1];
		}
		return $fragment[1]. $eucstr;
	}

?>