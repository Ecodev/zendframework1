<?php
/**
 * Zend Framework.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.

/** Zend_Validate_Identical */
require_once 'Zend/Validate/Identical.php';

/**
 * Zend_Validate_Identical.
 *
 * @uses       Zend_Validate_Identical
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_IdenticalTest extends \PHPUnit\Framework\TestCase
{
    private \Zend_Validate_Identical $validator;

    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_IdenticalTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        $this->validator = new Zend_Validate_Identical();
    }

    public function testTokenInitiallyNull()
    {
        $this->assertNull($this->validator->getToken());
    }

    public function testCanSetToken()
    {
        $this->testTokenInitiallyNull();
        $this->validator->setToken('foo');
        $this->assertEquals('foo', $this->validator->getToken());
    }

    public function testCanSetTokenViaConstructor()
    {
        $validator = new Zend_Validate_Identical('foo');
        $this->assertEquals('foo', $validator->getToken());
    }

    public function testValidatingWhenTokenNullReturnsFalse()
    {
        $this->assertFalse($this->validator->isValid('foo'));
    }

    public function testValidatingWhenTokenNullSetsMissingTokenMessage()
    {
        $this->testValidatingWhenTokenNullReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('missingToken', $messages));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueReturnsFalse()
    {
        $this->validator->setToken('foo');
        $this->assertFalse($this->validator->isValid('bar'));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueSetsNotSameMessage()
    {
        $this->testValidatingAgainstTokenWithNonMatchingValueReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('notSame', $messages));
    }

    public function testValidatingAgainstTokenWithMatchingValueReturnsTrue()
    {
        $this->validator->setToken('foo');
        $this->assertTrue($this->validator->isValid('foo'));
    }

    /**
     * @group ZF-6953
     */
    public function testValidatingAgainstEmptyToken()
    {
        $this->validator->setToken('');
        $this->assertTrue($this->validator->isValid(''));
    }

    /**
     * @group ZF-7128
     */
    public function testValidatingAgainstNonStrings()
    {
        $this->validator->setToken(true);
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(1));

        $this->validator->setToken(['one' => 'two', 'three']);
        $this->assertTrue($this->validator->isValid(['one' => 'two', 'three']));
        $this->assertFalse($this->validator->isValid([]));
    }

    public function testValidatingTokenArray()
    {
        $validator = new Zend_Validate_Identical(['token' => 123]);
        $this->assertTrue($validator->isValid(123));
        $this->assertFalse($validator->isValid(['token' => 123]));
    }

    public function testValidatingNonStrictToken()
    {
        $validator = new Zend_Validate_Identical(['token' => 123, 'strict' => false]);
        $this->assertTrue($validator->isValid('123'));

        $validator->setStrict(true);
        $this->assertFalse($validator->isValid(['token' => '123']));
    }
}

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.
