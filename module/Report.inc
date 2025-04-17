<?PHP

 include_once "./custom/module/report_conf.inc";


class mod_report extends command_base
{
    var $TIME_FORMAT = 'Y m/d H:i:s';

    function date(&$db , $rec , $colum = 'regist' ){
        global $TIME_FORMAT;
        return date( $TIME_FORMAT , $db->getData( $rec , $colum ) );
    }

    function no( &$db , $rec , &$no_cnt ){
        return ++$no_cnt;
    }
    function no_init( &$no_cnt ){
        $no_cnt=0;
    }

    function nomal( &$db , $rec , $colum ){
        return $db->getData( $rec , $colum);
    }

    function relational( &$db , $rec , $arg ){
        return $arg['db']->getData(
             $arg['db']->getRecord(
                  $arg['db']->searchTable(
                      $arg['db']->getTable() , $arg['key'] , '=' , $db->getData( $rec , $arg['id'] )
                      )
                  , 0 )
              , $arg['data'] );
    }

    function relational_init( $arg ){
    //job_type:id:name
        global $gm;
        
        $db = $gm[$arg[0]]->getDB();
        
        return Array( 'db' => $db , 'id' => $arg[1] ,  'key' => $arg[2] , 'data' => $arg[3] );
    }

    //multiple relational
    function mrelational( &$db , $rec , $arg ){
        $datas = $db->getData( $rec , $arg['id'] );
        $datas_array = explode( '/' , $datas );
        $str = '';
        foreach( $datas_array as $data ){
        
            $str .= $arg['db']->getData(
                $arg['db']->getRecord(
                    $arg['db']->searchTable( $arg['db']->getTable() , $arg['key'] , '=' , $data )
                    , 0 )
                , $arg['data'] ).'/';
        
        }
        return substr($str , 0 , -1);
    }


    /////////////////////mod tag


    function draw_case_list( &$gm, $rec, $args ){
    global $loginUserType;
    global $loginUserRank;
        $obj = report_conf::getInstance();
    
        $html = Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null, 'case_list_head' );

        foreach( $obj->report_list[$loginUserType]['report'] as $name  => $report ){
            $gm->setVariable( 'name' , $report['case_label'] );
            $gm->setVariable( 'id' , $name );
            
            if($report['dm'])
                $html .= Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null, 'case_list_dm');
            else
                $html .= Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null, 'case_list');
    
        }
        $html .= Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null,'case_list_foot');

        $this->addBuffer( $html );
    }

    function draw_type_list( &$gm, $rec, $args ){
    global $loginUserType;
    global $loginUserRank;
        $obj = report_conf::getInstance();

        $html = Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null, 'type_list_head');
        
        foreach( $obj->report_list[$loginUserType]['table'] as $table_name  => $table_label ){
            $gm->setVariable( 'name' , $table_label );
            $gm->setVariable( 'id' , $table_name );
        
            $html .= Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null, 'type_list');
    
        }
        $html .= Template::getTemplateString( $gm, null, 'report', $loginUserRank, '', 'REPORT_PARTS' , false, null,'type_list_foot');

        $this->addBuffer( $html );
    }
    
    function user_type_check($user_type){
        $obj = report_conf::getInstance();
        return isset($obj->report_list[$user_type]);
    }
}

?>