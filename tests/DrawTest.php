<?php

use PHPUnit\Framework\TestCase;
use Pachel\EasyFrameWork\Draw;
use Pachel\EasyFrameWork\Messages;
use Pachel\EasyFrameWork\Helpers\MethodInvoker;
use Pachel\EasyFrameWork\Base;
class DrawTest extends TestCase
{

    protected function setUp(): void
    {

    }

    protected function tearDown(): void
    {

    }

    /**
     * @covers
     */
    public function testtemplate()
    {

        $dir = __DIR__."/../examples/UI/views/";
        $template = "layout.index.php";
        (new MethodInvoker)->invoke(Base::instance(), 'set', ['APP.VIEWS',$dir]);
        Draw::template($template);
        $actual = (new MethodInvoker)->invokeconstant(Draw::class, 'vars');
        $this->assertEquals($dir.$template,$actual["template"],"Elmenti-e a template értékét");


        $this->expectExceptionMessage(Messages::DRAW_TEMPLATE_NOT_FOUND);
        Draw::template("dummies");
    }

    /**
     * @covers
     */
/*    public function testreplace_variables()
    {




    }
*/
}