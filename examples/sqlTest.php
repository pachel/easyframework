<?php
namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\DB\Modells\dataModel;
use \UserModel;


$db = clone Base::instance()->DB;




/**
 * @var UserModel $result
 */
$User = new UserModel(["id"=>1]);
$result = $db->select($User)->line();
echo $result->nev."\n";
$User->nev = "Teszt";
$db->update($User);
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


