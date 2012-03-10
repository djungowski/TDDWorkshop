<?php
namespace Storage;

use Posts\User;

use Storage\PDOMockable;

/**
 * @author jketterl
 * 
 * @group storage
 * 
 * @covers Storage\MysqlStorage
 *
 */
class MysqlStorageTest extends \PHPUnit_Framework_TestCase
{
    private function getQueryMock()
    {
        $methods = array(
            'bindValue',
            'setFetchMode',
            'execute',
            'fetch'
        );
        $mock = $this->getMock('SomeRandomMockupQueryObject', $methods);
        return $mock;
    }
    
    private function getPDOMock($query)
    {
        $methods = array(
            'prepare',
        );
        // PDO verlangt Parameter im Konstruktor. Ruft man den Originalkonstruktor nicht
        // auf beim Mocking, serialisiert phpunit das Objekt zum mocken
        // Serialisieren geht bei PDO nicht. Deswegen verwenden wir diese Hilfsklasse,
        // die lediglich PDO extended und den Konstruktor ueberschreibt
        $mock = $this->getMock('Storage\PDOMockable', $methods);
        $mock->expects($this->any())
             ->method('prepare')
             ->will($this->returnValue($query));
        return $mock;
    }
    
    public function testRetrievesUser()
    {
        $storage = new MysqlStorage('user', 'Posts\User');
        
        $userData = array(
            'name' => 'Alfons'
        );
        
        $query = $this->getQueryMock();
        $query->expects($this->once())
              ->method('fetch')
              ->will($this->returnValue($userData));
        
        $db = $this->getPDOMock($query);
        
        $storage->setDbHandler($db);
        
        $user = $storage->load(1);
        self::assertInstanceOf('Posts\User', $user);
        self::assertEquals('Alfons', $user->name);
    }
    
    public function fetchCallback()
    {
        var_dump($args = func_get_args());
    }
    
    public function testRetrievesList()
    {
        $storage = new MysqlStorage('user', 'Posts\User');
        
        $userData1 = array(
           'name' => 'Alfons'
        );
        $userData2 = array(
            'name' => 'Bernd'
        );
        
        $query = $this->getQueryMock();
        $query->expects($this->any())
              ->method('fetch')
              ->will($this->onConsecutiveCalls($userData1, $userData2, false));
        
        $db = $this->getPDOMock($query);
        $storage->setDbHandler($db);
        
        $list = $storage->getAll();
        
        self::assertInternalType('array', $list);
        self::assertInstanceOf('Posts\User', $list[0]);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage does not exist
     */
    public function testThrowsExceptionOnInvalidClassName()
    {
        $storage = new MysqlStorage('user', 'Dummy\Pseudo');
    }
    
    public function testInsertUser()
    {
        $user = new User();
        $user->setLogin('atest')->setName('Alfons Testbenutzer');
        
        $query = $this->getQueryMock();
        $dbHandlerMock = $this->getMock('Storage\PdoMockable');
        $dbHandlerMock->expects($this->at(0))
                      ->method('prepare')
                      ->with(
                          'INSERT INTO user (login, name) VALUES (:login, :name)'
                      )->will($this->returnValue($query));
        $query->expects($this->at(0))->method('bindValue')->with(':login', 'atest');
        $query->expects($this->at(1))->method('bindValue')->with(':name', 'Alfons Testbenutzer');
        $query->expects($this->at(2))->method('execute');
        $dbHandlerMock->expects($this->at(1))->method('lastInsertId')->will($this->returnValue(1234));
        
        $storage = new MysqlStorage('user', 'Posts\User');
        $storage->setDbHandler($dbHandlerMock);
        $storage->store($user);
        
        self::assertEquals(1234, $user->getId());
    }
}