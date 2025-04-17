<?php

/*****************************************************************
 ** �ėp�v���O�����iregist.php / search.php / info.php�j�p ��` **
 *****************************************************************/
/**********          �e�[�u����`          **********/


/**********          admin�̒�`          **********/

	$EDIT_TYPE                            = 'admin';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = true;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/admin.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/admin.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'ADMIN';
	$ID_LENGTH[ $EDIT_TYPE ]              = 5;


/**********          access�̒�`          **********/

	$EDIT_TYPE                            = 'access';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/access.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/access.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'AC';
	$ID_LENGTH[ $EDIT_TYPE ]              = 9;


/**********          click_log�̒�`          **********/

	$EDIT_TYPE                            = 'click_log';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/click_log.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/click_log.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'C';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;


/**********          adwares�̒�`          **********/

	$EDIT_TYPE                            = 'adwares';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/adwares.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/adwares.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'AC';
	$ID_LENGTH[ $EDIT_TYPE ]              = 9;

/**********          category�̒�`          **********/

	$EDIT_TYPE                            = 'category';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/category.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/category.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'CATE';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;

/**********          kickback�̒�`          **********/

	$EDIT_TYPE                            = 'kickback';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/kickback.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/kickback.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'K';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;


/**********          multimail�̒�`          **********/

	$EDIT_TYPE                            = 'multimail';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/multimail.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/multimail.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'MM';
	$ID_LENGTH[ $EDIT_TYPE ]              = 9;


/**********          nUser�̒�`          **********/

	$EDIT_TYPE                               = 'nUser';
	$TABLE_NAME[]                            = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ]    = true;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]      = false;
	$THIS_TABLE_IS_QUICK[ $EDIT_TYPE ]       = true;
	$THIS_TABLE_IS_STEP_PC[ $EDIT_TYPE ]     = false;
	$THIS_TABLE_IS_STEP_MOBILE[ $EDIT_TYPE ] = true;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]           = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]        = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]       = 'pass2';
	$LST[ $EDIT_TYPE ]                       = './lst/nuser.csv';
	$TDB[ $EDIT_TYPE ]                       = './tdb/nuser.csv';
	$ID_HEADER[ $EDIT_TYPE ]                 = 'N';
	$ID_LENGTH[ $EDIT_TYPE ]                 = 8;

/**********          cUser�̒�`          **********/

	$EDIT_TYPE                               = 'cUser';
	$TABLE_NAME[]                            = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ]    = true;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]      = false;
	$THIS_TABLE_IS_QUICK[ $EDIT_TYPE ]       = true;
	$THIS_TABLE_IS_STEP_PC[ $EDIT_TYPE ]     = false;
	$THIS_TABLE_IS_STEP_MOBILE[ $EDIT_TYPE ] = true;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]           = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]        = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]       = 'pass2';
	$LST[ $EDIT_TYPE ]                       = './lst/cuser.csv';
	$TDB[ $EDIT_TYPE ]                       = './tdb/cuser.csv';
	$ID_HEADER[ $EDIT_TYPE ]                 = 'C';
	$ID_LENGTH[ $EDIT_TYPE ]                 = 8;


/**********          payment�̒�`          **********/

	$EDIT_TYPE                            = 'payment';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/payment.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/payment.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'P';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;


/**********          prefectures�̒�`          **********/

	$EDIT_TYPE                            = 'prefectures';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/prefectures.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/prefectures.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'PF';
	$ID_LENGTH[ $EDIT_TYPE ]              = 4;


/**********          sendmail�̒�`          **********/

	$EDIT_TYPE                            = 'sendmail';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/sendmail.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/sendmail.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'SM';
	$ID_LENGTH[ $EDIT_TYPE ]              = 9;


/**********          template�̒�`          **********/

	$EDIT_TYPE                            = 'template';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/template.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/template.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'T';
	$ID_LENGTH[ $EDIT_TYPE ]              = 5;


/**********          system�̒�`          **********/

	$EDIT_TYPE                            = 'system';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/system.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/system.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = '';
	$ID_LENGTH[ $EDIT_TYPE ]              = 0;


/**********          page�̒�`          **********/

	$EDIT_TYPE                            = 'page';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = null;
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = null;
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = null;
	$LST[ $EDIT_TYPE ]                    = './lst/page.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/page.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'P';
	$ID_LENGTH[ $EDIT_TYPE ]              = 6;


/**********          page�̒�`          **********/

	$EDIT_TYPE                            = 'asp_type';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = null;
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = null;
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = null;
	$LST[ $EDIT_TYPE ]                    = './lst/asp_type.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/asp_type.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'ASP';
	$ID_LENGTH[ $EDIT_TYPE ]              = 7;

/**********          tier�̒�`          **********/

	$EDIT_TYPE                            = 'tier';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = null;
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = null;
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = null;
	$LST[ $EDIT_TYPE ]                    = './lst/tier.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/tier.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'TR';
	$ID_LENGTH[ $EDIT_TYPE ]              = 10;

	