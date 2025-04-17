<?php

	/***************************************************************************************************<pre>
	 * 
	 * ���O�t�@�C�������o���X�g���[��
	 * 
	 * @author �O�H��q
	 * @version 3.0.0<br/>
	 * 
	 * </pre>
	 ********************************************************************************************************/

	class OutputLog
	{
		var $file;
		var $MAX_LOGFILE_SIZE = 20971520; //20MB | 1024 * 1024 * 20
//		var $MAX_LOGFILE_SIZE = 5242880;  //5MB | 1024 * 1024 * 5
//		var $MAX_LOGFILE_SIZE = 20480;  //20KB | 1024 * 50
		
		/**
		 * �R���X�g���N�^�B
		 * @param $file ���O�������o���t�@�C���ւ̃p�X
		 */
		function __construct($file)
		{
			if( !file_exists( $file ) )	{ throw new Exception('LOG�t�@�C�����J���܂���B->'. $file); }
			$this->file = $file;
		}
		
		/**
		 * ���O�̏����o���B
		 * @param $str �����o��������
		 */
		function write($str)
		{
			$fp = fopen($this->file, 'a');
			
			// �t�@�C�������b�N����Ă��邩�̊m�F
			if(flock($fp, LOCK_EX))
			{
				fwrite($fp, $str. $_SERVER['HTTP_USER_AGENT']. ",". $_SERVER['REMOTE_ADDR']. ",". date("Y_m_d_H_i_s"). "\n");
				flock($fp, LOCK_UN);
			}
			
			fclose($fp);

			//print filesize($this->file)."/".$this->MAX_LOGFILE_SIZE;
			//�t�@�C���T�C�Y���m�F���ő�l�𒴂��Ă���ꍇ�A���l�[������B
			if($this->MAX_LOGFILE_SIZE < filesize($this->file)){
				$new_file = $this->file.date("_Y_m_d_H_i_s");
				if(rename($this->file, $new_file)){
					if(touch($this->file)){
						if(!chmod($this->file, 0777)){
							//�p�[�~�b�V�����ύX���s
							unlink($this->file);
							rename($new_file, $this->file);
						}
					}else{
						//�V�K���O�t�@�C���������s
						rename($new_file, $this->file);
					}
				}
			}
		}
	}

	/********************************************************************************************************/
?>