<?php

	class mod_accessTradeAPIApi
	{
		function second_category( $param )
		{
			global $gm;

			if( $param[ 'parent' ] )
				{ print $gm[ 'accessTradeSecondCategory' ]->getCCResult( null , '<!--# code tableSelectForm second_category accessTradeSecondCategory name id  �I�����Ă�������  parent_id = ' . $param[ 'parent' ] . ' #-->' ); }
			else
				{ print $gm[ 'accessTradeSecondCategory' ]->getCCResult( null , '<!--# form option second_category   �I�����Ă������� #-->' ); }
		}

		function third_category( $param )
		{
			global $gm;

			if( $param[ 'parent' ] )
				{ print $gm[ 'accessTradeThirdCategory' ]->getCCResult( null , '<!--# code tableSelectForm third_category accessTradeThirdCategory name id  �I�����Ă�������  parent_id = ' . $param[ 'parent' ] . ' #-->' ); }
			else
				{ print $gm[ 'accessTradeThirdCategory' ]->getCCResult( null , '<!--# form option third_category   �I�����Ă������� #-->' ); }
		}
	}
