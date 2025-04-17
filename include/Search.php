<?php

/*******************************************************************************************************
 * <PRE>
 *
 * Search�N���X
 *  �����t�H�[���̐����A
 *  �t�H�[������Post���ꂽ�f�[�^����GUIManager�̕ێ�����DB���猟�����ʂ�Ԃ��@�\���ۗL���܂��B
 * @author �O�H��q
 * @version 3.0.0
 *
 * </PRE>
 *******************************************************************************************************/

class Search
{
	var $addHiddenForm;
	var $gm;

	var $type;

	var $param = null;
	var $value = null;
	var $alias = null;
	var $sort = null;


	/**
	 * �R���X�g���N�^�B
	 * @param $gm GUIManager�I�u�W�F�N�g
	 */
	function __construct( &$gm = null, $type = null )	{ $this->gm = &$gm; $this->type = $type;}

	/**
	 * �����p�����[�^�\������͂��Ċe�p�����[�^�ϐ��ɃZ�b�g���܂��B
	 * @param $args �p�����[�^�\���̃Z�b�g���ꂽ�z��B���ۂɂ�$_GET���n����鎖�������B
	 */
	function setParamertorSet(&$args){
		global $TABLE_NAME;
		if(is_null($this->param)){ $this->param = Array(); }
		if(is_null($this->value)){ $this->value = Array(); }
		if(is_null($this->alias)){ $this->alias['alias'] = Array(); $this->alias['param'] = Array(); }
			
		// �����L�[���w�肳��Ă��Ȃ��ꍇ
		$db		 = $this->gm->getDB();
		$table	 = $db->getTable();

		for($i=0; $i<count($db->colName); $i++)
		{
			// �J�����̐������J��Ԃ�
			$column_name = $db->colName[$i];
			if(  isset( $args[ $column_name. '_PAL'] )  )
			{
				// �����p�����[�^���`�F�b�N
				if(   !is_array( $args[ $column_name. '_PAL']  )   )
				{
					// �l���z��̏ꍇ�̓G���[
					throw new Exception('Search param error -> '. $args[ $column_name ]. '_PAL[] is not array.');
				}
				for($j=0; $j<count( $args[$column_name. '_PAL'] ); $j++)
				{
					$this->param[$column_name] = explode( ' ', $args[$column_name. '_PAL'][$j] );
					
					if( $args[ $column_name ] == null || $args[ $column_name ] == "" ) { continue; }
						
					$this->value[$column_name] = $args[ $column_name ];
					if( $this->param[$column_name][1] == 'between' ){
						$this->value[$column_name] = Array( 'A' => $args[ $column_name.'A' ], 'B' => $args[ $column_name.'B' ]);
					}
				}
			}else if( isset( $args[ $column_name ] ) && !is_null(  $args[ $column_name ] ) ){
				// �������ɒl�����邩�ǂ���
				throw new Exception('Search param error -> '. $column_name. '_PAL[] is null.');
			}
		}
			
		//sort���Z�b�g
		if( isset( $args['sort'] ) )
		{
			if( !isset( $args['sort_PAL'] ) || $args['sort_PAL'] == 'asc' ){ $this->sort['param'] = 'asc'; }
			else{ $this->sort['param'] = 'desc'; }
			$this->sort['key'] =  $args['sort'];
		}else{
			$this->sort['key'] =  'SHADOW_ID';
			$this->sort['param'] = 'desc';
		}
		
		//alias���Z�b�g
		foreach( $TABLE_NAME as $tName )
		{// ��`����Ă���e�[�u�����m�F
			if( isset( $args[ $tName.'_alias' ] ) && isset( $args[ $tName. '_alias_PAL'] ) )
			{
				$this->setAlias( $tName, $args[ $tName.'_alias' ] );
				
				if(is_array($args[ $tName.'_alias_PAL' ])){
					foreach( $args[ $tName.'_alias_PAL' ] as $alias_pal ){
						$param			 = explode( ' ', $alias_pal );
						$this->setAliasParam($tName,$param);
						
						if( $args[ $param[0] ] == null || $args[ $param[0] ] == "" ) { continue; }
						
						$this->value[ $param[0] ] = $args[ $param[0] ];
						if( $param[3] == 'between'){
							$this->value[$param[0]] = Array( 'A' => $args[ $param[0].'A' ], 'B' => $args[ $param[0].'B' ] );
						}
					}
				}
			}
		}
	}

