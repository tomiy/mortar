<?php
namespace Mortar\Foundation\Tools;

/**
 * Prints an object into a collapsible table
 */
abstract class Debug {

    /**
     * Prints an object into a collapsible table
     * @param  object  $obj      the object to display
     * @param  string  $key      the name of the object we wish to display
     * @param  integer $depth    the depth into the hierarchy we are at
     * @param  array   $previous the previous objects met higher in the hierarchy (used to check for recursivity)
     * @return string            a pre element accompanied with a js script to collapse its children
     */
    public static function show($obj, $key = null, $depth = 0, $previous = []) {
        // get a function name and print the js script if it's not done yet
        static $functionRef = 0;
        if(!$functionRef) {
            $functionRef = 'a'.uniqid();
            echo "<script>function $functionRef(id){e=document.getElementById(id), d='inline-block',b=document.getElementById('btn-'+id); e.style.display=e.style.display==d?'none':d; b.innerHTML=b.innerHTML=='+'?'-':'+';}</script>";
        }

        $output = is_null($key)?'':"$key => ";

        if(in_array(gettype($obj), ['object','array'])) {
            $uid = uniqid();
            $type = is_array($obj)?'Array':get_class($obj).' Object';

            $output .= $type;

            // if recursion or callback, stop there, else dismount object to array
            if(in_array($obj, $previous) || is_callable($obj)) {
                $output .= " <b>*RECURSION*</b>";
                return $output;
            } else if(is_object($obj)) {
                $previous[] = $obj;
                $obj = static::dismount($obj);
            }

            // print button and collapsible span
            $col = (100 - ($depth + 1) * 5) % 50;
            $output .= "<button id='btn-$uid' style='font-family:monospace;' onclick='$functionRef(\"$uid\")'>+</button>
      <span id='$uid' style='border:1px solid #bbb;padding:2px; margin:2px;background:hsl(0,0%,$col%);display:none;'>";

            // recurse for each child of current object
            $noitems = true;
            foreach($obj as $k => $v) {
                $noitems = false;
                $output .= static::show($v, $k, $depth+1, $previous)."\n";
            } if($noitems) $output .= "&lt;EMPTY&gt;\n";
            $output .= "</span>";
        } else {
            // print object cast to string if necessary (for resources and such)
            $output .= "<b>".(is_null($obj)?'&lt;NULL&gt;':"$obj")."</b>";
        }
        // print pre if at top depth else return value to higher depths
        if($depth == 0) echo "<pre style='border:1px solid #bbb; padding:2px;margin:2px;'>$output</pre>";
        else return $output;
    }

    /**
     * Cast an object to an array with key => value association and variable access
     * @param  object $object the object to cast
     * @return array          the object cast
     */
    private static function dismount($object) {
        // simple array cast
        $objectCast = (array) $object;
        $output = [];
        $objectClass = get_class($object);
        foreach($objectCast as $k => $v) {
                    // trick to get variable access since they get cast weirdly by default
                    // namespace also prepended to the variable name
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
