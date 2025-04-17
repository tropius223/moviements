<?PHP

/*******************************************************************************************************
 * <PRE>
 *
 * デバッグ関数
 *
 * @author 吉岡 幸一郎
 * @version 1.0.0
 *
 * </PRE>
 *******************************************************************************************************/

ini_set( "display_errors",  1 );
//	error_reporting(E_ALL);
error_reporting( E_ERROR | E_WARNING |  E_PARSE );

switch($DEBUG_TYPE){
	case 'echo':
	default:
		// デバッグ用出力関数
		// 白背景・黒文字・等幅フォント・フォントサイズ固定・ボーダー囲み
		function d($v,$name='echo') {
			global $DEBUG_TRACE;
			echo '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
			echo "$name:";
			var_dump($v);
			echo '</pre>';
			if($DEBUG_TRACE){
				echo '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
				$trace = debug_backtrace();
				$row = count($trace);
				
				for( $i=0;$row>$i;$i++){
					unset($trace[$i]["args"]);
					unset($trace[$i]["object"]);
				}
				var_dump($trace);
				echo '</pre>';
			}
		}
		function p( $var, $label = FALSE ){
			if( $label ){
				$text = '' . $label . ' : ';
			}
			else{
				$text = '';
			}
			$text.= htmlspecialchars( print_r($var,1) , ENT_COMPAT | ENT_HTML401 , 'SJIS' );
			$text.= '';
			$text = preg_replace( '/(Array)([\r\n])/', '$1$2', $text );
			$text = preg_replace( '/ (\[.+?\]) /' , ' $1 ' , $text );
			$text = preg_replace( '/ (\=\>) /' , ' $1 ', $text );
			echo $text;
		}

		break;
	case 'file':
		function d($str,$name='log') {
			$path = "logs/debug.log";
			$log = new OutputLog($path);
			$log->write( $name.':'.$str);
		}
		break;
	case 'header':
		include_once './include/extends/FirePHPCore/FirePHP.class.php';
		function d($args,$name='fb'){
			global $DEBUG_TRACE;
			
			$firephp = FirePHP::getInstance(true);

			$firephp->log($args, $name);
			
			if($DEBUG_TRACE){
				$trace = debug_backtrace();
				$row = count($trace);
				
				for( $i=0;$row>$i;$i++){
					unset($trace[$i]["args"]);
					unset($trace[$i]["object"]);
				}
				$firephp->log($trace ,'trace');
			}
			
			
		}
		break;
}
?>