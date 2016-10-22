<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 09/09/2016
 * Time: 17:55
 */

namespace Base\Services\Factory;


use Base\Services\Tradutor;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TradutorFactory implements FactoryInterface {

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        return new Tradutor($container);

    }
}