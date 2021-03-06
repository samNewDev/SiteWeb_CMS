<?php
class Controller{

    public $request;
    //We store data that we want to display in this array
    private $vars = array();
    public $layout = 'default';
    private $rendered = false;
    /**
     * Initialize the request with all its parameters (controller, action and params)
     */
    function __construct($request = null){
        if($request){
            $this->request = $request;
        }
    }
    /**
     * Allows rendering views by the controller
     * @param $view the index page
     */
    public function render($view){
        if($this->rendered){
            return false;
        }
        extract($this->vars);
        //to handle errors and to have the possibility to render an action starting by a DS('/')
        if (strpos($view, DS) === 0) {
            $view = ROOT.DS.'view'.$view.'.php';
        }else {
            $view = ROOT.DS.'view'.DS.$this->request->controller.DS.$view.'.php';
        }
        ob_start();
        require($view);
        $content_for_layout = ob_get_clean();
        require ROOT.DS.'view'.DS.'layout'.DS.$this->layout.'.php';
        $this->rendered = true;
    }
    /**
     * Inserts variables we injected in PageController inside the array $vars
     * @param $key is the variable name
     * @param $value is the variable value
     */
    public function set($key, $value=null){
        if(is_array($key)){
            $this->vars += $key;
        }else {
            $this->vars[$key] = $value;
        }
    }
    /**
     * Allows charging a model
     */
    function loadModel($name){
        $file = ROOT.DS.'model'.DS.$name.'.php';
        require_once($file);
        //to avoid charging twice the object
        if (!isset($this->$name)) {
            $this->$name = new $name();
        }else {
            echo "Not charged";
        }
    }
    /**
     * Allows managing errors
     */
    function e404($message){
        header("HTTP/1.0 404 Not Found");
        $this->set('message', $message);
        $this->render(DS.'errors'.DS.'404');
        die();
    }
    /**
     * Allows to call a controller from a view
     */
    function request($controller, $action){
        $controller .= 'Controller';
        require_once ROOT.DS.'controller'.DS.$controller.'.php';
        $c = new $controller;
        return $c->$action();
    }
}