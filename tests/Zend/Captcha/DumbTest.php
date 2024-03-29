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
require_once 'Zend/View.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Captcha
 */
#[AllowDynamicProperties]
class Zend_Captcha_DumbTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Captcha_DumbTest');
        $result = (new \PHPUnit\TextUI\TestRunner())->run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        if (isset($this->word)) {
            unset($this->word);
        }

        $this->element = new Zend_Form_Element_Captcha(
            'captchaD',
            [
                'captcha' => [
                    'Dumb',
                    'sessionClass' => 'Zend_Captcha_DumbTest_SessionContainer',
                ],
            ]
        );
        $this->captcha = $this->element->getCaptcha();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testRendersWordInReverse()
    {
        $id = $this->captcha->generate('test');
        $word = $this->captcha->getWord();
        $html = $this->captcha->render(new Zend_View());
        $this->assertStringContainsString(strrev($word), $html);
        $this->assertStringNotContainsString($word, $html);
    }

    /**
     * @group ZF-11522
     */
    public function testDefaultLabelIsUsedWhenNoAlternateLabelSet()
    {
        $this->assertEquals('Please type this word backwards', $this->captcha->getLabel());
    }

    /**
     * @group ZF-11522
     */
    public function testChangeLabelViaSetterMethod()
    {
        $this->captcha->setLabel('Testing');
        $this->assertEquals('Testing', $this->captcha->getLabel());
    }

    /**
     * @group ZF-11522
     */
    public function testRendersLabelUsingProvidedValue()
    {
        $this->captcha->setLabel('Testing 123');

        $id = $this->captcha->generate('test');
        $html = $this->captcha->render(new Zend_View());
        $this->assertStringContainsString('Testing 123', $html);
    }
}

#[AllowDynamicProperties]
class Zend_Captcha_DumbTest_SessionContainer
{
    protected static $_word;

    public function __get($name)
    {
        if ('word' == $name) {
            return self::$_word;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if ('word' == $name) {
            self::$_word = $value;
        } else {
            $this->$name = $value;
        }
    }

    public function __isset($name)
    {
        if (('word' == $name) && (null !== self::$_word)) {
            return true;
        }

        return false;
    }

    public function __call($method, $args)
    {
        $this->$method = match ($method) {
            'setExpirationHops', 'setExpirationSeconds' => array_shift($args),
        };
    }
}
