<?php

include_once "./include/base/Database.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQL�f�[�^�x�[�X�V�X�e�� �x�[�X�N���X
 *
 * @author �g���K��Y
 * @original �O�H��q
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

class SQLDatabaseBase implements DatabaseBase
{

	var $connect;
	var $dbName;
	var $tableName;
	var $colName;
	var $colType;
	var $colSize;
	var $_DEBUG	 = false;
	var $row = -1;
	var $rec_cash = null;
	var $sql_char_code;
	var $prefix;

	/**
	 * �R���X�g���N�^�B
	 * @param $dbName DB��
	 * @param $tableName �e�[�u����
	 * @param $colName �J���������������z��
	 */
	function __construct($dbName, $tableName, $colName, $colType, $colSize ){
	}

	/**
	 * ���R�[�h���擾���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @param $index �擾���郌�R�[�h�ԍ�
	 * @return ���R�[�h�f�[�^
	 */
	function getRecord($table, $index){
		if( ! is_null( $this->rec_cash )){
			if( $this->_DEBUG )				{ d( "getRecord() : ".$this->tableName." load cash<br/>\n", 'sql'); }
			$result = $this->rec_cash;
		}else{

			if( $this->_DEBUG ){ d( "getRecord() : ". $table->getString(). "<br/>\n", 'sql' ); }

			$result	 = $this->sql_query( $table->getString() );
			$this->rec_cash = $result;
		}

		if( !$result ){
			throw new Exception("getRecord() : SQL MESSAGE ERROR. \n");
		}
		
		if($this->getRow($table) != 0){
			$rec = $this->sql_fetch_assoc( $result, $index);
		}
		return $rec;
	}

	/**
	 * ���R�[�h���擾���܂��B
	 *
	 * @param $id �擾���郌�R�[�hID
	 * @param $type ����ΏۂƂȂ�e�[�u����type(nomal/delete/all)
	 * @return ���R�[�h�f�[�^�B���R�[�h�f�[�^�����݂��Ȃ��ꍇnull��Ԃ��B
	 */
	function selectRecord( $id , $type = null)
	{
		if( is_null($id) ){ return null;}

		$table	 = $this->getTable($type);
		$table	 = $this->searchTable( $table, 'id', '=', $id );
		if( $this->getRow($table) > 0 )
		{// ���R�[�h�����݂���ꍇ
			$rec	 = $this->getRecord( $table, 0 );
			return $rec;
		}
		else	{ return null; }
	}

	/**
	 * �e�[�u������w�肵�����R�[�h���폜���܂��B
	 * @param table �e�[�u���f�[�^
	 * @param rec �폜�ΏۂƂȂ郌�R�[�h
	 * @return �e�[�u���f�[�^
	 */
	function pullRecord($table, $rec){
		return $this->searchTable( $table, 'SHADOW_ID', '!', $this->getData( $rec, 'SHADOW_ID' ) );
	}

	/**
	 * �f�[�^�̓��e���擾����B
	 * @param $rec ���R�[�h�f�[�^
	 * @param $name �J������
	 * @return �l
	 */
	function getData($rec, $name, $br = false){
		$name	 = strtolower( $name );
		if(  isset( $rec[ $name ] )  ){
			if( $this->colType[ $name ] == 'boolean' ){
				return $this->to_boolean( $rec[ $name ] );
			}else if(  is_string( $rec[ $name ] )  ){
				/*$rec[$name]	 = str_replace( '\\\\', '&CODE002;', $rec[$name] );
				//					$rec[$name]	 = stripslashes( $rec[$name] );
				$rec[$name]	 = str_replace( '&CODE002;', '\\', $rec[$name] );*/
				$rec[$name]	 = str_replace( '\\\\', '\\', $rec[$name] );
					
				if( !$br ){
					return str_replace(  "\r\n", "\n", $this->sql_convert( $rec[ $name ] )  );
				}else{
					return brChange( $this->sql_convert( $rec[ $name ] ) );
				}
			}
			return $rec[ $name ];
		}else{
			return null;
		}
	}
	
	function getDataList($table, $name ){
		if( ! is_null( $this->rec_cash )){
			if( $this->_DEBUG )				{ d( "getDataList() : ".$this->tableName." load cash<br/>\n", 'sql' ); }
			$result = $this->rec_cash;
		}else{

			if( $this->_DEBUG ){ d( "getDataList() : ". $table->getString(). "<br/>\n", 'sql' ); }

			$result	 = $this->sql_query( $table->getString() );
			$this->rec_cash = $result;
		}

		if( !$result ){
			throw new Exception("getDataList() : SQL MESSAGE ERROR. \n");
		}
		
		$list = null;
		if($this->getRow($table) != 0){
			$recs = $this->sql_fetch_all( $result );
			$list = Array();
			foreach( $recs as $row ){
				$list[] = $row[$name];
			}
		}
		
		return $list;
	}

