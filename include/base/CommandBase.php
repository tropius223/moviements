<?php
/*******************************************************************************************************
 * <PRE>
 *
 * �R�����g�R�}���h�����N���X�p�x�[�X�N���X
 * ���ʃ��\�b�h�ێ��N���X
 *
 * @author �g���K��Y
 * @original �O�H��q
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

class command_base
{
    var $buffer = "";
    
    /**
     * �o�̓o�b�t�@���������B
     */
    function flushBuffer()	{ $this->buffer	 = ""; }

    /**
     * �o�̓o�b�t�@�Ƀf�[�^��ǉ��B
     */
    function addBuffer($str)
    {
        $this->buffer	 .= str_replace( Array("/"," "), Array("&CODE000;","&CODE001;"), $str );
    }
    
    /**
     * �o�̓o�b�t�@�̓��e���擾�B
     * @return �o�b�t�@�̓��e
     */
    function getBuffer(){
    	global $BUFFER_FILTER;
    	if( $BUFFER_FILTER != null ){
    		return $BUFFER_FILTER->filter($this->buffer);
    	}
    	return $this->buffer;
    }

}
?>