	/**
	 * �����p�����[�^���Z�b�g���܂��B �K��setValue�őΉ�����value���Z�b�g���Ă�������
	 * @param $var �Z�b�g����p�����[�^�B
	 */
	function setParamertor($column_name,$var){
		if(is_null($this->param)){
			$this->param = Array();
		}
		$this->param[$column_name] = $var;
	}

	/**
	 * �����f�[�^���Z�b�g���܂��B
	 * @param $var �Z�b�g����f�[�^
	 */
	function setValue($column_name,$var,$key=null){
		if(is_null($this->value)){
			$this->value = Array();
		}
		if(is_null($key))
			$this->value[$column_name] = $var;
		else
			$this->value[$column_name][$key] = $var;
	}

	/**
	 * alias�����x�[�X�p�����[�^���Z�b�g���܂��B
	 * @param $var �Z�b�g����f�[�^
	 */
	function setAlias($table_name,$var){
		if(is_null($this->alias['alias'][$table_name])){
			$this->alias['alias'][$table_name] = Array();
		}
		$this->alias['alias'][$table_name] = $var;
	}
	/**
	 * alias�����p�����[�^���Z�b�g���܂��B
	 * @param $var �Z�b�g����f�[�^
	 */
	function setAliasParam($table_name,$var){
		if(is_null($this->alias['param'][$table_name])){
			$this->alias['param'][$table_name] = Array();
		}
		$this->alias['param'][$table_name][]= $var;
	}

	/**
	 * �����f�[�^���Q�b�g���܂��B
	 * @param $var �Z�b�g����f�[�^
	 */
	function getValue( $column_name,$key=null ){
		if(is_null($this->value) && isset( $this->value[$column_name] ) ){
			return null;
		}
		if(is_null($key) && is_array($this->value[$column_name]))
			return $this->value[$column_name];
		else
			return $this->value[$column_name][$key];
		
	}

	/**
	 * �t�H�[����`�悵�܂��B
	 * @param $html �f�U�C��HTML�t�@�C��
	 * @param $jump=null submit�Ŕ�Ԑ�
	 * @param $partkey=null �����L�[
	 */
	function drawForm( $html, $jump = null, $partkey = null )
	{ print $this->getFormString( $html, $jump, $partkey ); }

