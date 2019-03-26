<?php
namespace Mortar\Engine\Build;

use Mortar\Engine\Core;

/**
 * Parses templates into php output
 */
class Parser {

    private $mortar;
    private $variables;
    
    private $tags;
    
    /**
     * instanciate a parser and give it a worker
     * @param object $mortar the mortar instance
     */
    public function __construct($mortar) {
        $this->mortar = $mortar;
    
        $this->tags = [];
    }

    /**
     * load the template variables into the parser
     * @param array $variables the variables to load
     */
    public function loadVariables($variables) {
        $this->variables = $variables;
    }

    public function get($var) {
        return $this->variables[$var];
    }

    public function tag($tag, $callback) {
        $this->tags[$tag] = $callback;
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

            if(is_callable($this->tags[$callback])) {
                return $this->tags[$callback](...$params);
            } else if(isset($this->variables[$callback])) {
                return '<?=$this->variables[\''.$callback.'\']?>';
            } else return htmlspecialchars($matches[1]);
        }, $template);

        return (is_null($stamp)?'':"<?php#$stamp?>\n").$parsed;
    }
}
