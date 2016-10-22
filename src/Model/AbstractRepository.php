<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 10:20
 */

namespace Base\Model;


use ArrayObject;
use Zend\Db\Adapter\Exception\InvalidQueryException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Debug\Debug;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Validator\Digits;


abstract class AbstractRepository{

    abstract function __construct(TableGateway $tableGateway);

    /**
     * @var $tableGateway TableGateway
     */
    protected $tableGateway;
    protected $columns = array();
    protected $joins = array();
    protected $where;
    protected $currents;
    protected $limit = 50;
    protected $offset = 0;
    protected $extraWere = array();
    protected $from = array();
    protected $group = null;
    protected $order =['id'=>'DESC'];
    protected $distinct = null;

    /**
     * @var $data Result
     */
    protected $data;

    const ERROR="danger";
    const SUCCESS="success";
    const INFO="info";

    public function getTable()
    {
       return $this->tableGateway->getTable();
    }

    public function getAdapter(){
        return $this->tableGateway->getAdapter();
    }

    /**
     * @param null $where
     * @param int $page
     * @internal param array $filtro
     * @return ResultSet
     */
    public function select($where = null,$page=1,$paginas=12)
    {
        $this->setData(new Result());
        $this->filtro($where);
        $select=$this->tableGateway->getSql()->select();
        if ($this->columns):
            $select->columns($this->columns);
            //
        endif;
        //verificar e monta as união de tabelas vindas de sua table real
        if ($this->joins):
            foreach ($this->joins as $j):
                $select->join($j['tabela'], $j['w'], $j['c'], $j['predicate']);
            endforeach;
        endif;
        $select->where($this->where);
        $select->order($this->order);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $resultSet = new ResultSet(); //$this->tableGateway->getResultSetPrototype();
        $resultSet->initialize($results);
        $data = new Paginator(new
            ArrayAdapter($resultSet->toArray())
        );
        $data->setItemCountPerPage($paginas);
        $data->setCurrentPageNumber($page);
        if($data->count()):
            $this->data->setData($data);
            $this->data->setResult($data->count());
            $this->data->setError("REGISTRO ENCOTRADO");
            $this->data->setClass(self::SUCCESS);
            if (isset($data)):
                $this->setCurrents($data);
            endif;

        else:
            $this->data->setResult(false);
            $this->data->setError("NENHUM REGISTRO ENCOTRADO");
            $this->data->setClass(self::ERROR);
        endif;

        return $this->getData();
    }

    public function valueOptions($where = null,$ordering,$limit=null)
    {
        $data=$this->tableGateway->select(function (Select $select) use ($where,$ordering,$limit) {
            $select->where($where);
            $select->order($ordering);
            if($limit) {
                $select->limit($limit);
            }
        });
        if($data->count()):
             if (isset($data)):
                $this->setCurrents($data);
            endif;
        endif;

        return $this->getCurrents();
    }

    protected function filtro($condicao){
        $this->where = new  Where();
        if(isset($condicao['asset_id']))
        {
            // $operator=$condicao['state']>=0?"=":">";
            $this->where->addPredicate(new  Operator("{$this->getTable()}.asset_id", "=",$condicao['asset_id']));
        }
        if(isset($condicao['state']) && $condicao['state']>=0)
        {
            // $operator=$condicao['state']>=0?"=":">";
            $this->where->addPredicate(new  Operator("{$this->getTable()}.state", "=",$condicao['state']));
        }
        if(isset($condicao['busca']) && !empty($condicao['busca']))
        {
            $this->where->expression("CONCAT_WS(' ', {$this->getTable()}.title, {$this->getTable()}.description) LIKE ?", "%{$condicao['busca']}%");
        }
        return  $this->where;

    }

    /**
     * @param $id
     * @return array|ArrayObject|null
     */
    public function find($id,$object=true)
    {
        $this->setData(new Result());
        $data=$this->tableGateway->select(['id'=>$id])->current();

        if($data):
        if($object){
            $this->data->setData($data);
        }
        else{
            $this->data->setData($data->toArray());
        }
        $this->data->setResult(TRUE);
        $this->data->setError("REGISTRO ENCOTRADO");
        $this->data->setClass(self::SUCCESS);

        else:
            $this->data->setResult(false);
            $this->data->setError("NENHUM REGISTRO ENCOTRADO");
            $this->data->setClass(self::ERROR);
        endif;

        return $this->getData();
    }

