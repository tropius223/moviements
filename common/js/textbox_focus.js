    $(function () {  
      
      $('.advertise_search').each(function () {  
        $tb = $(this);  
        if ($tb.val() != this.title) {  
          $tb.removeClass('advertise_search');  
        }  
      });  
        
      $('.advertise_search').focus(function () {  
        $tb = $(this);  
        if ($tb.val() == this.title) {  
          $tb.val('');  
          $tb.removeClass('advertise_search');  
        }  
      });  
  
      $('.advertise_search').blur(function () {  
        $tb = $(this);  
        if ($.trim($tb.val()) == '') {  
          $tb.val(this.title);  
          $tb.addClass('advertise_search');  
        }  
      });  

		$( '.advertise_search_btn' ).bind
		(
			'click' ,
			function()
			{
				var $input = $( 'input.advertise_search' );
				var $title = $input.attr( 'title' );

				if( $title == $input.val() )
					{ $input.val( '' ); }

				return true;
			}
		);
    });    