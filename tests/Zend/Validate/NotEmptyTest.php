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

/**
 * @see Zend_Validate_NotEmpty
 */
require_once 'Zend/Validate/NotEmpty.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_NotEmptyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_NotEmptyTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Zend_Validate_NotEmpty object.
     *
     * @var Zend_Validate_NotEmpty
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_NotEmpty object for each test method.
     */
    public function setUp(): void
    {
        $this->_validator = new Zend_Validate_NotEmpty();
    }

    /**
     * Ensures that the validator follows expected behavior.
     *
     * ZF-6708 introduces a change for validating integer 0; it is a valid
     * integer value. '0' is also valid.
     *
     * @group ZF-6708
     */
    public function testBasic()
    {
        $valuesExpected = [
            ['word', true],
            ['', false],
            ['    ', false],
            ['  word  ', true],
            ['0', true],
            [1, true],
            [0, true],
            [true, true],
            [false, false],
            [null, false],
            [[], false],
            [[5], true],
        ];
        foreach ($valuesExpected as $i => $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                "Failed test #$i");
        }
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyBoolean()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::BOOLEAN);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyInteger()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::INTEGER);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyFloat()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::FLOAT);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyString()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::STRING);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyZero()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::ZERO);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyArray()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::EMPTY_ARRAY);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyNull()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::NULL);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyPHP()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::PHP);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlySpace()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::SPACE);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testOnlyAll()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::ALL);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid([]));
        $this->assertTrue($this->_validator->isValid(['xxx']));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testArrayConstantNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            [
                'type' => [
                    Zend_Validate_NotEmpty::ZERO,
                    Zend_Validate_NotEmpty::STRING,
                    Zend_Validate_NotEmpty::BOOLEAN,
                ],
            ]
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid([]));
        $this->assertTrue($filter->isValid(['xxx']));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testArrayConfigNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            [
                'type' => [
                    Zend_Validate_NotEmpty::ZERO,
                    Zend_Validate_NotEmpty::STRING,
                    Zend_Validate_NotEmpty::BOOLEAN, ],
                'test' => false,
            ]
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid([]));
        $this->assertTrue($filter->isValid(['xxx']));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testMultiConstantNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            Zend_Validate_NotEmpty::ZERO + Zend_Validate_NotEmpty::STRING + Zend_Validate_NotEmpty::BOOLEAN
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid([]));
        $this->assertTrue($filter->isValid(['xxx']));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testStringNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            [
                'type' => ['zero', 'string', 'boolean'],
            ]
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid([]));
        $this->assertTrue($filter->isValid(['xxx']));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testSingleStringNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            'boolean'
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertTrue($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertTrue($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid([]));
        $this->assertTrue($filter->isValid(['xxx']));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testConfigObject()
    {
        $options = ['type' => 'all'];
        $config = new Zend_Config($options);

        $filter = new Zend_Validate_NotEmpty(
            $config
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertFalse($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertFalse($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertFalse($filter->isValid([]));
        $this->assertTrue($filter->isValid(['xxx']));
        $this->assertFalse($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testSettingFalseType()
    {
        try {
            $this->_validator->setType(true);
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Unknown', $e->getMessage());
        }
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testGetType()
    {
        $this->assertEquals(493, $this->_validator->getType());
    }

    /**
     * @group ZF-3236
     */
    public function testStringWithZeroShouldNotBeTreatedAsEmpty()
    {
        $this->assertTrue($this->_validator->isValid('0'));
    }

    /**
     * Ensures that getMessages() returns expected default value.
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->_validator->getMessages());
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $v2 = new Zend_Validate_NotEmpty();
        $this->assertTrue($this->_validator->isValid($v2));
    }

    /**
     * @ZF-8767
     */
    public function testZF8767()
    {
        $valid = new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::STRING);

        $this->assertFalse($valid->isValid(''));
        $messages = $valid->getMessages();
        $this->assertTrue(array_key_exists('isEmpty', $messages));
        $this->assertStringContainsString("can't be empty", $messages['isEmpty']);
    }

    public function testObjects()
    {
        $valid = new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::STRING);
        $object = new ClassTest1();

        $this->assertFalse($valid->isValid($object));

        $valid = new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::OBJECT);
        $this->assertTrue($valid->isValid($object));
    }

    public function testStringObjects()
    {
        $valid = new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::STRING);
        $object = new ClassTest2();

        $this->assertFalse($valid->isValid($object));

        $valid = new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::OBJECT_STRING);
        $this->assertTrue($valid->isValid($object));

        $object = new ClassTest3();
        $this->assertFalse($valid->isValid($object));
    }
}

class ClassTest1
{
}

class ClassTest2
{
    public function __toString()
    {
        return 'Test';
    }
}

class ClassTest3
{
    public function toString()
    {
        return '';
    }
}
