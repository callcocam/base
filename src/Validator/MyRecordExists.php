<?php
/**
 * Created by PhpStorm.
 * User: Claudio
 * Date: 08/10/2016
 * Time: 10:40
 */

namespace Base\Validator;


use Zend\Validator\Db\RecordExists;

class MyRecordExists {

    protected $isvalid;

    public function __construct($table,$field,$Adapter,$exclude = "", $recordFound = "Registro Ja Existe", $noRecordFound = "Registro Não Existe")
    {
        $validator = new RecordExists(array(
            'table' => $table,
            'field' => $field,
            'adapter' => $Adapter
        ));

        if (!empty($exclude)):
            $validator->setExclude($exclude);
        endif;
        $validator->setMessage($noRecordFound, 'noRecordFound');
        $validator->setMessage($recordFound, 'recordFound');
        return $validator;
    }
} 