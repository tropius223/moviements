<?PHP


//その他の定義
//    $JS_IMPORT_LIST['report.report'] = './js/module/report/report.js';


/*
 * report_list[ ユーザーRANK ]['table']
 *   table名をkeyにして、要素がreport名
 *
 * report_list[ ユーザーRANK ]['report'] = reports;
 *
 * reportsは以下、出力ファイル名をkeyとするreportの配列
 *
 * report[case_label] = ラベル
 * report[table_name] = テーブル名
 * report[head_name][] = カラム名
 * report[colum_name][] = カラムの内容(カラム名か関数)
 * 以下、上の2つをループ
 *
 
//sample
        $report['case_label'] = '企業ユーザー 一覧';
        $report['table_name'] = 'cUser';
        
        $report['head_name'][] = 'No';
        $report['colum_name'][] = 'f:no';

        $report['head_name'][] = 'ID';
        $report['colum_name'][] = 'id';
        
        $report['head_name'][] = '名前';
        $report['colum_name'][] = 'name';
        
        $head_name[$nameme][] = '登録日時';
        $report['colum_name'][] = 'f:date:regist';

        $reports["cUser_list"] = $report;
 
 */
 
class report_conf{
//スタティック変数の定義

//変数の定義
    var $report_list = Array();

    // コンストラクタ
    private function __construct() {
		//変数内容の設定
        //tdbからのコピーを許可するtable
        $this->report_list['admin']['table'] = Array( 'nUser' => '一般ユーザー' );
        $reports = Array();


//job
        $report['case_label'] = 'ジョブ 一覧';
        $report['table_name'] = 'job';
        $report['dm'] = true;
        
        $report['head_name'][] = 'No';
        $report['colum_name'][] = 'f:no';

        $report['head_name'][] = 'ID';
        $report['colum_name'][] = 'id';
        
        $report['head_name'][] = '名前';
        $report['colum_name'][] = 'sub';
        
        $report['head_name'][] = '職種';
        $report['colum_name'][] = 'rm:job_form:job_form:id:name';
        
        $report['head_name'][] = '登録日時';
        $report['colum_name'][] = 'f:date:regist';
        $reports["job"] = $report;


//cUser
        $report['case_label'] = '求人企業ユーザー 一覧';
        $report['table_name'] = 'cUser';
        $report['dm'] = true;
        
        $report['head_name'][] = 'No';
        $report['colum_name'][] = 'f:no';

        $report['head_name'][] = 'ID';
        $report['colum_name'][] = 'id';
        
        $report['head_name'][] = '名前';
        $report['colum_name'][] = 'name';
        
        $report['head_name'][] = '登録日時';
        $report['colum_name'][] = 'f:date:regist';
        $reports["cUser"] = $report;

        $this->report_list['admin']['report'] = $reports;
        
    }

    

    
    private static $singleton = null;

    // インスタンスを生成する
    public static function getInstance() {
        if (self::$singleton == null) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }
}

?>