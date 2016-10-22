<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 09/09/2016
 * Time: 17:55
 */

namespace Base\Services;

use Admin\Model\Traduction\TraductionRepository;
use Interop\Container\ContainerInterface;

class Tradutor {


    protected $containerInterface;
    /**
     * @var array
     */
    protected $traduction;
    protected $config;
    protected $path;
    /**
     * $phparraywriter PhpArray
     */
    protected $phparraywriter;

    /**
     * @param ContainerInterface $containerInterface
     */
    function __construct(ContainerInterface $containerInterface)
    {
        $this->containerInterface=$containerInterface;
        $this->config=$containerInterface->get('ZfConfig');
        $module=$this->config->module;
        $ds=DIRECTORY_SEPARATOR;
        $this->path=".{$ds}{$module}{$ds}Base{$ds}language{$ds}pt_BR.php";
        $this->phparraywriter=$containerInterface->get('Base\Services\PhpArrayFactory');
        $this->traduction =  $this->phparraywriter->toString(include $this->path);

    }

    public function read(){
        return $this->traduction;
    }

    public function writer(){
        $traducao=[];
        if($this->traduction){
            $table=$this->containerInterface->get(TraductionRepository::class)->findBy();
            foreach($table->getData() as $o){
                $tradutor=$o->toArray();
                $label=explode("_",$tradutor['title']);
                $placeholder=str_replace(end($label),'PLACEHOLDER',$tradutor['title']);
                $desc=str_replace(end($label),'DESC',$tradutor['title']);
                $traducao[$tradutor['title']]=$tradutor['description'];
                if(reset($label)=="FILD"){
                    $traducao[$desc]=$tradutor['dica_tela'];
                    $traducao[$placeholder]=$tradutor['placeholder'];
                }

            }
            file_put_contents($this->path,$this->phparraywriter->toString($traducao));
        }


    }
}