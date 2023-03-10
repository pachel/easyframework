<?php
namespace Pachel\EasyFrameWork;
define("START_EFW",microtime(true));
session_start();
ob_start();

use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\DB\callBacks\deleteCallback;
use Pachel\EasyFrameWork\DB\callBacks\updateCallback;
use Pachel\EasyFrameWork\DB\Modells\dataModel;
use Pachel\EasyFrameWork\DB\mySql;
use Pachel\EasyFrameWork\Routing;
use Pachel\EasyFrameWork\Auth;
require_once __DIR__."/../vendor/autoload.php";
//requ
//ire_once __DIR__."/config/Routes.php";



/**
 * @method void view(string $name)
 */

//$s = new TestClass();

//exit();


//(new MethodInvoker)->invoke(new TestClass, 'privateMethod', ['argument_1']);

class SmallController{
    /**
     * @var \Pachel\EasyFrameWork\BaseAsArgument $app;
     */
    protected $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function authorise($path):bool
    {
        if($path == "multiples"){
            return true;
        }
        return false;
    }
    public function dashboard($app,$category,$id){
       // echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
        $app->kex = $category."-".$id;

    }
    public function ss(){

    }
    public function dashboard2($app){
        //$t = new CodeFormater();
        //$this->app->code = $t->php(file_get_contents(__DIR__."/config/App.php"));
    }
    public function email_szinkron(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function layout(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function always(){
        $this->app->teszt = 2;
      //  echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }

    /**
     * @param Pachel\EasyFrameWork\BaseAsArgument $app
     * @return void
     */
    public function landing($app){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";

    }

    /**
     * @param Pachel\EasyFrameWork\BaseAsArgument $app
     * @return array
     */
    public function api($app){
        return $this->app->env(null);
    }
    public function cli(){
        print_r(func_get_args());
    }
    public function api_key_check(){
        if(!isset($this->app->GET["apikey"]) || $this->app->GET["apikey"] != 15487){
            $this->app->send_error(403);
        }
    }
}

/*
$Base = Base::instance();
Base::instance()->config(__DIR__ . "/config/App.php");
*/
Base::instance()->config(__DIR__ . "/config/dev_App.php");

require __DIR__."/config/Routes.php";




$Base = Base::instance();
$Base->DB->setResultType(mySql::RESULT_OBJECT);
//$result = $Base->DB->select()->from("dolgozok");
/*
$Base->DB->delete("dolgozok")->id(1);
$Base->DB->delete("dolgozok")->where(["id"=>1]);
*/
$sql = "";

//$Base->DB->query("UPDATE m_felhasznalok SET nev='Izé' WHERE id=1")->exec();
final class User{
    public int $id;
    public string $nev;
    public int $id_csoportok;
    public string $email;
    public int $id_dolgozok;
}

$Base->DB->query("UPDATE m_felhasznalok SET nev=:nev WHERE id=:id")->params(["id"=>1,"nev"=>time()])->exec();
/**
 * @var User $line
 */
//$line = $Base->DB->query("SELECT *FROM m_felhasznalok WHERE id=?")->params([1])->line();
$line = $Base->DB->query("SELECT *FROM m_felhasznalok WHERE id=1")->line();
print_r($line);

//echo $line->email;
/**
* @var User[] $list
*/


$list = $Base->DB->query("SELECT *FROM m_felhasznalok WHERE id=?")->params([1])->array();
echo $list[0]->email;

//$d1 = $Base->DB->delete("dolgozok");
$Base->DB->query("update m_felhasznalok SET deleted=0")->exec();
$Base->DB->query("update dolgozok SET deleted=0")->exec();


$db = clone $Base->DB;

class UserModell extends dataModel{
    protected $_tablename = "m_felhasznalok";
    protected array $_not_visibles = ["jelszo"];
    public int $id;
    public int $id_dolgozok;
    public int $id_csoportok;
    public string $nev;
    public string $email;
    public string $jelszo;
}

$felhasznalo =  new User();
$felhasznalo->email = "teszt@gg.com";
$felhasznalo->nev = "Tóth László";

$User = new UserModell();
$User->nev = "Akármi";
$User->email = "alésjads@gmail.com";
$User->id_csoportok = 0;
$User->id_dolgozok = 0;
//$User->id = 25;
//print_r(get_class_vars(UserModell::class));


/**
 *
 */

$db->insert($User);
$User->id = $db->last_insert_id();
$db->delete($User);
//print_r($User);

exit();
//print_r(get_class_vars(User::class));
$line = $db->select($User)->where(["id"=>12,"nev"=>"s"])->line();
print_r($line);
exit();
//$db->delete("m_felhasznalok")->id(10);
$db->delete("m_felhasznalok")->id_csoportok(8);
$db->update("m_felhasznalok")->set(["nev"=>time()])->where(["id_csoportok"=>10]);

$db->update("m_felhasznalok")->nev("Akárki")->id_csoportok(10);


//$db->update("m_felhasznalok")->nev("Akárki")->id_csoportok(10);


//$Base->DB->delete("m_felhasznalok")->where(["id"=>4,"id_csoportok"=>6]);

//$Base->DB->query($sql)->params(["id"=>1])->exec();
/*
$Base->DB->query($sql)->exec();
$Base->DB->query($sql)->params()->exec();
$Base->DB->query($sql)->line()->object();
$Base->DB->query($sql)->params()->line()->object();
$Base->DB->query($sql)->params()->list()->object();
$Base->DB->query($sql)->params()->object();
*/


//$result = $Base->DB->select("nev,id,kex.id",["COUNT(*)","count"])->from("dolgozok",["ize","i"])->object();
//print_r($result);
exit();
$Base->run();

