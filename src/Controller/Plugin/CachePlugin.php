<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Base\Controller\Plugin;

/**
 * Description of CachePlugin
 *
 * @author Claudio
 */
class CachePlugin extends \Zend\Mvc\Controller\Plugin\AbstractPlugin {

    protected $cache;

    public function __construct() {
        $this->cache = new \Base\Model\Cache();
    }

    public function getItem($key) {
        return $this->cache->getItem($key);
    }

}
