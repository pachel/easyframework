<?php


use Pachel\EasyFrameWork\DB\Models\dataModel;
use Pachel\EasyFrameWork\DB\callBacks\DataModel\updateCallback;
use Pachel\EasyFrameWork\Functions;

/**
 * @method \Pachel\EasyFrameWork\UserModel getById(int $id)
 */
class UserModel extends dataModel{
    protected string $_tablename = "m_felhasznalok";
    protected array $_not_visibles = ["jelszo"];
    protected string $_modelclass = UserData::class;
    public function setPassword(string $password = ""):updateCallback{
        if(empty($password)){
            $password = Functions::get_random_string();
        }
        return $this->update(["jelszo"=>md5($password)]);
    }

}

class UserData {
    public int $id;
    public int $deleted;
    public int $id_dolgozok;
    public int $id_csoportok;
    public string $nev;
    public string $email;
    public string $jelszo;
}

