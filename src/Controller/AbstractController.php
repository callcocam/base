<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 17/08/2016
 * Time: 00:14
 */

namespace Base\Controller;


use Auth\Model\Users\Users;
use Auth\Storage\IdentityManager;
use Base\Form\AbstractFilter;
use Base\Form\AbstractForm;
use Base\Model\AbstractModel;
use Base\Model\AbstractRepository;
use Base\Model\Cache;
use Base\Model\Check;
use Base\Validator\MyNoRecordExists;
use Interop\Container\ContainerInterface;
use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

abstract class AbstractController extends AbstractActionController {

    /**
     * @var $containerInterface ContainerInterface
     */
    protected $containerInterface;
    /**
     * @var $IdentityManager IdentityManager
     */
    protected $IdentityManager;
    /**
     * @var $table AbstractRepository
     */
    protected $table;
    /**
     * @var AbstractModel
     */
    protected $model;
    /**
     * @var AbstractForm
     */
    protected $form;
    protected $filter;
    /**
     * @var Users
     */
    protected $user;
    protected $route;
    protected $template='/admin/admin/listar';
    protected $templateItem='/admin/admin/search-item';
    protected $templatePrint='/admin/admin/print';
    protected $terminal=false;
    protected $controller;
    protected $action;
    protected $id;
    protected $page;
    protected $data;
    protected $filtro=[];
    protected $config;
    protected $tplEditar="inserir";
    protected $colunas=3;
    protected $paginas=12;
    protected $action_print='print';
    protected $event;
    /**
     * @var $cache Cache
     */
    protected $cache;

    /**
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->event=$e;
        $this->getIdentityManager();
        $this->config=$this->containerInterface->get('ZfConfig');
        $this->cache=$this->containerInterface->get(Cache::class);
        return parent::onDispatch($e);
    }

    /**
     * @param ContainerInterface $containerInterface
     */
    abstract  function __construct(ContainerInterface $containerInterface);

    /**
     * @return AbstractRepository
     */
    public function getTable()
    {
        return $this->containerInterface->get($this->table);
    }

    /**
     * @return AbstractModel
     */
    public function getModel()
    {
        return $this->containerInterface->get($this->model);
    }

    /**
     * @return AbstractForm
     */
    public function getForm($form="")
    {
        if(empty($form)):
            if(is_string($this->form))
                $this->form=$this->containerInterface->get($this->form);
            else
                return $this->form;
        else:
            $this->form=$this->containerInterface->get($form);
        endif;
        return $this->form;
    }

    /**
     * @return AbstractFilter
     */
    public function getFilter($filter="")
    {
        if(empty($filter)):
            $this->filter=$this->containerInterface->get($this->filter);
        else:
            $this->filter=$this->containerInterface->get($filter);
        endif;
        return $this->filter;
    }

