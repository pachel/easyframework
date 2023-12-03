<?php

namespace Pachel\EasyFrameWork\Callbacks;
final class AuthPolityCallback extends CallbackBase
{
    public function allow(){
        $this->class->polity_allow();
    }
    public function deny(){
        $this->class->polity_deny();
    }
}
