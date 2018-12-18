<?php
use Mortar\Mortar\Core;

$mortar = Core::getInstance();
$parser = $mortar->component('parser');

$parser->tag('var', function($var) {
    return '<?=$this->variables[\''.$var.'\']?>';
});

$parser->tag('loop', function($counter, $content) use($parser) {
    $counter = $parser->parse($counter);
    $content = $parser->parse($content);
    $output = '';

    for ($i = 0; $i < $counter; $i++) {
        $output .= $content;
    }

    return $output;
});

$parser->tag('template', function($name) use($mortar){
    $cmpPath = $mortar->compile($name);
    return "<?include $cmpPath?>";
});

$parser->tag('csrf', function() {
    return '<input type="hidden" name="_token" value="<?=hash_hmac(\'sha256\', CURRENT_URI, $_SESSION[\'csrf_token\'])?>"/>';
});