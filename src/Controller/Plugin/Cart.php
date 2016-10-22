<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 29/07/2016
 * Time: 21:09
 */

namespace Base\Controller\Plugin;

use Base\Model\AbstractModel;
use Base\Model\Result;
use Interop\Container\ContainerInterface;
use Base\Controller\Plugin\Interfaces\CartInterface;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

class Cart extends AbstractPlugin implements CartInterface {


    protected $_session;
    /**
     * @var ContainerInterface
     */
    private $container;

     /**
     * @var $filter AbstractInputFilter
     */
    protected $filter;

    /**
     * @var $data Result
     */
    protected $data;

    /**
     * @param array $options
     */
    public  function __construct(ContainerInterface $container, array $options,$iputFiter)
    {
        $this->_session=new Container($options['table']);
        $this->container = $container;
        $this->filter=$iputFiter;
        $this->setData(new Result());
        $this->data->setError("NÃO EXISTEM ITENS NO CARRINHO");
        $this->data->setResult(false);
        $this->data->setClass("danger");
        $this->data->setData([]);
        $this->data->setTable($options['table']);

    }


    /**
     * @param AbstractModel $model
     * @param $token
     * @return mixed
     */
    public function add(AbstractModel $model,$token)
    {

        if($this->check($model)){
            if($this->isResult())
            {
                $this->update($model,$token);
            }
            else{
                $this->_session[$this->data->getTable()]=[];
                $this->_session[$this->data->getTable()][$token]=$model->toArray();
                $this->data->setData($this->read());
                $this->data->setError("O PEDIDO FOI INICIADO COM SUCESSO");
               }
        }
        return $this->getData();
    }

    /**
     * @param AbstractModel $model
     * @param $token
     * @return mixed
     */
    public function update(AbstractModel $model,$token)
    {
        if($this->check($model)){
            $this->_session[$this->data->getTable()][$token]=$model->toArray();
            $this->data->setData($this->read());
            $this->data->setError("O PEDIDO FOI ATUALIZADO COM SUCESSO");

        }
        return $this->getData();
    }


    /**
     * @param $token
     * @param $field
     * @param $value
     * @return mixed
     */
    public function updateItem($token, $field, $value)
    {
        if($this->isResult()){
            if(isset($this->_session[$this->data->getTable()][$token])){
               $this->_session[$this->data->getTable()][$token][$field]=$value;
            }
        }
        return $this->read();
    }

    /**
     * @param $token
     * @return bool
     */
    public function getItem($token){
        if($this->isResult()){
            if(isset($this->_session[$this->data->getTable()][$token])){
              return $this->_session[$this->data->getTable()][$token];
            }
        }
        return false;
    }

    /**
     * @param $token
     * @param $field
     * @return bool/Item
     */
    public function getItemField($token,$field){
        if($this->isResult()){
            if(isset($this->_session[$this->data->getTable()][$token])){
                return $this->_session[$this->data->getTable()][$token][$field];
            }
        }
        return false;
    }


    public function remove($token)
    {
        if($this->isResult()){
            if(isset($this->_session[$this->data->getTable()][$token])){
                unset( $this->_session[$this->data->getTable()][$token]);
            }
        }
        return $this->read();
    }

    /**
     * Destroy a sessão por completo
     */
    public function destroy()
    {
       $this->_session->offsetUnset($this->data->getTable());
    }

    /**
     * @param $sessao
     * @return bool
     */
    public function getToken($sessao){
        if($this->isResult()){
            if(isset($this->_session[$this->data->getTable()][$sessao])){
                return $this->_session[$this->data->getTable()][$sessao]['codigo'];
            }
        }
        return false;
    }

    /**
     * Le os dados do carrinho
     * @return mixed
     */
    public function read()
    {
        if($this->isResult()){
            $this->data->setData($this->_session->offsetGet($this->data->getTable()));
            $this->data->setClass('success');
            $this->data->setError("O CARRINHO CONTEM ITEM");
            $this->data->setResult(true);
            return $this->data->getData();
        }
        return $this->getData();
    }


    /**
     * Calcula o total do carrinho
     * @return array
     */
    public function total()
    {

        if($this->isResult())
        {
            $pago="0.00";
            $descontos="0.00";
            $juros="0.00";
            $total="0.00";
            foreach($this->read() as $cart){
                 if(isset($cart['valor'])){
                     $total=$this->Calcular($total,$cart['valor'],'+');
                 }
                if(isset($cart['pago'])){
                    $pago=$this->Calcular($pago,$cart['pago'],'+');
                }
                if(isset($cart['descontos'])){
                    $descontos=$this->Calcular($descontos,$cart['descontos'],'+');
                }
                if(isset($cart['juros'])){
                    $juros=$this->Calcular($juros,$cart['juros'],'+');
                }
            }
        }
        return ['pago'=>$pago,'descontos'=>$descontos,'juros'=>$juros,'valor'=>$total];
    }


    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function check(AbstractModel $model)
    {
          $inputFilter=$this->filter->getInputFilter();
            $inputFilter->setData($model->toArray());
            if(!$inputFilter->isValid())
            {
                $msg=[];
                foreach($inputFilter->getMessages() as $key => $messages){
                   if(is_array($messages)):
                        foreach($messages as $message):
                            $msg[]=sprintf("%s - %s",$key,$message);
                        endforeach;
                   endif;
                }
                $this->data->setError(implode(PHP_EOL,$msg));

            }
        return $inputFilter->isValid();
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

    public function isResult()
    {
        if($this->_session->offsetExists($this->data->getTable())){
            return true;
        }
        return false;
    }


    public function Calcular($v1,$v2,$op) {
        $v1 = str_replace ( ".", "", $v1);
        $v1 = str_replace ( ",", ".", $v1);
        $v2 = str_replace ( ".", "",$v2 );
        $v2 = str_replace ( ",", ".",$v2);
        switch ($op) {
            case "+":
                $r = $v1 + $v2;
                break;
            case "-":
                $r = $v1 - $v2;
                break;
            case "*":
                $r = $v1 * $v2;
                break;
            case "%":
                $bs = $v1 / 100;
                $j = $v2 * $bs;
                $r = $v1 + $j;
                break;
            case "/":
                @$r = @$v1 / $v2;
                break;
            case "tj":
                $bs = $v1 / 100;
                $j = $v2 * $bs;
                $r = $j;
                break;
            default :
                $r = $v1;
                break;
        }
        $ret = @number_format ( $r, 2, ",", "." );
        return $ret;
    }

}