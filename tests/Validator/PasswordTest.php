<?php
namespace Validator;

/**
 * Mindestanforderungen an ein Passwort:
 *  - Kleinbuchstaben
 *  - Grossbuchstaben
 *  - Zahlen
 *  - Mind. 8 Zeichen lang
 *
 * @author djungowski
 * 
 * @group validation
 * @group password
 *
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{
    public function testInheritance()
    {
        $pass = new Password();
        $this->assertInstanceOf('Validator\ValidatorInterface', $pass);
    }
    
    public function testValidPassword()
    {
        $pass = new Password();
        self::assertTrue($pass->isValid('einVal1desPas5wort'));
    }
    
    /**
     * @expectedException Validator\ValidatorException
     * @expectedExceptionMessage Fehlende Zeichen
     */
    public function testInvalidPasswordWithMissingNumber()
    {
        $pass = new Password();
        $pass->isValid('keinValidesPasswort');
    }
    
    /**
     * @expectedException Validator\ValidatorException
     * @expectedExceptionMessage Fehlende Zeichen
     */
    public function testInvalidPasswordWithMissingCapitalLetter()
    {
        $pass = new Password();
        $pass->isValid('keinval1despas5wort');
    }
    
    /**
     * @expectedException Validator\ValidatorException
     * @expectedExceptionMessage Fehlende Zeichen
     */
    public function testInvalidPasswordWithMissingLowerCaseLetter()
    {
        $pass = new Password();
        $pass->isValid('KEINVAL1DESPAS5WORT');
    }
    
    /**
     * @expectedException Validator\ValidatorException
     * @expectedExceptionMessage Fehlende Zeichen
     */
    public function testInvalidPasswordOnlyNumbers()
    {
        $pass = new Password();
        $pass->isValid('12345678');
    }
    
    /**
     * Minimum: 8 Zeichen
     * @expectedException Validator\ValidatorException
     * @expectedExceptionMessage Zu kurzes Passwort
     */
    public function testPasswordTooShort()
    {
        $pass = new Password();
        $pass->isValid('zuKur5');
    }
}