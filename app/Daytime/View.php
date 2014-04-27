<?php namespace Daytime;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;

class View {

    /**
     * @var \Twig_Environment
     */
    protected $twig_env;

    /**
     * Create a new instance of View
     */
    public function __construct()
    {
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem(path('templates'));
        $this->twig_env = new Twig_Environment($loader, array(
            'cache' => path('templates.storage'),
            'auto_reload' => isDebugging()
        ));
    }

    /**
     * Render the output for a template
     * 
     * @param string  $template
     * @param array   $data
     * @return string
     */
    public function render($template, $data = null)
    {
        $template = $this->twig_env->loadTemplate($template);
        return $template->render($data);
    }
} 
