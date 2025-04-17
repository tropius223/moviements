<?php

    //テーブル定義
    $EDIT_TYPE                            = 'sound_source';
    $TABLE_NAME[]                         = $EDIT_TYPE;
    $THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
    $THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
    $LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = null;
    $LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = null;
    $LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = null;
    $LST[ $EDIT_TYPE ]                    = './lst/module/sound_source.csv';
    $TDB[ $EDIT_TYPE ]                    = './tdb/module/sound_source.csv';
    $ID_HEADER[ $EDIT_TYPE ]              = 'S';
    $ID_LENGTH[ $EDIT_TYPE ]              = 8;

class mod_sound_source extends command_base {

    function drawIndexList($iGM_, $iRec_, $iArgs_) {
        global $loginUserType;
        global $loginUserRank;

        $maxNum = array_shift($iArgs_);

        if (!$maxNum) {
            $maxNum = 12;
        }

        $gm = GMList::getGM(self::$type);
        $db = $gm->getDB();
        $table = $db->getTable();
        $table = WS::Finder(self::$type)->SearchReadableTable($table);
        $table = WS::Finder(self::$type)->SortTable($table);
        $table = $db->limitOffset($table, 0, $maxNum);
        $row = $db->getRow($table);

        $templatePath = Template::getTemplate($loginUserType, $loginUserRank, 'sound_source', 'EX_INDEX_LIST_DESIGN');

        $this->addBuffer($gm->getString($templatePath, null, 'head'));

        for ($i = 0; $row > $i; ++$i) {
            $rec = $db->getRecord($table, $i);

            $this->addBuffer($gm->getString($templatePath, $rec, 'element'));
        }

        $this->addBuffer($gm->getString($templatePath, null, 'foot'));
    }

    function drawSuccess($iGM_, $iRec_, $iArgs_) {
        global $loginUserType;
        global $loginUserRank;
        global $TABLE_PREFIX;
        global $ACTIVE_ACCEPT;

        $gm = GMList::getGM(self::$type);
        $db = $gm->getDB();
        $table = $db->getTable();
        $table = WS::Finder(self::$type)->SearchReadableTable($table);
        $table = WS::Finder(self::$type)->SortTable($table);

        $target_point = 'target_point';
        $voted_point = 'voted_point';
        if(!empty($TABLE_PREFIX)){
            $target_point = $TABLE_PREFIX.'.'.$target_point;
            $voted_point = $TABLE_PREFIX.'.'.$voted_point;
        }
        $table->addWhere($target_point.' <= '.$voted_point);
        $table = $db->searchTable($table, 'activate','!',$ACTIVE_ACCEPT);
        $row = $db->getRow($table);

        $this->addBuffer($row);
    }

    private static $type = 'sound_source';

}
