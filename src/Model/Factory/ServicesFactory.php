<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 26/08/2016
 * Time: 18:45
 */

namespace Base\Model\Factory;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Admin\Form\Factory\ConfiguracaoFilterFactory;

use Admin\Form\Factory\ConfiguracaoFormFactory;

use Admin\Form\ConfiguracaoFilter;

use Admin\Form\ConfiguracaoForm;

use Admin\Model\Configuracao\Factory\ConfiguracaoFactory;

use Admin\Model\Configuracao\Factory\ConfiguracaoRepositoryFactory;

use Admin\Model\Configuracao\Configuracao;

use Admin\Model\Configuracao\ConfiguracaoRepository;

use Admin\Form\Factory\ClientesFilterFactory;

use Admin\Form\Factory\ClientesFormFactory;

use Admin\Form\ClientesFilter;

use Admin\Form\ClientesForm;

use Admin\Model\Clientes\Factory\ClientesFactory;

use Admin\Model\Clientes\Factory\ClientesRepositoryFactory;

use Admin\Model\Clientes\Clientes;

use Admin\Model\Clientes\ClientesRepository;

class ServicesFactory implements FactoryInterface{

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
        return[
            Configuracao::class=>ConfiguracaoFactory::class,

            ConfiguracaoRepository::class=>ConfiguracaoRepositoryFactory::class,

            ConfiguracaoForm::class=>ConfiguracaoFormFactory::class,

            ConfiguracaoFilter::class=>ConfiguracaoFilterFactory::class,

            Clientes::class=>ClientesFactory::class,

            ClientesRepository::class=>ClientesRepositoryFactory::class,

            ClientesForm::class=>ClientesFormFactory::class,

            ClientesFilter::class=>ClientesFilterFactory::class,
        ];
    }
}