<?php
namespace Pachel\EasyFrameWork\Tests;

use Pachel\EasyFrameWork\DB\Models\dataModel;
/**
 * @method \Pachel\EasyFrameWork\UserModel getById(int $id)
 */
class TestUserModel extends dataModel{
    protected $_tablename = "m_felhasznalsok";
    protected array $_not_visibles = ["jelszo"];
    public int $id;
    public int $deleted;
    public string $nev;
}
