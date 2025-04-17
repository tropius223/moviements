<?php

include_once "./include/OutputLog.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQL�f�[�^�x�[�X�V�X�e���\�z�p���b�N
 * �������Ɍp������K�v�͂Ȃ�
 *
 * @author �g���K��Y
 * @original �O�H��q
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

interface DatabaseBase
{

	/**
	 * ���R�[�h���擾���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @param $index �擾���郌�R�[�h�ԍ�
	 * @return ���R�[�h�f�[�^
	 */
	function getRecord($table, $index);

	/**
	 * ���R�[�h���擾���܂��B
	 *
	 * @param $id �擾���郌�R�[�hID
	 * @param $type ����ΏۂƂȂ�e�[�u����type(nomal/delete/all)
	 * @return ���R�[�h�f�[�^�B���R�[�h�f�[�^�����݂��Ȃ��ꍇnull��Ԃ��B
	 */
	function selectRecord( $id , $type = null);

	/**
	 * �e�[�u������w�肵�����R�[�h���폜���܂��B
	 * @param table �e�[�u���f�[�^
	 * @param rec �폜�ΏۂƂȂ郌�R�[�h
	 * @return �e�[�u���f�[�^
	 */
	function pullRecord($table, $rec);

	/**
	 * �f�[�^�̓��e���擾����B
	 * @param $rec ���R�[�h�f�[�^
	 * @param $name �J������
	 * @return �l
	 */
	function getData($rec, $name, $br = false);

	/**
	 * ���R�[�h�̓��e���X�V����B
	 * DB�t�@�C���ւ̍X�V���܂݂܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function updateRecord($rec);

	/**
	 * ���R�[�h�̍폜�B
	 * DB�t�@�C���ւ̔��f���s���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function deleteRecord(&$rec);

	/**
	 * where�ɂ���đI�������e�[�u���̍s���폜���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @return �s��
	 */
	function deleteTable($table);

	/**
	 * �f�[�^���Z�b�g����B
	 * @param $rec ���R�[�h�f�[�^
	 * @param $name �J������
	 * @param $val �l
	 */
	function setData(&$rec, $name, $val);

	/**
	 * �ȈՂȉ��Z���s�Ȃ��A���ʂ��Z�b�g����B
	 * �J���������l�^�Ŗ����ꍇ�͖���
	 *
	 * @param $rec ���R�[�h�f�[�^
	 * @param $name �J������
	 * @param $opp ���Z�q
	 * @param $name �l
	 */
	function setCalc(&$rec, $name , $opp , $val );

	/**
	 * �����Ƃ��ēn���ꂽtable�̑S�s�Ƀf�[�^���Z�b�g����update����B
	 *
	 * @param $table �X�V���s�Ȃ��J�����̓�����table
	 * @param $name �J������
	 * @param $val �l
	 */
	function setTableDataUpdate(&$table, $name, $val);
	
	/**
	 * ���R�[�h�Ƀf�[�^���܂Ƃ߂ăZ�b�g���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 * @param data �f�[�^�A�z�z��i�Y�����̓J�������j
	 */
	function setRecord(&$rec, $data);

	/**
	 * �V�������R�[�h���擾���܂��B
	 * �f�t�H���g�l���w�肵�����ꍇ��
	 * $data['�J������']�̘A�z�z��ŏ����l���w�肵�Ă��������B
	 * @param data �����l��`�A�z�z��
	 * @return ���R�[�h�f�[�^
	 */
	function getNewRecord($data = null);

	function setFile(&$rec, $colname);

	/**
	 * ���R�[�h�̒ǉ��B
	 * DB�ւ̔��f�������ɍs���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function addRecord($rec);

	/**
	 * DB�����e�[�u�����擾���܂��B
	 * @return �e�[�u���f�[�^
	 * @param $type table type(nomal/delete/all)
	 */
	function getTable($type = null);

