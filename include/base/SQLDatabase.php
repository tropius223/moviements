<?php

include_once "./include/base/Database.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQLデータベースシステム ベースクラス
 *
 * @author 吉岡幸一郎
 * @original 丹羽一智
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
	 * コンストラクタ。
	 * @param $dbName DB名
	 * @param $tableName テーブル名
	 * @param $colName カラム名を持った配列
	 */
	function __construct($dbName, $tableName, $colName, $colType, $colSize ){
	}

	/**
	 * レコードを取得します。
	 * @param $table テーブルデータ
	 * @param $index 取得するレコード番号
	 * @return レコードデータ
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
	 * レコードを取得します。
	 *
	 * @param $id 取得するレコードID
	 * @param $type 操作対象となるテーブルのtype(nomal/delete/all)
	 * @return レコードデータ。レコードデータが存在しない場合nullを返す。
	 */
	function selectRecord( $id , $type = null)
	{
		if( is_null($id) ){ return null;}

		$table	 = $this->getTable($type);
		$table	 = $this->searchTable( $table, 'id', '=', $id );
		if( $this->getRow($table) > 0 )
		{// レコードが存在する場合
			$rec	 = $this->getRecord( $table, 0 );
			return $rec;
		}
		else	{ return null; }
	}

	/**
	 * テーブルから指定したレコードを削除します。
	 * @param table テーブルデータ
	 * @param rec 削除対象となるレコード
	 * @return テーブルデータ
	 */
	function pullRecord($table, $rec){
		return $this->searchTable( $table, 'SHADOW_ID', '!', $this->getData( $rec, 'SHADOW_ID' ) );
	}

	/**
	 * データの内容を取得する。
	 * @param $rec レコードデータ
	 * @param $name カラム名
	 * @return 値
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
	 * レコードの内容を更新する。
	 * DBファイルへの更新も含みます。
	 * @param $rec レコードデータ
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
	 * レコードの削除。
	 * DBファイルへの反映も行います。
	 * @param $rec レコードデータ
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
	 * whereによって選択されるテーブルの行を削除します。
	 * @param $table テーブルデータ
	 * @return 行数
	 */
	function deleteTable($table){
		//deleteのためのUpdate文
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
	 * データをセットする。
	 * @param $rec レコードデータ
	 * @param $name カラム名
	 * @param $val 値
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
	 * 簡易な演算を行ない、結果をセットする。
	 * カラムが数値型で無い場合は無効
	 *
	 * @param $rec レコードデータ
	 * @param $name カラム名
	 * @param $opp 演算子
	 * @param $name 値
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
	 * 引数として渡されたtableの全行にデータをセットしてupdateする。
	 *
	 * @param $table 更新を行なうカラムの入ったtable
	 * @param $name カラム名
	 * @param $val 値
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
	 * レコードにデータをまとめてセットします。
	 * @param $rec レコードデータ
	 * @param data データ連想配列（添え字はカラム名）
	 */
	function setRecord(&$rec, $data){
		$tmp	 = $this->getData( $rec, 'SHADOW_ID' );
		$rec	 = $this->getNewRecord( $data );
		$this->setData( $rec, 'SHADOW_ID', $tmp );
		return $rec;
	}

	/**
	 * 新しくレコードを取得します。
	 * デフォルト値を指定したい場合は
	 * $data['カラム名']の連想配列で初期値を指定してください。
	 * @param data 初期値定義連想配列
	 * @return レコードデータ
	 */
	function getNewRecord($data = null){
			
		$rec = array();
			
		// レコードの中身を null で初期化
		for($i=0; $i<count( $this->colName ); $i++){
			$rec[ $this->colName[$i] ] = null;
		}
			
		// 初期値が指定されていなければ return
		if(  !isset( $data )  )
		return $rec;
			
		// 初期値を代入
		for($i=0; $i<count( $this->colName ); $i++){
			if( $this->colType[ $this->colName[$i] ] == 'image' ||  $this->colType[ $this->colName[$i] ] == 'file' ){
//			if(  $_FILES[ $this->colName[$i] ]['name'] != "" || $_POST[ $this->colName[$i] . '_filetmp' ] != "" ){
				// データファイルの場合
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
		// 暗黙の主キーを定義
		$rec[ 'shadow_id' ]	 = $this->getMaxID()+1;
		return $rec;
	}

	function setFile(&$rec, $colname){
		$sys	 = SystemUtil::getSystem( $_GET["type"] );
		$sys->doFileUpload( $this, $rec, $colname, $_FILES );
	}

	/**
	 * レコードの追加。
	 * DBへの反映も同時に行います。
	 * @param $rec レコードデータ
	 */
	function addRecord($rec){
		// SQL文の生成を開始
		$sql	 = "INSERT INTO ". strtolower($this->tableName). " (";
			
		// カラム名リストを出力
		for($i=0; $i<count( $this->colName ); $i++){
				
			$sql	 .= $this->colName[$i];
				
			if( $i != count( $this->colName ) - 1 ){
				$sql	 .= ", \n";
			}
		}
			
		// 重複を避ける為に暗黙の主キーを再設定
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
	 * DBが持つテーブルを取得します。
	 * @return テーブルデータ
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
	 * テーブルの行数を取得します。
	 * @param $table テーブルデータ
	 * @return 行数
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
	 * テーブルの検索を行います。
	 * 利用できる演算子は以下のものです。
	 * >, <	 不等号演算子
	 * =	 等号演算子
	 * !	 非等号演算子
	 * in    in演算子
	 * &     bit演算子
	 * b	 ビトゥイーン演算子
	 * ビトゥイーン演算子の場合のみ$val2を指定します。
	 * @param $table テーブルデータ
	 * @param $name カラム名
	 * @param $opp 演算子
	 * @param $val 値１
	 * @param $val2 値２
	 */
	function searchTable(&$tbl, $name, $opp, $val, $val2 = null){
		if( $tbl->join ){
			//joinされている時はjoin用の検索関数を使う
			return $this->joinTableSearch( $this, $tbl , $name, $opp, $val, $val2 );
		}

		$table	 = $tbl->getClone();
		//検索パラメータがnullの場合、検索条件の追加を行わない
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
	 * searchTableで使用する構文生成を行なう。
	 * join後のtable外部tableなどからも再利用する為の分離
	 * 
	 * 内部的にしか呼ばせない為private
	 */
	function searchTableCore(&$name, &$opp, &$val, &$val2 = null ){
		if( is_array( $val ) ){
			//array_mapと匿名関数の代替処理
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
			}else{//val2がフラグ判定後のチェック
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
	 * 空のテーブルを返す。
	 * searchの結果を空にしたりする時に使用。
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
	 * レコードをソートします。
	 * @param $table テーブルデータ
	 * @param $name カラム名
	 * @param $asc 昇順・降順を 'asc' 'desc' で指定します。
	 * @param $reset sort条件を追加にするかどうかのフラグ。  デフォルト値はfalse 
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
	 * テーブルの論理和。
	 * @param $table1 テーブルデータ
	 * @param $table2 テーブルデータ
	 * @return テーブルデータ
	 */
	function orTable(&$tbl, &$table2){
		$table1	 = $tbl->getClone();
		if( $table1->where == null ){
			if( $table2->where == null ){
				// どちらのテーブルも絞り込み条件が無い場合
				return $this->getTable();
			}else{
				// table1 に絞り込み条件が無い場合
				return $table2;
			}
		}else{
			if( $table2->where == null ){
				// table2 に絞り込み条件が無い場合
				return $table1;
			}else{
				$table1->addWhere( $table2->where , 'or' );
				$this->cashReset();
			}
			return $table1;
		}
	}
	/**
	 * テーブルの論理和。(配列対応版
	 * @param $a テーブルデータの入った配列
	 * @return テーブルデータ
	 *
	 * func_get_argsでは参照を受けれない為配列にて
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
	 * テーブルの論理積。
	 * @param $table1 テーブルデータ
	 * @param $table2 テーブルデータ
	 * @return テーブルデータ
	 */
	function andTable(&$tbl, &$table2){
		$table1	 = $tbl->getClone();
		if( $table1->where == null ){
			if( $table2->where == null ){
				// どちらのテーブルも絞り込み条件が無い場合
				return $this->getTable();
			}else{
				// table1 に絞り込み条件が無い場合
				return $table2;
			}
		}else{
			if( $table2->where == null ){
				// table2 に絞り込み条件が無い場合
				return $table1;
			}else{
				$table1->addWhere( $table2->where , 'and' );
				$this->cashReset();
			}
			return $table1;
		}
	}

	/**
	 * ユニオンテーブルを作成します。
	 * ソート条件等はrTableのものを使用。
	 * @param $table テーブルデータ
	 * @param $name カラム名
	 * @param $asc 昇順・降順を 'asc' 'desc' で指定します。
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
	 * テーブルの結合
	 * ソート条件等はrTableのものを使用。
	 * @param $table テーブルデータ
	 * @param $name カラム名
	 * @param $asc 昇順・降順を 'asc' 'desc' で指定します。
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
	//テーブルの結合(結合条件をsql文で渡す
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
		
		//検索パラメータがnullの場合、検索条件の追加を行わない
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
	 * テーブルの $start 行目から $num 個取り出す。
	 * @param table テーブルデータ
	 * @param start オフセット
	 * @param num 数
	 */
	function limitOffset( $table, $start, $num ){
		$ttable			 = $table->getClone();
		$ttable->offset	 = $start;
		$ttable->limit	 = $num;
			
		$this->cashReset();
		return $ttable;
	}

	/**
	 * 暗黙IDの最大値を返す
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
	 * recordから登録用のSQL文を取得します。
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

			// カラムの型を取得
			$type	 = $this->colType[ $this->colName[$i] ];

			// カラムの値を取得
			$data	 = $this->getData( $rec, $this->colName[$i] );

			//sqlとして利用可能なデータに変形
			if( is_array( $data ) ){
				// カラムの値が配列の場合、配列データを / で区切って格納
				$sql .= $this->sqlDataEscape( join( $data,'/') , $type  );
			}else{
				// カラムの値が実値の場合
				$sql .= $this->sqlDataEscape( $data , $type );
			}

			if( $i != count( $this->colName ) - 1 ){
				$sql	 .= ", \n";
			}
		}
		return $sql;
	}

	/**
	 * 現在のテーブルから指定したcolumnの総合計を取得します。
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
	 * 現在のテーブルのSQLを指定したcolumnでgroupbyし、指定カラムをsumした結果を付与したテーブルを返す。
	 * 実際の取得はgetRecored、getDataを使う
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
	 * 現在のテーブルのSQLを指定したcolumnでgroupbyし、指定カラムをcntした結果を付与したテーブルを返す。
	 * 実際の取得はgetRecored、getDataを使う
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
	//選択カラムを追加。  geCountTableなどでデータの欲しいカラムが表示されない時に有効
	function addSelectColumn( &$tbl, $name, $group = true ){
		$table	 = $tbl->getClone();
		
		$table->select .= ','.$name;
		
		if(strlen($table->group) && $group){
			$table->group .= ','.$name;
		}

		return $table;
	}

	//指定カラムのみ結果を重複を削除して返す
	function getDistinctColumn( $name , &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
		$table = $this->getTable();

		$table->select	 = "DISTINCT " . $table->select;
		$table->select	 = str_replace( '*' , "$name " , $table->select );
		$table->order[ $name ] = 'ASC';

		return $table;
	}

	//重複を削除して返す
	function getDistinct( &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
			$table = $this->getTable();

		$table->select	 = str_replace( 'SELECT ' , "SELECT DISTINCT " , $table->select );

		return $table;
	}

	//指定カラムのみ結果を重複を削除して返す
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
	 * rowとRecordのcashを削除します。
	 */
	function cashReset(){
		if( $this->_DEBUG ){ d( "cashReset() : reset <br/>\n", 'sql' ); }
		$this->row = -1;
		$this->rec_cash = null;
	}
	
	/*
	 * 期間でgoroup化する
	 * @param $column テーブルデータ
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
				// カラムの型が文字列の場合
				if($quots){ $sqlstr = "'"; }else{ $sqlstr = ""; }

				$sqlstr .= $this->sql_escape( $val );

				if( preg_match( '/\\\\$/' , $sqlstr ) )
					$sqlstr .= ' ';

				if($quots){ $sqlstr .= "'"; }
				break;
			case 'double':
				// カラムの型が実数の場合
				$sqlstr = doubleval($val);
				break;
			case 'boolean':
				if( SystemUtil::convertBool($val) ) { $sqlstr = 'TRUE'; }
				else								{ $sqlstr = 'FALSE'; }
				break;
			default:
			case 'int':
			case 'timestamp':
				// カラムの型が整数の場合
				$sqlstr = intval($val);
				break;
		}
		return $sqlstr;
	}

	//debugフラグ操作用
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
	
	var $join    = false;//joinフラグ
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

	//Row取得用なのだが、Offsetが存在する場合にバグが出るので　しばらく凍結
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
	 * and/orの条件を追加する
	 * @param sql			追加する条件
	 * @param conjunction	接続詞(and/or)
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
	 * 保存しているwhereの内容を解析して文字列に整形する
	 * @param $array		andもしくはorの配列
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
	 * 保存しているwhereの内容を引数に指定されたtableをベースとするjoinテーブルに変更する
	 */
	function changeJoinTable( $base_tbl_name, $array = null ){
		if(is_null($array)){
			
			//まずorder
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