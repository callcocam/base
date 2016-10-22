<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 29/08/2016
 * Time: 16:26
 */

namespace Base\Services;

use Zend\Http\Client as ClienteHttp;

class Client extends ClienteHttp {

    public function __construct($uri = null, $options = null)
    {
        parent::__construct($uri, $options);
    }

} 