	/**
	 * ���R�[�h�̓��e���X�V����B
	 * DB�t�@�C���ւ̍X�V���܂݂܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function updateRecord($rec){
		$sql	 = "UPDATE ". strtolower($this->tableName). " SET ";
		$sql	 .= $this->toString( $rec, "UPDATE" );
		$sql	 .= " WHERE SHADOW_ID = ". $this->getData( $rec, 'SHADOW_ID' );
		if( $this->_DEBUG ){ d( "updateRecord() : ". $sql. "<br/>\n", 'sql' ); }

		$this->sql_query( $sql );
			
		if($this->updateLog == true){
			$str = $rec['SHADOW_ID']. ",".$rec['DELETE_KEY']. ",";
			$cnt = count($this->colName);
			for($i=0; $i<$cnt; $i++){
				$str .= $rec[ $this->colName[$i] ]. ",";
			}
			$this->log->write('UPDATE,'. $this->dbInfo. ",". $str);
		}
		$this->cashReset();
	}

	/**
	 * ���R�[�h�̍폜�B
	 * DB�t�@�C���ւ̔��f���s���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function deleteRecord(&$rec){
		$rec	 = $this->setData( $rec, 'delete_key', true );
		$this->updateRecord( $rec );
			
		if($this->delLog == true){

			$str = "";
			for($i=0; $i<count($this->colName) + 2; $i++){
				switch($i){
					case 0:
						$str .= $rec['SHADOW_ID']. ",";
						break;
					case 1:
						$str .= $rec['DELETE_KEY']. ",";
						break;
					default:
						$str .= $rec[ $this->colName[$i - 2] ]. ",";
				}
			}

			$this->log->write('DELETE,'. $this->dbInfo. ",". $str);
		}
		$this->cashReset();
		
		return $rec;
	}

	/**
	 * where�ɂ���đI�������e�[�u���̍s���폜���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @return �s��
	 */
	function deleteTable($table){
		//delete�̂��߂�Update��
		$sql = "UPDATE ";
		$sql	 = "UPDATE ". strtolower($this->tableName). " SET delete_key = ".$this->sqlDataEscape(true,'boolean')." ";
		$sql	 .= $table->getWhere();
		if( $this->_DEBUG )				{ d( "deleteTable() : ". $sql. "<br/>\n", 'sql' ); }
		$result	 = $this->sql_query( $sql );

		if( !$result ){
			throw new Exception("deleteTable() : SQL MESSAGE ERROR. \n");
		}
		$this->cashReset();
		return;
	}

	/**
	 * �f�[�^���Z�b�g����B
	 * @param $rec ���R�[�h�f�[�^
	 * @param $name �J������
	 * @param $val �l
	 */
	function setData(&$rec, $name, $val){
		$name			 = strtolower( $name );

		$str_types = Array( 'string','varchar','password','char' );

		if( in_array( $this->colType[ $name ], $str_types ) ){
			$val = GUIManager::replaceString( $val, '' );
		}

		if(  is_bool( $val )  ){
			if( $val ){
				$val	 = 'TRUE';
			}else{
				$val	 = 'FALSE';
			}
		}
			
		$rec[ $name ]	 = $this->sql_convert($val);
			
		return $rec;
	}

	/**
	 * �ȈՂȉ��Z���s�Ȃ��A���ʂ��Z�b�g����B
	 * �J���������l�^�Ŗ����ꍇ�͖���
	 *
	 * @param $rec ���R�[�h�f�[�^
	 * @param $name �J������
	 * @param $opp ���Z�q
	 * @param $name �l
	 */
	function setCalc(&$rec, $name , $opp , $val )
	{
		switch($this->colType[$name]){
			case 'string':
			case 'varchar' :
			case 'char' :
			case 'image':
			case 'boolean':
				return null;
				break;
			default:
				$old = $this->getData( $rec , $name );
				eval('$new = ' .$old.$opp.$val.";");
				$this->setData( $rec , $name , $new );
				break;
		}
		return $rec;
	}

	/**
	 * �����Ƃ��ēn���ꂽtable�̑S�s�Ƀf�[�^���Z�b�g����update����B
	 *
	 * @param $table �X�V���s�Ȃ��J�����̓�����table
	 * @param $name �J������
	 * @param $val �l
	 */
	function setTableDataUpdate(&$table, $name, $val)
	{
		$row = $this->getRow($table);
		if(!$row){
			return $table;
		}

		$sql	 = "UPDATE ". $this->tableName. " SET ";
		$sql	 .= $name ."=" .$this->sqlDataEscape($val,$this->colType[$name]);
		$sql	 .= $table->getWhere();
		if( $this->_DEBUG ){ d( "updateRecord() : ". $sql. "<br/>\n", 'sql'); }

		$this->sql_query( $sql );
			
		if($this->updateLog == true){
			$str = $table->getWhere(). ",".$name."=".$val. ",".$row;
			$this->log->write('TABLE_UPDATE,'. $this->dbInfo. ",". $str);
		}
		$this->cashReset();

		return $table;
	}

	/**
	 * ���R�[�h�Ƀf�[�^���܂Ƃ߂ăZ�b�g���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 * @param data �f�[�^�A�z�z��i�Y�����̓J�������j
	 */
	function setRecord(&$rec, $data){
		$tmp	 = $this->getData( $rec, 'SHADOW_ID' );
		$rec	 = $this->getNewRecord( $data );
		$this->setData( $rec, 'SHADOW_ID', $tmp );
		return $rec;
	}

