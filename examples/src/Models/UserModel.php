<?php


use Pachel\EasyFrameWork\DB\Models\dataModel;

/**
 * @method \Pachel\EasyFrameWork\UserModel getById(int $id)
 */
class UserModel extends dataModel{
    protected string $_tablename = "m_felhasznalok";
    protected array $_not_visibles = ["jelszo"];

}

class UserData extends stdClass {
    public int $id;
    public int $deleted;
    public int $id_dolgozok;
    public int $id_csoportok;
    public string $nev;
    public string $email;
    public string $jelszo;
}

