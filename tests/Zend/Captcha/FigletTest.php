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
require_once 'Zend/Captcha/Adapter.php';
require_once 'Zend/Config.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Captcha
 */
#[AllowDynamicProperties]
class Zend_Captcha_FigletTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Captcha_FigletTest');
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
            'captchaF',
            [
                'captcha' => [
                    'Figlet',
                    'sessionClass' => 'Zend_Captcha_FigletTest_SessionContainer',
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

    public function testCaptchaAdapterCreated()
    {
        $this->assertTrue($this->element->getCaptcha() instanceof Zend_Captcha_Adapter);
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper');

        return $view;
    }

    public function testCaptchaIsRendered()
    {
        $html = $this->element->render($this->getView());
        $this->assertStringContainsString($this->element->getName(), $html);
    }

    public function testCaptchaHasIdAndInput()
    {
        $html = $this->element->render($this->getView());
        $expect = sprintf('type="hidden" name="%s\[id\]" value="%s"', $this->element->getName(), $this->captcha->getId());
        $this->assertMatchesRegularExpression("/<input[^>]*?$expect/", $html, $html);
        $expect = sprintf('type="text" name="%s\[input\]"', $this->element->getName());
        $this->assertMatchesRegularExpression("/<input[^>]*?$expect/", $html, $html);
    }

    // @group ZF-8268
    public function testLabelIdIsCorrect()
    {
        $form = new Zend_Form();
        $form->setElementsBelongTo('comment');
        $this->element->setLabel('My Captcha');
        $form->addElement($this->element);
        $html = $form->render($this->getView());
        $expect = sprintf('for="comment-%s-input"', $this->element->getName());
        $this->assertMatchesRegularExpression("/<label [^>]*?$expect/", $html, $html);
    }

    public function testTimeoutPopulatedByDefault()
    {
        $ttl = $this->captcha->getTimeout();
        $this->assertFalse(empty($ttl));
        $this->assertTrue(is_int($ttl));
    }

    public function testCanSetTimeout()
    {
        $ttl = $this->captcha->getTimeout();
        $this->captcha->setTimeout(3600);
        $this->assertNotEquals($ttl, $this->captcha->getTimeout());
        $this->assertEquals(3600, $this->captcha->getTimeout());
    }

    public function testGenerateReturnsId()
    {
        $id = $this->captcha->generate();
        $this->assertFalse(empty($id));
        $this->assertTrue(is_string($id));
        $this->id = $id;
    }

    public function testGetWordReturnsWord()
    {
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertFalse(empty($word));
        $this->assertTrue(is_string($word));
        $this->assertTrue(strlen((string) $word) == 8);
        $this->word = $word;
    }

    public function testGetWordLength()
    {
        $this->captcha->setWordLen(4);
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertTrue(is_string($word));
        $this->assertTrue(strlen((string) $word) == 4);
        $this->word = $word;
    }

    public function testAdapterElementName()
    {
        $this->assertEquals($this->captcha->getName(),
            $this->element->getName());
    }

    public function testGenerateIsRandomised()
    {
        $id1 = $this->captcha->generate();
        $word1 = $this->captcha->getWord();
        $id2 = $this->captcha->generate();
        $word2 = $this->captcha->getWord();

        $this->assertFalse(empty($id1));
        $this->assertFalse(empty($id2));
        $this->assertFalse($id1 == $id2);
        $this->assertFalse($word1 == $word2);
    }

    public function testRenderSetsValue()
    {
        $this->testCaptchaIsRendered();
        $this->assertEquals($this->captcha->getId(),
            $this->element->getValue());
    }

    public function testLabelIsNull()
    {
        $this->assertNull($this->element->getLabel());
    }

    public function testRenderInitializesSessionData()
    {
        $this->testCaptchaIsRendered();
        $session = $this->captcha->getSession();
        $this->assertEquals($this->captcha->getTimeout(), $session->setExpirationSeconds);
        $this->assertEquals(1, $session->setExpirationHops);
        $this->assertEquals($this->captcha->getWord(), $session->word);
    }

    public function testWordValidates()
    {
        $this->testCaptchaIsRendered();
        $input = [$this->element->getName() => ['id' => $this->captcha->getId(), 'input' => $this->captcha->getWord()]];
        $this->assertTrue($this->element->isValid('', $input));
    }

    public function testMissingNotValid()
    {
        $this->testCaptchaIsRendered();
        $this->assertFalse($this->element->isValid('', []));
        $input = [$this->element->getName() => ['input' => 'blah']];
        $this->assertFalse($this->element->isValid('', $input));
    }

    public function testWrongWordNotValid()
    {
        $this->testCaptchaIsRendered();
        $input = [$this->element->getName() => ['id' => $this->captcha->getId(), 'input' => 'blah']];
        $this->assertFalse($this->element->isValid('', $input));
    }

    public function testUsesWordCaptchaDecoratorByDefault()
    {
        $this->assertEquals('Captcha_Word', $this->element->getCaptcha()->getDecorator());
    }

    public function testCaptchaShouldBeConfigurableViaConfigObject()
    {
        $options = [
            'name' => 'foo',
            'sessionClass' => 'Zend_Captcha_FigletTest_SessionContainer',
            'wordLen' => 6,
            'timeout' => 300,
        ];
        $config = new Zend_Config($options);
        $captcha = new Zend_Captcha_Figlet($config);
        $test = $captcha->getOptions();
        $this->assertEquals($options, $test);
    }

    public function testShouldAllowFigletsLargerThanFourteenCharacters()
    {
        $this->captcha->setName('foo')
            ->setWordLen(14);
        $id = $this->captcha->generate();

        self::assertIsString($id);
    }

    public function testShouldNotValidateEmptyInputAgainstEmptySession()
    {
        // Regression Test for ZF-4245
        $this->captcha->setName('foo')
            ->setWordLen(6)
            ->setTimeout(300);
        $id = $this->captcha->generate();
        // Unset the generated word
        // we have to reset $this->captcha for that
        $this->captcha->getSession()->word = null;
        $this->setUp();
        $this->captcha->setName('foo')
            ->setWordLen(6)
            ->setTimeout(300);
        $empty = [$this->captcha->getName() => ['id' => $id, 'input' => '']];
        $this->assertEquals(false, $this->captcha->isValid(null, $empty));
    }

    /**
     * @group ZF-3995
     */
    public function testIsValidShouldAllowPassingArrayValueAndOmittingContext()
    {
        $this->testCaptchaIsRendered();
        $input = [$this->element->getName() => ['id' => $this->captcha->getId(), 'input' => $this->captcha->getWord()]];
        $this->assertTrue($this->element->isValid($input));
    }

    /**
     * @group ZF-3995
     */
    public function testIsValidShouldNotRequireValueToBeNestedArray()
    {
        $this->testCaptchaIsRendered();
        $input = ['id' => $this->captcha->getId(), 'input' => $this->captcha->getWord()];
        $this->assertTrue($this->element->isValid($input));
    }

    /**
     * @group ZF-5728
     */
    public function testSetSessionWorks()
    {
        if (headers_sent($file, $line)) {
            $this->markTestSkipped('Cannot use sessions because headers already sent');
        }
        $session = new Zend_Session_Namespace('captcha');
        $this->captcha->setSession($session);
        $this->testCaptchaIsRendered();
        $input = ['id' => $this->captcha->getId(), 'input' => $this->captcha->getWord()];
        $this->assertTrue($this->element->isValid($input));
        $this->assertEquals($session->word, $this->captcha->getWord());
    }
}

#[AllowDynamicProperties]
class Zend_Captcha_FigletTest_SessionContainer
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
