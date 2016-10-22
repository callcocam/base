<?php
/**
 * Created by PhpStorm.
 * User: Claudio
 * Date: 18/09/2016
 * Time: 22:16
 */

namespace Base\Services;




use PagSeguro\Domains\Requests\DirectPayment\Boleto;
use PagSeguro\Library;

class UsingBoleto{

    protected $boleto;
    protected  $paymentlink;

    /**
     * @return mixed
     */
    public function getBoleto()
    {
        return $this->boleto;
    }

    /**
     * @return mixed
     */
    public function getPaymentlink()
    {
        return $this->paymentlink;
    }


    /**
     * @param string $extraamount
     * @param string $Mode
     * @param string $currency
     */
    function __construct($reference,$shipping,$item,$extraamount="0.00",$Mode="DEFAULT",$currency='BRL')
    {
        Library::initialize();
        Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
        Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");
        $this->boleto=new Boleto();
        $this->boleto->setMode($Mode);
        $this->boleto->setCurrency($currency);
        $this->boleto->setExtraAmount($extraamount);
        $this->boleto->setReference($reference);

        $this->boleto->setSender()->setName($shipping['title']);
        $this->boleto->setSender()->setEmail('email@sandbox.pagseguro.com.br');
        $phone_code=substr($shipping['phone'],0,2);
        $phone=substr($shipping['phone'],3,8);
        $this->boleto->setSender()->setPhone()->withParameters(
            $phone_code,
            $phone
        );

        $this->boleto->setSender()->setDocument()->withParameters(
            'CPF',
            $shipping['cpf']
        );

        $this->boleto->setSender()->setHash($shipping['sender_hash']);

        $this->boleto->setSender()->setIp('127.0.0.0');
        // Set shipping information for this payment request
        $this->boleto->setShipping()->setAddress()->withParameters(
            $shipping['endereco'],
            $shipping['numero'],
            $shipping['bairro'],
            $shipping['cep'],
            $shipping['cidade'],
            $shipping['uf'],
            $shipping['pais'],
            $shipping['complemento']
        );
          list($id,$description,$qtd,$valor)=$item;
          $this->boleto->addItems()->withParameters($id,$description,$qtd,$valor);


        try {
            //Get the crendentials and register the boleto payment
            $result = $this->boleto->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
               $this->paymentlink= $result->getPaymentLink();
        } catch (Exception $e) {
               die($e->getMessage());
        }


    }



}