	/**
	 * �e�[�u���̍s�����擾���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @return �s��
	 */
	function getRow($table);

	/**
	 * �e�[�u���̌������s���܂��B
	 * ���p�ł��鉉�Z�q�͈ȉ��̂��̂ł��B
	 * >, <	 �s�������Z�q
	 * =	 �������Z�q
	 * !	 �񓙍����Z�q
	 * b	 �r�g�D�C�[�����Z�q
	 * �r�g�D�C�[�����Z�q�̏ꍇ�̂�$val2���w�肵�܂��B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $opp ���Z�q
	 * @param $val �l�P
	 * @param $val2 �l�Q
	 */
	function searchTable(&$tbl, $name, $opp, $val, $val2 = null);

	/**
	 * ��̃e�[�u����Ԃ��B
	 * search�̌��ʂ���ɂ����肷�鎞�Ɏg�p�B
	 */
	/**
	 * @return unknown_type
	 */
	function getEmptyTable();

	/**
	 * ���R�[�h���\�[�g���܂��B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $asc �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 */
	function sortTable(&$tbl, $name, $asc);

	/**
	 * �e�[�u���̘_���a�B
	 * @param $table1 �e�[�u���f�[�^
	 * @param $table2 �e�[�u���f�[�^
	 * @return �e�[�u���f�[�^
	 */
	function orTable(&$tbl, &$table2);
	
	/**
	 * �e�[�u���̘_���a�B(�ψ����Ή�
	 * @param $a �e�[�u���f�[�^�̓������z��
	 * @return �e�[�u���f�[�^
	 *
	 * func_get_args�ł͎Q�Ƃ��󂯂�Ȃ��הz��ɂ�
	 */
	function orTableM($a);

	/**
	 * �e�[�u���̘_���ρB
	 * @param $table1 �e�[�u���f�[�^
	 * @param $table2 �e�[�u���f�[�^
	 * @return �e�[�u���f�[�^
	 */
	function andTable(&$tbl, &$table2);

	/**
	 * ���j�I���e�[�u�����쐬���܂��B
	 * �\�[�g��������rTable�̂��̂��g�p�B
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $asc �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 */
	function unionTable(&$lTable, &$rTable, $colum = null);

	//�e�[�u���̌���
	function joinTable( &$tbl, $b_name, $n_name, $b_col, $n_col );

	/**
	 * �e�[�u���� $start �s�ڂ��� $num ���o���B
	 * @param table �e�[�u���f�[�^
	 * @param start �I�t�Z�b�g
	 * @param num ��
	 */
	function limitOffset( $table, $start, $num );

	/**
	 * �Ö�ID�̍ő�l��Ԃ�
	 */
	function getMaxID();

	/**
	 * ���݂̃e�[�u������w�肵��column�̑����v���擾���܂��B
	 */
	function getSum( $name, $table = null);

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������sum�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 */
	function getSumTable( $sum_name, $group_name, $table = null);
	
	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������cnt�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 */
	function getCountTable( $name, $table = null);
	
	//�I���J������ǉ��B  geCountTable�ȂǂŃf�[�^�̗~�����J�������\������Ȃ����ɗL��
	function addSelectColumn( &$tbl, $name );
	
	//�w��J�����̂݌��ʂ��d�����폜���ĕԂ�
	function getDistinctColumn( $name , &$tbl);

	//�w��J�����̂݌��ʂ��d�����폜���ĕԂ�
	function getColumn( $name , &$tbl);

	function getClumnNameList();
}

/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/

/**
 * ���s�R�[�h�i\n�j��<br/> �ɒu�������܂�
 * @param $str ������
 */
function brChange($str){
	return str_replace(  "\r", "", str_replace( "\n", "<br/>", $str )  );
}

/**
 * <br/>�����s�R�[�h�i\n�j �ɒu�������܂�
 * @param $str ������
 */
function brChangeRe($str){
	return str_replace("<br/>", "\n", $str);
}
?>