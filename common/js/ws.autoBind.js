window.ws        = new Object();
window.ws.system = new Object();
window.ws.config = new Object();
window.ws.bind   = new Object();

window.ws.bind = function( $iConfig )
{
	for( var $propertyName in $iConfig ) //全てのプロパティを処理
		{ window.ws.config[ $propertyName ] = $iConfig[ $propertyName ]; }

	window.ws.rebind();
}

window.ws.rebind = function()
{
	$(
		function()
		{
			for( var $propertyName in window.ws.config )
				{ window.ws.system.rebindCase( $propertyName , window.ws.config[ $propertyName ] ); }
		}
	);
}

window.ws.system.rebindCase = function( $iName , $iMethod )
{
	var $splits   = $iName.split( ',' );
	var $pages    = $splits.shift().split( '/' );
	var $targets  = $splits.shift().split( '/' );
	var $handlers = $splits.shift().split( '/' );

	for( var $i = 0 ; $pages.length > $i ; ++$i )
	{
		if( !window.ws.system.isCurrentPage( $pages[ $i ] ) )
			{ continue; }

		for( var $i = 0 ; $targets.length > $i ; ++$i )
		{
			var $target = $( $targets[ $i ] );

			if( 0 >= $target.size() )
				{ continue; }

			for( var $i = 0 ; $handlers.length > $i ; ++$i )
			{
				if( 'rebind' == $handlers[ $i ] )
				{
					if( !$target.attr( 'ws.rebind' ) )
					{
						$target.attr( 'ws.rebind' , 'ws.rebind' );
						$target[ 'ws.rebind' ] = $iMethod;
						$target[ 'ws.rebind' ]();
					}
				}
				else if( 'bind' == $handlers[ $i ] )
				{
					if( !( window.ws.bind[ $targets[ $i ] ] ) )
					{
						window.ws.bind[ $targets[ $i ] ] = true;

						if( !$target.attr( 'ws.bind' ) )
						{
							$target.attr( 'ws.bind' , 'ws.bind' );
							$target[ 'ws.bind' ] = $iMethod;
							$target[ 'ws.bind' ]();
						}
					}
				}
				else
				{
					$target.unbind( $handlers[ $i ] , $iMethod );
					$target.bind( $handlers[ $i ] , $iMethod );
				}
			}
		}
	}
}

window.ws.system.isCurrentPage = function( $iPageName )
{
	switch( $iPageName )
	{
		case '*' :
			{ return true; }

		default :
		{
			var $currentScript = window.ws.system.getCurrentScriptName();
			var $currentType   = window.ws.system.getCurrentTypeQuery();

			var $splits = $iPageName.split( '_' );
			var $page   = $splits.pop();
			var $type   = $splits.pop();

			if( $page != $currentScript )
				{ return false; }

			if( $type && $type != $currentType )
				{ return false; }

			return true;
		}
	}
}

window.ws.ajax = function( $iParam )
{
	var $class  = 'c=Api';
	var $method = 'post=' + $iParam[ 'call' ];
	var $splits = Array();

	if( 0 <= $iParam[ 'call' ].search( '\\.' ) )
	{
		$splits = $iParam[ 'call' ].split( '.' );
		$class  = 'c=' + $splits.shift() + 'Api';
		$method = 'm=' + $splits.shift();
	}

	var $input  = 'POST';
	var $output = 'json';

	if( $iParam[ 'io' ] && 0 <= $iParam[ 'io' ].search( '/' ) )
	{
		$splits = $iParam[ 'io' ].split( '/' );
		$input  = $splits.shift();
		$output = $splits.shift();
	}

	var $success = $iParam[ 'res' ];
	var $error   = $iParam[ 'err' ];
	var $args    = $iParam[ 'args' ];

	if( !$success )
		{ $success = function(){}; }

	if( !$error )
		{ $error = function(){}; }

	if( $args )
		{ $args = '&' + $args; }
	else
		{ $args = ''; }

	jQuery.ajax({
		url      : 'api.php' ,
		type     : $input ,
		dataType : $output ,
		data     : $class + '&' + $method + $args ,
		success  : $success ,
		error    : $error
	});
}

window.ws.system.getCurrentScriptName = function()
{
	var $url         = location.href;
	var $query       = location.search;
	var $urlLength   = $url.length;
	var $queryLength = $query.length;

	$url = $url.substr( 0 , $urlLength - $queryLength );

	if( $url.match( /\/$/ ) ) //URLが/で終了する場合
		{ $url = 'index'; }
	else //URLがスクリプト名で終了する場合
		{ $url = $url.match( /([^\/]+)\.[^\/]+$/ )[ 1 ]; }

	return $url;
}

window.ws.system.getCurrentTypeQuery = function()
{
	var $type = location.search.match( /type=(\w+)/ );

	if( $type ) //typeパラメータがある場合
		{ return $type[ 1 ]; }
	else //typeパラメータがない場合
		{ return ''; }
}
