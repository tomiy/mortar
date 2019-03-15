<?php
namespace Mortar\Mortar;

use Mortar\Foundation\Traits\Singleton;

use Mortar\Mortar\Http\Router;
use Mortar\Mortar\Build\Parser;

class Core extends Singleton {
    private $views;

    private $router;
    private $parser;
    private $template;
    private $variables;

    public $request;

    /**
     * Instanciate the paths with config values
     * Start capturing the output used for debug
     */
    protected function __construct($request, $tplPath = VIEWS_TEMPLATES, $cmpPath = VIEWS_COMPILED) {
        ob_start();
        $this->setTemplatesPath(path().$tplPath);
        $this->setCompiledPath(path().$cmpPath);

        $this->request = $request;

        $this->router = new Router($this);
        $this->parser = new Parser($this);
        $this->variables = [];
    }

    public function component($component) {
        return $this->$component;
    }

    //shorthands
    public function tplpath() { return $this->views['templates']; }
    public function cmppath() { return $this->views['compiled']; }

    public function setTemplatesPath($path) {
        $this->setViewPath('templates', $path);
    }

    public function setCompiledPath($path) {
        $this->setViewPath('compiled', $path);
    }

    private function setViewPath($key, $path) {
        if(!is_dir(realpath($path))) {
            echo "Warning, folder $path does not exist.";
            return;
        }
        $this->views[$key] = realpath($path).DS;
    }

    public function assign($arguments) {
        foreach($arguments as $name => $value) {
            $this->variables[$name] = $value;
        }
    }

    public function view($name) {
        $this->template = $name;
    }

    public function tag($tag, $callback) {
        $this->parser->tag($tag, $callback);
    }

    private function compile($tpl) {
        $tplContents = file_get_contents($tplPath = $this->tplpath().$tpl.VIEWS_EXTENSION);
        if(
            !file_exists($cmpPath = $this->cmppath().md5($tpl).".php") ||
            fgets(fopen($cmpPath, 'r')) != '<?php#'.filemtime($tplPath)."?>\n"
        ) {
            $compiled = $this->parser->parse($tplContents, filemtime($tplPath));
            file_put_contents($cmpPath, $compiled);
        }

        return $cmpPath;
    }

    /**
     * Capture the debug and display the content
     * @param  boolean $debug should we return the debug
     * @return string         the debug
     */
    public function display($debug = false) {
        $errorReporting = ob_get_contents();
        ob_end_clean();

        $this->parser->loadVariables($this->variables);

        //display
        if($this->template) {
            $compiled = $this->compile($this->template);
            include_once $compiled;
        }

        echo $debug?$errorReporting:null;
    }
}
