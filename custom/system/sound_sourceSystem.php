<?php

/**
 * システムコールクラス
 * 
 * @author ----
 * @version 1.0.0
 * 
 */
class sound_sourceSystem extends System {
    /*     * ********************************************************************************************************
     * 汎用システム用メソッド
     * ******************************************************************************************************** */



    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // 登録関係
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 登録前段階処理。
     * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
     *
     * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
     * @param rec フォームのからの入力データを反映したレコードデータ。
     */
    function registProc(&$gm, &$rec, $loginUserType, $loginUserRank, $check = false) {
        // ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
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
    // 編集関係
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 編集前段階処理。
     * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
     *
     * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
     * @param rec フォームのからの入力データを反映したレコードデータ。
     */
    function editProc(&$gm, &$rec, $loginUserType, $loginUserRank, $check = false) {
        // ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
        // **************************************************************************************

        $limitTime = mktime(0, 0, 0, $_POST['limit_month'], $_POST['limit_day'] + 1, $_POST['limit_year']);

        $db = $gm[$_GET['type']]->getDB();

        $db->setData($rec, 'limit_time', $limitTime);

        AdwaresLogic::setSelectCarrierName($rec);

        parent::editProc($gm, $rec, $loginUserType, $loginUserRank, $check);
    }

    /**
     * 検索処理。
     * フォーム入力以外の方法で検索条件を設定したい場合に利用します。
     *
     * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
     * @param table フォームのからの入力内容に一致するレコードを格納したテーブルデータ。
     */
    function searchProc(&$gm, &$table, $loginUserType, $loginUserRank) {
        // ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
        global $LOGIN_ID;
        global $terminal_type;
        global $TABLE_PREFIX;
        // **************************************************************************************

        $type = SearchTableStack::getType();
        $db = $gm[ $type ]->getDB();

        switch($loginUserType){
            case 'admin':
                // 一覧で承認状況変更
                if (isset($_POST) && isset($_POST['id'])) {
                    $rec = $db->selectRecord($_POST['id']);
                    if( !is_null($rec) ) {
                        $db->setData($rec, 'activate', $_POST['activate']);
                        $db->updateRecord($rec);
                    }
                    unset($_POST['id']);
                    unset($_POST['activate']);
                }
                // 目標達成か？
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
     * 登録内容確認。
     *
     * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
     * @param edit 編集なのか、新規追加なのかを真偽値で渡す。
     * @return エラーがあるかを真偽値で渡す。
     */
    function registCheck(&$gm, $edit, $loginUserType, $loginUserRank) {
        // ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
        // **************************************************************************************

        $result = parent::registCheck($gm, $edit, $loginUserType, $loginUserRank);
        return $result;
    }

}

?>