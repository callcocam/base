<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 27/08/2016
 * Time: 13:53
 */

namespace Base\View\Helper;


use Zend\Navigation;
use Zend\View\Helper\Navigation\AbstractHelper;

class NavigationHelper extends AbstractHelper{

    protected $tipoIco='fa';//fa ou 'glyphicon';
    protected $renderHtlm=[];
    protected $dropdown='dropdown';
    protected $dropdownMenu='dropdown-menu';

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
    public function render($container = null)
    {
        $elementUl=$this->view->Html('ul')->setClass('nav navbar-nav');
        if ($container):
            foreach ($container as $page):
                if (!$this->view->navigation()->accept($page)) continue;
                $hasChildren = $page->hasPages();
                if ($hasChildren):
                    if ($this->access($page)) :
                      $this->child($page,$container);
                    else:
                      $this->parent($page,$container);
                    endif;
                endif;
            endforeach;
        endif;
        $elementUl->setText(implode("",$this->renderHtlm));
        echo sprintf("%s%s",$elementUl,$this->nav_user());
    }

    protected function parent($page,$container){
        $elementLi=$this->view->Html('li');
        if($page->isActive()){
            $elementLi->setClass('active');
        }
        $link=$this->view->Html('a')->setAttributes(['href'=>$page->getHref()]);
        if(!empty($page->getTarget())){
            $link->appendAttribute('target',$page->getTarget());
        }
        $span=$this->view->Html('span')->setClass('hidden-xs')->setText($this->view->escapeHtml($this->view->translate($page->getLabel(), $this->view->navigation($container)->getTranslatorTextDomain())));
        if(!empty($page->get("icone"))):
            if($this->tipoIco=='fa'):
                $link->setText($this->view->fontAwesome($page->get("icone")));
            else:
                $link->setText($this->view->glyphicon($page->get("icone")));
            endif;
            $link->appendText($span);
        else:
            $link->setText($span);
        endif;
        $elementLi->setText($link);
        $this->renderHtlm[]=$elementLi;

    }

    protected function child($page,$container){
        $elementLi=$this->view->Html('li')->setClass($this->dropdown);
        if($page->isActive()){
            $elementLi->appendClass('active');
        }
        $link=$this->view->Html('a')->setAttributes(['class'=>'dropdown-toggle','data-toggle'=>'dropdown','role'=>'button','aria-haspopup'=>'true','aria-expanded'=>'false']);
        $span=$this->view->escapeHtml($this->view->translate($page->getLabel(), $this->view->navigation($container)->getTranslatorTextDomain()));
        $link->setText($span);
        $link->appendText($this->view->fontAwesome('chevron-down'));

        $elementLi->setText($link)->appendText($this->childParent($page,$container));
        $this->renderHtlm[]=$elementLi;
    }

    protected function childParent($page,$container)
    {
        $elementUl=$this->view->Html('ul')->setClass($this->dropdownMenu);
        foreach ($page->getPages() as $child):
            if (!$this->view->navigation()->accept($child)) continue;

            $elementLi=$this->view->Html('li');
            if($page->isActive()){
                $elementLi->setClass('active');
            }
            $link=$this->view->Html('a')->setAttributes(['href'=>$child->getHref()]);
            $span=$this->view->Html('span')->setClass('hidden-xs')->setText($this->view->escapeHtml($this->view->translate($child->getLabel(), $this->view->navigation($container)->getTranslatorTextDomain())));
            if(!empty($child->getTarget())){
                $link->appendAttribute('target',$child->getTarget());
            }
            $link->setText($span);
            $elementLi->setText($link);
            $elementUl->appendText($elementLi);
            endforeach;
           return $elementUl;
    }

    public function nav_user(){
        $navbar_right=$this->view->Html('ul')->setClass('nav navbar-nav navbar-right');

        $elementLi=$this->view->Html('li')->setClass($this->dropdown);

        $link=$this->view->Html('a')->setAttributes(['class'=>'dropdown-toggle','data-toggle'=>'dropdown','role'=>'button','aria-haspopup'=>'true','aria-expanded'=>'false']);

        $link->setText($this->view->UserIdentity()->getHasIdentity()->title);

        $link->appendText($this->view->fontAwesome('user'));

        $elementLi->setText($link);

        $dropdown_menu=$this->view->Html('ul')->setClass('dropdown-menu');

        $logout=$this->view->Html('a')->setAttributes(['href'=>$this->view->url('auth',['action'=>'profile'])])->setText("MINHA CONTA");
        $liLogout=$this->view->Html('li')->setText($logout);
        $dropdown_menu->setText($liLogout);

        $logout=$this->view->Html('a')->setAttributes(['href'=>$this->view->url('auth',['action'=>'logout'])])->setText("LOGOUT");
        $liLogout=$this->view->Html('li')->setText($logout);
        $dropdown_menu->appendText($liLogout);

        $elementLi->setText($link);
        $navbar_right->setText($elementLi->appendText($dropdown_menu));
        return $navbar_right;

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