<?php
namespace Mortar\Mortar\Build;

use Mortar\Mortar\Core;

/**
 * Parses templates into php output
 */
class Parser {

    private $mortar;
    private $worker;
    private $variables;

    /**
     * instanciate a parser and give it a worker
     * @param object $mortar the mortar instance
     */
    public function __construct($mortar) {
        $this->mortar = $mortar;
        $this->worker = new ParserWorker($this, $mortar);
    }

    /**
     * load the template variables into the parser
     * @param array $variables the variables to load
     */
    public function loadVariables($variables) {
        $this->variables = $variables;
    }

    /**
     * parse the template
     * @param string $template the template to parse
     * @param string $stamp the identifier put into the parsed file to check for when to update it from the template
     * @return string the parsed file
     */
    public function parse($template, $stamp = null) {
        $regex = '/('.PARSER_OPEN.'((?>[^'.PARSER_MASK.']|(?1))*)'.PARSER_STOP.')/sm';
        $parsed = preg_replace_callback($regex, function($matches) {
            $params = explode('|', $matches[2]);
            $callback = array_shift($params);

            if(is_callable($this->worker->tags[$callback])) {
                return $this->worker->$callback(...$params);
            } else return htmlspecialchars($matches[1]);
        }, $template);

        return (is_null($stamp)?'':"<?php#$stamp?>\n").$parsed;
    }

    public function tag($tag, $callback) {
        $this->worker->tags[$tag] = $callback;
    }
}
