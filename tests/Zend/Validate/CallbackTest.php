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
 * @see Zend_Validate_Callback
 */
require_once 'Zend/Validate/Callback.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Validate')]
class Zend_Validate_CallbackTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs this test suite.
     */

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valid = new Zend_Validate_Callback([$this, 'objectCallback']);
        $this->assertTrue($valid->isValid('test'));
    }

    public function testStaticCallback()
    {
        $valid = new Zend_Validate_Callback(
            ['Zend_Validate_CallbackTest', 'staticCallback']
        );
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $valid = new Zend_Validate_Callback([$this, 'objectCallback']);
        $valid->setOptions('options');
        $this->assertEquals(['options'], $valid->getOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptions()
    {
        $valid = new Zend_Validate_Callback(['callback' => [$this, 'objectCallback'], 'options' => 'options']);
        $this->assertEquals(['options'], $valid->getOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testGettingCallback()
    {
        $valid = new Zend_Validate_Callback([$this, 'objectCallback']);
        $this->assertEquals([$this, 'objectCallback'], $valid->getCallback());
    }

    public function testInvalidCallback()
    {
        $valid = new Zend_Validate_Callback([$this, 'objectCallback']);

        try {
            $valid->setCallback('invalidcallback');
            $this->fail('Exception expected');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Invalid callback given', $e->getMessage());
        }
    }

    public function testAddingValueOptions()
    {
        $valid = new Zend_Validate_Callback(['callback' => [$this, 'optionsCallback'], 'options' => 'options']);
        $this->assertEquals(['options'], $valid->getOptions());
        $this->assertTrue($valid->isValid('test', 'something'));
    }

    public function objectCallback($value)
    {
        return true;
    }

    public static function staticCallback($value)
    {
        return true;
    }

    public function optionsCallback($value)
    {
        $args = func_get_args();
        $this->assertContains('something', $args);

        return $args;
    }
}
