<?PHP

class GMList
{
	static $gmList;
	
	/**
	 * GMオブジェクトを取得する
	 * 
	 * @param name テーブル名。
	 * @return GMオブジェクト。
	 */
	static function getGM( $name )
	{
		if( !isset( self::$gmList[$name] ) ) { self::$gmList[$name] = SystemUtil::getGMforType($name); }
		
		return self::$gmList[$name];
	}
	
	/**
	 * DBオブジェクトを取得する
	 * 
	 * @param name テーブル名。
	 * @return DBオブジェクト。
	 */
	static function getDB( $name )
	{
		if( !isset( self::$gmList[$name] ) ) { self::$gmList[$name] = SystemUtil::getGMforType($name); }
		
		return self::$gmList[$name]->getDB();
	}
	
}

?>