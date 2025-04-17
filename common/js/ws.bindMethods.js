//Å°adwares //

window.ws.bind
(
	{
		'adwares_regist/adwares_edit,input[name="point"],keyup' :
			function()
			{
				window.ws.ajax({
					call : 'point.calcPointToYen' ,
					args : 'point=' + $( this ).val() ,
					res  : function( $res ){ $( '#point_to_yen_area' ).html( $res[ 'res' ] ); }
				});
			}
		,
		'adwares_regist/adwares_edit,select[name="asp_type"],bind/change' :
			function()
			{
				window.ws.ajax({
					call : 'point.changePointTypeByASP' ,
					args : 'asp_type=' + $( this ).val() + '&selected=' + $( 'input[name="point_type"]:checked' ).val() ,
					res  : function( $res )
					{
						if( $res[ 'res' ] )
						{
							$( 'input[name="point_type"][value="p"]' ).parent().css( 'color' , '#444' );
							$( 'input[name="point_type"][value="p"]' ).removeAttr( 'disabled' );
						}
						else
						{
							$( 'input[name="point_type"][value="p"]' ).parent().css( 'color' , '#999' );
							$( 'input[name="point_type"][value="p"]' ).attr( 'disabled' , 'disabled' );
						}

						$( 'input[name="point_type"]:checked' ).change();
					}
				});
			}
		,
		'adwares_regist/adwares_edit,input[name="point_type"],bind/change' :
			function()
			{
				if( 'p' == $( this ).val() && 'disabled' != $( this ).attr( 'disabled' ) )
				{
					$( '#point_view_type_selector_area' ).css( 'color' , '#444' );
					$( 'input' , $( '#point_view_type_selector_area' ) ).removeAttr( 'disabled' );
				}
				else
				{
					$( '#point_view_type_selector_area' ).css( 'color' , '#999' );
					$( 'input' , $( '#point_view_type_selector_area' ) ).attr( 'disabled' , 'disabled' );
				}
			}
	}
);

//Å°news //

window.ws.bind
(
	{
		'news_regist/news_edit,input[name="detail_mode"],bind/change' :
			function()
			{
				switch( $( 'input[name="detail_mode"]:checked' ).val() )
				{
					case '1' :
					{
						$( '#use_link_message_div' ).css( 'display' , 'block' );
						$( '#link_message_div' ).css( 'display' , 'none' );
						$( '#detail_message_div' ).css( 'display' , 'block' );
						$( '#link_url_div' ).css( 'display' , 'none' );
			
						$( 'input[name="use_link_message"]' ).change();
			
						break;
					}
			
					case '2' :
					{
						$( '#use_link_message_div' ).css( 'display' , 'none' );
						$( '#link_message_div' ).css( 'display' , 'none' );
						$( '#detail_message_div' ).css( 'display' , 'none' );
						$( '#link_url_div' ).css( 'display' , 'block' );
						break;
					}
			
					default :
					{
						$( '#use_link_message_div' ).css( 'display' , 'none' );
						$( '#link_message_div' ).css( 'display' , 'none' );
						$( '#detail_message_div' ).css( 'display' , 'none' );
						$( '#link_url_div' ).css( 'display' , 'none' );
						break;
					}
				}
			}
		,
		'news_regist/news_edit,input[name="use_link_message"],bind/change' :
			function()
			{
				switch( $( 'input[name="use_link_message"]:checked' ).val() )
				{
					case 'TRUE' :
					{
						$( '#link_message_div' ).css( 'display' , 'block' );
						break;
					}
			
					default :
					{
						$( '#link_message_div' ).css( 'display' , 'none' );
						break;
					}
				}
			}
	}
);

//Å°moba8API //

window.ws.bind
(
	{
		'moba8API_submit,select[name="first_category"],bind/change' :
			function()
			{
				window.ws.ajax({
					call : 'GetSecondCategoryForm' ,
					args : 'first_category=' + $( this ).val() ,
					io   : 'POST/text' ,
					res  : function( $res ){ $( '.second_category_area' ).html( $res ); }
				});
			}
		,
		'moba8API_submit,input[name="point"],keyup' :
			function()
			{
				window.ws.ajax({
					call : 'point.calcPointToYen' ,
					args : 'point=' + $( this ).val() ,
					res  : function( $res ){ $( '#point_to_yen_area' ).html( $res[ 'res' ] ); }
				});
			}
		,
		'moba8API_submit,input[name="point_type"],bind/change' :
			function()
			{
				if( 'p' == $( this ).val() && 'disabled' != $( this ).attr( 'disabled' ) )
				{
					$( '#point_view_type_selector_area' ).css( 'color' , '#444' );
					$( 'input' , $( '#point_view_type_selector_area' ) ).removeAttr( 'disabled' );
				}
				else
				{
					$( '#point_view_type_selector_area' ).css( 'color' , '#999' );
					$( 'input' , $( '#point_view_type_selector_area' ) ).attr( 'disabled' , 'disabled' );
				}
			}
	}
);

//Å°accessTradeAPI //

window.ws.bind
(
	{
		'accessTradeAPI_submit,select[name="first_category"],bind/change' :
			function()
			{
				window.ws.ajax({
					call : 'accessTradeApi.second_category' ,
					args : 'parent=' + $( this ).val() ,
					io   : 'POST/text' ,
					res  : function( $res )
					{
						$( '.second_category_area' ).html( $res );
						window.ws.rebind();
					}
				});
			}
		,
		'accessTradeAPI_submit,select[name="second_category"],rebind/change' :
			function()
			{
				window.ws.ajax({
					call : 'accessTradeApi.third_category' ,
					args : 'parent=' + $( this ).val() ,
					io   : 'POST/text' ,
					res  : function( $res ){ $( '.third_category_area' ).html( $res ); }
				});
			}
		,
		'accessTradeAPI_submit,input[name="point"],keyup' :
			function()
			{
				window.ws.ajax({
					call : 'point.calcPointToYen' ,
					args : 'point=' + $( this ).val() ,
					res  : function( $res ){ $( '#point_to_yen_area' ).html( $res[ 'res' ] ); }
				});
			}
		,
		'accessTradeAPI_submit,input[name="point_type"],bind/change' :
			function()
			{
				if( 'p' == $( this ).val() && 'disabled' != $( this ).attr( 'disabled' ) )
				{
					$( '#point_view_type_selector_area' ).css( 'color' , '#444' );
					$( 'input' , $( '#point_view_type_selector_area' ) ).removeAttr( 'disabled' );
				}
				else
				{
					$( '#point_view_type_selector_area' ).css( 'color' , '#999' );
					$( 'input' , $( '#point_view_type_selector_area' ) ).attr( 'disabled' , 'disabled' );
				}
			}
	}
);
