<?php

//テーブル定義
$EDIT_TYPE = 'vote';
$TABLE_NAME[] = $EDIT_TYPE;
$THIS_TABLE_IS_USERDATA[$EDIT_TYPE] = false;
$THIS_TABLE_IS_NOHTML[$EDIT_TYPE] = false;
$LOGIN_KEY_COLUM[$EDIT_TYPE] = null;
$LOGIN_PASSWD_COLUM[$EDIT_TYPE] = null;
$LOGIN_PASSWD_COLUM2[$EDIT_TYPE] = null;
$LST[$EDIT_TYPE] = './lst/module/vote.csv';
$TDB[$EDIT_TYPE] = './tdb/module/vote.csv';
$ID_HEADER[$EDIT_TYPE] = 'V';
$ID_LENGTH[$EDIT_TYPE] = 8;

    class mod_vote extends command_base {
        
    }