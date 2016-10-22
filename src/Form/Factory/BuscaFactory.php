<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 28/08/2016
 * Time: 23:43
 */

namespace Base\Form\Factory;


use Base\Form\BuscaForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BuscaFactory implements FactoryInterface{

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
       return new BuscaForm();
    }
}