<?php

    //テーブル定義
    $EDIT_TYPE                            = 'paid_point';
    $TABLE_NAME[]                         = $EDIT_TYPE;
    $THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
    $THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
    $LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = null;
    $LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = null;
    $LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = null;
    $LST[ $EDIT_TYPE ]                    = './lst/module/paid_point.csv';
    $TDB[ $EDIT_TYPE ]                    = './tdb/module/paid_point.csv';
    $ID_HEADER[ $EDIT_TYPE ]              = 'P';
    $ID_LENGTH[ $EDIT_TYPE ]              = 8;

class mod_paid_point extends command_base {

    function getMaxExchangeYen($iGM_, $iRec_, $iArgs_){
        $sound_source_id = $iArgs_[0];
        $voted_point = SystemUtil::getTableData( 'sound_source'  , $sound_source_id , 'voted_point' );
        $paid_point = SystemUtil::getTableData( 'sound_source'  , $sound_source_id , 'paid_point' );
        $point = $voted_point - $paid_point;
        $rate  = SystemUtil::getTableData( 'system' , 'ADMIN'   , 'point_to_yen_rate' );

        $this->addBuffer( $point * $rate );
    }
    function getPointBalance($iGM_, $iRec_, $iArgs_){
        $sound_source_id = $iArgs_[0];
        $voted_point = SystemUtil::getTableData( 'sound_source'  , $sound_source_id , 'voted_point' );
        $paid_point = SystemUtil::getTableData( 'sound_source'  , $sound_source_id , 'paid_point' );
        $point = $voted_point - $paid_point;

        $this->addBuffer( $point );
    }

    private static $type = 'sound_source';

}
