<?php

include_once "./include/base/SQLDatabase.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQLデータベースシステム　MySQL用
 *
 * @author 吉岡幸一郎
 * @original 丹羽一智
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

class SQLDatabase extends SQLDatabaseBase
{
	static $commonConnect = null;

	/**
	 * コンストラクタ。
	 * @param $dbName DB名
	 * @param $tableName テーブル名
	 * @param $colName カラム名を持った配列
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
			
		// フィールド変数の初期化
		$this->log		 = new OutputLog($DB_LOG_FILE);

		if( !self::$commonConnect )
		{
			if($SQL_PORT != "")
				self::$commonConnect	 = mysqli_connect( $SQL_SERVER.":".$SQL_PORT, $SQL_ID, $SQL_PASS );
			else
				self::$commonConnect	 = mysqli_connect( $SQL_SERVER, $SQL_ID, $SQL_PASS );

			if( !self::$commonConnect ){
				throw new Exception("SQLDatabase() : DB CONNECT ERROR. -> mysqli_connect( ".$SQL_SERVER." )\n");
			}
			if(  !mysqli_select_db( self::$commonConnect , $dbName )  ){
				throw new Exception("SQLDatabase() : DB CONNECT ERROR. -> mysqli_select_db( ". $dbName. " )\n");
			}
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

		//mySQLからの出力コードをSJISに
		mysqli_query( self::$commonConnect , "set names sjis");
			
		//set name ***と違い、[mysqli_real_escape_string]にも有効。  正しphp5.2.3、MySQL5.0.7以降のみ利用可能
//		mysqli_set_charset(self::$commonConnect , 'sjis');

			
		//	mysqli_query( self::$commonConnect , "SET NAMES binary;");
		//	mysqli_set_charset( self::$commonConnect , 'binary');
	}

	function sql_query($sql){
		return mysqli_query( self::$commonConnect , $sql );
	}

	function sql_fetch_assoc( $result ,$index){
		mysqli_data_seek($result , $index);
		return mysqli_fetch_assoc($result);
	}

	function sql_fetch_array( $result ){
		return mysqli_fetch_array( $result );
	}

	function sql_fetch_all( $result ){
		if(function_exists('mysqli_fetch_all')){return mysqli_fetch_all( $result );}
	    $all = array();
	    while ($row = mysqli_fetch_assoc($result)) {$all[]=$row;}
	    mysqli_data_seek($result,0);
	    return $all;
	}

	function sql_num_rows( $result ){
		return mysqli_num_rows( $result );
	}

	function sql_convert( $val ){
		return $val;
	}

	function sql_escape($val){
		return mysqli_real_escape_string( self::$commonConnect , ($val));
	}
	
	function sql_date_group($column,$format){
		return "FROM_UNIXTIME($column,'$format')";
	}
	

	function to_boolean( $val ){
		if( is_bool( $val ) )		{ return $val; }
		else if( is_string($val )){
			$val = strtolower($val);
			if( $val == 'false' ){ return false; }
			if( $val == 'true' ){ return true; }
		}
		if( $val == 1 )		{ return true; }
		//else if( !strlen($val) )    { return false;}
		return false;
	}

	private function getColumnType($name){
		$t = $this->getTable();
		$t->offset	 = 0;
		$t->limit	 = 1;
		$ret = $this->sql_query( "SELECT $name FROM ". strtolower($this->tableName)." ".$t->getLimitOffset() );

		return mysqli_field_type($ret,0);
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
		$this->delete	 = '( delete_key = FALSE OR delete_key IS NULL )';

		$this->sql_char_code = "EUC-JP";//mysqli_client_encoding();
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