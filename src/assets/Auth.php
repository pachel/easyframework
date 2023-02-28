<?php

namespace Pachel\EasyFrameWork;

class Auth extends Prefab
{
    public static array $vars;
    public function policy()
    {

    }

    public function allow()
    {

    }

    public function deny()
    {

    }

    /**
     * @param object $param($request)
     * @return void
     */
    public function authorise($param)
    {

        $page = Routing::matchroute();
        if(is_object($param) &&  $param($page)){

        }
        elseif(is_array($param)){

        }
    }

}