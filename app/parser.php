<?php
use Mortar\Engine\Core;

$mortar = Core::getInstance();
$parser = $mortar->component('parser');

$parser->tag('get', function($name, $default = null) use($parser) {
    $name = $parser->parse($name);
    $default = $parser->parse($default);

    return "<?=isset(\$_GET['$name'])?\$_GET['$name']:'$default'?>";
});

$parser->tag('loop', function($counter, $content) use($parser) {
    $counter = $parser->parse($counter);
    $content = $parser->parse($content);

    for ($i = 0, $output = ''; $i < $counter; $i++) {
        $output .= $content;
    }
    return $output;
});

$parser->tag('each', function($variable, $tag, $content) use($parser) {
    $tag = $parser->parse($tag);
    $content = $parser->parse($content);
    $output = '';

    foreach ($parser->get($variable) as $value) {
        $output .= str_replace($tag, $value, $content);
    }
    return $output;
});

$parser->tag('template', function($name) use($mortar, $parser) {
    $name = $parser->parse($name);
    
    $cmpPath = $mortar->compile($name);
    return "<?include $cmpPath?>";
});

$parser->tag('csrf', function() {
    return '<?=hash_hmac(\'sha256\', CURRENT_URI, $_SESSION[\'csrf_token\'])?>';
});