<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 09/09/2016
 * Time: 18:00
 */

namespace Base\Services\Factory;


use Zend\Config\Writer\PhpArray;

class PhpArrayFactory {

    public function __invoke()
    {
        return new PhpArray();
    }
} 