	/**
	 * �V�������R�[�h���擾���܂��B
	 * �f�t�H���g�l���w�肵�����ꍇ��
	 * $data['�J������']�̘A�z�z��ŏ����l���w�肵�Ă��������B
	 * @param data �����l��`�A�z�z��
	 * @return ���R�[�h�f�[�^
	 */
	function getNewRecord($data = null){
			
		$rec = array();
			
		// ���R�[�h�̒��g�� null �ŏ�����
		for($i=0; $i<count( $this->colName ); $i++){
			$rec[ $this->colName[$i] ] = null;
		}
			
		// �����l���w�肳��Ă��Ȃ���� return
		if(  !isset( $data )  )
		return $rec;
			
		// �����l����
		for($i=0; $i<count( $this->colName ); $i++){
			if( $this->colType[ $this->colName[$i] ] == 'image' ||  $this->colType[ $this->colName[$i] ] == 'file' ){
//			if(  $_FILES[ $this->colName[$i] ]['name'] != "" || $_POST[ $this->colName[$i] . '_filetmp' ] != "" ){
				// �f�[�^�t�@�C���̏ꍇ
				$this->setFile( $rec, $this->colName[$i] );
			}else{
				if(    isset(  $data[ $this->colName[$i]  ]   ) && $data[ $this->colName[$i]  ] != null    ){
					if(    is_array(  $data[ $this->colName[$i]  ]   )    ){
						$str = '';
						for($j=0; $j<count(  $data[ $this->colName[$i] ]  ); $j++){
							$str .= $data[ $this->colName[$i] ][$j];
							if(   $j != count(  $data[  $this->colName[$i] ]  ) - 1   )
							$str .= '/';
						}
						$this->setData( $rec, $this->colName[$i], $str );
					}else{
						if(  is_bool( $data[ $this->colName[$i] ] )  ){
							$this->setData( $rec, $this->colName[$i], $data[ $this->colName[$i] ] );
						}else if( strtolower( $data[ $this->colName[$i] ] ) == 'true' ){
							$this->setData( $rec, $this->colName[$i], true );
						}else if( strtolower( $data[ $this->colName[$i] ] ) == 'false' ){
							$this->setData( $rec, $this->colName[$i], false );
						}else{
							$this->setData(   $rec, $this->colName[$i], $data[ $this->colName[$i] ]   );
						}
					}
				}
			}
		}
		// �Öق̎�L�[���`
		$rec[ 'shadow_id' ]	 = $this->getMaxID()+1;
		return $rec;
	}

	function setFile(&$rec, $colname){
		$sys	 = SystemUtil::getSystem( $_GET["type"] );
		$sys->doFileUpload( $this, $rec, $colname, $_FILES );
	}

	/**
	 * ���R�[�h�̒ǉ��B
	 * DB�ւ̔��f�������ɍs���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function addRecord($rec){
		// SQL���̐������J�n
		$sql	 = "INSERT INTO ". strtolower($this->tableName). " (";
			
		// �J���������X�g���o��
		for($i=0; $i<count( $this->colName ); $i++){
				
			$sql	 .= $this->colName[$i];
				
			if( $i != count( $this->colName ) - 1 ){
				$sql	 .= ", \n";
			}
		}
			
		// �d���������ׂɈÖق̎�L�[���Đݒ�
		$rec[ 'shadow_id' ]	 = $this->getMaxID()+1;

		$sql	 .= ")VALUES ( ";
		$sql	 .= $this->toString( $rec, "INSERT" );
		$sql	 .= " )";
		if( $this->_DEBUG ){ d( "addRecord() : ". $sql. "<br/>\n", 'sql' ); }

		$this->sql_query( $sql );

		if($this->addLog == true){
			$str = "";
			for($i=0; $i<count($this->colName) + 2; $i++){
				switch($i){
					case 0:
						$str .= $rec['SHADOW_ID']. ",";
						break;
					case 1:
						$str .= $rec['DELETE_KEY']. ",";
						break;
					default:
						$str .= $rec[ $this->colName[$i - 2] ]. ",";
				}
			}

			$this->log->write('ADD,'. $this->dbInfo. ",". $str);
		}
			
		$this->cashReset();
	}

	/**
	 * DB�����e�[�u�����擾���܂��B
	 * @return �e�[�u���f�[�^
	 * @param $type table type(nomal/delete/all)
	 */
	function getTable($type = null){
		$table_name = strtolower($this->tableName);
		switch($type){
			default:
			case 'n':
			case 'nomal':
				$table	 = new Table($table_name );
				break;
			case 'd':
			case 'delete':
				$table	 = new Table($table_name );
				$table->delete	 = '( delete_key = '.$this->sqlDataEscape(true,'boolean').' )';
				break;
			case 'a':
			case 'all':
				$table	 = new Table($table_name );
				$table->delete	 = '';
				break;
		}
		return $table;
	}

