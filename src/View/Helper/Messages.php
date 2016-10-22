<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 26/08/2016
 * Time: 09:03
 */

namespace Base\View\Helper;


use Zend\View\Helper\AbstractHelper;

class Messages extends AbstractHelper{
    protected $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function __invoke()
    {
        if (count($this->messages) == 0) {
            return '';
        }
        $html = '<ul class="nav">';
        foreach ($this->messages as $key => $messagesArray) {
            switch ($key) {
                case "success":
                    $ico="ion-checkmark-circled";
                    break;
                case "alert":
                    $ico="ion-alert-circled";
                    break;
                case "info":
                    $ico="ion-alert";
                    break;
                case "error":
                    $ico="ion-ios-minus";
                    break;
                default:
                    $ico="ion-ios-medical";
                    break;
            }
            $html .= '';
            foreach ($messagesArray as  $value) {

                $html .= '<li class="trigger trigger-'.$key.' alert alert-'.$key.' alert-dismissable">
						<i class="ion '.$ico.'"></i> <b>ALERTA!</b>: '.$this->view->translate($value).' </li>';
            }

        }
        $html .= '</ul>';
        return '<div  id="messages">'.$html.'</div>';
    }
}
