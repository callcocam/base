<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 10:21
 */

namespace Base\Form;


use Base\Model\Cache;
use Interop\Container\ContainerInterface;
use Zend\Filter\File\RenameUpload;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class AbstractFilter extends InputFilter {
    /**
     * @var ContainerInterface
     */
    protected  $containerInterface;
    protected $exclude=null;
    public  $data;
    /**
     * @param ContainerInterface $containerInterface
     */
    function __construct(ContainerInterface $containerInterface)
    {
        // TODO: Implement __construct() method.
        $this->containerInterface = $containerInterface;
        $this->data=$this->containerInterface->get(Cache::class)->getItem('current_item');

    }

    public function setExclude($exclude){
        $this->exclude=$exclude;
        
    }

    public function getExclude()
    {
        return $this->exclude;
    }
    /**
     * @param mixed $id
     */
    public function setId($id=[])
    {
        $this->add(array_replace([
            'name' => 'id',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ],$id));
    }


    /**
     * @param mixed $assetid
     */
    public function setAssetid($assetid=[])
    {
        $this->add(array_replace([
            'name' => 'asset_id',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ],$assetid));
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo=[])
    {
        $this->add(array_replace([
            'name' => 'codigo',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ],$codigo));
    }

    /**
     * @param mixed $empresa
     */
    public function setEmpresa($empresa=[])
    {
        $this->add(array_replace([
            'name' => 'empresa',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ],$empresa));
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title=[])
    {
        $this->add(array_replace([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 250,
                        'messages'=>[StringLength::TOO_LONG=>"Texto Muito Longo, Maximo:255",StringLength::TOO_SHORT=>"Texto Muito Curto, Minimo 5"]
                    ],
                ],
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages'=>[NotEmpty::IS_EMPTY=>"Campo Obrigatorio"]
                    ],
                ],
            ],
        ],$title));
    }


    /**
     * @param mixed $description
     */
    public function setDescription($description=[])
    {
        $this->add(array_replace([
            'name' => 'description',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]            ,
            'validators' => []

        ],$description));
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created=[])
    {
        $this->add(array_replace([
            'name' => 'created',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ],$created));
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified=[])
    {
        $this->add(array_replace([
            'name' => 'modified',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ],$modified));
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access=[])
    {
        $this->add(array_replace([
            'name' => 'access',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ],$access));
    }


    /**
     * @param mixed $state
     */
    public function setState($state=[])
    {
        $this->add(array_replace([
            'name' => 'state',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ],$state));
    }

    /**
     * @param mixed $csrf
     */
    public function setCsrf($csrf=[])
    {
        $this->add(array_replace("",$csrf));
    }



    /**
     * @param mixed $captcha
     */
    public function setCaptcha($captcha=[])
    {
        $this->add(array_replace("",$captcha));
    }

    /**
     * METHODS DE UPLOAD
     */
    protected $ImageAccept = [
        'image/jpg',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'audio/mp3',
        'video/mp4',
        'video/x-mpeg',
        'video/mp4',
        'audio/mpeg',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/pdf',
        'application/x-rar-compressed',
        'application/octet-stream',
        'application/zip'
    ];

    /**
     * @param array $atachament
     */
    public function setAtachament($atachament=[])
    {

        $this->add(array_replace([
            'name' => 'atachament',
            'required' => false,
            'filters' => [
                [
                    'name'=>RenameUpload::class,
                    'options' => [
                        'overwrite' => true,
                        'use_upload_name' => true,
                        'target'    =>$this->CheckFolder($this->containerInterface->get('request')->getServer('DOCUMENT_ROOT')),
                        //'randomize' => true,
                    ]

                ],
            ],
            'validators' => [
                [
                    'name' => FilesSize::class,
                    'options' => [
                        'max' => 1004800,
                        'min' => 1000,
                        'messages'=>[
                            FilesSize::TOO_BIG => 'O arquivo fornecido é maior que o tamanho de arquivo permitido',
                            FilesSize::TOO_SMALL => 'O arquivo fornecido é muito pequeno'
                        ],
                    ],
                ],
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' => $this->ImageAccept,
                        'messages'=>[
                            MimeType::FALSE_TYPE => 'Este arquivo não é um tipo permitido',
                            MimeType::NOT_DETECTED => 'O tipo de arquivo não foi detectado',
                            MimeType::NOT_READABLE => 'O tipo de arquivo não era legível',
                        ],
                    ],
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'maxWidth' => 8000,
                        'maxHeight' => 8500,
                        'messages'=>[
                            Size::TOO_BIG => 'O arquivo fornecido é maior que o tamanho de arquivo permitido',
                            Size::TOO_SMALL => 'O arquivo fornecido é muito pequeno',
                            Size::NOT_FOUND => 'O arquivo não pode ser encontrado'
                        ],
                    ],
                ],
            ]

        ],$atachament));

    }

    //Verifica e cria os diretórios com base em tipo de arquivo, ano e mês!
    public function CheckFolder($basePath,$Folder="images") {
        $ds=DIRECTORY_SEPARATOR;
        list($y, $m) = explode('/', date('Y/m'));
        $this->CreateFolder("{$basePath}{$ds}dist{$ds}{$Folder}");
        $this->CreateFolder("{$basePath}{$ds}dist{$ds}{$Folder}{$ds}{$y}");
        $this->CreateFolder("{$basePath}{$ds}dist{$ds}{$Folder}{$ds}{$y}{$ds}{$m}{$ds}");
        $basePath = "{$basePath}{$ds}dist{$ds}{$Folder}{$ds}{$y}{$ds}{$m}{$ds}";
        return $basePath;
    }

    //Verifica e cria o diretório base!
    public function CreateFolder($Folder) {
        if (!file_exists($Folder) && !is_dir($Folder)):
            mkdir($Folder, 0777);
        endif;
    }
}