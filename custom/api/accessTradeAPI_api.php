<?php

	class mod_accessTradeAPIApi
	{
		function second_category( $param )
		{
			global $gm;

			if( $param[ 'parent' ] )
				{ print $gm[ 'accessTradeSecondCategory' ]->getCCResult( null , '<!--# code tableSelectForm second_category accessTradeSecondCategory name id  選択してください  parent_id = ' . $param[ 'parent' ] . ' #-->' ); }
			else
				{ print $gm[ 'accessTradeSecondCategory' ]->getCCResult( null , '<!--# form option second_category   選択してください #-->' ); }
		}

		function third_category( $param )
		{
			global $gm;

			if( $param[ 'parent' ] )
				{ print $gm[ 'accessTradeThirdCategory' ]->getCCResult( null , '<!--# code tableSelectForm third_category accessTradeThirdCategory name id  選択してください  parent_id = ' . $param[ 'parent' ] . ' #-->' ); }
			else
				{ print $gm[ 'accessTradeThirdCategory' ]->getCCResult( null , '<!--# form option third_category   選択してください #-->' ); }
		}
	}