	/**
	 * �t�H�[����`�悵�܂��B
	 * @param $html �f�U�C��HTML�t�@�C��
	 * @param $jump=null submit�Ŕ�Ԑ�
	 * @param $partkey=null �����L�[
	 */
	function getFormString( $html, $jump = null, $partkey = null, $form_flg = null )
	{
		if( !isset($form_flg) ) { $form_flg = $this->gm->form_flg; }
		switch($form_flg)
		{
			case 'variable':
			case 'v':
				return $this->getFormStringSetVariable( $html, $jump, $partkey );
				break;
			case 'buffer':
			case 'b':
			default:
				return $this->getFormStringSetBuffer( $html, null, $jump, $partkey );
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
		$buffer	 = "";
		$buffer	 .= '<form name="search_form" method="get" action="'. $jump .'" style="margin: 0px 0px;">';
		$this->gm->addHiddenForm('run', 'true');
		$this->gm->addForm	 .= $this->addHiddenForm;
		$this->addHiddenForm = "";
		$buffer	 .= $this->gm->getFormString($html, null, null, $partkey, 'buffer');
		$buffer	 .= '</form>';
			
		return $buffer;
	}

	/**
	 * �t�H�[����`�悵�܂��B
	 *
	 *  form�^�O����variable�ɃZ�b�g���ăe���v���[�g�ɓn���܂��B
	 *  header������form���g���Ă��āAgetFormString����form���l�X�g���Ă��܂����ɂ��g�����������B
	 *
	 * @param $html �f�U�C��HTML�t�@�C��
	 * @param $jump=null submit�Ŕ�Ԑ�
	 * @param $partkey=null �����L�[
	 */
	function getFormStringSetVariable( $html, $jump = null, $partkey = null )
	{
		$this->gm->addHiddenForm('run', 'true');
		$this->gm->addForm	 .= $this->addHiddenForm;
		$this->addHiddenForm = "";
		$this->gm->setVariable('form_begin','<form name="search_form" method="get" action="'. $jump .'" style="margin: 0px 0px;">'.$this->gm->addForm);
		$this->gm->setVariable('form_end','</form>');
		$this->gm->addForm = "";
		$buffer	 = $this->gm->getFormString($html, null, null, $partkey, 'buffer');

		return $buffer;
	}

	/**
	 * �s���t�H�[����ǉ����܂��B
	 * @param $name �t�H�[����
	 * @param $val �t�H�[���̒l
	 */
	function addHiddenForm($name, $val)
	{ $this->addHiddenForm .= '<INPUT type="hidden" name="'. $name .'" value="'. $val .'">'. "\n"; }

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
	 * �������ʂ��擾���܂��B
	 * �������ʂ̎擾�ɂ�class�̃v���p�e�B�ɃZ�b�g���ꂽ�l($param,$value)���g���܂��B
	 * �l���Z�b�g����Ă��Ȃ��ꍇ�A�A�N�Z�X���ɓn���ꂽGET���e��p���܂��B
	 * @param $gmkey=null _SKEY�\��������DB����������GUIManager�I�u�W�F�N�g
	 * @param $reckey=null $gmkey�ɓn���Ă���GUIManager����擾�������R�[�h
	 * @return �������ʂ̃e�[�u��
	 */
	function getResult( $args = null )
	{
		if( !is_null($args) ){ $this->setParamettorSet($args); }
		else if( is_null($this->param) ){ $this->setParamertorSet($_GET); }

		// �����L�[���w�肳��Ă��Ȃ��ꍇ
		$db		 = $this->gm->getDB();
		$table	 = $db->getTable();
		
		foreach( $this->param as $column_name => $param ){
			if( $this->value[ $column_name ] == null || $this->value[ $column_name ] == "" ) { continue; }
			$table = $this->searchTable( $db , $table, $column_name, $param , $this->value[ $column_name ] );
		}
		// alias���������s
		$table = $this->searchAlias( $db , $table );
		
		$table = $db->sortTable( $table, $this->sort['key'], $this->sort['param'] );
			
		return $table;
	}


	/**
	 * alias�������ʂ��擾���܂��B
	 *
	 *�p�����[�^�L�q��
	 *<input name="cUser_alias" type="hidden" value="owner id match or">
	 *<input name="cUser_alias_PAL[]" type="hidden" value="companyName name matchlike">
	 *<input name="cUser_alias_PAL[]" type="hidden" value="area area match like">
	 *<input name="cUser_alias_PAL[]" type="hidden" value="line line match like">
	 *
	 * @param $db �����Ώۂ�DB
	 * @param $table �����Ώۂ̃e�[�u��
	 * @return �������ʂ̃e�[�u��
	 */
	function searchAlias( $db, $table )
	{
		global $TABLE_NAME;
		global $gm;
		
		if( is_array($this->alias['alias']) && count($this->alias['alias']) ){
			foreach( $this->alias['alias'] as $tName => $alias )
			{// ��`����Ă���e�[�u�����m�F
				if( isset( $alias ) && isset( $this->alias['param'][$tName] ) )
				{
					$param			 = explode( ' ', $alias );
					$base_colum		 = array_shift($param);
					$alias_colum	 = array_shift($param);
					$tDb			 = $gm[$tName]->getDB();
					
					$fast_alias = true;

					foreach( $this->alias['param'][$tName] as $key => $param )
					{// alias�e�[�u���Ɍ����������Z�b�g
	
						$data_colum		 = array_shift($param);
						$search_colum	 = array_shift($param);
					
						if( $this->value[ $data_colum ] == null || $this->value[ $data_colum ] == "" ) { continue; }
						
						if( $fast_alias ){
							$table = $db->joinTable( $table, $this->type, $tName, $base_colum, $alias_colum );

							$fast_alias = false;
						}
						
						$data			 = $this->value[ $data_colum ];

						$table = $this->searchTable( $db, $table, $search_colum,  $param , $data , $tDb );
					}
				}
			}
			if( $this->sort['key'] === 'SHADOW_ID'){ $this->sort['key'] =  strtolower( $this->type ).'.SHADOW_ID'; }
		}
		return $table;

	}


	/**
	 * ���ۂɌ����������s�Ȃ��܂��B
	 *
	 * @param $db    �������s�Ȃ��e�[�u���̃f�[�^�x�[�X
	 * @param $table �������s�Ȃ��e�[�u��
	 * @param $name  �����Ώۂ̃J����
	 * @param $param �����Ɏg�p����p�����[�^�z��
	 * @param $data  �����Ɏg�p����f�[�^
	 * @return �������ʂ̃e�[�u��
	 */
	function searchTable( $db , $table , $name , $param , $data , $join_db = null ){
		
		switch( $param[0] )
		{
			case 'alias'://�Â��L�@  ���݂͔񐄏��B  �V�������Ɋւ��Ă�searchAlias���Q��
				//���ۂɌ����ŗ����f�[�^�ŕʃe�[�u�����������A���̌��ʂ̃J�������g���Č��X�������Ă�e�[�u���̔C�ӂ̃J����������
				//data:�u���������f�[�^
				//column_name:�����J����
				//param:
				//    0:
				//    1:alias����������v�����ɂ��邩like�����ɂ��邩
				//    2:�u���������e�[�u��
				//    3:�u���������̌����J����
				//    4:�u���������̃L�[�J����
				//    5:match�p�����[�^
				//�֘A�f�[�^�̕␳��Amatch�Ɠ�����
				//
				//�g��
				//    �������e�[�u���̕ʍ��ڂ������Ώۂɂ���ꍇ�͑����ċL�ڂ���B
				//    6:�u���������̌����J����
				//    7:�����Ɏg���L�[���
				//    8:��v������like������
				//(�ȏ�3���ڂ����[�v

				if( is_array( $data ) )
				{// �l���z��̏ꍇ�̓G���[
					throw new Exception('Search param error -> '. $name. '_PAL[] is alias. but '. $name .' is array.');
				}
		
				if( !isset( $this->aliasDB[ $param[2] ] ) )	{ $this->addAlias( $param[2] ); }

				if( $param[5] == 'between' ){
					// �G�C���A�X��between�͖�����(���̂Ƃ���
					throw new Exception('Search param error -> '. $name. '_PAL[] is alias+between. but between is alias.');
				}

				$tdb	 = $this->aliasDB[ $param[2] ];
				if( $param[1] == 'comp' ){
					$ttable = $tdb->searchTable( $tdb->getTable() , $param[3] , "==" , $data );
				}else{
					$ttable = $tdb->searchTable( $tdb->getTable() , $param[3] , "=" , '%'.$data.'%' );
				}

				if( count($param) > 6 ){
					$cnt = count($param);

					for( $k=6 ; $k < $cnt ; $k+=3 ){
						if(   is_array(  $this->value[$param[$k+1]]  )   ){
							// �l���z��̏ꍇ�̓G���[
							throw new Exception('Search param error -> '. $param[$k] . '_PAL[] is alias. but '. $param[$k] .' is array.');
						}
						if( $param[$k+2] == 'comp' )
						$ttable = $tdb->searchTable( $ttable , $param[$k] , "==" , $this->value[$param[$k+1]] );
						else
						$ttable = $tdb->searchTable( $ttable , $param[$k] , "=" , "%".$this->value[$param[$k+1]]."%" );

					}
				}

				$trow = $tdb->getRow( $ttable );

				if( $trow != 0 ){
					if( $param[5] == 'and' || $param[5] == 'or' ){
						//�z��
						$data = Array();
						for( $k=0;$k<$trow;$k++ ){
							$data[] = $tdb->getData( $tdb->getRecord( $ttable  , $k ) , $param[4] );
						}
					}else{
						//�P��f�[�^
						$data = $tdb->getData( $tdb->getRecord( $ttable  , 0 ) , $param[4] );
					}
				}else{
					//���ʖ���
					$table = $db->getEmptyTable();
					break;
				}
				$param[1] = $param[5];

			case 'match':
				// ��v�n�����̏ꍇ
				switch( $param[1] )
				{
					case 'comp':
						// ���S��v�̏ꍇ
						if(   is_array(  $data  )   )
						{// �l���z��̏ꍇ�̓G���[
							throw new Exception('Search param error -> '. $name. '_PAL[] is match+comp. but '. $name .' is array.');
						}
						$table = $this->searchExecute( $db, $join_db, $table, $name, '=', ($data));
						break;
					case 'like':
						// ������v�̏ꍇ
						if(   is_array(  $data  )   )
						{// �l���z��̏ꍇ�̓G���[
							throw new Exception('Search param error -> '. $name. '_PAL[] is match+like. but '. $name .' is array.');
						}
						$table = $this->searchExecute( $db, $join_db, $table, $name, '=', '%'. ($data). '%' );
						break;
					case 'keyword':
						// �L�[���[�h�����̏ꍇ
						$table = $this->searchKeyword( $db, $table, $name, $data, array($name), $join_db );
						break;

					case 'between':
						$table = $this->searchBetween( $db, $table, $name, $data[ 'A' ], $data[ 'B' ], $join_db );
						break;

					case 'or':
						if(isset($param[2])){ $table = $this->searchOR( $db , $table , $name, $data, $join_db, $param[2] ); }
						else				{ $table = $this->searchOR( $db , $table , $name, $data, $join_db); }
						break;
					case 'and':
						$table = $this->searchAND( $db , $table , $name, $data, $join_db);
						break;
					case 'in':
						$table = $this->searchIN( $db , $table , $name, $data, $join_db);
						break;
				}
				break;
					case 'group':
						// �O���[�s���O�����̏ꍇ
						switch( $param[1] )
						{
							default:
								$param[2] = $param[1];
							case 'file':
								$table = $this->searchFile( $db, $table, $name, $data, $param[2] );
								break;
							case 'table':
								//$param
								//    2:��ԏ�̐eTable�̖��O
								//    3:�e�e�[�u���̖��O
								//    4:�q�e�[�u���̖��O
								//    5-:�ȉ� 3~4�̌J��Ԃ�(�e����q�̏���
								$table = $this->searchMultipleTable( $db, $table, $name, $data, array_slice( $param , 2 ), $this->type, $join_db );
								break;
							case 'keyword':
								// �L�[���[�h�����̏ꍇ
								//�i$param�Ɍ����Ɏg���J�����𗅗񂵂ēn��
								$table = $this->searchKeyword( $db, $table, $name, $data, array_slice( $param , 2 ), $join_db );
								break;
						}
						break;
					case 'array':
						$table = $this->searchTable($db,$table,$name,array_slice($param,1),explode('/',$data),$join_db);
						break;
		}
		return $table;
	}


	/*******************************************************************************************************
	 *
	 * search extension
	 *
	 *  SQL::searchTable�ŏo���Ȃ��t�B���^����������ׂ̃��\�b�h�S
	 *  system�̃T�[�`�Ŏg�p���Ă�����̂Ɠ����@�\���ǂ��ł����p�o����B
	 *
	 *******************************************************************************************************/
	 
	/**
	 * �z��ɂ��OR�������s�Ȃ��܂��B
	 *
	 * @param $db    ��������e�[�u���̑�����f�[�^�x�[�X
	 * @param $table ��������e�[�u��
	 * @param $name  ��������J������
	 * @param $data  �����p�̔z��
	 * @return �������ʂ̃e�[�u��
	 */
	function searchOR( &$db , $table , $name, $data, $join_db = null , $param = null ){
		if( !is_array(  $data  ) ) { $data = array($data); }

		$blankTable = $db->getTable();
		$ttable	 = array();
		for($k=0; $k<count($data); $k++)	{
			if( !is_null($param) && $param == "comp" )	{	$str = ($data[$k]);	}
			else
			{
				switch( $db->colType[ $name ] )
				{
					case 'int' :
					case 'dobule' :
					case 'boolean' :
						$str = $data[$k];
						break;

					default :
						$str = "%".($data[$k])."%";
						break;
				}
			}
			
			$ttable[]	 = $this->searchExecute( $db, $join_db, $blankTable, $name, '=', $str );
		}
		$blankTable	 = $ttable[0];
		for($k=1; $k<count($data); $k++)	{ $blankTable		 = $db->orTable( $blankTable, $ttable[$k] ); }
		$table = $db->andTable( $table , $blankTable );

		return $table;
	}
	function searchIN( &$db , $table , $name, $data , $join_db = null ){
		if(   !is_array(  $data  )   )
		{
			// �l���z�񂶂�Ȃ��ꍇ�̓G���[
			throw new Exception('Search param error -> '. $name. '_PAL[] is match+or. but '. $name .' is not array.');
		}
		return $this->searchExecute( $db, $join_db, $table, $name, 'in', $data );
	}

	/**
	 * �z��ɂ��AND�������s�Ȃ��܂��B
	 *
	 * @param $db    ��������e�[�u���̑�����f�[�^�x�[�X
	 * @param $table ��������e�[�u��
	 * @param $name  ��������J������
	 * @param $data  �����p�̔z��
	 * @return �������ʂ̃e�[�u��
	 */
	function searchAND( &$db , $table , $name, $data , $join_db = null ){
		if( !is_array(  $data  ) ) { $data = array($data); }

		$blankTable = $db->getTable();

		$ttable	 = array();
		for($k=0; $k<count($data); $k++)	{ $ttable[]	 = $this->searchExecute( $db, $join_db, $blankTable, $name, '=', '%'.($data[$k]).'%' ); }

		$blankTable	 = $ttable[0];
		for($k=1; $k<count($data); $k++)	{ $blankTable		 = $db->andTable( $blankTable, $ttable[$k] ); }

		$table = $db->andTable( $table , $blankTable );

		return $table;
	}


	/**
	 * �͈͌������s�Ȃ��܂��B
	 *
	 * @param $db    ��������e�[�u���̑�����f�[�^�x�[�X
	 * @param $table ��������e�[�u��
	 * @param $name  ��������J������
	 * @param $dataA  �����p�̔z��
	 * @param $dataB  �����p�̔z��
	 * @return �������ʂ̃e�[�u��
	 */
	function searchBetween( &$db , $table , $name, $dataA, $dataB , $join_db = null ){
		if(   is_null(  $dataA  ) || is_null(  $dataB  )   )
		{// �l�����݂��Ȃ��ꍇ�̓G���[
			throw new Exception('Search param error -> '. $name. '_PAL[] is match+between. but '. $name .'A or '. $name .'B is null.');
		}

		if(strpos( $dataA ,'*') !== FALSE){ $tmpA = explode( '*', $dataA ); $dataA = $tmpA[1]; }
		if(strpos( $dataB ,'*') !== FALSE){ $tmpB = explode( '*', $dataB ); $dataB = $tmpB[2]; }

		if($dataA == "bottom" || $dataA == "" ){
			if($dataB == "top" || $dataB == "" ){
				//�������Ȃ��B
				return $table;
			}
			//�����̂ݖ���
			$table	 = $this->searchExecute( $db, $join_db, $table, $name, '<=', $dataB  );
			return $table;
		}else if($dataB == "top" ||  $dataB == "" ){
			//����̂ݖ���
			$table	 = $this->searchExecute( $db, $join_db, $table, $name, '>=', $dataA  );
			return $table;
		}

		switch( $this->gm->colType[ $name ] ){
			case 'int':
			case 'timestamp':
				$dataA	 = (int)$dataA;
				$dataB	 = (int)$dataB;
			case 'double':
				$table	 = $this->searchExecute( $db, $join_db, $table, $name, 'b', $dataA,$dataB  );
				break;
			default:
				throw new Exception('Search param error -> '. $name. '_PAL[] is between. but '. $name .' is not number.');
		}
		return $table;
	}

	/**
	 * �t�@�C���ɂ��O���[�s���O�������s�Ȃ��܂��B
	 * �݊����ׂ̈ɗp�ӂ��Ă��܂����A���܂萄������܂���Atable�𗘗p����`����������܂��B
	 *
	 * @param $db    ��������e�[�u���̑�����f�[�^�x�[�X
	 * @param $table ��������e�[�u��
	 * @param $name  ��������J������
	 * @param $data  �����p�̔z��
	 * @param $file  �O���[�v�����p�̃t�@�C����
	 * @return �������ʂ̃e�[�u��
	 */
	function searchFile( &$db , $table , $name , $data , $file ){
		if(  !file_exists( './group/'. $file )  )
		{
			// �O���[�s���O�t�@�C����������Ȃ��ꍇ
			throw new Exception( 'Search param error -> '. $name. '_PAL[] is group. but group file not found : ./group/'. $file );
		}
		if(   is_array(  $data  )   )
		{
			// �l���z��̏ꍇ�̓G���[
			throw new Exception('Search param error -> '. $name. '_PAL[] is group. but '. $name .' is array.');
		}
		 
		// �O���[�s���O�t�@�C�����J��
		$fp		 = fopen ( './group/'. $file, 'r' );
		$flg	 = false;
		while(  !feof( $fp ) )
		{
			$buffer	 = fgets( $fp, 20480 );
			$group	 = explode( ',', $buffer );
			if(  count( $group ) < 2  )	{ continue; }
			if(  $data == $group[0]  )
			{
				// �O���[�s���O�����Ώۂ̏ꍇ�B
				if( $group[1] == 'all' || $group[1] == 'ALL' )
				{
					// �O���[�s���O�p�����[�^�� ALL �̏ꍇ�͂��̂܂ܕԂ�
					$flg	 = true;
					break;
				}
				else
				{
					// �O���[�s���O����
					$ttable	 = array();
					for($k=1; $k<count( $group ); $k++)
					{
						if( $group[$k] == null || $group[$k] == '' ) {	continue; }
						$ttable[]	 = $db->searchTable( $table, $name, '=', '%'. $group[$k]. '%' );
					}
					$table	 = $ttable[0];
					for($k=1; $k<count($ttable); $k++)	{ $table		 = $db->orTable( $table, $ttable[$k] ); }
					$flg	 = true;
				}
			}
		}
		fclose( $fp );
		 
		if( !$flg )
		{
			// �ǂ̐e�ɂ���v���Ȃ������ꍇ�͕�����v����
			$table	 = $db->searchTable( $table, $name, '=', '%'.( $data). '%' );
		}
		return $table;
	}

	/**
	 * �O���[�v�������s�Ȃ��܂��B�B
	 *  $param
	 *    0:��ԏ�̐eTable�̖��O
	 *    1:�e�e�[�u���̖��O
	 *    2:�q�e�[�u���̖��O
	 *    3-:�ȉ� 1~2�̌J��Ԃ�(�e����q�̏���
	 *
	 * @param $db    ��������e�[�u���̑�����f�[�^�x�[�X
	 * @param $table ��������e�[�u��
	 * @param $name  ��������J������
	 * @param $param �����p�̃p�����[�^�z��
	 * @param $type  �����Ώۂ̃e�[�u����
	 * @return �������ʂ̃e�[�u��
	 */
	function searchMultipleTable( &$db , $table , $name , $data , $param, $type , $join_db = null ){
		if(   is_array(  $data  )   )
		{
			// �l���z��̏ꍇ�̓G���[
			throw new Exception('Search param error -> '. $name. '_PAL[] is match+comp. but '. $name .' is array.');
		}

		//�e���q���𔻒f
		if( strpos( $data , $ID_HEADER[ $type ] ) === 0 ){
			//�q�ł���ꍇ
			$table	 = $db->searchTable( $table, $name, '=', ($data) );
			return $table;;
		}else if(strpos( $data , $ID_HEADER[ $param[0] ] ) === 0){
			//�c��(�g�b�v�e)�ł���
			$start = true;
		}else{
			$start = false;
		}

		$atable = Array();
		$atable[] = $data;
		$trow = 1;
		$cnt = ( count($param) - 1 ) /2;
		//�c��ł͂Ȃ����q�ł��Ȃ��ꍇ
		for($k=0;$k<$cnt;$k++){
			$table_num = 1+$k*2;
			$key_num = 2+$k*2;

			if( !isset( $param[$table_num] ) || !strlen( $param[$table_num] ) )
			{
				// �e�[�u���������݂��Ȃ��ꍇ�G���[
				throw new Exception('Search param error -> '. $name. '_PAL[] is group. but table name not found.');
			}
			
			if( !isset( $this->aliasDB[ $param[$table_num] ] ) )	{ $this->addAlias( $param[$table_num] ); }

			//�J�n����܂�continue
			if( !$start ){
				if(strpos( $data , $ID_HEADER[ $param[$table_num] ] ) === 0 ){
					$start = true;

				}
				continue;
			}

			if( !isset( $param[$key_num] ) || !strlen( $param[$key_num] ) ){ $param[$key_num] = 'parent'; }

			$tdb	 = $this->aliasDB[ $param[$table_num] ];

			$atable2 = Array();
			for($l=0; $l<$trow; $l++){
				$ttable	 = $tdb->searchTable( $tdb->getTable(), $param[$key_num], '=', $atable[$l] );
				$trow2 = $tdb->getRow( $ttable );
				for($m=0; $m<$trow2; $m++){
					$atable2[] = $tdb->getData( $tdb->getRecord( $ttable , $m ) , 'id' );
				}
			}
			$atable = $atable2;
			$trow = $trow2;
		}

		$ttable = Array();
		for($k=0; $k<$trow; $k++){
			$ttable[]	 = $db->searchTable( $table, $name, '=', $atable[$k] );
		}

		$table	 = $ttable[0];
		for($k=1; $k<$trow; $k++)	{ $table		 = $db->orTable( $table, $ttable[$k] ); }
		return $table;
	}

	/**
	 * �L�[���[�h�ɂ�錟�����s�Ȃ��܂��B
	 *
	 * @param $db    ��������e�[�u���̑�����f�[�^�x�[�X
	 * @param $table ��������e�[�u��
	 * @param $data  ����������
	 * @param $param �����p�p�����[�^�z��A�����Ɏg���J�����𗅗񂷂�
	 * @return �������ʂ̃e�[�u��
	 */
	function searchKeyword( &$db , $table , $name , $data , $param , $join_db = null ){
		if(   is_array(  $data  )   )
		{
			// �l���z��̏ꍇ�̓G���[
			throw new Exception('Search param error -> '. $name. '_PAL[] is match+keyword. but '. $name .' is array.');
		}
		$data	 = str_replace( "�@", " ", ($data) );
		$key	 = explode(  ' ', $data  );
		$ttable	 = null;
		for( $k=0; $k<count($key); $k++ )
		{
			if(  substr( $key[$k], 0, 1 ) == '-'  ) {
				$keyword = '%'.  substr( $key[$k], 1 ). '%';
				$ttable[$k]	 =  $this->searchExecute( $db, $join_db, $table, $param[0],'!', $keyword );
				for( $l=1; $l < count($param) ; $l++ ){
					$ttable[$k]	 = $db->andTable( $db->searchTable( $table, $param[$l] , '!', $keyword ) , $ttable[$k] );
				}
			}
			else{
				$keyword = '%'. $key[$k]. '%';
				$ttable[$k]	 =  $this->searchExecute( $db, $join_db, $table, $param[0], '=', $keyword );
				for( $l=1; $l < count($param) ; $l++ ){
					$ttable[$k]	 = $db->orTable( $db->searchTable( $table, $param[$l] , '=', $keyword ) , $ttable[$k] );
				}
			}
		}
		$table	 = $ttable[0];
		for( $k=1; $k<count($ttable); $k++ )		{ $table	 = $db->andTable( $table, $ttable[$k] ); }
		return $table;
	}

	function paramReset(){ $this->param = Array(); $this->value = Array(); $this->alias['alias'] = Array(); $this->alias['param'] = Array(); $this->sort['key'] =  'SHADOW_ID'; $this->sort['param'] = 'desc'; }
	
	private function searchExecute(  SQLDatabase &$db,$join_db,Table &$tbl, $name, $opp, $val, $val2 = null ){
		
		if($join_db==null){
			return $db->searchTable( $tbl, $name, $opp, $val, $val2 );
		}else{
			return $db->joinTableSearch( $join_db, $tbl, $name, $opp, $val, $val2 );
		}
		return $tbl;
	}
}

/*******************************************************************************************************/

?>