    /**
     * @param $where
     * @return ResultSet
     */
    public function findBy($where=['state'=>'0'],$ordering=['id'=>'ASC'],$limit=null,$columns=null,$joins=null){
        $this->setData(new Result());
        $data=$this->tableGateway->select(function (Select $select) use ($where,$ordering,$limit,$columns,$joins) {
            $select->where($where);
            if ($columns):
                $select->columns($columns);
            endif;
            if ($joins):
                foreach ($joins as $j):
                    $select->join($j['tabela'], $j['w'], $j['c'], $j['predicate']);
                endforeach;
            endif;
            $select->order($ordering);
            if($limit) {
                $select->limit($limit);
            }
        });

        if($data->count()):
            $this->setCurrents($data);
            $this->data->setData($data);
            $this->data->setResult($data->count());
            $this->data->setError("REGISTRO ENCOTRADO");
            $this->data->setClass(self::SUCCESS);
        else:
            $this->data->setResult(false);
            $this->data->setError("NENHUM REGISTRO ENCOTRADO");
            $this->data->setClass(self::ERROR);
        endif;
        return $this->getData();
    }

    /**
     * @param $where
     * @return array|ArrayObject|null
     */
    public function findOneBy($where,$object=true,$columns=null,$joins=null){
        $this->setData(new Result());
        $data=$this->tableGateway->select(function (Select $select) use ($where,$columns,$joins) {
            $select->where($where);
            if ($columns):
                $select->columns($columns);
            endif;
            if ($joins):
                foreach ($joins as $j):
                    $select->join($j['tabela'], $j['w'], $j['c'], $j['predicate']);
                endforeach;
            endif;
        })->current();

        if($data):
            if($object){
                $this->data->setData($data);
                $this->setCurrents($data);
            }
            else{
                $this->data->setData($data->toArray());
                $this->setCurrents($data->toArray());
            }
            $this->data->setResult(1);
            $this->data->setError("REGISTRO ENCOTRADO");
            $this->data->setClass(self::SUCCESS);
        else:
            $this->data->setResult(false);
            $this->data->setError("NENHUM REGISTRO ENCOTRADO");
            $this->data->setClass(self::ERROR);
        endif;
        return $this->getData();
    }

    /**
     * @param AbstractModel $model
     * @return mixed
     */
    public function insert(AbstractModel $model)
    {
        $this->setData(new Result());
        try {
            if(empty($model->getCodigo())){
                $model->setCodigo(md5(date('YmdHis')));
            }
            if ($this->tableGateway->insert($model->toArray())):
                $this->find($this->tableGateway->getLastInsertValue(),false);
                $this->data->setLastedInsert($this->data->getData());
                $this->data->setError("O REGISTRO [ <b>{$model->getTitle()}</b> ] FOI SALVO COM SUCESSO!");
                $this->data->setResult(TRUE);
                $this->data->setClass(self::SUCCESS);
                return $this->getData();
            endif;
            $this->data->setError("Nao Foi Possivel Finalizar a Operação!");
            $this->data->setResult(FALSE);
            $this->data->setErr(FALSE);
            $this->data->setClass(self::ERROR);
        } catch (InvalidQueryException $exc) {
            $this->data->setError(sprintf("ERROR: %s - %s", $exc->getCode(), $exc->getMessage()));
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
        }
        return $this->getData();
    }

    /**
     * @param AbstractModel $model
     * @param null $where
     * @return \Base\Model\Result
     */
    public function update(AbstractModel $model, $where = null)
    {
        $this->setData(new Result());
        $result=false;
        try {
            $oldData = $this->find($model->getId());
            if ($oldData) {
                if($where):
                    $result = $this->tableGateway->update($model->toArray(), [$where]);
                else:
                    $result = $this->tableGateway->update($model->toArray(), ['id' => $model->getId()]);
                endif;

                if ($result) {
                    $this->find($model->getId(),false);
                    $this->data->setError("O REGISTRO [ <b>{$model->getTitle()}</b> ] FOI ATUALIZADO COM SUCESSO!");
                    $this->data->setLastedInsert($this->data->getData());
                    $this->data->setClass(self::SUCCESS);
                    $this->data->setResult(TRUE);
                    $this->data->setErr(FALSE);
                } else {
                    $this->data->setResult(FALSE);
                    $this->data->setClass(self::ERROR);
                    $this->data->setError("NÃO FOI POSSIVEL CONCLUIR A SUA SOLISITAÇÃO, NENHUMA ALTERAÇÃO FOI DETECTADA NO REGISTRO [ <b>{$model->getTitle()}</b> ]!");
                }
                return $this->getData();
            }
            $this->data->setError("NÃO FOI POSSIVEL CONCLUIR A SUA SOLISITAÇÃO, POR QUE NENHUM REGISTRO CORRESPONDENTE FOI ENCONTRADO!!");
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
        } catch (InvalidQueryException $exc) {
            $this->data->setError(sprintf("ERROR: %s - %s", $exc->getCode(), $exc->getMessage()));
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
        }
        return $this->getData();

    }

