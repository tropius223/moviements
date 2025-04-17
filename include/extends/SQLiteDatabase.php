<?php

include_once "./include/base/SQLDatabase.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQL�f�[�^�x�[�X�V�X�e���@SQLite�p
 *
 * @author �V����
 * @author �g���K��Y
 * @original �O�H��q
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

class SQLDatabase extends SQLDatabaseBase
{

	/**
	 * �R���X�g���N�^�B
	 * @param $dbName DB��
	 * @param $tableName �e�[�u����
	 * @param $colName �J���������������z��
	 */
	function __construct($dbName, $tableName, $colName, $colType, $colSize ){

		global $DB_LOG_FILE;
		global $ADD_LOG;
		global $UPDATE_LOG;
		global $DELETE_LOG;
		global $SQL_SERVER;
		global $TABLE_PREFIX;
			
		// �t�B�[���h�ϐ��̏�����
		$this->log		 = new OutputLog($DB_LOG_FILE);

		$this->connect = sqlite_open( "./tdb/".$dbName.".db", 0666, $SQLITE_ERROR );
		if( !$this->connect ){
			throw new Exception("SQLDatabase() : DB CONNECT ERROR. -> sqlite_open( ".$dbName." )\n");
		}

		$this->dbName		 = $dbName ;
		$this->tableName	 = strtolower( $TABLE_PREFIX.$tableName );
		$colName[]			 = strtolower( 'SHADOW_ID' );
		$this->colName		 = $colName;
		$this->colType		 = $colType;
		$this->colSize		 = $colSize;
			
		$this->addLog		 = $ADD_LOG;
		$this->updateLog	 = $UPDATE_LOG;
		$this->delLog		 = $DELETE_LOG;
			
		$this->dbInfo		 = $dbName. ",". $tableName;

		$this->prefix		 = $TABLE_PREFIX;

	}

	function sql_query($sql){
		return sqlite_query( $this->connect, mb_convert_encoding( $sql, 'utf-8', mb_internal_encoding()) );
	}

	function sql_fetch_assoc( $result ,$index){
		sqlite_seek($result , $index);
		return sqlite_fetch_array($result, SQLITE_ASSOC);
	}

	function sql_fetch_array( $result ){
		return sqlite_fetch_array( $result );
	}

	function sql_fetch_all( $result ){
		return sqlite_fetch_all( $result );
	}
	
	function sql_num_rows( $result ){
		return sqlite_num_rows( $result );
	}

	function sql_convert( $val ){
		if( 'UTF-8' == SystemUtil::detect_encoding_ja($val) )
			return mb_convert_encoding( $val, mb_internal_encoding(), 'UTF-8' );
		
		return $val;
	}

	function sql_escape($val){
		if(!strlen($val)){return $val;}
		return sqlite_escape_string($val);
	}
	
	function sql_date_group($column,$format){
		return "strftime('$format',$column,'unixepoch')";
	}

	//���g�p
	private function getColumnType($name){
		return null;
	}

	function to_boolean( $val ){
		if( is_bool( $val ) )		{ return $val; }
		else if( is_string($val )){
			if( $val == 'FALSE' ){ return false; }
			if( $val == 'TRUE' ){ return true; }
		}
		if( $val == 1 || $val == '1')		{ return true; }
		//else if( !strlen($val) )    { return false;}
		return false;
	}
	
	function sqlDataEscape($val,$type,$quots = true)
	{
		if($type == "boolean"){
			if( SystemUtil::convertBool($val) )	{ return $sqlstr = 1; }
			else								{ return $sqlstr = "''"; }
		}else{
			return parent::sqlDataEscape($val,$type,$quots);
		}
	}
	
	function getRecord($table, $index){
		$rec = parent::getRecord($table,$index);
		if( $rec != null && strpos( $table->select , $this->tableName.'.' ) !== FALSE ){
			foreach( $rec as $key => $val ){
				if( strpos( $key, $this->tableName.'.' ) !== FALSE ){
					$newrec[ substr($key,strlen($this->tableName)+1) ] = $val;
				}else{
					$newrec[ $key ] = $val;
				}
			}
			return $newrec;
		}
		return $rec;
	}
}

/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/

class Table extends TableBase{

	function __construct($from){

		$this->select	 = '*';
		$this->from		 = $from;
		$this->delete	 = '( delete_key = "" OR delete_key IS NULL )';

		$this->sql_char_code = "SJIS";
	}

	function getLimitOffset(){
		global $SQL_MASTER;
		if( ($this->offset == 0 || $this->offset != null) && $this->limit != null ){
			$str	 = " LIMIT ". $this->offset. ',' .$this->limit;
			return $str;
		}else{
			return "";
		}
	}

	function sql_convert( $val ){
		return $val;
	}
}
?>