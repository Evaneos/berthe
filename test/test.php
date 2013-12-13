<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/test/fixtures/My/Manager.php';


class MyVO extends \Berthe\AbstractVO {
    const VERSION = 1;
    protected $id = 0;
    protected $name = '';

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }
}

class MyStorage extends \Berthe\DAL\AbstractStorage {
    public function fetchObject() {
        return $this->getStorePersistent()->getReader()->fetchObject(1);
    }
}

class MyValidator extends \Berthe\Validation\AbstractValidator {

    protected function doValidateSave(Berthe\AbstractVO $vo) {
        return true;
    }
    protected function doValidateDelete(Berthe\AbstractVO $vo) {
        return true;
    }
}

class MyReader extends \Berthe\DAL\AbstractReader {
    const VO_CLASS = '\MyVO';

    public function getSelectQuery() {
        return "SELECT * FROM users";
    }

    public function getTableName() {
        return 'users';
    }

    public function fetchObject($id) {
        $dbDriver = $this->db->getAdapter();
        $sql = "SELECT * FROM users WHERE id IN (?, ?)";
        $stmt = $dbDriver->fetchAll($sql, array(1, 2));
        $obj = $stmt->fetchObject("\MyVO");
        var_dump($obj);
    }
}

class MyWriter extends \Berthe\DAL\AbstractWriter {
    public function update(\Berthe\AbstractVO $object) {
        return (bool)$this->db->query("UPDATE users SET name=? where id=?", array($object->getName(), $object->getId()));
    }

    public function insert(\Berthe\AbstractVO $object) {
        $ret = (bool)$this->db->query("INSERT INTO users (name) VALUES (?)", array($object->getName()));
        $id = (int)$this->db->lastInsertId("users","id");
        if ($id > 0) {
            $object->setId($id);
            return true;
        }
        else {
            return false;
        }
    }

    public function delete(\Berthe\AbstractVO $object) {

    }
    public function deleteById($id) {

    }
}

$adapter = Zend_Db::factory("PDO_MYSQL",
    array("port" => 3306,
        "host" => "localhost",
        "username" => "root",
        "password" => "gamping",
        "dbname" => "gamping"));

$dbReader = new \Berthe\DAL\DbReader($adapter);
$dbWriter = new \Berthe\DAL\DbWriter($adapter);

$storage = new MyStorage();

$storeMemcached = new \Berthe\DAL\StoreMemcached();
$storeDatabase  = new \Berthe\DAL\StoreDatabase();

$myReader = new MyReader();
$myReader->setDb($dbReader);
$myWriter = new MyWriter();
$myWriter->setDb($dbWriter);

$storeDatabase->setReader($myReader);
$storeDatabase->setWriter($myWriter);
$storeLevel1 = new \Berthe\DAL\StoreArray();

$storage->setStoreVolatile($storeMemcached);
$storage->setStorePersistent($storeDatabase);
$storage->setStoreLevel1($storeLevel1);

$validator = new MyValidator();

$manager = new My\Manager();
$manager->setStorage($storage);
$manager->setValidator($validator);

$object = $manager->run();
var_dump($object);

// $vo = new MyVO();
// $vo->setName('JOSEF');
// $manager->save($vo);
// var_dump($vo);