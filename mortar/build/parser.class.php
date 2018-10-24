<?php
namespace Mortar\Mortar\Build;

use Mortar\Mortar\Mortar;

class Parser {

    private $mortar;
    private $worker;
    private $variables;

    public function __construct($mortar) {
        $this->mortar = $mortar;
        $this->worker = new ParserWorker($this, $mortar);
    }

    public function loadVariables($variables) {
        $this->variables = $variables;
    }

    public function parse($template, $stamp = null) {
        $regex = '/('.PARSER_OPEN.'((?>[^'.PARSER_MASK.']|(?1))*)'.PARSER_STOP.')/sm';
        $parsed = preg_replace_callback($regex, function($matches) {
            $params = explode('|', $matches[2]);
            $callback = array_shift($params);

            if(is_callable([$this->worker, $callback])) {
                return $this->worker->$callback(...$params);
            } else return htmlspecialchars($matches[1]);
        }, $template);

        return (is_null($stamp)?'':"<?php#$stamp?>\n").$parsed;
    }
}
