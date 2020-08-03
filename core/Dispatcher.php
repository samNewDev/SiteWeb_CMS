<?php

class Dispatcher {

    var $request;

    function __construct(){
        $this->request = new Request();
        Router::parse($this->request->url, $this->request);
        $controller = $this->loadController();
        if (!in_array($this->request->action, get_class_methods($controller))) {
            $this->error("The controller ". $this->request->controller. " doesn't have the action ". $this->request->action);
        }
        call_user_func_array(array($controller, $this->request->action), $this->request->params);
        //to do an auto-render, in case we only type an action in the URL
        $controller->render($this->request->action);
    }

    function error($message){
        header("HTTP/1.0 404 Not Found");
        $controller = new Controller($this->request);
        $controller->set('message', $message);
        $controller->render(DS.'errors'.DS.'404');
        die();
    }

    function loadController(){
        $name = ucfirst($this->request->controller).'Controller';
        $file = ROOT.DS.'controller'.DS.$name.'.php';
        require $file;
        return new $name($this->request);
    }

}