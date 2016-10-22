<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 10:20
 */

namespace Base\Model;


use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class AbstractFactory {

    protected $table;
    protected $model;
    protected $adapter;

    /**
     * @return TableGateway
     */
    public function getTablegateway(ContainerInterface $containerInterface)
    {
        $this->adapter=AdapterInterface::class;
        $resultSet=new ResultSet();
        $ObjectPrototype=$resultSet->setArrayObjectPrototype($containerInterface->get($this->model));
        return new TableGateway($this->table,$containerInterface->get($this->adapter),null, $ObjectPrototype);
    }
} 