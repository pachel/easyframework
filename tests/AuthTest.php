<?php
use PHPUnit\Framework\TestCase;
use \Pachel\EasyFrameWork\Tests;
class AuthTest extends TestCase
{
    /**
     * @covers 1
     * @return void
     */
    public function testPolicy(){
        $Auth = new \Pachel\EasyFrameWork\Tests\AuthTest();
        $Auth->policy("allow");
        $this->assertEquals("DENY",$Auth->default_policy);

        $Auth->policy("deny");
        $this->assertEquals("DENY",$Auth->default_policy);
    }

    /**
     * @covers 2
     * @return void
     */
    public function testAllow(){
        $Auth = new \Pachel\EasyFrameWork\Tests\AuthTest();
        $Auth->allow("teszt");
        $Auth->allow("ss/");
        $Auth->allow("teszt2*");
        $Auth->allow("tes*zt3");

        /**
         * @var \Pachel\EasyFrameWork\Route $new
         */
        $new = $Auth->allowedSitesList->find("path")->equal("teszt")->get()[0];
        $this->assertEquals("teszt",$new->path);

        $new = $Auth->allowedSitesList->find("path")->equal("ss")->get()[0];
        $this->assertEquals("ss",$new->path);

        $new = $Auth->allowedSitesList->find("path")->equal("teszt2*")->get()[0];
        $this->assertEquals("teszt2.*",$new->path_to_regex);

        $new = $Auth->allowedSitesList->find("path")->equal("tes*zt3")->get()[0];
        $this->assertEquals("tes\*zt3",$new->path_to_regex);
    }

    /**
     * @covers 3
     * @return void
     */
    public function test_is_authorised(){/*
        $Auth = new \Pachel\EasyFrameWork\Tests\AuthTest();

        $Routes[0] = new \Pachel\EasyFrameWork\Route(["path"=>"teszt","method"=>"GET"]);

        $active = $Auth->is_authorised($Routes[0]);
        $this->assertTrue($active,1);


        //$active = $Auth->is_authorised([]);
        //$this->assertTrue($active,2);

        $Auth->authorise(function (){return false;});
        $active = $Auth->is_authorised($Routes);
        $this->assertFalse($active,"A check függvény lefut-e?");


        $Auth->allow("teszt/");
        $active = $Auth->is_authorised($Routes);
        $this->assertTrue($active,"Engedélyezett oldal a perjellel a végén is oké");

        $Auth->allowedSitesList->reset();
        $Auth->allow("tesz*");
        $active = $Auth->is_authorised($Routes);
        $this->assertTrue($active,"Engedélyezett oldal * a végén");

        $Auth->allowedSitesList->reset();
        $Auth->allow("te*szt");
        $active = $Auth->is_authorised($Routes);
        $this->assertFalse($active,"Engedélyezett oldal * a közepén");


        $Routes[] = new \Pachel\EasyFrameWork\Route(["path"=>"teszt2"]);
        $this->expectExceptionCode(10102);
        $active = $Auth->is_authorised($Routes);
*/
    }

    /**
     * @covers 4
     * @return void
     */
    public function test_run_autorise_function(){
        $path = "teszt/teszt";
        $Auth = new \Pachel\EasyFrameWork\Tests\AuthTest();
        $Auth->authorise("");

        $active = $Auth->run_autorise_function($path);
        $this->assertFalse($active,"Ha ne objektum az auth. method");

        $Auth->authorise([Tests\AuthTest::class,"tesztFalse"]);
        $active = $Auth->run_autorise_function($path);
        $this->assertFalse($active,"Ha objektum az auth. method");

        $Auth->authorise([Tests\AuthTest::class,"tesztTrue"]);
        $active = $Auth->run_autorise_function($path);
        $this->assertTrue($active,"Ha objektum az auth. method");

        $Auth->authorise("\Pachel\EasyFrameWork\Tests\AuthTest->tesztFalse");
        $active = $Auth->run_autorise_function($path);
        $this->assertFalse($active,"Ha objektum az auth. method és szöveges");

        $Auth->authorise("\Pachel\EasyFrameWork\Tests\AuthTest->tesztTrue");
        $active = $Auth->run_autorise_function($path);
        $this->assertTrue($active,"Ha objektum az auth. method és szöveges");

        $Auth->authorise(function (){return false;});
        $active = $Auth->run_autorise_function($path);
        $this->assertFalse($active,"Ha objektum az auth. method");

        $Auth->authorise(function (){return true;});
        $active = $Auth->run_autorise_function($path);
        $this->assertTrue($active,"Ha objektum az auth. method");


    }
}