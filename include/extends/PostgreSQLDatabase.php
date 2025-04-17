<?php

include_once "./include/base/SQLDatabase.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQL�f�[�^�x�[�X�V�X�e���@PostgreSQL�p
 *
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
		global $SQL_ID;
		global $SQL_PASS;
		global $TABLE_PREFIX;
		global $SQL_PORT;
			
		// �t�B�[���h�ϐ��̏�����
		$this->log		 = new OutputLog($DB_LOG_FILE);

		if($SQL_PORT != "")
			$this->connect  = pg_connect( 'host=' . $SQL_SERVER . ' port=' . $SQL_PORT . ' dbname=' . $dbName . ' user=' . $SQL_ID . ' password=' . $SQL_PASS );
		else
			$this->connect  = pg_connect( 'host=' . $SQL_SERVER . ' dbname=' . $dbName . ' user=' . $SQL_ID . ' password=' . $SQL_PASS );

		if( !$this->connect ) { throw new Exception("DB CONNECT ERROR. -> dbname=". $dbName. " host=".$SQL_SERVER."\n"); }

			
		$this->dbName		 = $dbName ;
		$this->tableName	 = strtolower( $TABLE_PREFIX.$tableName );
		
		array_unshift( $colName, strtolower( 'SHADOW_ID' ));
		$colType[ strtolower( 'SHADOW_ID' ) ] = 'string';
		$colType[ strtolower( 'DELETE_KEY' ) ] = 'boolean';
		
		$this->colName		 = $colName;
		$this->colType		 = $colType;
		$this->colSize		 = $colSize;
			
		$this->addLog		 = $ADD_LOG;
		$this->updateLog	 = $UPDATE_LOG;
		$this->delLog		 = $DELETE_LOG;
			
		$this->dbInfo		 = $dbName. ",". $tableName;

		$this->prefix		 = $TABLE_PREFIX;
			
		//                  $this->sql_char_code = "UTF8";
		//                $this->sql_char_code = "SJIS";
		pg_set_client_encoding('SJIS');
		$this->sql_char_code = pg_client_encoding();  //pg_client_encoding($connect)
		//                pg_set_client_encoding($connect,'SJIS');

	}

	function sql_query($sql){
		return pg_query( $this->connect, $sql );
	}

	function sql_fetch_assoc( $result,$index){
		return pg_fetch_array( $result, $index, PGSQL_ASSOC );;
	}

	function sql_fetch_array($result){
		return pg_fetch_array( $result, 0, PGSQL_ASSOC );
	}
	
	function sql_fetch_all( $result ){
		return pg_fetch_all( $result );
	}

	function sql_num_rows($result){
		return pg_num_rows( $result );
	}

	function sql_convert( $val ){
		return mb_convert_encoding( $val, mb_internal_encoding(), $this->sql_char_code );
	}

	function sql_escape($val){
		return mb_convert_encoding( str_replace(  '\'', '\\\'', $val ), $this->sql_char_code, mb_internal_encoding() );
	}

	function to_boolean( $val ){
		if( is_bool( $val ) )		{ return $val; }
		else if( is_string($val )){
			if( $val == 'f' ){ return false; }
			if( $val == 't' ){ return true; }
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
			if( SystemUtil::convertBool($val) )	{ return $sqlstr = "TRUE"; }
			else								{ return $sqlstr = "FALSE"; }
		}else{
			return parent::sqlDataEscape($val,$type,$quots);
		}
	}

	private function getColumnType($name){
		$t = $this->getTable();
		$t->offset	 = 0;
		$t->limit	 = 1;
		$ret = $this->sql_query( "SELECT $name FROM ". strtolower($this->tableName)." ".$t->getLimitOffset() );

		return pg_field_type($ret,0);;
	}
	
	function getRecord($table, $index){
		$rec = parent::getRecord($table,$index);
		if( $rec != null && strpos( $table->select , $this->type.'.' ) !== FALSE ){
			foreach( $rec as $key => $val ){
				if( strpos( $key, $this->type.'.' ) !== FALSE ){
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
		global $SQL_MASTER;

		$this->select	 = '*';
		$this->from		 = $from;
		$this->delete	 = '( delete_key = FALSE OR delete_key IS NULL )';

		$this->sql_char_code = pg_client_encoding();
	}


	function getLimitOffset(){
		if( ($this->offset == 0 || $this->offset != null) && $this->limit != null ){
			$str	 = " OFFSET ". $this->offset. " LIMIT ". $this->limit;
			return $str;
		}else{
			return "";
		}
	}
	function sql_convert( $val ){
		return mb_convert_encoding( $val, $this->sql_char_code, mb_internal_encoding() );
	}
}
?>