    /**
     * @return mixed
     */
    public function getData($pega=false)
    {
        $request=$this->getRequest();
        if(!$request->isPost()):
            return [];
        endif;
        if(!$this->data && !$pega){
            $this->data=array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());
        }
        return $this->data;
    }

    /**
     * @return IdentityManager
     */
    public function getIdentityManager()
    {
        $this->IdentityManager=$this->containerInterface->get(IdentityManager::class);
        $this->user=$this->IdentityManager->hasIdentity();
        return $this->IdentityManager;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        $this->cache=$this->containerInterface->get(Cache::class);
        return $this->cache;
    }



    public function indexAction()
    {

        if(!$this->IdentityManager->hasIdentity()){
            $this->Messages()->flashInfo("ACESSO NEGADO, POR FAVOR FAÇA OGIN DE USUARIO");
            return $this->redirect()->toRoute($this->config->routeAuthenticate);
        }
        //VERIFACA O NIVEL DE ACESSO
        if(!$this->IsAllowed($this->event)){
            return $this->redirect()->toRoute('auth');
        }
        $this->page=$this->params()->fromRoute('page','1');
        if($this->table):
            $this->filtro=$this->getData();
            $this->filtro['asset_id']=$this->controller;
            $this->data=$this->getTable()->select($this->filtro,$this->page,$this->paginas);
            $this->data=$this->data->toArray();
        endif;
        $view=$this->getView($this->data);
        $view->setTemplate($this->template);
        return $view;
    }

    public function searchitemAction(){
        if(!$this->IdentityManager->hasIdentity()){
            $this->Messages()->flashInfo("ACESSO NEGADO, POR FAVOR FAÇA LOGIN DE USUARIO");
            return $this->redirect()->toRoute($this->config->routeAuthenticate);
        }
        //VERIFACA O NIVEL DE ACESSO
        if(!$this->IsAllowed($this->event)){
            return $this->redirect()->toRoute('auth');
        }
        $id=$this->params()->fromRoute('id','0');
        $joins=null;
        if(isset($this->containerInterface->get('Config')[$this->model])){
            $joins = $this->containerInterface->get('Config')[$this->model];
        }

        $this->getTable()->findOneBy(["{$this->getTable()->getTable()}.id"=>$id],true,null,$joins);
        $this->data= $this->getTable()->getData()->toArray();
        $view=$this->getView($this->data);
        $view->setVariable('form',$this->form);
        $view->setTerminal(true);
        $view->setTemplate($this->templateItem);
        return $view;

    }

    public function printAction(){
        if(!$this->IdentityManager->hasIdentity()){
            $this->Messages()->flashInfo("ACESSO NEGADO, POR FAVOR FAÇA LOGIN DE USUARIO");
            return $this->redirect()->toRoute($this->config->routeAuthenticate);
        }
        //VERIFACA O NIVEL DE ACESSO
        if(!$this->IsAllowed($this->event)){
            return $this->redirect()->toRoute('auth');
        }
        $id=$this->params()->fromRoute('id','0');
        $joins=null;
        if(isset($this->containerInterface->get('Config')[$this->model])){
            $joins = $this->containerInterface->get('Config')[$this->model];
        }
        if($id){
            $this->getTable()->findBy(["{$this->getTable()->getTable()}.id"=>$id],['id'=>"ASC"],null,null,$joins);
        }
        else{
            $this->getTable()->findBy(["{$this->getTable()->getTable()}.state"=>"0"],['id'=>"ASC"],null,null,$joins);
        }

        $this->data= $this->getTable()->getData()->toArray();
        $view=$this->getView($this->data);
        $view->setTerminal($this->terminal);
        $view->setTemplate($this->templatePrint);
        return $view;
    }

    public function createAction()
    {
        if(!$this->IdentityManager->hasIdentity()){
            $this->Messages()->flashInfo("ACESSO NEGADO, POR FAVOR FAÇA OGIN DE USUARIO");
            return $this->redirect()->toRoute($this->config->routeAuthenticate);
        }
        //VERIFACA O NIVEL DE ACESSO
        if(!$this->IsAllowed($this->event)){
            return $this->redirect()->toRoute('auth');
        }
        $this->form=$this->getForm();
        $view=$this->getView($this->data);
        $view->setVariable('form',$this->form);
        $view->setTemplate('/admin/admin/editar');
        return $view;
    }

    public function updateAction()
    {
        if(!$this->IdentityManager->hasIdentity()){
            $this->Messages()->flashInfo("ACESSO NEGADO, POR FAVOR FAÇA OGIN DE USUARIO");
            return $this->redirect()->toRoute($this->config->routeAuthenticate);
        }
        $id=$this->params()->fromRoute('id','0');
        if(!(int)$id){
            return $this->redirect()->toRoute($this->route);
        }
        //VERIFACA O NIVEL DE ACESSO
        if(!$this->IsAllowed($this->event)){
            return $this->redirect()->toRoute('auth');
        }
        $this->getTable()->find($id,false);
        if(!$this->getTable()->getData()->getResult()){
            return $this->redirect()->toRoute($this->route);
        }

        $this->form=$this->getForm();
        $this->form->setData($this->getTable()->getData()->getData());
        $view=$this->getView($this->data);
        $view->setVariable('form',$this->form);
        $view->setTemplate('/admin/admin/editar');
        return $view;
    }

    public function deleteAction()
    {
        if(!$this->IdentityManager->hasIdentity()){
            $this->Messages()->flashInfo("ACESSO NEGADO, POR FAVOR FAÇA OGIN DE USUARIO");
            return $this->redirect()->toRoute($this->config->routeAuthenticate);
        }
        //VERIFACA O NIVEL DE ACESSO
        if(!$this->IsAllowed($this->event)){
            return $this->redirect()->toRoute('auth');
        }
        $id=$this->params()->fromRoute('id','0');
        if(!(int)$id){
            $this->Messages()->flashError("VOCÊ DEVE PASAR UM CODIGO VALIDO!");
            return $this->redirect()->toRoute($this->route);
        }
        $this->getTable()->find($id,false);
        if(!$this->getTable()->getData()->getResult()){
            $this->Messages()->flashError("O REGISTRO {$id} NÃO FOI ENCONTRADO!");
            return $this->redirect()->toRoute($this->route);
        }
        $this->getTable()->delete($id);
        if($this->getTable()->getData()->getResult()){
            $this->Messages()->flashSuccess("O REGISTRO {$id} FOI EXCLUIDO COM SUCESSO!");
        }
        return $this->redirect()->toRoute("{$this->route}/default",['controller'=>$this->controller,'action'=>'index']);
    }

    /**
     * @return JsonModel
     */
    public function finalizarAction()
    {
        $this->form=$this->getForm();
        $this->filter=$this->getFilter();
        $tempFile = null;
        if($this->getData()){
            $this->data['err']=$tempFile;
            $tempFile = null;
            if(isset($this->data['atachament'])){
                if(is_array($this->data['atachament'])){
                    $fileName=$this->setFileName($this->data['atachament']['name']);
                    $this->data['atachament']['name']=$fileName;
                    $this->data['images']=$this->CheckFolder($fileName);
                }
            }
            if(empty($this->data['asset_id'])){
                $this->data['asset_id']=$this->controller;
            }

            /**
             * @var $mode AbstractModel
             */
            $model=$this->getModel();
            if (isset($this->data['save-copy'])):
                $this->data['id'] = 'AUTOMATICO';
                $model->setId(null);
            endif;
            $this->cache->addItem('current_item',$this->data);
            $this->data= $this->SigaContas()->decimal($this->data);
            $model->exchangeArray($this->data);
            $this->form->getInputFilter()->setData($this->data);

            $this->data['model']=$model->toArray();
            if ($this->form->getInputFilter()->isValid()) {
                if((int)$this->data['id']){
                    $this->getTable()->update($model);
                }
                else{
                    $model->setAssetId($this->controller);
                    $this->getTable()->insert($model);
                }
                $this->cache->removeItem('current_item');
                $view=new JsonModel($this->getTable()->getData()->toArray());
                return $view;
            }
            else
            {
                $error=[];
                foreach ($this->form->getInputFilter()->getMessages() as $key=> $messages){
                    foreach($messages as  $ms){
                        $error[$key]=sprintf("[%s-%s]",$key,$ms);
                    }
                }
                $this->data['err']=$error;
                $this->data['error']=implode(PHP_EOL,$error);
            }

        }
        $view=new JsonModel($this->data);
        return $view;
    }

    public function getView($data){
        $view=new ViewModel($data);
        $view->setVariable('controller',$this->controller);
        $view->setVariable('route',$this->route);
        $view->setVariable('page',$this->page);
        $view->setVariable('tplEditar',$this->tplEditar);
        $view->setVariable('page',$this->page);
        $view->setVariable('config',$this->config);
        $view->setVariable('filtro',$this->filtro);
        $view->setVariable('colunas',$this->colunas);
        $view->setVariable('action_print',$this->action_print);
        return $view;
    }

    //Verifica e monta o nome dos arquivos tratando a string!
    public function setFileName($Name) {
        $FileName = $this->setName(substr($Name, 0, strrpos($Name, '.')));
        $FileName =strtolower($FileName) . strrchr($Name, '.');
        return $FileName;
    }
    //Verifica e cria os diretórios com base em tipo de arquivo, ano e mês!
    //Verifica e cria os diretórios com base em tipo de arquivo, ano e mês!
    public function CheckFolder($fileName,$Folder="images") {
        $ds=DIRECTORY_SEPARATOR;
        list($y, $m) = explode('/', date('Y/m'));
        $basePath = "{$Folder}{$ds}{$y}{$ds}{$m}{$ds}";
        return sprintf("%s%s",$basePath,$fileName);
    }
    //Verifica e cria o diretório base!
    public function CreateFolder($Folder) {
        if (!file_exists($Folder) && !is_dir($Folder)):
            mkdir($Folder, 0777);
        endif;
    }
    /**
     * <b>Tranforma Nome:</b> Retira acentos e caracteres especias!
     * @param STRING $Name = Uma string qualquer
     * @return STRING um nome tratado
     */
    public function setName($Name) {
        $var = strtolower(utf8_encode($Name));
        return preg_replace('{\W}', '', preg_replace('{ +}', '_', strtr(
            utf8_decode(html_entity_decode($var)), utf8_decode('ÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ'), 'AAAAEEIOOOUUCNaaaaeeiooouucn')));
    }

    public function encryptPassword($login, $password) {
        return base64_encode(Pbkdf2::calc('sha256', $password, $login, 10000, strlen($this->config->staticsalt * 2)));
    }

    public function prepareData($data) {
        if (!empty($data['password'])):
            $this->data['password'] = $this->encryptPassword($data['email'], $data['password']);
            $this->data['usr_password_confirm'] = $this->encryptPassword($data['email'], $data['usr_password_confirm']);
            if($this->data['usr_registration_token']!='active'){
                $this->data['usr_registration_token'] = md5(uniqid(mt_rand(), true));
            }

        endif;
        return $this->data;
    }

    //VIDA E MINISTERIO
    public function semanaAction(){
        $this->getData();
        return new JsonModel(['texto'=>$this->week()]);
    }


    public function week(){
        $i=strtotime(date("Y-m-d",strtotime($this->data['publish_up'])));
        if((int)$this->data['publish_down']){
            $days=$this->data['publish_down'];
            $new_date = strtotime(date("Y-m-d", strtotime($this->data['publish_up'])) . " {$days} day");
            $f=strtotime(date("Y-m-d",$new_date));
        }
        else{
            $f=strtotime(date("Y-m-d",strtotime($this->data['publish_down'])));
        }

        $dia_start =date('d', $i);
        $dia_stop =date('d', $f);

        $mes_start = $this->getMes(date('m', $i));
        $mes_stop = $this->getMes(date('m', $f));

        $ano_start = date('Y', $i);
        $ano_stop = date('Y', $f);
        if($mes_start===$mes_stop){
            $date_format="SEMANA DE {$dia_start} - {$dia_stop} DE {$mes_start} DE {$ano_start}";
        }
        else{
            if($ano_start===$ano_stop){
                $date_format="SEMANA DE {$dia_start} DE {$mes_start} - {$dia_stop} DE {$mes_stop} DE {$ano_start}";
            }
            else{
                $date_format="SEMANA DE {$dia_start} DE {$mes_start} DE {$ano_start}  - {$dia_stop} DE {$mes_stop} DE {$ano_stop}";
            }
        }
        return $date_format;
    }


    public function getMes($mes){
        switch ($mes) {

            case 1: $mes = "JANEIRO";
                break;
            case 2: $mes = "FEVEREIRO";
                break;
            case 3: $mes = "MARÇO";
                break;
            case 4: $mes = "ABRIL";
                break;
            case 5: $mes = "MAIO";
                break;
            case 6: $mes = "JUNHO";
                break;
            case 7: $mes = "JULHO";
                break;
            case 8: $mes = "AGOSTO";
                break;
            case 9: $mes = "SETEMBRO";
                break;
            case 10: $mes = "OUTUBRO";
                break;
            case 11: $mes = "NOVEMBRO";
                break;
            case 12: $mes = "DEZEMBRO";
                break;
        }
        return $mes;
    }

}