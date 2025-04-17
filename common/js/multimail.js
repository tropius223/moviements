(function(){
    var id_list = null;
    var id_count = 0;
    var now_pt = 0;
    var success_cnt = 0;

	//id�̒����A
    var id_length = 8;
	//��x�Ƀ��[���𑗐M���郆�[�U�[��
	//php�Ƃ̒ʐM���^�C���A�E�g����ꍇ�͂�����������ăe�X�g���Ă��������B
    var set = 50;

function sendMailStart(){
    id_list = document.mail.receive_id.value + "/";
    id_count = (id_list.length) / (id_length+1);
    now_pt = 0;
    
    sendMail( );
    
}

function nextMail(data){
    //PHP�o�͂̊m�F
    if( !data.success ){
        $('#mail_success').html("error:�F�؃G���[���������܂����B("+data.error+")");
        return;
    }else{
        success_cnt += (data.count);
    }
    if(now_pt*( id_length + 1 )*set < id_list.length ){

        sendMail();
    }else{
        sendMailEnd();
    }
}

function sendMailEnd(){
    //������ʂ֑J��
    $('#main_message').html( ' ���[���̑��M���������܂����B<br><br>'+
        id_count + '����' + success_cnt +'���̃��[���̑��M�ɐ������܂����B');

}

function sendMailError(){
    now_pt--;
    successed_id = id_list.substring( 0 , now_pt * set * (id_length+1) );
    errors_id = id_list.substring( now_pt * set * (id_length+1) , (now_pt+1) * set * (id_length+1) );
    untra_id = id_list.substring( (now_pt+1) * set * (id_length+1) , id_list.length );



	$('#main_message').html('<font color="red">error:�ʐM�G���[���������܂����B</font><br>'+
        id_count + '����' + success_cnt +'���̃��[���̑��M�ɐ������Ă��܂��B<br><br>' + 
		'�ȉ��͑��M�ɐ�������ID�ꗗ�ł��B<br>'+
		'<textarea>'+successed_id.replace('/',"\n")+'</textarea><br><br>'+
		'�ȉ��͑��M���ɃG���[����������ID�ꗗ�ł��B<br>'+
		'<textarea>'+errors_id.replace('/',"\n")+'</textarea><br><br>'+
		'�ȉ��͖����M��ID�ꗗ�ł��B<br>'+
		'<textarea>'+untra_id.replace('/',"\n")+'</textarea><br><br>');
}

function sendMail(){
    ids = id_list.substring( now_pt * set * (id_length+1) , (now_pt+1) * set * (id_length+1) );
    setProgress();
    now_pt++;

    jQuery.ajax({
      url : 'multimail_send.php',
      type : 'POST',
      dataType : "json",
	  data : {
		 'send_id' : ids ,
		 'sub' : document.mail.sub.value ,
		 'main' : document.mail.main.value
	  } ,
      success : function(data){ nextMail(data); } ,
      error   : function(){ sendMailError(); }
    });

    
}

function setProgress(  ){
    upper = (now_pt+1)*set;
    if( (now_pt+1)*set > id_count ){ upper = id_count;}
    $('#mail_count').html( "�S"+id_count+"�����A"+now_pt*set+"-"+upper+"���ڂ��������B" );
    $('#mail_success').html( "�������F"+success_cnt+"���B" );
    
}

window.sendMailStart = sendMailStart;

})();