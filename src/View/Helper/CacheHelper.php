<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Base\View\Helper;
use Base\Model\Cache;
use Zend\View\Helper\AbstractHelper;
/**
 * Description of CacheHelper
 *
 * @author Claudio
 */
class CacheHelper extends AbstractHelper {
    protected $cache;
    public function __construct() {
        $this->cache=new Cache();

    }

    function __invoke()
    {
        return $this->cache;
    }


    public function getItem($key) {
        return $this->cache->getItem($key);
    }
}
