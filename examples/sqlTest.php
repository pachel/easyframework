<?php
namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\DB\Modells\dataModel;
use \UserModel;


$db = clone Base::instance()->DB;




/**
 * @var UserModel $result
 */
$User = new UserModel();
/**
 * @var array{id:int} $z
 */

//$User->set(["nev"=>1])->deleted(0);
$data = new \UserData();
$data->id_dolgozok = 1;
$data->nev = "jhkljasd";

$data->email = "asdasdasd";
//$User->params([0,1])->update("deleted=? AND ceg=?")->set($data);
//$User->params(["id"=>1])->delete("")
$User->insert($data);
$result = $User->params(["id_csoportok"=>3])->select("id_csoportok=:id_csoportok")->array();
$User->delete(["id"=>3]);

print_r($result);
exit();
$data->deleted=1;
unset($data->id);
print_r($data);
$User->update($data)->email("asdasdasd");
$User->update(["id_csoportok"=>1])->id(69);
exit();
$User->nev = "Teszt";
if($db->update($User)){
    echo "SIKER";
}

exit();
$result = $db->select($User)->line();
echo $result->nev."\n";
$result = $db->select($User)->line();
echo $result->nev."\n";

$User = new UserModel();
if($result = $db->select($User)->where(["id"=>1])->line())
    echo $result->nev."\n";

$User->nev = "Teszt2d2";
if($db->update($User)->where(["id"=>1])){
    echo "sikeres\n";
}
$result = $db->select($User)->where(["id"=>1])->line();
echo $result->nev."\n";

$db->update("m_felhasznalok")->set(["nev"=>"TEszt3"])->where(["id"=>1]);
$result = $db->query("SELECT nev FROM m_felhasznalok WHERE id=1")->line();
echo $result->nev."\n";

if(!$db->query("SELECT *FROM m_felhasznalok")->exec()){
    echo "Valami hiba van\n";
}
$result = $User->getById(11);
echo $result->nev."\n";

$User->id = 5;
$db->select($User)->line();

exit();


