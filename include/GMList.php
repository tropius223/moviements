<?PHP

class GMList
{
	static $gmList;
	
	/**
	 * GM�I�u�W�F�N�g���擾����
	 * 
	 * @param name �e�[�u�����B
	 * @return GM�I�u�W�F�N�g�B
	 */
	static function getGM( $name )
	{
		if( !isset( self::$gmList[$name] ) ) { self::$gmList[$name] = SystemUtil::getGMforType($name); }
		
		return self::$gmList[$name];
	}
	
	/**
	 * DB�I�u�W�F�N�g���擾����
	 * 
	 * @param name �e�[�u�����B
	 * @return DB�I�u�W�F�N�g�B
	 */
	static function getDB( $name )
	{
		if( !isset( self::$gmList[$name] ) ) { self::$gmList[$name] = SystemUtil::getGMforType($name); }
		
		return self::$gmList[$name]->getDB();
	}
	
}

?>