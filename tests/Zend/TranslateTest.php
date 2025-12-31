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
 * @version    $Id $
 */

/**
 * Zend_Translate.
 */
require_once 'Zend/Translate.php';

/**
 * Zend_Translate_Plural.
 */
require_once 'Zend/Translate/Plural.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Translate
 */
#[AllowDynamicProperties]
class Zend_TranslateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_TranslateTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function testCreate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['1' => '1']);
        $this->assertTrue($lang instanceof Zend_Translate);
    }

    public function testLocaleInitialization()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'message1'], 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testDefaultLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'message1']);
        $defaultLocale = new Zend_Locale();
        $this->assertEquals($defaultLocale->toString(), $lang->getLocale());
    }

    public function testGetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY , ['1' => '1'], 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);
    }

    public function testAddTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');

        $this->assertEquals('msg2', $lang->_('msg2'));

        $lang->addTranslation(['msg2' => 'Message 2'], 'en');
        $this->assertEquals('Message 2', $lang->_('msg2'));
        $this->assertEquals('msg3',      $lang->_('msg3'));

        $lang->addTranslation(['msg3' => 'Message 3'], 'en', ['clear' => true]);
        $this->assertEquals('msg2',      $lang->_('msg2'));
        $this->assertEquals('Message 3', $lang->_('msg3'));
    }

    public function testGetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testSetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());

        $lang->setLocale('ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('ru_RU');
        $this->assertEquals('ru', $lang->getLocale());
    }

    public function testSetLanguage()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testGetLanguageList()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals(2, is_countable($lang->getList()) ? count($lang->getList()) : 0);
        $this->assertTrue(in_array('en', $lang->getList()));
        $this->assertTrue(in_array('ru', $lang->getList()));
    }

    public function testIsAvailable()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertTrue($lang->isAvailable('en'));
        $this->assertTrue($lang->isAvailable('ru'));
        $this->assertFalse($lang->isAvailable('fr'));
    }

    public function testTranslate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1 (en)'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals('Message 1 (en)', $lang->_('msg1', 'en'));
        $this->assertEquals('Message 1 (ru)', $lang->_('msg1'));
        $this->assertEquals('msg2',           $lang->_('msg2', 'en'));
        $this->assertEquals('msg2',           $lang->_('msg2'));
        $this->assertEquals('Message 1 (en)', $lang->translate('msg1', 'en'));
        $this->assertEquals('Message 1 (ru)', $lang->translate('msg1'));
        $this->assertEquals('msg2',           $lang->translate('msg2', 'en'));
        $this->assertEquals('msg2',           $lang->translate('msg2'));
    }

    public function testIsTranslated()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1 (en)'], 'en_US');
        $this->assertTrue($lang->isTranslated('msg1'));
        $this->assertFalse($lang->isTranslated('msg2'));
        $this->assertFalse($lang->isTranslated('msg1', false, 'en'));
        $this->assertFalse($lang->isTranslated('msg1', true,  'en'));
        $this->assertFalse($lang->isTranslated('msg1', false, 'ru'));
    }

    public function testExceptionWhenNoAdapterClassWasSet()
    {
        try {
            $lang = new Zend_Translate(\Zend_Locale::class, __DIR__ . '/Translate/_files/test2', null, ['scan' => Zend_Translate::LOCALE_FILENAME]);
            $this->fail('Exception due to false adapter class expected');
        } catch (Throwable $e) {
            $this->assertStringContainsString('Cannot assign Zend_Locale to property Zend_Translate::$_adapter of type Zend_Translate_Adapter', $e->getMessage());
        }
    }

    public function testZF3679()
    {
        $locale = new Zend_Locale('de_AT');
        Zend_Registry::set(\Zend_Locale::class, $locale);

        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'message1'], 'de_AT');
        $this->assertEquals('de_AT', $lang->getLocale());
        Zend_Registry::_unsetInstance();
    }

    /**
     * Tests if setting locale as options sets locale.
     */
    public function testSetLocaleAsOption()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $lang->setOptions(['locale' => 'ru']);
        $this->assertEquals('ru', $lang->getLocale());
        $lang->setOptions(['locale' => 'en']);
        $this->assertEquals('en', $lang->getLocale());
    }

    /**
     * Tests getting null returns all options.
     */
    public function testGettingAllOptions()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $this->assertTrue(is_array($lang->getOptions()));
    }

    /**
     * Tests if setting locale as options sets locale.
     */
    public function testGettingUnknownOption()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $this->assertEquals(null, $lang->getOptions('unknown'));
    }

    /**
     * Tests getting of all message ids works.
     */
    public function testGettingAllMessageIds()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1', 'msg2' => 'Message 2'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals(['msg1'], $lang->getMessageIds());
        $this->assertEquals(['msg1', 'msg2'], $lang->getMessageIds('en'));
    }

    /**
     * Tests getting of single message ids.
     */
    public function testGettingSingleMessageIds()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1', 'msg2' => 'Message 2'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals('msg1', $lang->getMessageId('Message 1 (ru)'));
        $this->assertEquals('msg2', $lang->getMessageId('Message 2', 'en'));
        $this->assertFalse($lang->getMessageId('Message 5'));
    }

    /**
     * Tests getting of all messages.
     */
    public function testGettingAllMessages()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1', 'msg2' => 'Message 2'], 'en');
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'ru');
        $this->assertEquals(['msg1' => 'Message 1 (ru)'], $lang->getMessages());
        $this->assertEquals(
            ['msg1' => 'Message 1', 'msg2' => 'Message 2'],
            $lang->getMessages('en'));
        $this->assertEquals(
            [
                'en' => ['msg1' => 'Message 1', 'msg2' => 'Message 2'],
                'ru' => ['msg1' => 'Message 1 (ru)'], ],
            $lang->getMessages('all'));
    }

    /**
     * Tests getting default plurals.
     */
    public function testGettingPlurals()
    {
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            ['singular' => ['plural_0 (en)',
                'plural_1 (en)',
                'plural_2 (en)',
                'plural_3 (en)', ],
                'plural' => '', ], 'en'
        );

        $this->assertEquals('plural_0 (en)', $lang->translate(['singular', 'plural', 1]));
        $this->assertEquals('plural_1 (en)', $lang->translate(['singular', 'plural', 2]));

        $this->assertEquals('plural_0 (en)', $lang->plural('singular', 'plural', 1));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 2));
    }

    /**
     * Tests getting plurals from lowered locale.
     */
    public function testGettingPluralsFromLoweredLocale()
    {
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            ['singular' => ['plural_0 (en)',
                'plural_1 (en)',
                'plural_2 (en)',
                'plural_3 (en)', ],
                'plural' => '', ], 'en'
        );
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'en_US');
        $lang->setLocale('en_US');

        $this->assertEquals('plural_0 (en)', $lang->translate(['singular', 'plural', 1]));
        $this->assertEquals('plural_0 (en)', $lang->plural('singular', 'plural', 1));
    }

    /**
     * Tests getting plurals from lowered locale.
     */
    public function testGettingPluralsFromUnknownLocale()
    {
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            ['singular' => ['plural_0 (en)',
                'plural_1 (en)',
                'plural_2 (en)',
                'plural_3 (en)', ],
                'plural' => '', ], 'en'
        );

        $this->assertEquals('singular', $lang->translate(['singular', 'plural', 1], 'ru'));
        $this->assertEquals('singular', $lang->plural('singular', 'plural', 1, 'ru'));
        $this->assertEquals('plural', $lang->translate(['singular', 'plural', 'plural2', 2, 'en'], 'ru'));
        $this->assertEquals('plural', $lang->plural('singular', 'plural', 2, 'ru'));
    }

    /**
     * ZF-6671.
     */
    public function testAddTranslationAfterwards()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, ['msg1' => 'Message 1'], 'en');
        $this->assertEquals('Message 1', $lang->_('msg1'));

        $lang->addTranslation(['msg1' => 'Message 1 (en)'], 'en');
        $this->assertEquals('Message 1 (en)', $lang->_('msg1'));
    }

    /**
     * ZF-7560.
     */
    public function testUseNumericTranslations()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, [0 => 'Message 1', 2 => 'Message 2'], 'en');
        $this->assertEquals('Message 1', $lang->_(0));
        $this->assertEquals('Message 2', $lang->_(2));

        $lang->addTranslation([4 => 'Message 4'], 'en');
        $this->assertEquals('Message 4', $lang->_(4));
    }

    /**
     * ZF-7130.
     */
    public function testMultiFolderScan()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, __DIR__ . '/Translate/Adapter/_files/testarray', 'en_GB', ['scan' => Zend_Translate::LOCALE_DIRECTORY]);
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1'));
    }

    /**
     * ZF-7214.
     */
    public function testMultiClear()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, __DIR__ . '/Translate/Adapter/_files/testarray', 'en_GB', ['scan' => Zend_Translate::LOCALE_DIRECTORY]);
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));
        $lang->addTranslation(__DIR__ . '/Translate/Adapter/_files/translation_en.php', 'ja', ['clear']);
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1', 'ja'));
    }

    /**
     * ZF-7941.
     */
    public function testEmptyTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, null, null, ['disableNotices' => true]);
        $this->assertEquals(0, is_countable($lang->getList()) ? count($lang->getList()) : 0);
    }

    /**
     * Translating Object.
     */
    public function testObjectTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, __DIR__ . '/Translate/Adapter/_files/testarray', 'en_GB', ['scan' => Zend_Translate::LOCALE_DIRECTORY]);
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));

        $this->assertEquals($lang, $lang->translate($lang));
    }

    /**
     * Tests getting plurals from lowered locale.
     */
    public function testGettingPluralsUsingOwnRule()
    {
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            ['singular' => ['plural_0 (en)',
                'plural_1 (en)',
                'plural_2 (en)',
                'plural_3 (en)', ],
                'plural' => '', ], 'en'
        );
        $lang->addTranslation(['msg1' => 'Message 1 (ru)'], 'en_US');
        $lang->setLocale('en_US');

        Zend_Translate_Plural::setPlural([$this, 'customPlural'], 'en_US');
        $this->assertEquals('plural_1 (en)', $lang->translate(['singular', 'plural', 1]));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 1));
        $this->assertEquals('plural_1 (en)', $lang->translate(['singular', 'plural', 0]));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 0));
    }

    /**
     * @group ZF-9500
     */
    public function testIgnoreMultipleDirectories()
    {
        $translate = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            __DIR__ . '/Translate/Adapter/_files/testarray/',
            'auto',
            [
                'scan' => Zend_Translate::LOCALE_FILENAME,
                'ignore' => ['.', 'ignoreme', 'LC_TEST'],
            ]
        );

        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));

        $translate2 = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            __DIR__ . '/Translate/Adapter/_files/testarray/',
            'auto',
            [
                'scan' => Zend_Translate::LOCALE_FILENAME,
                'ignore' => ['.', 'regex_1' => '/de_DE/', 'regex' => '/ja/'],
            ]
        );

        $langs = $translate2->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertFalse(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
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
        $this->_errorOccured = true;
    }

    /**
     * Custom callback for testGettingPluralsUsingOwnRule.
     *
     * @param  int $number
     *
     * @return int
     */
    public function customPlural($number)
    {
        return 1;
    }
}