    /**
     * @param $where
     * @return \Base\Model\Result
     */
    public function delete($where)
    {
        $this->setData(new Result());
        try {
            $filter=new Digits();
            if($filter->isValid($where)):
                $result = $this->tableGateway->delete(['id' =>$where]);
            else:
                $result = $this->tableGateway->delete($where);
            endif;
            if ($result) {
                $this->data->setResult(TRUE);
                $this->data->setError("O REGISTRO FOI EXCLUIDO COM SUCESSO!");
                $this->data->setClass(self::SUCCESS);
                $this->data->setLastedInsert(TRUE);
                $this->data->setErr(FALSE);
                return $this->getData();
            }

            $this->data->setError("NÃO FOI POSSIVEL CONCLUIR A SUA SOLISITAÇÃO, POR QUE NENHUM REGISTRO CORRESPONDENTE FOI ENCONTRADO!!");
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
            return $this->getData();
        } catch (InvalidQueryException $exc) {
            $this->data->setError(sprintf("ERROR: %s - %s", $exc->getCode(), $exc->getMessage()));
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
            return $this->getData();
        }
    }

    /**
     * @param $currents
     * @return $this
     */
    public function setCurrents($currents){
       $this->currents=$currents;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrents()
    {
        return $this->currents;
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
    public function setData(Result $data)
    {
        $this->data = $data;
    }

    /**
     * @param $Name
     * @return mixed
     */
    public function StringSpace($Name)
    {
        //$var = strtolower(utf8_encode($name));
        $var = ucwords(str_replace("_", " ", $Name));
        $Name = preg_replace('{\W}', '', preg_replace('{ +}', '', strtr(
            utf8_decode(html_entity_decode($var)), utf8_decode('ÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ'), 'AAAAEEIOOOUUCNaaaaeeiooouucn')));
        return $Name;
    }

 /** MANUTENÇÃO DOS DADOS DOS USUARIOS */

    public function getUserByToken($token) {
        $rowset = $this->tableGateway->select(['usr_registration_token' => $token]);
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function activateUser($usr_id) {
        $this->setData(new Result());
        $data['state'] = 0;
        $result= $this->tableGateway->update($data, ['id' => (int) $usr_id]);
        if ($result) {
            $this->find($usr_id,false);
            $this->data->setError("O SUA SENHA FOI ATUALIZADO COM SUCESSO!");
            $this->data->setLastedInsert($this->data->getData());
            $this->data->setClass(self::SUCCESS);
            $this->data->setResult(TRUE);
        } else {
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
            $this->data->setError("NÃO FOI POSSIVEL CONCLUIR A SUA SOLISITAÇÃO, NENHUMA ALTERAÇÃO FOI DETECTADA NO REGISTRO!");
        }
        return $result;
    }

    public function getUserByEmail($usr_email) {
        $rowset = $this->tableGateway->select(['email' => $usr_email]);
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function changePassword($usr_id, $password) {
        $this->setData(new Result());
        $data['password'] = $password;
        $result=$this->tableGateway->update($data, ['id' => (int) $usr_id]);
        if ($result) {
            $this->find($usr_id,false);
            $this->data->setError("O SUA SENHA FOI ATUALIZADO COM SUCESSO!");
            $this->data->setLastedInsert($this->data->getData());
            $this->data->setClass(self::SUCCESS);
            $this->data->setResult(TRUE);
        } else {
            $this->data->setResult(FALSE);
            $this->data->setClass(self::ERROR);
            $this->data->setError("NÃO FOI POSSIVEL CONCLUIR A SUA SOLISITAÇÃO, NENHUMA ALTERAÇÃO FOI DETECTADA NO REGISTRO!");
        }
        return $result;
    }

    public function url(AbstractModel $model){
       return Check::Name($model->getTitle());
    }

}