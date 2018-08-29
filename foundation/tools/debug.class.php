<?php
namespace Foundation\Tools;

abstract class Debug {

	public static function show($o, $key = null, $depth = 0, $previous = []) {
		static $functionRef = 0;
		if(!$functionRef) {
			$functionRef = 'a'.uniqid();
			echo "<script>function $functionRef(id){e=document.getElementById(id), d='inline-block',b=document.getElementById('btn-'+id); e.style.display=e.style.display==d?'none':d; b.innerHTML=b.innerHTML=='+'?'-':'+';}</script>";
		}

		$output = '';
		$keyOutput = is_null($key)?'':"$key => ";

		if(in_array(gettype($o), ['object','array'])) {
			$id = uniqid();
			$type = is_array($o)?'Array':get_class($o).' Object';

			$output .= $keyOutput.$type;

			if(in_array($o, $previous) || is_callable($o)) {
				$output .= " <b>*RECURSION*</b>";
				return $output;
			} else if(is_object($o)) {
				$previous[] = $o;
				$o = static::dismount($o);
			}

			$c = (100 - ($depth + 1) * 5) % 50;
			$output .= "<button id='btn-$id' style='font-family:monospace;' onclick='$functionRef(\"$id\")'>+</button>
      <span id='$id' style='border:1px solid #bbb;padding:2px; margin:2px;background:hsl(0,0%,$c%);display:none;'>";

			$noitems = true;
			foreach($o as $k => $v) {
				$noitems = false;
				$output .= static::show($v, $k, $depth+1, $previous)."\n";
			} if($noitems) $output .= "&lt;EMPTY&gt;\n";
			$output .= "</span>";

		} else {
			$output .= $keyOutput."<b>".(
				is_null($o)?'&lt;NULL&gt;':"$o"
			)."</b>";
		} if($depth == 0)
		echo "<pre style='border:1px solid #bbb; padding:2px;margin:2px;'>$output</pre>"; else return $output;
	}

	private static function dismount($object) {
		$objectCast = (array) $object;
		$output = [];
		$objectClass = get_class($object);
		foreach($objectCast as $k => $v) {
					$_k = explode("\0", $k);
					$name =
						$objectClass.'\\'.$_k[0].$_k[2].':'.
						[
							NULL => 'public',
							'*' => 'protected',
							$objectClass => 'private'
						][$_k[1]];
					$output[$name] = $v;
		}
		return $output;
	}

}
