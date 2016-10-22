<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Router extends AbstractHelper
{
    protected $router;
    protected $namespace;
    protected $controller;
    protected $action;
    protected $matchedRouteName;

    public function __construct($router)
    {
        $params=$router->getParams();
        if(isset($params['__NAMESPACE__']))
        {

        }
        if(isset($params['controller']))
        {

        }
        if(isset($params['__CONTROLLER__']))
        {
            $this->controller=$params['__CONTROLLER__'];
        }
        if(isset($params['action']))
        {
            $this->action=$params['action'];
        }
        $this->matchedRouteName=$router->getMatchedRouteName();
        $this->router = $router;
    }

    /**
     * @return mixed
     */
    public function getMatchedRouteName()
    {
        return $this->matchedRouteName;
    }



    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

}