	/**
	 * �e�[�u���̍s�����擾���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @return �s��
	 */
	function getRow($table){

		if($this->row >= 0){
			if( $this->_DEBUG ){ d( "getRow() : load cash<br/>\n", 'sql'); }
			return $this->row;
		}
		
		if( $this->_DEBUG ){ d( "getRow() : ". $table->getString(). "<br/>\n", 'sql'); }

		$result	 = $this->sql_query( $table->getString() );

		if( !$result ){
			if( !$this->_DEBUG ){ d( "getRow() : ". $table->getString(). "<br/>\n", 'sql'); }
			throw new Exception("getRow() : SQL MESSAGE ERROR. \n");
		}
			
		$row	 =  $this->sql_num_rows($result);
		if( $row == -1 ){
			throw new Exception("getRow() : GET ROW ERROR ( RETURN -1 ).\n");
		}
		$this->row = $row;
		return $row;
	}

	/**
	 * �e�[�u���̌������s���܂��B
	 * ���p�ł��鉉�Z�q�͈ȉ��̂��̂ł��B
	 * >, <	 �s�������Z�q
	 * =	 �������Z�q
	 * !	 �񓙍����Z�q
	 * in    in���Z�q
	 * &     bit���Z�q
	 * b	 �r�g�D�C�[�����Z�q
	 * �r�g�D�C�[�����Z�q�̏ꍇ�̂�$val2���w�肵�܂��B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $opp ���Z�q
	 * @param $val �l�P
	 * @param $val2 �l�Q
	 */
	function searchTable(&$tbl, $name, $opp, $val, $val2 = null){
		if( $tbl->join ){
			//join����Ă��鎞��join�p�̌����֐����g��
			return $this->joinTableSearch( $this, $tbl , $name, $opp, $val, $val2 );
		}

		$table	 = $tbl->getClone();
		//�����p�����[�^��null�̏ꍇ�A���������̒ǉ����s��Ȃ�
		if( is_null($val) )
			return $table;
		
		if( is_array( $val ) && !count($val) ){ return $table; }

		$table->addWhere( $this->searchTableCore($name, $opp, $val, $val2 ) );
			
		$this->cashReset();
		return $table;
	}
	function searchTableSubQuery(&$tbl, $name, $opp, &$subTable){

		$table	 = $tbl->getClone();
		
		$table->addWhere( '( '.$name.' '.$opp.' ('.$subTable->getString().') )' );
			
		$this->cashReset();
		return $table;
	}
	
	/*
	 * searchTable�Ŏg�p����\���������s�Ȃ��B
	 * join���table�O��table�Ȃǂ�����ė��p����ׂ̕���
	 * 
	 * �����I�ɂ����Ă΂��Ȃ���private
	 */
	function searchTableCore(&$name, &$opp, &$val, &$val2 = null ){
		if( is_array( $val ) ){
			//array_map�Ɠ����֐��̑�֏���
			$val_buf = Array();
			foreach( $val as $v ){
				$val_buf[] = $this->sqlDataEscape($v,$this->colType[$name]);
			}
			$val = '('.join(',',$val_buf).')';
		}else{
			$val = $this->sqlDataEscape($val,$this->colType[$name]);
		}
			
		if( isset($val2) ){
			if( $opp == 'b' ){
				if( $val > $val2 ){ $tmp = $val; $val = $val2; $val2 = $tmp; }
				if( is_string($val2) ){ $val2	 = "'". $val2. "'"; }
				$str = " ". $name. " BETWEEN ". $val. " AND ". $val2;
			}else{//val2���t���O�����̃`�F�b�N
				$str = " ". $name. " ".$opp." ". $val. " ". $val2." ".$val;
				//sample
				//$name:id
				//$opp:&
				//$val:0x00000001
				//$val2:=
				// ->where : id & 0x00000001 = 0x00000001
			}
		}else{
			if( $opp == '==' || strpos( $val, '%' ) === false ){
				if($opp == '=='){
					$opp = "=";
				}
				if( $opp == '!' ){
					$str  = " ". $name. " <> ". $val;
				}else if($opp == 'isnull'){
					$str  = " ( ". $name. " is null OR ". $name. " = '' ) ";
				}else{
					$str  = " ". $name. " ". $opp ." ". $val;
				}
			}else{
				if( $opp == '!' ){
					$str  = " ". $name. " not like ". $val;
				}else{
					$str  = " ". $name. " like ". $val;
				}
			}
		}
		return $str;
	}
	

	/**
	 * ��̃e�[�u����Ԃ��B
	 * search�̌��ʂ���ɂ����肷�鎞�Ɏg�p�B
	 */
	/**
	 * @return unknown_type
	 */
	function getEmptyTable(){
		$table	 = new Table(strtolower($this->tableName) );
		$table->delete = "(1=0)";
		return $table;
	}

	/**
	 * ���R�[�h���\�[�g���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $asc �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 * @param $reset sort������ǉ��ɂ��邩�ǂ����̃t���O�B  �f�t�H���g�l��false 
	 */
	function sortTable(&$tbl, $name, $asc, $add = false){
		$table	 = $tbl->getClone();
		
		if(is_null($add) || ! $add ){
			$table->order = Array();
		}
		
		if( $asc == 'asc' )
			$table->order[ $name ] = 'ASC';
		else
			$table->order[ $name ] = 'DESC';

		return $table;
	}
	function sortReset(&$tbl){
		$table	 = $tbl->getClone();
		$table->order = Array();
		return $table;
	}
	
