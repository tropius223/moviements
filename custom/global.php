<?PHP
	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * 汎用関数群
	 * 
	 * @version 1.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/


class CleanGlobal
{
	private function escape($array)
	{
		$array = self::nullbyte($array);
		return $array;
	}

	private function nullbyte($array)
	{
		if(is_array($array)) return array_map( array('CleanGlobal', 'nullbyte'), $array );
		return str_replace( "\0", "", $array );
	}

	function action()
	{
		$_GET = self::escape($_GET);
		$_POST = self::escape($_POST);
		$_REQUEST = self::escape($_REQUEST);
		$_FILES = self::escape($_FILES);
		if(isset($_SESSION)) { $_SESSION = self::escape($_SESSION); }
		$_COOKIE = self::escape($_COOKIE);
	}
}

function SaveTierParameter()
{
	if( isset( $_GET[ 'friend' ] ) ) //ティアパラメータが付加されている場合
		{ $_SESSION[ 'friend' ] = $_GET[ 'friend' ]; }
}

function CreateTier( $iBaseUserID_ , $iAdwaresID_ , $iPoint_ , $iKickbackRec_ )
{
	$baseRec       = null;
	$parentRec     = null;
	$gParentRec    = null;
	$parentDelete  = false;
	$gParentDelete = false;

	$kickbackDB = GMList::getDB( 'kickback' );
	$kickbackID = $kickbackDB->getData( $iKickbackRec_ , 'id' );

	$nUserDB    = GMList::getDB( 'nUser' );
	$baseRec    = $nUserDB->selectRecord( $iBaseUserID_ );
	$parentID   = $nUserDB->getData( $baseRec , 'parent_id' );

	if( $parentID ) //親がいる場合
	{
		$parentRec    = $nUserDB->selectRecord( $parentID , 'all' );
		$parentDelete = $nUserDB->getData( $parentRec , 'delete_key' );
		$gParentID    = $nUserDB->getData( $parentRec , 'parent_id' );
	}

	if( $gParentID ) //親の親がいる場合
	{
		$gParentRec    = $nUserDB->selectRecord( $gParentID , 'all' );
		$gParentDelete = $nUserDB->getData( $gParentRec , 'delete_key' );
	}

	$adwaresDB   = GMList::getDB( 'adwares' );
	$adwaresRec  = $adwaresDB->selectRecord( $iAdwaresID_ );
	$parentRate  = $adwaresDB->getData( $adwaresRec , 'parent_rate' );
	$gParentRate = $adwaresDB->getData( $adwaresRec , 'grandparent_rate' );

	$parentPoint  = $iPoint_ * ( $parentRate / 100.0 );
	$gParentPoint = $iPoint_ * ( $gParentRate / 100.0 );

	if( $parentID && !$parentDelete ) //親が生きている場合
	{
		$currentPoint = $nUserDB->getData( $parentRec , 'point' );

		$nUserDB->setData( $parentRec , 'point' , $currentPoint + $parentPoint );
		$nUserDB->updateRecord( $parentRec );

		CreateTierRec( $kickbackID , $parentID , $parentPoint , $iKickbackRec_ );
	}

	if( $gParentID && !$gParentDelete ) //親の親が生きている場合
	{
		$currentPoint = $nUserDB->getData( $gParentRec , 'point' );

		$nUserDB->setData( $gParentRec , 'point' , $currentPoint + $gParentPoint );
		$nUserDB->updateRecord( $gParentRec );

		CreateTierRec( $kickbackID , $gParentID , $gParentPoint , $iKickbackRec_ );
	}
}

function CreateTierRec( $iKickbackID_ , $iOwnerID_ , $iPoint_ , $iKickbackRec_ )
{
	$db  = GMList::getDB( 'tier' );
	$rec = $db->getNewRecord();

	$kickbackDB = GMList::getDB( 'kickback' );

	$db->setData( $rec , 'id' , SystemUtil::getNewId( $db , 'tier' ) );
	$db->setData( $rec , 'kickback_id' , $iKickbackID_ );
	$db->setData( $rec , 'owner' , $iOwnerID_ );
	$db->setData( $rec , 'point' , $iPoint_ );
	$db->setData( $rec , 'state' , $kickbackDB->getData( $iKickbackRec_ , 'state' ) );
	$db->setData( $rec , 'regist' , time() );

	$db->addRecord( $rec );
}

function ChangeTier( $iState_ , $iKickbackID_ )
{
	global $KICKBACK_STATE_ON;

	$db    = GMList::getDB( 'tier' );
	$table = $db->getTable();
	$table = $db->searchTable( $table , 'kickback_id' , '=' , $iKickbackID_ );
	$row   = $db->getRow( $table );

	$nDB = GMList::getDB( 'nUser' );

	for( $i = 0 ; $row > $i ; ++$i )
	{
		$rec     = $db->getRecord( $table , $i );
		$nUserID = $db->getData( $rec , 'owner' );
		$point   = $db->getData( $rec , 'point' );

		$nRec = $nDB->selectRecord( $nUserID );

		if( $KICKBACK_STATE_ON == $iState_ )
			{ $nDB->setCalc( $nRec , 'point' , '+' , $point ); }
		else
			{ $nDB->setCalc( $nRec , 'point' , '-' , $point ); }

		$nDB->updateRecord( $nRec );
	}
}

function addClickPoint( $iUserRec_ )
{
	$nUserDB       = GMList::getDB( 'nUser' );
	$lastClickTime = $nUserDB->getData( $iUserRec_ , 'last_click_point_time' );
	$thisTime      = mktime( 0 , 0 , 0 , date( 'n' ) , date( 'j' ) , date( 'Y' ) );

	if( $thisTime > $lastClickTime ) //前回のクリックから日が経っている場合
	{
		$systemDB     = GMList::getDB( 'system' );
		$systemRec    = $systemDB->selectRecord( 'ADMIN' );
		$clickPoint   = $systemDB->getData( $systemRec , 'click_point' );
		$currentPoint = $nUserDB->getData( $iUserRec_ , 'point' );

		$nUserDB->setData( $iUserRec_ , 'point' , $currentPoint + $clickPoint );
		$nUserDB->setData( $iUserRec_ , 'last_click_point_time' , $thisTime );
		$nUserDB->updateRecord( $iUserRec_ );

		return true;
	}

	return false;
}

function fileWrite( $file_name , $html ){
	
	 if(!$f = fopen($file_name,'w')){
	 	return;
	 }

	 if(fwrite($f,$html) === FALSE ){
		fclose($f);
	 	return;
	 }

	fclose($f);
}
function fileRead( $file_name ){
	$html = file_get_contents($file_name);
	return $html;
}
?>