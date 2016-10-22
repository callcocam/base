<?php
/**
 * Created by PhpStorm.
 * User: Claudio
 * Date: 08/10/2016
 * Time: 10:40
 */

namespace Base\Validator;


class MyNoRecordExists {

    public static  function setNoRecordExists($table, $field, $exclude = "", $recordFound = "Registro Ja Existe", $noRecordFound = "Registro Não Existe") {
        $validator = new \Zend\Validator\Db\NoRecordExists(array(
            'table' => $table->getTable(),
            'field' => $field,
            'adapter' => $table->getAdapter()
        ));

        if (!empty($exclude)):
            $validator->setExclude($exclude);
        endif;
        $validator->setMessage($noRecordFound, 'noRecordFound');
        $validator->setMessage($recordFound, 'recordFound');
        return $validator;
    }
} 