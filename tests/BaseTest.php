<?php

use PHPUnit\Framework\TestCase;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Messages;
use Pachel\EasyFrameWork\Helpers\MethodInvoker;
class BaseTest extends TestCase
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
    public function testinstance()
    {
        $actual = Base::instance();
        $this->assertIsObject($actual);
    }

    /**
     * @covers
     */
    public function testenv()
    {
        $t = Base::instance();
        /**
         * Ha nincs még a változó beállítva akkor üresen kell visszajönnie az env() függvénynek
         */
        $actual = $t->env("S");
        $this->assertEquals("", $actual, "Nem létezőre üres string");

        /**
         * Megnézzük, hogy menti-e az értékeket!
         */
        $t->env("S", 2);
        $actual = $t->env("S");
        $this->assertEquals(2, $actual, "env() írás/olvasás");

        /**
         * Kis/nagy betűre is érzékenynek kell lennie
         */
        $t->env("s", 1);
        $actual = $t->env("S");
        $this->assertEquals(2, $actual, "Kis/nagy betű teszt");

        /**
         * Azt teszteljük, hogy az APP visszadja-e a tömböt,
         * ha azt kérjük le
         */
        $actual = $t->env("SERVER");
        $this->assertIsArray($actual);

        /**
         * @covers \Exception
         * Rendszerváltozónál hibát kellgenerálni
         */

        $this->expectException(Exception::class);
        $t->env("APP.akarmi", 1);
        $actual = $t->env("APP.akarmi");
    }

    /**
     * @covers
     * @return void
     */
    public function testconfig()
    {
        $t = Base::instance();
        $actual = $t->env("EFW.CONFIGURED");
        $this->assertFalse($actual,"Konfig jelző teszt");

        $this->expectExceptionMessage(Messages::BASE_CONFIG_NOT_VALID);
        $t->config("dummy.php");


        $this->expectExceptionMessage(Messages::BASE_CONFIG_MISSING_REQ);
        $t->config([
            "APP"
        ]);

        $this->expectExceptionMessage(Messages::BASE_CONFIG_MISSING_REQ);
        $t->config([
            "APP"=>[
                "UI" => ""
            ]
        ]);
        $this->expectExceptionMessage(Messages::BASE_CONFIG_MISSING_REQ);
        $t->config([
            "APP"=>[
                "UI" => "",
                "LOGS" => ""
            ]
        ]);

        $this->expectExceptionMessage(Messages::BASE_APP_NOT_CONFIGURED);
        $t->run();
    }

    /**
     * @covers
     * @return void
     */
    public function testset(){

        (new MethodInvoker)->invoke(Base::instance(), 'set', ['APP.TEST',1]);
        $actual = Base::instance()->env("APP.TEST");
        $this->assertEquals(1,$actual,"Védett változók írása belűlről");
    }

    /**
     * @covers
     * @return void
     */
    public function testis_systemvarialbe(){

        $k = (new MethodInvoker)->invoke(Base::instance(), 'is_systemvarialbe', ['APP.TEST']);
        $this->assertTrue($k,"Az APP.TEST az systemvar");

        $k = (new MethodInvoker)->invoke(Base::instance(), 'is_systemvarialbe', ['gecet']);
        $this->assertFalse($k,"A gecet az systemvar");

    }

    /**
     * @covers
     * @return void
     */
    public function testis_path(){

        $k = (new MethodInvoker)->invoke(Base::instance(), 'is_path', ['APP.VIEWS']);
        $this->assertTrue($k,"Az APP.VIEWS az könyvtár");

        $k = (new MethodInvoker)->invoke(Base::instance(), 'is_path', ['app.kex']);
        $this->assertFalse($k,"Az app.kex nem könyvtár");

    }

    /**
     * @covers
     * @return void
     */
    public function testrun_content(){

        $k = (new MethodInvoker)->invoke(Base::instance(), 'run_content', [""]);
        $this->assertFalse($k,"Üres route csekk!");

        $k = (new MethodInvoker)->invoke(Base::instance(), 'run_content', [["object"=>function(){

        }]]);
        $this->assertTrue($k,"Direkt függvény lefut!");


        $k = (new MethodInvoker)->invoke(Base::instance(), 'run_content', [["object"=>function(){
            echo 2;
        }]]);
        $actual = $this->getActualOutputForAssertion();
        $this->assertEquals(2,$actual,"Függvény végrehajtása!");
    }

    /**
     * @covers
     * @return void
     */
    public function testsetvars(){
        $_COOKIE["TESTTTTT"] = 1;
        (new MethodInvoker)->invoke(Base::instance(), 'setvars', [""]);
        $actual = Base::instance()->env("COOKIE.TESTTTTT");
        $this->assertEquals(1,$actual,"Változók mentése appba");
    }

    /**
     * @covers
     * @return void
     */
    public function testrun(){


    //    Base::instance()->run();
        //$this->assertEquals(1,$actual,"Változók mentése appba");
    }

}