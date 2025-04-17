<?php

/**
 * �V�X�e���R�[���N���X
 * 
 * @author ----
 * @version 1.0.0
 * 
 */
class sound_sourceSystem extends System {
    /*     * ********************************************************************************************************
     * �ėp�V�X�e���p���\�b�h
     * ******************************************************************************************************** */



    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // �o�^�֌W
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * �o�^�O�i�K�����B
     * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
     *
     * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
     * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
     */
    function registProc(&$gm, &$rec, $loginUserType, $loginUserRank, $check = false) {
        // ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
        // **************************************************************************************
        global $LOGIN_ID;
        global $ACTIVE_NONE;

        $limitTime = mktime(0, 0, 0, $_POST['limit_month'], $_POST['limit_day'] + 1, $_POST['limit_year']);

        $db = $gm[$_GET['type']]->getDB();

        $db->setData($rec, 'limit_time', $limitTime);
        $db->setData( $rec , 'activate' , $ACTIVE_NONE );

        parent::registProc($gm, $rec, $loginUserType, $loginUserRank, $check);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // �ҏW�֌W
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * �ҏW�O�i�K�����B
     * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
     *
     * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
     * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
     */
    function editProc(&$gm, &$rec, $loginUserType, $loginUserRank, $check = false) {
        // ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
        // **************************************************************************************

        $limitTime = mktime(0, 0, 0, $_POST['limit_month'], $_POST['limit_day'] + 1, $_POST['limit_year']);

        $db = $gm[$_GET['type']]->getDB();

        $db->setData($rec, 'limit_time', $limitTime);

        AdwaresLogic::setSelectCarrierName($rec);

        parent::editProc($gm, $rec, $loginUserType, $loginUserRank, $check);
    }

    /**
     * ���������B
     * �t�H�[�����͈ȊO�̕��@�Ō���������ݒ肵�����ꍇ�ɗ��p���܂��B
     *
     * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
     * @param table �t�H�[���̂���̓��͓��e�Ɉ�v���郌�R�[�h���i�[�����e�[�u���f�[�^�B
     */
    function searchProc(&$gm, &$table, $loginUserType, $loginUserRank) {
        // ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
        global $LOGIN_ID;
        global $terminal_type;
        global $TABLE_PREFIX;
        // **************************************************************************************

        $type = SearchTableStack::getType();
        $db = $gm[ $type ]->getDB();

        switch($loginUserType){
            case 'admin':
                // �ꗗ�ŏ��F�󋵕ύX
                if (isset($_POST) && isset($_POST['id'])) {
                    $rec = $db->selectRecord($_POST['id']);
                    if( !is_null($rec) ) {
                        $db->setData($rec, 'activate', $_POST['activate']);
                        $db->updateRecord($rec);
                    }
                    unset($_POST['id']);
                    unset($_POST['activate']);
                }
                // �ڕW�B�����H
                if(isset($_GET['success'])){
                    $target_point = 'target_point';
                    $voted_point = 'voted_point';
                    if(!empty($TABLE_PREFIX)){
                        $target_point = $TABLE_PREFIX.'.'.$target_point;
                        $voted_point = $TABLE_PREFIX.'.'.$voted_point;
                    }
                    $table->addWhere($target_point.' <= '.$voted_point);
//                    $table = $db->searchTable($table, 'target_point', '<=', 'voted_point');
                }
                break;
            case 'cUser':
                $table = $db->searchTable($table, 'owner', '=', $LOGIN_ID);
                break;
        }

        $table = WS::Finder($type)->searchQueryTable($table);
        $table = WS::Finder($type)->searchReadableTable($table);

        if (!$_GET['sort']) {
            $table = WS::Finder($type)->sortTable($table);
        }

        parent::searchProc($gm, $table, $loginUserType, $loginUserRank);
    }

    /**
     * �o�^���e�m�F�B
     *
     * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
     * @param edit �ҏW�Ȃ̂��A�V�K�ǉ��Ȃ̂���^�U�l�œn���B
     * @return �G���[�����邩��^�U�l�œn���B
     */
    function registCheck(&$gm, $edit, $loginUserType, $loginUserRank) {
        // ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
        // **************************************************************************************

        $result = parent::registCheck($gm, $edit, $loginUserType, $loginUserRank);
        return $result;
    }

}

?>