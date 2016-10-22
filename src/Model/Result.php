<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 10:32
 */

namespace Base\Model;


use Zend\Hydrator\ClassMethods;

class Result {


    protected $error;
    protected $data;
    protected $result;
    protected $class;
    protected $lastedInsert;
    protected $err=['motivo'=>'Não Passou'];

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getLastedInsert()
    {
        return $this->lastedInsert;
    }

    /**
     * @param mixed $lastedInsert
     */
    public function setLastedInsert($lastedInsert)
    {
        $this->lastedInsert = $lastedInsert;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

/**
     * @return mixed
     */
    public function getErr()
    {
        return $this->err;
    }

    /**
     * @param mixed $err
     */
    public function setErr($err)
    {
        $this->err = $err;
    }


    public function toArray()
    {
        $hidrator=new ClassMethods();
        return $hidrator->extract($this);
    }
} 