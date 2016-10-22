<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 27/08/2016
 * Time: 13:53
 */

namespace Base\View\Helper;


use Zend\Debug\Debug;
use Zend\Navigation;
use Zend\View\Helper\Navigation\AbstractHelper;

class NavigationHelper extends AbstractHelper{

    protected $tipoIco='glyphicon';//fa ou 'glyphicon';
    protected $renderHtlm=[];
    protected $dropdown='gui-folder';
    protected $dropdownMenu='';
    protected $attr;

    /**
     * Renders helper
     *
     * @param  string|Navigation\AbstractContainer $container [optional] container to render.
     *                                         Default is null, which indicates
     *                                         that the helper should render
     *                                         the container returned by {@link
     *                                         getContainer()}.
     * @return string helper output
     * @throws \Zend\View\Exception\ExceptionInterface<ul class="nav navbar-nav">
     */
    public function render($container = null,$attr=['class'=>'gui-controls','id'=>'main-menu'])
    {
        $this->attr=$attr;
        $elementUl=$this->view->Html('ul')->setAttributes($attr);
        if ($container):
            foreach ($container as $page):
                if (!$this->view->navigation()->accept($page)) continue;
                $hasChildren = $page->hasPages();
                if ($hasChildren):
                    if ($this->access($page)) :
                      $this->child($page,$container);
                    endif;
                else:
                    $this->parent($page,$container);
                endif;
            endforeach;
        endif;
        $this->renderHtlm[]=$this->nav_user();
        $elementUl->setText(implode("",$this->renderHtlm));
        echo $elementUl;
    }

    protected function parent($page,$container){

        if($page->getHref()=="#")
            return "";

        $elementLi=$this->view->Html('li');
        if($page->isActive()){
            $elementLi->setClass('active');
        }
        $link=$this->view->Html('a')->setAttributes(['href'=>$page->getHref()]);
        if(!empty($page->getTarget())){
            $link->appendAttribute('target',$page->getTarget());
        }
          $title=$this->view->escapeHtml($this->view->translate($page->getLabel(), $this->view->navigation($container)->getTranslatorTextDomain()));
          $span=$this->view->Html('span')->setClass('hidden-xs')->setText(
              strtoupper($title)
          );
          if(!empty($page->get("icone"))):
            if($this->tipoIco=='fa'):
                $i=$this->view->fontAwesome($page->get("icone"));
            else:
                $i=$this->view->glyphicon($page->get("icone"));
            endif;
            $link->appendText($span);
        else:
            $link->setText($span);
        endif;
        $div=$this->view->Html('div')->setClass('gui-icon')->setText($i)->appendText($span);

        $link->setText($div);
        $elementLi->setText($link);

        $this->renderHtlm[]=$elementLi;

    }

    protected function child($page,$container){
        $elementLi=$this->view->Html('li')->setClass($this->dropdown);
        if($page->isActive()){
            $elementLi->appendClass('active');
        }
        $ul=$this->childParent($page,$container);
        if(empty($ul))
            return "";

        $title=$this->view->escapeHtml($this->view->translate($page->getLabel(), $this->view->navigation($container)->getTranslatorTextDomain()));
        $span=$this->view->Html('span')->setClass('title')->setText(
            strtoupper($title)
        );
        if(!empty($page->get("icone"))):
            if($this->tipoIco=='fa'):
                $i=$this->view->fontAwesome($page->get("icone"));
            else:
                $i=$this->view->glyphicon($page->get("icone"));
            endif;
        else:
            $i="";
        endif;
        $div=$this->view->Html('div')->setClass('gui-icon')->setText($i);
        $link=$this->view->Html('a');


        $link->setText($div)->appendText($span);
        //$link->appendText($this->view->fontAwesome('chevron-down'));

        $elementLi->setText($link)->appendText($ul);
        $this->renderHtlm[]=$elementLi;
    }

    /**
     * @param $page
     * @param $container
     * @return mixed
     */
    protected function childParent($page,$container)
    {
        $ul=false;
        $elementUl=$this->view->Html('ul')->setClass($this->dropdownMenu);
        foreach ($page->getPages() as $child):
            if (!$this->view->navigation()->accept($child)) continue;
            $elementLi=$this->view->Html('li');
            if($page->isActive()){
                $elementLi->setClass('active');
            }
            $link=$this->view->Html('a')->setAttributes(['href'=>$child->getHref()]);
            $title=$this->view->escapeHtml($this->view->translate($child->getLabel(), $this->view->navigation($container)->getTranslatorTextDomain()));
            $span=$this->view->Html('span')->setClass('hidden-xs')->setText(strtoupper($title));
            if(!empty($child->getTarget())){
                $link->appendAttribute('target',$child->getTarget());
            }
            $link->setText($span);
            $elementLi->setText($link);
            $elementUl->appendText($elementLi);
            $ul=true;
            endforeach;
            if($ul)
                return $elementUl;
            else
                return "";
    }

    public function nav_user(){
        $elementLi=$this->view->Html('li')->setClass($this->dropdown);
        $title=strtoupper($this->view->UserIdentity()->getHasIdentity()->title);
        $span=$this->view->Html('span')->setClass('title')->setText(
            strtoupper($title)
        );
        $div=$this->view->Html('div')->setClass('gui-icon')->setText($this->view->fontAwesome('user'));
        $link=$this->view->Html('a');

        $link->setText($div)->appendText($span);

        $elementLi->setText($link);

        $dropdown_menu=$this->view->Html('ul')->setClass('');

        $minhaConta=$this->view->Html('a')->setAttributes(['href'=>$this->view->url('auth/default',['controller'=>'profile','action'=>'update-profile'])])->setText("MINHA CONTA");
        $liMinhaConta=$this->view->Html('li')->setText($minhaConta);
        $dropdown_menu->setText($liMinhaConta);

        $updatePass=$this->view->Html('a')->setAttributes(['href'=>$this->view->url('auth/default',['controller'=>'update-password','action'=>'update-password'])])->setText("ALTERAR SENHA");
        $updatePassword=$this->view->Html('li')->setText($updatePass);
        $dropdown_menu->appendText($updatePassword);

        $logout=$this->view->Html('a')->setAttributes(['href'=>$this->view->url('auth/default',['controller'=>'auth','action'=>'logout'])])->setText("LOGOUT");
        $liLogout=$this->view->Html('li')->setText($logout);
        $dropdown_menu->appendText($liLogout);

        $elementLi->setText($link)->appendText($dropdown_menu);

        return $elementLi;

    }



    protected function access($page){
        $access = false;
        foreach ($page->getPages() as $child) {
            if ($this->view->navigation()->accept($child) && $child->get("separator") !== true) {
                $access = true;
            }
        }
        return $access;
    }
}