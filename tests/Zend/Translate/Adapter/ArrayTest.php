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
 * Zend_Translate_Adapter_Array.
 */
require_once 'Zend/Translate/Adapter/Array.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Translate
 */
#[AllowDynamicProperties]
class Zend_Translate_Adapter_ArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Error flag.
     *
     * @var bool
     */
    protected $_errorOccurred = false;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Translate_Adapter_ArrayTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        if (Zend_Translate_Adapter_Array::hasCache()) {
            Zend_Translate_Adapter_Array::clearCache();
            Zend_Translate_Adapter_Array::removeCache();
        }
    }

    public function tearDown(): void
    {
        if (Zend_Translate_Adapter_Array::hasCache()) {
            Zend_Translate_Adapter_Array::clearCache();
            Zend_Translate_Adapter_Array::removeCache();
        }
    }

    public function testCreate()
    {
        set_error_handler([$this, 'errorHandlerIgnore']);
        $adapter = new Zend_Translate_Adapter_Array([]);
        restore_error_handler();
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);

        try {
            $adapter = new Zend_Translate_Adapter_Array('hastofail', 'en');
            $this->fail('Exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertStringContainsString('Error including array or file', $e->getMessage());
        }

        try {
            $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/failed.php', 'en');
            $this->fail('Exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertStringContainsString('Error including array or file', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Array(['msg1' => 'Message 1 (en)', 'msg2' => 'Message 2 (en)', 'msg3' => 'Message 3 (en)'], 'en');
        $this->assertEquals('Array', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertFalse($adapter->isTranslated('Message 1', true, 'en_US'));
        $this->assertTrue($adapter->isTranslated('Message 1', false, 'en_US'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
        $this->assertFalse($adapter->isTranslated('Message 1', 'es'));
        $this->assertFalse($adapter->isTranslated('Message 1', 'xx_XX'));
        $this->assertTrue($adapter->isTranslated('Message 1', 'en_XX'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(__DIR__ . '/_files/translation_en.php', 'xx');
            $this->fail('exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertStringContainsString('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.php', 'de', ['clear' => true]);
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $adapter->setOptions(['testoption' => 'testkey']);
        $expected = [
            'testoption' => 'testkey',
            'clear' => false,
            'content' => __DIR__ . '/_files/translation_en.php',
            'scan' => null,
            'locale' => 'en',
            'ignore' => '.',
            'disableNotices' => false,
            'log' => false,
            'logMessage' => 'Untranslated message within \'%locale%\': %message%',
            'logUntranslated' => false,
            'reload' => false,
        ];

        $options = $adapter->getOptions();

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $options);
            $this->assertEquals($value, $options[$key]);
        }

        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.php', 'de', ['clear' => true]);
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail('exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertStringContainsString('does not exist', $e->getMessage());
        }

        set_error_handler([$this, 'errorHandlerIgnore']);
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(['en' => 'en'], $adapter->getList());
        $adapter->addTranslation(['msg1'], 'de');
        $this->assertEquals(['en' => 'en', 'de' => 'de'], $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/testarray', 'de_AT', ['scan' => Zend_Translate::LOCALE_DIRECTORY]);
        $this->assertEquals(['de_AT' => 'de_AT', 'en_GB' => 'en_GB', 'ja' => 'ja'], $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/testarray', 'de_DE', ['scan' => Zend_Translate::LOCALE_FILENAME]);
        $this->assertEquals(['de_DE' => 'de_DE', 'en_US' => 'en_US', 'ja' => 'ja'], $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testLoadArrayFile()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);
    }

    public function testGettingAllMessageIds()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(6, count($adapter->getMessageIds()));
        $test = $adapter->getMessageIds();
        $this->assertEquals('Message 1', $test[0]);
    }

    public function testGettingMessages()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(6, count($adapter->getMessages()));
        $test = $adapter->getMessages();
        $this->assertEquals('Message 1 (en)', $test['Message 1']);
    }

    public function testGettingAllMessages()
    {
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(1, count($adapter->getMessages('all')));
        $test = $adapter->getMessages('all');
        $this->assertEquals('Message 1 (en)', $test['en']['Message 1']);
    }

    public function testCaching()
    {
        $cache = Zend_Cache::factory('Core', 'File',
            ['lifetime' => 120, 'automatic_serialization' => true],
            ['cache_dir' => __DIR__ . '/_files/']);

        $this->assertFalse(Zend_Translate_Adapter_Array::hasCache());
        Zend_Translate_Adapter_Array::setCache($cache);
        $this->assertTrue(Zend_Translate_Adapter_Array::hasCache());

        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $cache = Zend_Translate_Adapter_Array::getCache();
        $this->assertTrue($cache instanceof Zend_Cache_Core);
        unset($adapter);

        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $cache = Zend_Translate_Adapter_Array::getCache();
        $this->assertTrue($cache instanceof Zend_Cache_Core);

        Zend_Translate_Adapter_Array::removeCache();
        $this->assertFalse(Zend_Translate_Adapter_Array::hasCache());

        $cache->save('testdata', 'testid');
        Zend_Translate_Adapter_Array::setCache($cache);
        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        Zend_Translate_Adapter_Array::removeCache();
        $temp = $cache->load('testid');
        $this->assertEquals('testdata', $temp);
    }

    public function testLoadingFilesIntoCacheAfterwards()
    {
        $cache = Zend_Cache::factory('Core', 'File',
            ['lifetime' => 120, 'automatic_serialization' => true],
            ['cache_dir' => __DIR__ . '/_files/']);

        $this->assertFalse(Zend_Translate_Adapter_Array::hasCache());
        Zend_Translate_Adapter_Array::setCache($cache);
        $this->assertTrue(Zend_Translate_Adapter_Array::hasCache());

        $adapter = new Zend_Translate_Adapter_Array(__DIR__ . '/_files/translation_en.php', 'en');
        $cache = Zend_Translate_Adapter_Array::getCache();
        $this->assertTrue($cache instanceof Zend_Cache_Core);

        $adapter->addTranslation(__DIR__ . '/_files/translation_en.php', 'ru', ['reload' => true]);
        $test = $adapter->getMessages('all');
        $this->assertEquals(6, is_countable($test['ru']) ? count($test['ru']) : 0);
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred.
     *
     * @param  int $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  int $errline
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline)
    {
        $this->_errorOccurred = true;
    }
}
