<?php
use PHPUnit\Framework\TestCase;
use \Pachel\EasyFrameWork\Tests;
use Pachel\EasyFrameWork\Base;
class MySqlTest extends TestCase
{
    /**
     * @covers 1
     * @return void
     * @throws Exception
     */
    public function test_connect(){

        /*
        $testMock = $this->createMock(Base::class);
        $testMock->expects($this->once())->method("env")->with($this->logicalOr("MYSQL","MYSQLI"))->willReturn(true);

        $this->assertTrue($testMock->env("MYSQLI"));*/

        try {
            $db = new \Pachel\EasyFrameWork\DB\mySql();
        }
        catch (Exception $exception){
            $this->assertEquals(\Pachel\EasyFrameWork\Messages::MYSQL_CONFIG_NOT_EXISTS,$exception->getMessage());
        }


        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");
        $db = new \Pachel\EasyFrameWork\DB\mySql();

        $active = $db::$CONNECTED;
        $this->assertTrue($active);


    }
}