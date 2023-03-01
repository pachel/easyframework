<?php

use PHPUnit\Framework\TestCase;
use Pachel\EasyFrameWork\Base;

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

        $this->expectExceptionMessage(\Pachel\EasyFrameWork\Functions::ERROR_NOT_CONFIGURED);
        $t->run();



    }
}