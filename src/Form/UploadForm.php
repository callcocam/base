<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 21:45
 */

namespace Base\Form;

use Interop\Container\ContainerInterface;
use Zend\Form\Element;
use Zend\InputFilter;

class UploadForm extends AbstractForm
{
    public function __construct(ContainerInterface $containerInterface,$name = null, $options = [])
    {
        parent::__construct($containerInterface,$name, $options);
        $this->addElements(['name'=>'title','attributes'=>[ 'id' => 'id']]);
        $this->addElements(['name'=>'cidade','type'=>'select','attributes'=>[ 'id' => 'id']]);

        $this->setCaptha(['options'=>[
            'label' => 'Virificação.',
            'captcha' => [
                'class'   => 'Image',
                'options' => [
                    'font' =>  './data/arial.ttf',
                    'width' => 200,
                    'height' => 100,
                    'dotNoiseLevel' => 40,
                    'lineNoiseLevel' => 3,
                    'wordLen' => 2,
                    'imgDir' => './public/images/captcha/generated',
                    'imgUrl' => '/images/captcha/generated'
                ],
            ],
        ]]);
        $this->get('cidade')->setOptions(['value_options'=>['1'=>'Primeiro']]);
        $this->setAtachament([]);
        $this->addInputFilter();
    }



    public function addInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        // File Input
        $fileInput = new InputFilter\FileInput('atachament');
        $fileInput->setRequired(true);
        $fileInput->getValidatorChain()
            ->attachByName('filesize',      ['max' => 904800])
            ->attachByName('filemimetype',  ['mimeType' => 'image/png,image/x-png,image/jpeg,image/jpg'])
            ->attachByName('fileimagesize', ['maxWidth' => 5000, 'maxHeight' => 2500]);

        $fileInput->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'overwrite' => true,
                'use_upload_name' => true,
                'target'    => './data/',
                //'randomize' => true,
            ]
        );
        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }
}