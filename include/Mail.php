<?php

	/***************************************************************************************************<pre>
	 * 
	 * ���[�����M�N���X
	 *  static�ȃN���X�Ȃ̂ŁA�C���X�^���X�𐶐������ɗ��p���Ă��������B
	 * 
	 * @author �O�H��q
	 * @version 3.0.0<br/>
	 * 
	 * </pre>
	 ********************************************************************************************************/

	class Mail
	{
		/**
		 * ���[���̑��M�B
		 * �O���f�[�^�ƂȂ郁�[���t�@�C���̓��e�Ń��[���𑗐M����B
		 * �R�}���h�R�����g��$gm �ɓn���Ă���GUIManager�I�u�W�F�N�g��
		 * $rec �ɓn���Ă������R�[�h�f�[�^�ŏ��������܂��B
		 * �܂��A���[���f�[�^�� sub �p�[�g�͑薼�Ƃ��āA main �p�[�g�͖{���Ƃ��ď�������܂��B
		 *
		 * @param $mail ���[���t�@�C��
		 * @param $from ���M�����[���A�h���X
		 * @param $to ���M�惁�[���A�h���X
		 * @param $gm=null GUIManager�I�u�W�F�N�g
		 * @param $rec=null ���R�[�h�f�[�^
         * @param $from_name ���M���� *�ȗ��\
		 */
		function send($mail, $from, $to, $gm = null, $rec = null, $from_name = null, $ccs = null, $bccs = null )
		{
			global $MAILSEND_ADDRES;
            
            if(is_null($from_name)){
                $from_str = 'From:'. trim($from);
            }else{
				$from_name = SystemUtil::hankakukana2zenkakukana($from_name);
                $from_str = 'From:"'.mb_encode_mimeheader($from_name).'" <'. trim($from).'>';
            }
			
			if(  isset( $gm )  ){
				$sub	 = str_replace(  "\n", "", $gm->getString($mail, $rec, 'subject') );
				$main	 = str_replace(  "<br/>", "\n", $gm->getString($mail, $rec, 'main') );
			}else{
				$sub	 = stripslashes( str_replace(  "\n", "", GUIManager::partGetString($mail, 'subject') ) );
				$main	 = str_replace(  "<br/>", "\n", GUIManager::partGetString($mail, 'main') );
			}
			
            if( !is_null($ccs) && is_array($ccs) ){
                foreach( $ccs as $cc ){
                    if(!isset($cc['name'])){
                        $from_str .= "\n".'Cc:'. trim($cc['mail']);
                    }else{
						$cc['name'] = SystemUtil::hankakukana2zenkakukana($cc['name']);
                        $from_str .= "\n".'Cc:"'.mb_encode_mimeheader($cc['name']).'" <'. trim($cc['mail']).'>';
                    }
                }
            }
			
            if( !is_null($bccs) && is_array($bccs) ){
                foreach( $bccs as $bcc ){
                    if(!isset($bcc['name'])){
                        $from_str .= "\n".'Bcc:'. trim($bcc['mail']);
                    }else{
						$bcc['name'] = SystemUtil::hankakukana2zenkakukana($bcc['name']);
                        $from_str .= "\n".'Bcc:"'.mb_encode_mimeheader($bcc['name']).'" <'. trim($bcc['mail']).'>';
                    }
                }
            }
			
			if($to == $MAILSEND_ADDRES){
				$main .= '-----------------------------------'."\n";
				$main .= 'REMOTE_HOST�F'.$_SERVER["REMOTE_ADDR"]."\n";
				$main .= 'HTTP_USER_AGENT�F'.$_SERVER["HTTP_USER_AGENT"]."\n";
				$main .= '-----------------------------------'."\n";
			}
			
			mb_language("ja");
			$sub = str_replace("\n", "", $sub);
			$sub = str_replace("\r", "", $sub);

			$main = str_replace("\r", "", $main);
			
			//���p�J�i��S�p�J�i��
			$sub = SystemUtil::hankakukana2zenkakukana($sub);
			$main = SystemUtil::hankakukana2zenkakukana($main);
			
			$rcd = mb_send_mail( $to, $sub, $main, $from_str , '-f ' . trim($from));
		}



		/**
		 * �Y�t�t�@�C���t�����[���̑��M�B
		 * �O���f�[�^�ƂȂ郁�[���t�@�C���̓��e�Ń��[���𑗐M����B
		 * �R�}���h�R�����g��$gm �ɓn���Ă���GUIManager�I�u�W�F�N�g��
		 * $rec �ɓn���Ă������R�[�h�f�[�^�ŏ��������܂��B
		 * �܂��A���[���f�[�^�� sub �p�[�g�͑薼�Ƃ��āA main �p�[�g�͖{���Ƃ��ď�������܂��B
		 *
		 * @param $mail ���[���t�@�C��
		 * @param $from ���M�����[���A�h���X
		 * @param $to ���M�惁�[���A�h���X
		 * @param $gm=null GUIManager�I�u�W�F�N�g
		 * @param $rec=null ���R�[�h�f�[�^
         * @param $from_name ���M���� *�ȗ��\
		 */
		function sendAttach($mail, $from, $to, $gm = null, $rec = null, $attach = null, $from_name = null )
		{
			global $MAILSEND_ADDRES;
			global $HOME;
			
			if( $attach != '' && file_exists( $attach ) )
			{// �Y�t�t�@�C��������ꍇ
				if(  isset( $gm ) && isset( $rec )  )
				{
					$sub	 = str_replace(  "\n", "", $gm->getString($mail, $rec, 'subject') );
					$main	 = str_replace(  "<br/>", "\n", $gm->getString($mail, $rec, 'main') );
				}
				else
				{
					$sub	 = stripslashes( str_replace(  "\n", "", GUIManager::partGetString($mail, 'subject') ) );
					$main	 = str_replace(  "<br/>", "\n", GUIManager::partGetString($mail, 'main') );
				}
				
				if($to == $MAILSEND_ADDRES)
				{
					$main .= '-----------------------------------'."\n";
					$main .= 'REMOTE_HOST�F'.$_SERVER["REMOTE_ADDR"]."\n";
					$main .= 'HTTP_USER_AGENT�F'.$_SERVER["HTTP_USER_AGENT"]."\n";
					$main .= '-----------------------------------'."\n";
				}
	
				mb_language("japanese");
				$sub = str_replace("\n", "", $sub);
				$sub = str_replace("\r", "", $sub);
				
				$main = str_replace("\r", "", $main);

				$sub = SystemUtil::hankakukana2zenkakukana($sub);
				$main = SystemUtil::hankakukana2zenkakukana($main);
				
				//�摜�̎擾�Ɖ摜�̃G���R�[�h
				if(  strpos( $attach, ".pdf" ) || strpos( $attach, ".PDF" ) )
				{
					$type		 = 'application/pdf';
					$img_name	 = "data.pdf";
				}
				else
				{
					$img_name			 = "image";
					list($width, $height,$type) = getimagesize($attach);
					switch( $type )
					{
						case '1':
							$type		 = 'image/gif';
							$img_name	.= '.gif';
							break;
						case '2':
							$type		 = 'image/jpeg';
							$img_name	.= '.jpeg';
							break;
						case '3':
							$type		 = 'image/png';
							$img_name	.= '.png';
							break;
					}	
				}
				
				$img				 = file_get_contents($HOME.$attach);
				$img_encode64_000	 = chunk_split(base64_encode($img));
				
				//�w�b�_���
                if(is_null($from_name))
    				$headers  = "From:" . trim($from). "\r\n";
                else
                    $headers  = 'From:"'.mb_encode_mimeheader($from_name).'" <'. trim($from).'>'. "\r\n";
                
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-Type: multipart/related;boundary="1000000000"' . "\r\n";
			
//�e�L�X�g�p�[�g
$message =<<<END

--1000000000
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 7bit

$main

--1000000000
Content-Type: $type; name="$img_name"
Content-Disposition: attachment; filename="$img_name"
Content-Transfer-Encoding: base64
Content-Disposition: inline;  filename="$img_name"

$img_encode64_000

--1000000000--

END;
			
				$sub	 = mb_encode_mimeheader($sub);// �e�L�X�g���Ƃ�����
				$message = mb_convert_encoding($message, "UTF-8");
				$rcd 	 = mail( $to, $sub, $message, $headers );
			
			}
			else {
				Mail::send( $mail, $from, $to, $gm, $rec, $from_name );
			} // �Y�t�t�@�C���������ꍇ

		}

		/**
		 * ���[���̑��M�B
		 * ������𒼐ڎw�肵�ă��[���𑗐M���܂��B
		 *
		 * @param $sub �^�C�g��������
		 * @param $main �{��������
		 * @param $from ���M�����[���A�h���X
		 * @param $to ���M�惁�[���A�h���X
         * @param $from_name ���M���� *�ȗ��\
		 */
		function sendString($sub, $main, $from, $to , $from_name = null)
		{
            if(is_null($from_name)){
                $from_str = 'From:'. trim($from);
            }else{
				$from_str = SystemUtil::hankakukana2zenkakukana($from_str);
                $from_str = 'From:"'.mb_encode_mimeheader($from_name).'" <'. trim($from).'>';
            }
			mb_language("ja");
			
			$main = str_replace("\r", "", $main);

			$sub = SystemUtil::hankakukana2zenkakukana($sub);
			$main = SystemUtil::hankakukana2zenkakukana($main);
			$from_str = SystemUtil::hankakukana2zenkakukana($from_str);
			$rcd = mb_send_mail(  $to, stripslashes( $sub ), str_replace( "<br/>", "\n", stripslashes($main) ), $from_str ,'-f ' . trim($from));
		}
	}

	/********************************************************************************************************/
?>