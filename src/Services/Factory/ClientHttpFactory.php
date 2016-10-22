<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 29/08/2016
 * Time: 16:28
 */

namespace Base\Services\Factory;


use Base\Services\Client;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ClientHttpFactory implements FactoryInterface {

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
        $config=$container->get("ZfConfig");
        return new Client($config->serverHost);
    }
}