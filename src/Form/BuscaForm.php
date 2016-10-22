<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 28/08/2016
 * Time: 23:35
 */

namespace Base\Form;


use Zend\Form\Form;

class BuscaForm extends Form{

    /**
     *__construct
     */
    public function __construct() {
        // Configurações iniciais do Form
        parent::__construct("Busca");
        $this->setAttribute("method", "post");
        $this->setAttribute("class", "form form-inverse");
        $this->setAttribute("role", "search");



        // informações da coluna state:
        $this->add(
            array(
                'type' => 'Select',
                'name' => 'state',
                'options' => array(
                    'value_options'=>array('-1'=>'Tudo','0'=>'Publicado','1'=>'Arquivado')
                ),
                'attributes' => array(
                    'id' => 'state',
                    'class' => 'form-control',
                    'placeholder' => 'FILD_STATE_PLACEHOLDER',
                    'title' => 'FILD_STATE_DESC',
                ),
            )
        );

        // informações da coluna title:
        $this->add(
            array(
                'type' => 'text',
                'name' => 'busca',
                'options' => array(
                    'label' => '- : -',
                ),
                'attributes' => array(
                    'id' => 'busca',
                    'class' => 'form-control',
                    'placeholder' => 'FILD_BUSCA_PLACEHOLDER',
                    'title' => 'FILD_BUSCA_DESC',
                ),
            )
        );

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'BUSCAR',
                'id' => 'submit',
                'class' => 'btn ink-reaction btn-raised btn-info'
            ),
        ));
    }

}