	/**
	 * �e�[�u���̘_���a�B
	 * @param $table1 �e�[�u���f�[�^
	 * @param $table2 �e�[�u���f�[�^
	 * @return �e�[�u���f�[�^
	 */
	function orTable(&$tbl, &$table2){
		$table1	 = $tbl->getClone();
		if( $table1->where == null ){
			if( $table2->where == null ){
				// �ǂ���̃e�[�u�����i�荞�ݏ����������ꍇ
				return $this->getTable();
			}else{
				// table1 �ɍi�荞�ݏ����������ꍇ
				return $table2;
			}
		}else{
			if( $table2->where == null ){
				// table2 �ɍi�荞�ݏ����������ꍇ
				return $table1;
			}else{
				$table1->addWhere( $table2->where , 'or' );
				$this->cashReset();
			}
			return $table1;
		}
	}
	/**
	 * �e�[�u���̘_���a�B(�z��Ή���
	 * @param $a �e�[�u���f�[�^�̓������z��
	 * @return �e�[�u���f�[�^
	 *
	 * func_get_args�ł͎Q�Ƃ��󂯂�Ȃ��הz��ɂ�
	 */
	function orTableM($a){
		$list = array();
		for ($i = 0; $i < count($a); $i++) {
			if( $a[$i]->where != null ){
				$list[] = $i;
			}
		}
		switch( count($list) ){
			case 0:
				return $this->getTable();
			case 1:
				return $a[$list[0]];
			default:
				$table	 = $a[$list[0]]->getClone();
				for($i=1;$i<count($list);$i++){
					$table->addWhere( $a[$list[$i]]->where , 'or' );
				}
				return $table;
		}
	}

	/**
	 * �e�[�u���̘_���ρB
	 * @param $table1 �e�[�u���f�[�^
	 * @param $table2 �e�[�u���f�[�^
	 * @return �e�[�u���f�[�^
	 */
	function andTable(&$tbl, &$table2){
		$table1	 = $tbl->getClone();
		if( $table1->where == null ){
			if( $table2->where == null ){
				// �ǂ���̃e�[�u�����i�荞�ݏ����������ꍇ
				return $this->getTable();
			}else{
				// table1 �ɍi�荞�ݏ����������ꍇ
				return $table2;
			}
		}else{
			if( $table2->where == null ){
				// table2 �ɍi�荞�ݏ����������ꍇ
				return $table1;
			}else{
				$table1->addWhere( $table2->where , 'and' );
				$this->cashReset();
			}
			return $table1;
		}
	}

	/**
	 * ���j�I���e�[�u�����쐬���܂��B
	 * �\�[�g��������rTable�̂��̂��g�p�B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $asc �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 */
	function unionTable(&$lTable, &$rTable, $colum = null)
	{
		$sql = " UNION ALL ";
		$tmp = $rTable->getClone();
		$sql .= $tmp->getUnionString($colum);
			
		$table	 = new Table( $lTable->from . $sql );
		$table->select	 =  "SHADOW_ID,".$colum ;
		$table->delete	 = null;
		$table->order	 = $tmp->order;
		$table->offset	 = $tmp->offset;
		$table->limit	 = $tmp->limit;
			
		return $table;
	}

	/**
	 * �e�[�u���̌���
	 * �\�[�g��������rTable�̂��̂��g�p�B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $asc �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 */
	function joinTable( &$tbl, $b_name, $n_name, $b_col, $n_col ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->prefix.$b_name);
		$n_name = strtolower($this->prefix.$n_name);

		if( $table->join ){
			$table->from .= ", ".$n_name;
			$table->delete	 .= ' AND ( '.$n_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$n_name.'.delete_key IS NULL )';
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".$n_name;
			$table->delete	 = '( '.$b_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$b_name.'.delete_key IS NULL ) AND ( '.$n_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$n_name.'.delete_key IS NULL )';
			
			$table->changeJoinTable( $b_name );
		}

		$table->addWhere( $b_name.".".$b_col." = ".$n_name.".".$n_col." " );
		
		$table->join = true;

