<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 10:20
 */

namespace Base\Model;


use Zend\Hydrator\ClassMethods;

class AbstractModel {

    protected $id;
    protected $codigo;
    protected $empresa;
    protected $asset_id;
    protected $description;
    protected $state;
    protected $access;
    protected $created;
    protected $modified;

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return mixed
     */
    public function getAssetId()
    {
        return $this->asset_id;
    }

    /**
     * @param mixed $asset_id
     */
    public function setAssetId($asset_id)
    {
        $this->asset_id = $asset_id;
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        if($this->created instanceof \DateTime){
            return $this->created->format('Y-m-d');
        }
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = new \DateTime($created);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param mixed $empresa
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        if($this->modified instanceof \DateTime){
            return $this->modified->format('Y-m-d');
        }
        return $this->modified;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified)
    {

        $this->modified =new \DateTime($modified);
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }




    public function exchangeArray($options=[])
    {
        $hidrator=new ClassMethods();
        $hidrator->hydrate($options,$this);
    }

    public function toArray()
    {
        $hidrator=new ClassMethods();
        return $hidrator->extract($this);
    }

} 