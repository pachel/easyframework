<?php
namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\DB\Modells\dataModel;


$db = clone Base::instance()->DB;

/**
 * @method \Pachel\EasyFrameWork\UserModell getById(int $id)
 */
class UserModell extends dataModel{
    protected $_tablename = "m_felhasznalok";
    protected array $_not_visibles = ["jelszo"];
    public int $id;
    public int $deleted;
    public int $id_dolgozok;
    public int $id_csoportok;
    public string $nev;
    public string $email;
    public string $jelszo;
}


/**
 * @var UserModell $result
 */
$User = new UserModell(["id"=>1]);
$result = $db->select($User)->line();
echo $result->nev."\n";
$User->nev = "Teszt";
$db->update($User);
$result = $db->select($User)->line();
echo $result->nev."\n";

$User = new UserModell();
$result = $db->select($User)->where(["id"=>1])->line();
echo $result->nev."\n";
$User->nev = "Teszt2";
$db->update($User)->where(["id"=>1]);
$result = $db->select($User)->where(["id"=>1])->line();
echo $result->nev."\n";

$db->update("m_felhasznalok")->set(["nev"=>"TEszt3"])->where(["id"=>1]);
$result = $db->query("SELECT nev FROM m_felhasznalok WHERE id=1")->line();
echo $result->nev."\n";

if($db->query("SELECT *FROM m_felhasznssalok")->exec()){
    echo "Minden fasza\n";
}

exit();


