<?php
/**
 * User: dongww
 * Date: 14-3-20
 * Time: 下午3:34
 */
namespace Controller;

use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction()
    {
        return new Response('hello');;
    }
}
 