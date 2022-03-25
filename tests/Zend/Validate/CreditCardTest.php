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
 * @version    $Id:$
 */

/**
 * @see Zend_Validate_CreditCard
 */
require_once 'Zend/Validate/CreditCard.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_CreditCardTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $validator = new Zend_Validate_CreditCard();
        $valuesExpected = [
            ['4111111111111111', true],
            ['5404000000000001', true],
            ['374200000000004',  true],
            ['4444555566667777', false],
            ['ABCDEF',           false],
        ];
        foreach ($valuesExpected as $test) {
            $input = $test[0];
            $result = $test[1];
            $this->assertEquals($result, $validator->isValid($input), 'Test failed at ' . $input);
        }
    }

    /**
     * Ensures that getMessages() returns expected default value.
     */
    public function testGetMessages()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that get and setType works as expected.
     */
    public function testGetSetType()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(11, count($validator->getType()));

        $validator->setType(Zend_Validate_CreditCard::MAESTRO);
        $this->assertEquals([Zend_Validate_CreditCard::MAESTRO], $validator->getType());

        $validator->setType(
            [
                Zend_Validate_CreditCard::AMERICAN_EXPRESS,
                Zend_Validate_CreditCard::MAESTRO,
            ]
        );
        $this->assertEquals(
            [
                Zend_Validate_CreditCard::AMERICAN_EXPRESS,
                Zend_Validate_CreditCard::MAESTRO,
            ],
            $validator->getType()
        );

        $validator->addType(
            Zend_Validate_CreditCard::MASTERCARD
        );
        $this->assertEquals(
            [
                Zend_Validate_CreditCard::AMERICAN_EXPRESS,
                Zend_Validate_CreditCard::MAESTRO,
                Zend_Validate_CreditCard::MASTERCARD,
            ],
            $validator->getType()
        );
    }

    /**
     * Test specific provider.
     */
    public function testProvider()
    {
        $validator = new Zend_Validate_CreditCard(Zend_Validate_CreditCard::VISA);
        $valuesExpected = [
            ['4111111111111111', true],
            ['5404000000000001', false],
            ['374200000000004',  false],
            ['4444555566667777', false],
            ['ABCDEF',           false],
        ];
        foreach ($valuesExpected as $test) {
            $input = $test[0];
            $result = $test[1];
            $this->assertEquals($result, $validator->isValid($input));
        }
    }

    /**
     * Test non string input.
     */
    public function testIsValidWithNonString()
    {
        $validator = new Zend_Validate_CreditCard(Zend_Validate_CreditCard::VISA);
        $this->assertFalse($validator->isValid(['something']));
    }

    /**
     * Test service class with invalid validation.
     */
    public function testServiceClass()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(null, $validator->getService());
        $validator->setService(['Zend_Validate_CreditCardTest', 'staticCallback']);
        $valuesExpected = [
            '4111111111111111' => false,
            '5404000000000001' => false,
            '374200000000004' => false,
            '4444555566667777' => false,
            'ABCDEF' => false,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input));
        }
    }

    /**
     * Test non string input.
     */
    public function testConstructionWithOptions()
    {
        $validator = new Zend_Validate_CreditCard(
            [
                'type' => Zend_Validate_CreditCard::VISA,
                'service' => ['Zend_Validate_CreditCardTest', 'staticCallback'],
            ]
        );

        $valuesExpected = [
            '4111111111111111' => false,
            '5404000000000001' => false,
            '374200000000004' => false,
            '4444555566667777' => false,
            'ABCDEF' => false,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input));
        }
    }

    /**
     * Test a invalid service class.
     */
    public function testInvalidServiceClass()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(null, $validator->getService());

        try {
            $validator->setService(['Zend_Validate_CreditCardTest', 'nocallback']);
            $this->fail('Exception expected');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Invalid callback given', $e->getMessage());
        }
    }

    /**
     * Test a config object.
     */
    public function testConfigObject()
    {
        require_once 'Zend/Config.php';
        $options = ['type' => 'Visa'];
        $config = new Zend_Config($options, false);

        $validator = new Zend_Validate_CreditCard($config);
        $this->assertEquals(['Visa'], $validator->getType());
    }

    /**
     * Test optional parameters with config object.
     */
    public function testOptionalConstructorParameterByConfigObject()
    {
        require_once 'Zend/Config.php';
        $config = new Zend_Config(['type' => 'Visa', 'service' => ['Zend_Validate_CreditCardTest', 'staticCallback']]);

        $validator = new Zend_Validate_CreditCard($config);
        $this->assertEquals(['Visa'], $validator->getType());
        $this->assertEquals(['Zend_Validate_CreditCardTest', 'staticCallback'], $validator->getService());
    }

    /**
     * Test optional constructor parameters.
     */
    public function testOptionalConstructorParameter()
    {
        $validator = new Zend_Validate_CreditCard('Visa', ['Zend_Validate_CreditCardTest', 'staticCallback']);
        $this->assertEquals(['Visa'], $validator->getType());
        $this->assertEquals(['Zend_Validate_CreditCardTest', 'staticCallback'], $validator->getService());
    }

    /**
     * @group ZF-9477
     */
    public function testMultiInstitute()
    {
        $validator = new Zend_Validate_CreditCard(['type' => Zend_Validate_CreditCard::MASTERCARD]);
        $this->assertFalse($validator->isValid('4111111111111111'));
        $message = $validator->getMessages();
        $this->assertStringContainsString('not from an allowed institute', current($message));
    }

    public static function staticCallback($value)
    {
        return false;
    }
}