		return $table;
	}
	//�e�[�u���̌���(����������sql���œn��
	function joinTableSQL( &$tbl, $b_name, $n_name, $join_sql, $n_tbl_name = null ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->prefix.$b_name);
		$n_name = strtolower($this->prefix.$n_name);
		
		if( !is_null($n_tbl_name) )	{ $n_name = $n_name.' '.$n_tbl_name; }
		else						{ $n_tbl_name = $n_name; }

		if( $table->join ){
			$table->from .= ", ".$n_name;
			$table->delete	 .= ' AND ( '.$n_tbl_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$n_tbl_name.'.delete_key IS NULL )';
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".$n_name;
			$table->delete	 = '( '.$b_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$b_name.'.delete_key IS NULL ) AND ( '.$n_tbl_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$n_tbl_name.'.delete_key IS NULL )';
			
			$table->changeJoinTable( $b_name );
		}

		$table->addWhere( '('. $join_sql .')' );
		
		$table->join = true;

		return $table;
	}
	function joinTableSubQuery( &$tbl, &$sub_tbl, $n_name, $b_col, $n_col ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->prefix.$this->tableName);

		if( $table->join ){
			$table->from .= ", ".'('.$sub_tbl->getString().') '.$n_name;
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".'('.$sub_tbl->getString().') '.$n_name;
			
			$table->changeJoinTable( $b_name );
		}
		
		$table->addWhere( $b_name.".".$b_col." = ".$n_name.".".$n_col." " );
		
		$table->join = true;

		return $table;
	}
	function joinTableSubQuerySQL( &$tbl, &$sub_tbl, $n_name, $join_sql ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->prefix.$this->tableName);

		if( $table->join ){
			$table->from .= ", ".'('.$sub_tbl->getString().') '.$n_name;
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".'('.$sub_tbl->getString().') '.$n_name;
			
			$table->changeJoinTable( $b_name );
		}
		
		$table->addWhere( $join_sql );
		
		$table->join = true;

		return $table;
	}
	function joinTableSearch( &$db ,&$tbl ,$name, $opp, $val, $val2 = null, $tbl_name = null ){

		$table	 = $tbl->getClone();
		
		//�����p�����[�^��null�̏ꍇ�A���������̒ǉ����s��Ȃ�
		if( is_null($val) ){ return $table; }
		if( is_array( $val ) && !count($val) ){ return $table; }

		$sql = $db->searchTableCore($name, $opp, $val, $val2 );
		
		if( is_null($tbl_name) )
			$tbl_name = $db->tableName;
			
		$sql = str_replace($name,$tbl_name.'.'.$name,$sql);
		
		$table->addWhere( $sql );
		
		$this->cashReset();
		return $table;
	}

	/**
	 * �e�[�u���� $start �s�ڂ��� $num ���o���B
	 * @param table �e�[�u���f�[�^
	 * @param start �I�t�Z�b�g
	 * @param num ��
	 */
	function limitOffset( $table, $start, $num ){
		$ttable			 = $table->getClone();
		$ttable->offset	 = $start;
		$ttable->limit	 = $num;
			
		$this->cashReset();
		return $ttable;
	}

	/**
	 * �Ö�ID�̍ő�l��Ԃ�
	 */
	function getMaxID(){
		global $SQL_MASTER;

		if( $this->_DEBUG ){ d( "getMaxID() : ". "select max(shadow_id) as max from ". strtolower($this->tableName). "<br/>\n", 'sql'); }
			
		$result	 = $this->sql_query( "select max(shadow_id) as max from ". strtolower($this->tableName) );
		
		if( !$result ){
			if( !$this->_DEBUG ){ d( "getMaxID() : select max(shadow_id) as max from ". strtolower($this->tableName). "<br/>\n", 'sql'); }
			throw new Exception("getMaxID() : SQL MESSAGE ERROR. \n");
		}
		
		$data = $this->sql_fetch_array($result);
			
		return $data['max'];
			
	}

	/**
	 * record����o�^�p��SQL�����擾���܂��B
	 */
	function toString($rec, $mode){

		if(  $this->getData( $rec, 'delete_key' )  ){
			$sql	 = "delete_key = ".$this->sqlDataEscape(true,'boolean').", \n";
		}else{
			if($mode != "INSERT")
			$sql	 = "delete_key = ".$this->sqlDataEscape(false,'boolean').", \n";
			else
			$sql = "";
		}

		$row = count( $this->colName );
		
		for($i=0; $i<$row; $i++){

			if( $mode == "UPDATE" )
			$sql	 .= $this->colName[$i]. " = ";

			// �J�����̌^���擾
			$type	 = $this->colType[ $this->colName[$i] ];

			// �J�����̒l���擾
			$data	 = $this->getData( $rec, $this->colName[$i] );

			//sql�Ƃ��ė��p�\�ȃf�[�^�ɕό`
			if( is_array( $data ) ){
				// �J�����̒l���z��̏ꍇ�A�z��f�[�^�� / �ŋ�؂��Ċi�[
				$sql .= $this->sqlDataEscape( join( $data,'/') , $type  );
			}else{
				// �J�����̒l�����l�̏ꍇ
				$sql .= $this->sqlDataEscape( $data , $type );
			}

			if( $i != count( $this->colName ) - 1 ){
				$sql	 .= ", \n";
			}
		}
		return $sql;
	}

	/**
	 * ���݂̃e�[�u������w�肵��column�̑����v���擾���܂��B
	 */
	function getSum( $name, $table = null){

		if( $this->_DEBUG ){ d( "getSum() : ". "select sum($name) as sum from ". strtolower($this->tableName).$table->getWhere(). "<br/>\n", 'sql' ); }
			
			
		$result	 = $this->sql_query( "select sum($name) as sum from ". strtolower($this->tableName).$table->getWhere() );
		
		if( !$result ){
			if( !$this->_DEBUG ){ d( "getSum() : select sum($name) as sum from ". strtolower($this->tableName).$table->getWhere(). "<br/>\n", 'sql'); }
			throw new Exception("getSum() : SQL MESSAGE ERROR. \n");
		}
		
		$data = $this->sql_fetch_array($result);
		return (int)$data['sum'];
	}

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������sum�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 */
	function getSumTable( $sum_name, $group_name, $table = null){
			
		if( $this->_DEBUG ){ d( "getSumTable();\n", 'sql' ); }

		if( is_null($table) )
		$table = $this->getTable();

		$table->select	 = str_replace( '*' , "$group_name , sum($sum_name) as sum" , $table->select );
			
		$table->group = $group_name;

		return $table;
	}
	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������cnt�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 */
	function getCountTable( $name, $tbl = null){
		$name	 = strtolower( $name );

		if( $this->_DEBUG ){ d( "getCountTable();\n", 'sql' ); }

		if( is_null($tbl) ){
			$table = $this->getTable();
		}else{
			$table = $tbl->getClone();
		}

		$table->select = "$name , count($name) as cnt";
			
		$table->group = $name;

		$table->order[ 'cnt' ] = 'ASC';

		return $table;
	}
	//�I���J������ǉ��B  geCountTable�ȂǂŃf�[�^�̗~�����J�������\������Ȃ����ɗL��
	function addSelectColumn( &$tbl, $name, $group = true ){
		$table	 = $tbl->getClone();
		
		$table->select .= ','.$name;
		
		if(strlen($table->group) && $group){
			$table->group .= ','.$name;
		}

		return $table;
	}

	//�w��J�����̂݌��ʂ��d�����폜���ĕԂ�
	function getDistinctColumn( $name , &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
		$table = $this->getTable();

		$table->select	 = "DISTINCT " . $table->select;
		$table->select	 = str_replace( '*' , "$name " , $table->select );
		$table->order[ $name ] = 'ASC';

		return $table;
	}

	//�d�����폜���ĕԂ�
	function getDistinct( &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
			$table = $this->getTable();

		$table->select	 = str_replace( 'SELECT ' , "SELECT DISTINCT " , $table->select );

		return $table;
	}

	//�w��J�����̂݌��ʂ��d�����폜���ĕԂ�
	function getColumn( $name , &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
		$table = $this->getTable();

		$table->select	 = str_replace( '*' , " $name " , $table->select );
		$table->order[ $name ] = 'ASC';

		return $table;
	}

	function getClumnNameList(){
		return array_slice($this->colName,0,-1);
	}

	/**
	 * row��Record��cash���폜���܂��B
	 */
	function cashReset(){
		if( $this->_DEBUG ){ d( "cashReset() : reset <br/>\n", 'sql' ); }
		$this->row = -1;
		$this->rec_cash = null;
	}
	
	/*
	 * ���Ԃ�goroup������
	 * @param $column �e�[�u���f�[�^
	 */
	function dateGroup(&$tbl,$column,$format){
		$table	 = $tbl->getClone();
		
		$table->group = $this->sql_date_group($column,$format);
		
		return $table;
	}

	function sql_query($sql){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_fetch_assoc($result,$index){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_fetch_array($result){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}
	
	function sql_fetch_all($result){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmsounting');
	}

	function sql_num_rows($result){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_convert( $val ){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_escape( $val ){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}
	
	function sql_date_group($column,$format){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}
	
	function to_boolean( $val ){
		if( is_bool( $val ) )		{ return $val; }
		else						{ return SystemUtil::convertBool($val);}
	}


	function sqlDataEscape($val,$type,$quots = true)
	{
		switch ($type) {
			case 'string':
			case 'varchar' :
			case 'char' :
			case 'image':
			case 'text':
			case 'blob':
				// �J�����̌^��������̏ꍇ
				if($quots){ $sqlstr = "'"; }else{ $sqlstr = ""; }

				$sqlstr .= $this->sql_escape( $val );

				if( preg_match( '/\\\\$/' , $sqlstr ) )
					$sqlstr .= ' ';

				if($quots){ $sqlstr .= "'"; }
				break;
			case 'double':
				// �J�����̌^�������̏ꍇ
				$sqlstr = doubleval($val);
				break;
			case 'boolean':
				if( SystemUtil::convertBool($val) ) { $sqlstr = 'TRUE'; }
				else								{ $sqlstr = 'FALSE'; }
				break;
			default:
			case 'int':
			case 'timestamp':
				// �J�����̌^�������̏ꍇ
				$sqlstr = intval($val);
				break;
		}
		return $sqlstr;
	}

	//debug�t���O����p
	function onDebug(){ $this->_DEBUG = true; }
	function offDebug(){ $this->_DEBUG = false; }

	private function getColumnType($name){
		throw new Exception('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}
}

/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/

class TableBase{
	var $sql;
	var $select	 = null;
	var $from	 = null;
	var $delete	 = null;
	var $where	 = null;
	var $order	 = Array();
	var $group	 = null;
	var $offset	 = null;
	var $limit	 = null;
	
	var $join    = false;//join�t���O
	var $sql_char_code = null;

	function __construct($from){}

	function getClone(){
		$table			 = new Table( $this->from );
		$table->select	 = $this->select;
		$table->delete	 = $this->delete;
		$table->where	 = $this->where;
		$table->group	 = $this->group;
		$table->order	 = $this->order;
		$table->offset	 = $this->offset;
		$table->limit	 = $this->limit;
		$table->join	 = $this->join;
		return $table;
	}

	function getString(){
		global $SQL_MASTER;
			
		$sql	 = $this->toSelectFrom();
		$sql	.= $this->getWhere();
		
		if( $this->group != null ){
			$sql	 .= " GROUP BY ". $this->group;
		}
		
		if( count($this->order) ){
			$sql	 .= " ORDER BY";
			$ord = Array();
			foreach( $this->order as $col => $val ){
				$ord[] = " $col $val";
			}
			$sql .= join(',',$ord); 
		}else{
			$sql	 .= " ORDER BY shadow_id";
		}
		
		$sql .= $this->getLimitOffset();

		return $this->sql_convert( $sql );
	}

	function getUnionString( $data = null ){
		
		if( isset($data) ) { $this->select	 = str_replace( '*' , "SHADOW_ID,".$data , $this->select ); }
		$sql	 = $this->toSelectFrom();
		$sql	.= $this->getWhere();
		
		if( $this->group != null ){
			$sql	 .= " GROUP BY ". $this->group;
		}

		return $this->sql_convert( $sql );
			
	}

	//Row�擾�p�Ȃ̂����AOffset�����݂���ꍇ�Ƀo�O���o��̂Ł@���΂炭����
	function getRowString(){
		$cTable = $this->getClone();
		
		$cTable->select	 = str_replace( '*' , 'COUNT(*) as cnt' , $cTable->select );
		$sql	 = $cTable->toSelectFrom();
		
		$sql	.= $this->getWhere();

		$sql .= $this->getLimitOffset();
		
		return $this->sql_convert( $sql );
	}

	function getLimitOffset(){
		throw new Exception('Table::'.__FUNCTION__.'  Unmounting');
	}

	function sql_convert( $val ){
		throw new Exception('Table::'.__FUNCTION__.'  Unmounting');
	}
	
	/*
	 * and/or�̏�����ǉ�����
	 * @param sql			�ǉ��������
	 * @param conjunction	�ڑ���(and/or)
	 */
	function addWhere( $sql, $conjunction = 'and' ){
		$conjunction = strtolower($conjunction);
		if( is_null($this->where) ){
			$this->where= $sql ;
		}else{
			if(is_string($this->where)){
				$this->where = Array( $conjunction => Array( $this->where ) );
			}else if(!isset($this->where[$conjunction])){
				$old = $conjunction == 'and' ? 'or' : 'and';
				$this->where[ $conjunction ][$old] = $this->where[ $old ];
				
				unset($this->where[ $old ]);
			}
			array_push($this->where[$conjunction],$sql);
		}
	}
	
	/*
	 * �ۑ����Ă���where�̓��e����͂��ĕ�����ɐ��`����
	 * @param $array		and��������or�̔z��
	 */
	function getWhere( $del_falg = true ){
		$ret = "";
		
		if( $del_falg && strlen( $this->delete ) ){
			$ret .= $this->delete;
		}
		$where = $this->getWhereReflexive();
		
		if( $ret && $where ){
			$ret .= " AND ";
		}
		$ret .= $where;
		
		if(strlen($ret)){ $ret = " WHERE " . $ret; }
		
		return $ret;
	}
	
	
	function getWhereReflexive( $conjunction = null , $array = null ){
		if(is_null($array)){
			if(is_null($this->where)){return "";}
			else if(!is_array($this->where)){return '('.$this->where.')';}
			
			foreach( $this->where as $key => $val ){
				$array = $val;
				$conjunction = $key;
				break;
			}
		}
		foreach( $array as $key => $val ){
			if(is_array($val)){$array[$key]=$this->getWhereReflexive($key,$val);}
		}
		return "(".implode($array," $conjunction ").")";
	}
	
	/*
	 * �ۑ����Ă���where�̓��e�������Ɏw�肳�ꂽtable���x�[�X�Ƃ���join�e�[�u���ɕύX����
	 */
	function changeJoinTable( $base_tbl_name, $array = null ){
		if(is_null($array)){
			
			//�܂�order
			if(!count($this->order)){ $this->order['SHADOW_ID'] = 'DESC'; }
			$new_order = Array();
			foreach( $this->order as $key => $val ){
				$new_order[ $base_tbl_name.'.'.$key ] = $val;
			}
			$this->order = $new_order;
			
			
			$base_tbl_name = ' '.$base_tbl_name.'.';
			if(is_null($this->where)){return;}
			else if(!is_array($this->where)){$this->where = $base_tbl_name.$this->where; return; }
			$array = $this->where;
			$flg = true;
		}
		foreach( $array as $key => $val ){
			if(is_array($val)){$array[$key]=$this->changeJoinTable($base_tbl_name,$val);}
			else{ $array[$key]=$base_tbl_name.$val; }
		}
		if($flg){ $this->where = $array; }
		return $array;
	}
	
	function toSelectFrom(){
		return 'SELECT '.$this->select.' FROM '.$this->from;
	}
}

?>