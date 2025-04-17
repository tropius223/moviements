<?php

	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * html�t�@�C���ǂݍ��݃N���X
	 *  �e���v���[�g�擾�E�`�惁�\�b�h��static�ȃ��\�b�h�ł��B
     *  �C���X�^���X�𐶐������ɗ��p���Ă��������B
     *
	 * @author �g�� �K��Y
	 * @version 1.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/

	class Template
	{
        private static $_DEBUG	 = false;
        private static $template_cash = Array();
        static function getTemplate( $usertype , $activate , $target , $label , $owner = null ){
            global $NOT_LOGIN_USER_TYPE;
            global $LOGIN_ID;
            global $template_path;
            
            if(isset(self::$template_cash[$target.$label])){
                if(self::$_DEBUG){ d( "getTemplate() : cash load(".self::$template_cash[$target.$label].")\n","template"); d(func_get_args(),"args");}
                return self::$template_cash[$target.$label];
            }
            $tgm = SystemUtil::getGMforType("template");
            $tdb = $tgm->getDB();

            $table = $tdb->getTable();
            $table = $tdb->searchTable( $table , 'label' , '==' , $label );
            $table = $tdb->searchTable( $table , 'user_type' , '=' , "%/".$usertype."/%" );
            if(strlen($target))
                $table = $tdb->searchTable( $table , 'target_type' , '==' , $target );
            else
                $table = $tdb->searchTable( $table , 'target_type' , 'isnull' , $target );
            $table = $tdb->searchTable( $table , 'activate' , '&' , $activate , '=');

            if(is_null($owner)){
                if( $usertype === $NOT_LOGIN_USER_TYPE ) { $owner = 2; }
                else{
                    if( $target == $usertype ){
                        if(  isset( $_GET['id'] ) && $_GET['id'] == $LOGIN_ID  )	 { $owner = 1; }
                        else { $owner = 2; }
                    }else { $owner = 2; }
                }
            }
            $table = $tdb->searchTable( $table , 'owner' , '&' , $owner , '=');
            
            
            if( $tdb->getRow($table) != 0 ){
                self::$template_cash[$target.$label] = $template_path.$tdb->getData( $tdb->getRecord( $table , 0 ) , 'file' );
                if(self::$_DEBUG){ d( "getTemplate() : hit=".self::$template_cash[$target.$label]."\n","template"); d(func_get_args(),"args");}
                return self::$template_cash[$target.$label];
            }
            if(self::$_DEBUG){ d( "getTemplate() : no hit\n","template"); d(func_get_args(),"args");}
            return "";
        }
        
        static function drawTemplate( $gm , $rec , $usertype , $activate , $target , $label , $form = false , $owner = null, $partkey = null, $form_flg = null){
            print Template::getTemplateString( $gm , $rec , $usertype , $activate , $target , $label , $form , $owner , $partkey, $form_flg );
        }
        
        static function getTemplateString( $gm , $rec , $usertype , $activate , $target , $label , $form = false , $owner = null , $partkey = null, $form_flg = null ){
            $file = Template::getTemplate( $usertype , $activate , $target , $label , $owner );

            if( ! strlen($file) ){
                $html = IncludeObject::get( Template::getErrorTemplate() );
            }else if( is_null($gm) ){
                $html = IncludeObject::get( $file );
            }else if($form){
                $html = $gm->getFormString( $file, $rec , $form ,$partkey , $form_flg);
            }else{
                $html = $gm->getString( $file , $rec , $partkey );
            }
            
            return $html;
        }
        
        static function drawListTemplate( $gm , $table , $usertype , $activate , $target , $label , $owner = null, $partkey = null ){
            print Template::getListTemplateString( $gm , $table , $usertype , $activate , $target , $label , $owner , $partkey );
        }
        
        static function getListTemplateString( $gm , $table , $usertype , $activate , $target , $label , $owner = null, $partkey = null){
            $file = Template::getTemplate( $usertype , $activate , $target , $label , $owner );
            if( ! strlen($file) ){
                $html = $gm->getString( Template::drawErrorTemplate() );
            }else{
                $html = $gm->getListString( $file , $table , $partkey );
            }
            return $html;
        }
        
        
        static function simpleDrawTemplate( $label ){
            $file = Template::getLabelFile( $label );
            print IncludeObject::get( $file );
        }
        static function simpleGetTemplate( $label ){
            $file = Template::getLabelFile( $label );
            return IncludeObject::get( $file );
        }
        
        static function getErrorTemplate(){
            return Template::getLabelFile( "ERROR_PAGE_DESIGN" );
        }
        static function drawErrorTemplate(){
        global $gm;
        global $loginUserType;
        global $NOT_LOGIN_USER_TYPE;
            if(  $loginUserType == $NOT_LOGIN_USER_TYPE  )
                 print $gm['system']->getString( Template::getErrorTemplate() , null ,null );
            else
                 print $gm[$loginUserType]->getString( Template::getErrorTemplate() , null ,null );
        }
	
        static function getLabelFile( $label ){
            global $template_path;
            $tgm = SystemUtil::getGMforType("template");
            $tdb = $tgm->getDB();
            $table = $tdb->searchTable( $tdb->getTable() , 'label' , '==' , $label );
            $file = $tdb->getData( $tdb->getRecord(  $table , 0 ) , 'file' );
			if( !$file )
				return null;
			else
				return $template_path.$file;
        }
        
        static function onDebug(){ self::$_DEBUG = true; }
        static function offDebug(){ self::$_DEBUG = false; }
		
	}

	/********************************************************************************************************/
?>