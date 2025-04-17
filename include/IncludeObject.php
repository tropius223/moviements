<?php

	/*******************************************************************************************************
	 * <PRE>
	 * 
	 * html�t�@�C���ǂݍ��݃N���X
	 *  ���̃N���X��static�ȃN���X�ł��B�C���X�^���X�𐶐������ɗ��p���Ă��������B
	 * 
	 * @author �O�H��q
	 * @version 3.0.0
	 * 
	 * </PRE>
	 *******************************************************************************************************/

	class IncludeObject
	{
		/**
		 * �O���t�@�C����ǂݍ��݂܂��B
		 * �R�}���h�R�����g�����݂���ꍇ��GUIManager�I�u�W�F�N�g�ƃ��R�[�h�f�[�^��p���ď��������܂��B
		 * @param $file �t�@�C����
		 * @param $gm=null GUIManager�I�u�W�F�N�g
		 * @param $rec=null ���R�[�h�f�[�^
		 */
		function run($file, $gm = null, $rec = null)
		{
			if( !file_exists( $file ) )	{ throw new Exception('INCLUDE�t�@�C�����J���܂���B->'. $file); }
			
			$fp		 = fopen ($file, 'r');
            $state = Array('draw'=>1,'if'=>false);
			while(!feof($fp))
			{
				$buffer	 = fgets($fp, 20480);
				$str	 = GUIManager::commandComment($buffer, $gm, $rec, $state , $c_part = null);
				
				$str	 = str_replace( Array("&CODE000;","&CODE001;"), Array("/"," "), $str );

				print $str;
			}
			fclose($fp);
			
		}
		
		/**
		 * �O���t�@�C����ǂݍ��݁A������f�[�^��Ԃ��܂��B
		 * �R�}���h�R�����g�����݂���ꍇ��GUIManager�I�u�W�F�N�g�ƃ��R�[�h�f�[�^��p���ď��������܂��B
		 * @param $file �t�@�C����
		 * @param $gm=null GUIManager�I�u�W�F�N�g
		 * @param $rec=null ���R�[�h�f�[�^
		 */
		function get($file, $gm = null, $rec = null)
		{
			if( !file_exists( $file ) )	{ throw new Exception('INCLUDE�t�@�C�����J���܂���B->'. $file); }
			
			$fp		 = fopen ($file, 'r');
			$ret	 = "";
            $state = Array('draw'=>1,'if'=>false);
			while(!feof($fp))
			{
				$buffer	 = fgets($fp, 20480);
				$ret	 .= GUIManager::commandComment($buffer, $gm, $rec, $state , $c_part = null);
			}
			fclose($fp);
			
			$ret = str_replace( Array("&CODE000;","&CODE001;"), Array("/"," "), $ret );

			return $ret;
		}
		
	}

	/********************************************************************************************************/
?>