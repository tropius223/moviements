<?php
/*****************************************************************************
 *
 * �萔�錾
 *
 ****************************************************************************/

	define( "WS_PACKAGE_ID", "pointget" );				//�Z�b�V�����L�[prefix�ȂǂɎg���A�p�b�P�[�W�����ʂ���ׂ�ID

	$NOT_LOGIN_USER_TYPE							 = 'nobody';							// ���O�C�����Ă��Ȃ���Ԃ̃��[�U��ʖ�
	$NOT_HEADER_FOOTER_USER_TYPE					 = 'nothf';								// �w�b�_�[�E�t�b�^�[��\�����Ă��Ȃ���Ԃ̃��[�U��ʖ�

	$LOGIN_KEY_FORM_NAME							 = 'mail';								// ���O�C���t�H�[���̃L�[����͂���t�H�[����
	$LOGIN_PASSWD_FORM_NAME							 = 'passwd';							// ���O�C���t�H�[���̃p�X���[�h����͂���t�H�[����

	$ADD_LOG										 = true;								// DB�̐V�K�ǉ������L�^���邩
	$UPDATE_LOG										 = true;								// DB�̍X�V�����L�^���邩
	$DELETE_LOG										 = true;								// DB�̍폜�����L�^���邩

	$SESSION_NAME									 = WS_PACKAGE_ID.'loginid';							// ���O�C�������Ǘ�����SESSION �̖��O
	$COOKIE_NAME									 = WS_PACKAGE_ID.'loginid';							// ���O�C�������Ǘ�����COOKIE �̖��O

	$ACTIVE_NONE									 = 1;									// �A�N�e�B�x�[�g����Ă��Ȃ���Ԃ�\���萔
	$ACTIVE_ACTIVATE	 							 = 2;									// �A�N�e�B�x�[�g����Ă����Ԃ�\���萔
	$ACTIVE_ACCEPT		 							 = 4;									// ������Ă����Ԃ�\���萔
	$ACTIVE_DENY		 							 = 8;									// ���ۂ���Ă����Ԃ�\���萔
	$ACTIVE_ALL	 									 = 15;

	$IMAGE_NOT_FOUND								= '';

	$terminal_type = 0;
	$sid = "";

    $template_path                                   = "./template/pc/";
	$system_path                          	         = "./custom/system/";
	$page_path										 = "./file/page/";

	$FORM_TAG_DRAW_FLAG	 							 = 'variable';					//  buffer/variable

	$DB_LOG_FILE									 = "./logs/dbaccess.log";				// �f�[�^�x�[�X�A�N�Z�X���O�t�@�C��
	$COOKIE_PATH 									 = '/';

	$MAX_FILE_SIZE = 512000;

	//kickback�̏��
	$KICKBACK_STATE_OFF	= 1;
	$KICKBACK_STATE_ON	= 2;
	$KICKBACK_STATE_DEF	= $KICKBACK_STATE_ON;

/***************************
 ** �ݒ�t�@�C���̓ǂݍ���**
 ***************************/

	include_once "./custom/extends/sqlConf.php";
	include_once "./custom/extends/mobileConf.php";
	include_once "./custom/extends/tableConf.php";
	include_once "./custom/extends/exceptionConf.php";
	include_once "./custom/extends/modelConf.php";
	include_once "./custom/extends/logicConf.php";

/*************************
 *  �g���N���X�̓ǂݍ��� *
 *************************/

	//include_once "./include/extends/";
	//include_once "./include/extends/MobileUtil.php";


/***************************
 ** LINK&JS IMPORT�֘A **
 ****************************/

    $css_file_paths['nobody']['import'] = './common/css/style.css';
    $css_file_paths['nUser']['import']  = './common/css/style.css';
    $css_file_paths['cUser']['import']  = './common/css/style.css';
    $css_file_paths['admin']['import']  = './common/css/system/admin.css';

    $js_file_paths['all']['jquery']        = './common/js/jquery-1.6.4.min.js';
    $js_file_paths['all']['wsAutoBind']    = './common/js/ws.autoBind.js';
    $js_file_paths['all']['wsBindMethods'] = './common/js/ws.bindMethods.js';
    $js_file_paths['nobody']['textbox']    = './common/js/textbox_focus.js';
    $js_file_paths['nobody']['biggerlink'] = './common/js/jquery.biggerlink.js';
    $js_file_paths['nUser']['textbox']     = './common/js/textbox_focus.js';
    $js_file_paths['cUser']['textbox']     = './common/js/textbox_focus.js';
    $js_file_paths['nUser']['biggerlink']  = './common/js/jquery.biggerlink.js';
    $js_file_paths['cUser']['biggerlink']  = './common/js/jquery.biggerlink.js';
    $js_file_paths['admin']['droppy']      = './common/js/jquery.droppy.js';
    $js_file_paths['admin']['odd']         = './common/js/odd.js';

?>