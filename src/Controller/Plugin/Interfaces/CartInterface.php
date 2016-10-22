<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 29/07/2016
 * Time: 21:13
 */

namespace Base\Controller\Plugin\Interfaces;



use Base\Model\AbstractModel;


interface CartInterface {

    public function add(AbstractModel $vendas,$token);
    public function update(AbstractModel $vendas,$token);
    public function remove($token);
    public function destroy();
    public function read();
    public function updateItem($token,$field,$value);
    public function total();
    public function check(AbstractModel $filter);
    public function isResult();

} 