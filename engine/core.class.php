<?php

namespace Mortar\Engine;

class Core
{
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
    public function __construct($request, $router, $parser, $database)
    {
        ob_start();
        $this->setTemplatesPath(path() . VIEWS_TEMPLATES);
        $this->setCompiledPath(path() . VIEWS_COMPILED);

        $this->request = $request;

        $this->router = $router;
        $this->parser = $parser;
        $this->database = $database;

        $this->variables = [];
    }

    //shorthands
    public function tplpath()
    {
        return $this->views['templates'];
    }
    public function cmppath()
    {
        return $this->views['compiled'];
    }

    public function setTemplatesPath($path)
    {
        $this->setViewPath('templates', $path);
    }

    public function setCompiledPath($path)
    {
        $this->setViewPath('compiled', $path);
    }

    private function setViewPath($key, $path)
    {
        if (!is_dir(realpath($path))) {
            echo "Warning, folder $path does not exist.";
            return;
        }
        $this->views[$key] = realpath($path) . DS;
    }

    public function assign($arguments)
    {
        foreach ($arguments as $name => $value) {
            $this->variables[$name] = $value;
        }
    }

    public function view($name)
    {
        $this->template = $name;
    }

    public function tag($tag, $callback)
    {
        $this->parser->tag($tag, $callback);
    }

    private function compile($tpl)
    {
        $tplContents = file_get_contents($tplPath = $this->tplpath() . $tpl . VIEWS_EXTENSION);
        if (
            !file_exists($cmpPath = $this->cmppath() . md5($tpl) . ".php") ||
            fgets(fopen($cmpPath, 'r')) != '<?php#' . filemtime($tplPath) . "?>\n"
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
    public function display($debug = false)
    {
        $errorReporting = ob_get_contents();
        ob_end_clean();

        $this->parser->loadVariables($this->variables);

        //display
        if ($this->template) {
            $compiled = $this->compile($this->template);
            include_once $compiled;
        }

        echo $debug ? $errorReporting : null;
    }
}
