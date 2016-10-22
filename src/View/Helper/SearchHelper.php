<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 28/08/2016
 * Time: 23:51
 */

namespace Base\View\Helper;


use Base\Form\BuscaForm;
use Interop\Container\ContainerInterface;
use TwbBundle\Form\View\Helper\TwbBundleForm;
use Zend\Debug\Debug;
use Zend\View\Helper\AbstractHelper;


class SearchHelper extends AbstractHelper{

    /**
     * @var ContainerInterface
     */
    private $containerInterface;

    /**
     * @param ContainerInterface $containerInterface
     */
    function __construct(ContainerInterface $containerInterface)
    {

        $this->containerInterface = $containerInterface;
    }

    /**
     * @param array $filtro
     * @return string
     */
    public function search($filtro=[],$btn_add=""){
        /**
         * @var $form BuscaForm
         */

        $form=$this->containerInterface->get(BuscaForm::class);
        $form->setData($filtro);
        $form_group=$this->view->Html('div')->setClass('form-group');

        $input_group=$this->view->Html('div')->setClass('input-group input-group-lg');

        $input_group_content_1=$this->view->Html('div')->setClass('input-group-content');
        $form_control_line_1=$this->view->Html('div')->setClass('form-control-line');
        $input_group_content_1->setText($this->view->formElement($form->get('state')))->appendText($form_control_line_1);

        $input_group_content_2=$this->view->Html('div')->setClass('input-group-content');
        $form_control_line_2=$this->view->Html('div')->setClass('form-control-line');
        $input_group_content_2->setText($this->view->formElement($form->get('busca')))->appendText($form_control_line_2);

        $input_group_btn=$this->view->Html('div')->setClass('input-group-btn');
        $input_group_btn->setText($this->view->formElement($form->get('submit')));

        $input_group_btn_add=$this->view->Html('div')->setClass('input-group-btn');
        $input_group_btn_add->setText($btn_add);


        $input_group->setText($input_group_content_1);
        $input_group->appendText($input_group_content_2);
        $input_group->appendText($input_group_btn);
        $input_group->appendText($input_group_btn_add);
        $form_group->setText($input_group);

        $html[]=$this->view->form()->openTag($form);
        $html[]=$form_group;
        $html[]=$this->view->form()->closeTag($form);
        return implode(PHP_EOL,$html);

    }

    /**
     * @param array $filtro
     * @return string
     */
    public function search_basic($btn_refresh="/"){
        /**
         * @var $form BuscaForm
         */
        $form=$this->containerInterface->get(BuscaForm::class);
        $form->setAttribute('class','navbar-search margin-bottom-xxl');
        $form_group=$this->view->Html('div')->setClass('form-group');
        $form_group->setText($this->view->formElement($form->get('busca')));
        $html[]=$this->view->form()->openTag($form);
        $html[]=$form_group;
        $html[]=$this->view->MakeButton(new \Zend\Form\Element\Button('align-left',array('glyphicon' => 'search')));
        $html[]=$this->view->Html('a')->setAttributes(['class'=>'btn btn-default','href'=>$this->view->url($this->view->router()->getMatchedRouteName(),['controller'=>$this->view->router()->getController(),'action'=>$this->view->router()->getAction()])])->setText($this->view->fontAwesome('refresh'));
        $html[]=$this->view->form()->closeTag($form);
        return implode(PHP_EOL,$html);

    }


} 