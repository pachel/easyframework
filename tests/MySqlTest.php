<?php

use PHPUnit\Framework\TestCase;
use \Pachel\EasyFrameWork\Tests;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\UserModel;
use \Pachel\EasyFrameWork\DB\Models\Query;

class MySqlTest extends TestCase
{
    protected function setUp(): void
    {


        Base::instance()->config(require __DIR__ . "/../examples/config/dev_App.php");
        $db = new \Pachel\EasyFrameWork\Tests\MySqlTest();
        $db->QUERY = new Query();
        $db->QUERY->safemode = true;
        $this->db = $db;


    }

    /**
     * @covers 1
     * @return void
     * @throws Exception
     */
    public function test_connect()
    {

        /*
        $testMock = $this->createMock(Base::class);
        $testMock->expects($this->once())->method("env")->with($this->logicalOr("MYSQL","MYSQLI"))->willReturn(true);

        $this->assertTrue($testMock->env("MYSQLI"));*/

        try {
            $db = new \Pachel\EasyFrameWork\DB\mySql();
        } catch (Exception $exception) {
            $this->assertEquals(\Pachel\EasyFrameWork\Messages::MYSQL_CONFIG_NOT_EXISTS, $exception->getMessage());
        }


        Base::instance()->config(require __DIR__ . "/../examples/config/dev_App.php");
        $db = new \Pachel\EasyFrameWork\DB\mySql();

        $active = $db::$CONNECTED;
        $this->assertTrue($active);
    }

    /**
     * @covers 2
     * @return void
     * @throws Exception
     */
    public function test_arrayFromObject()
    {

        /**
         * @var \UserModel $user
         */
        $user_object = ["id" => 1, "nev" => "object"];
        $user_array = ["id" => 2, "nev" => "array"];
        $user = new Tests\TestUserModel($user_object);

        $this->db->QUERY = new Query();
        $this->db->QUERY->method = Tests\MySqlTest::QUERY_TYPE_SELECT;
        /**
         * Ha array paramétert kapott
         */
        $result = $this->db->arrayFromObject($user_object);
        $this->assertEquals($user_object, $result);
        /**
         * SELECT kérések tesztelése
         */
        $result = $this->db->arrayFromObject($user);
        $this->assertEquals($user->tableName(), $this->db->QUERY->from);
        $this->assertEquals(["nev" => 0, "id" => 0, "deleted" => 0], $result);
        $this->assertEquals(["id" => "1"], $this->db->QUERY->where, "Ha van primary az objektumban, akkor beteszi a WHERE feltételbe");

        /**
         * INSERT kérések tesztelése
         */
        $this->db->QUERY->method = Tests\MySqlTest::QUERY_TYPE_INSERT;
        $result = $this->db->arrayFromObject($user);
        $this->assertEquals(["nev" => "object"], $result);

        /**
         * DELETE kérések
         */
        $this->db->QUERY->method = Tests\MySqlTest::QUERY_TYPE_DELETE;
        $result = $this->db->arrayFromObject($user);
        $this->assertEquals(["nev" => "object"], $result);


    }

    /**
     * @covers
     * @return void
     */
    public function test_exec()
    {
        $mockSTM = $this->createMock(PDOStatement::class);
        $mockSTM->method("execute")->willReturn(true);

        $mockPDO = $this->createMock(PDO::class);
        $mockPDO->method("prepare")->willReturn($mockSTM);
        $this->db->PDO = $mockPDO;

        $this->db->QUERY = new Query();
        $this->db->QUERY->sql_query = "asd";
        $this->db->QUERY->pdo_parameters = [];

        $result = $this->db->exec();
        $this->assertTrue($result);

        $mockSTM = $this->createMock(PDOStatement::class);
        $mockSTM->method("execute")->willThrowException(new Exception());


        $mockPDO = $this->createMock(PDO::class);
        $mockPDO->method("prepare")->willReturn($mockSTM);
        $this->db->PDO = $mockPDO;

        $this->db->QUERY = new Query();
        $this->db->QUERY->sql_query = "asd";
        $this->db->QUERY->pdo_parameters = [];

        $result = $this->db->exec();
        $this->assertFalse($result);


    }

    /**
     * @covers
     * @return void
     */
    public function test_set()
    {

        $this->db->QUERY->from = "asda";
        $this->db->QUERY->method = \Pachel\EasyFrameWork\DB\mySql::QUERY_TYPE_INSERT;
        $result = $this->db->set(["nev" => "x"]);
        $this->assertFalse($result);

        $this->db->QUERY->method = \Pachel\EasyFrameWork\DB\mySql::QUERY_TYPE_UPDATE;
        $result = $this->db->set(["nev" => "x"]);
        $this->assertEquals(["nev" => "x"], $this->db->QUERY->pdo_parameters);

        $this->assertEquals(\Pachel\EasyFrameWork\DB\callBacks\setCallback::class, get_class($result));


    }

    private function callBackUpdate2()
    {

        return true;
    }

    /**
     * @covers
     * @return void
     */
    public function test_id()
    {
        $this->db->QUERY->from = "s";
        $this->db->QUERY->method = \Pachel\EasyFrameWork\DB\mySql::QUERY_TYPE_DELETE;


        $result = $this->db->id(1);
        $this->assertFalse($result);

        $this->db->QUERY->safemode = false;
        $result = $this->db->id(1);
        $this->assertFalse($result);


        $mockSTM = $this->createMock(PDOStatement::class);
        $mockSTM->method("execute")->willReturn(true);

        $mockPDO = $this->createMock(PDO::class);
        $mockPDO->method("prepare")->willReturn($mockSTM);
        $this->db->PDO = $mockPDO;

        $result = $this->db->id(1);
        $this->assertTrue($result);

        $this->db->QUERY->safemode = true;

        $result = $this->db->id(1);
        $this->assertTrue($result);

        $this->db->QUERY->method = \Pachel\EasyFrameWork\DB\mySql::QUERY_TYPE_UPDATE;
        $result = $this->db->id(1);
        $this->assertTrue($result);

        $this->db->QUERY->method = \Pachel\EasyFrameWork\DB\mySql::QUERY_TYPE_SELECT;
        $result = $this->db->id(1);
        $this->assertEquals(\Pachel\EasyFrameWork\DB\callBacks\idCallback::class, get_class($result));
    }

    /**
     * @covers 3
     * @return void
     */
    public function test_insert()
    {

        /**
         * @var \UserModel $user
         */
        $user_object = ["id" => 1, "nev" => "object"];
        $user = new Tests\TestUserModel($user_object);


        $mockSTM = $this->createMock(PDOStatement::class);
        $mockSTM->method("execute")->willReturn(true);

        $mockPDO = $this->createMock(PDO::class);
        $mockPDO->method("prepare")->willReturn($mockSTM);
        $this->db->PDO = $mockPDO;

        $result = $this->db->insert($user);
        $this->assertTrue($result);

        $user = new Tests\TestUserModel(["id" => 1]);
        $result = $this->db->insert($user);
        $this->assertFalse($result,"Ha nicns adat az objektumban");

       // $user = new Tests\TestUserModel($user_object);
    }

    /**
     * @covers
     * @return void
     */
    public function test_delete()
    {

        $this->assertFalse(false);
    }
}