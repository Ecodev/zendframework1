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
 * Test class for Zend_Form_Element_File.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_FileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_Form_Element_File
     */
    protected $element;

    /**
     * @var bool
     */
    protected $_errorOccurred = false;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_FileTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        Zend_Registry::_unsetInstance();
        Zend_Form::setDefaultTranslator(null);
        $this->element = new Zend_Form_Element_File('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testElementShouldProxyToParentForDecoratorPluginLoader()
    {
        $loader = $this->element->getPluginLoader('decorator');
        $paths = $loader->getPaths('Zend_Form_Decorator');
        $this->assertTrue(is_array($paths));

        $loader = new Zend_Loader_PluginLoader();
        $this->element->setPluginLoader($loader, 'decorator');
        $test = $this->element->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testElementShouldProxyToParentWhenSettingDecoratorPrefixPaths()
    {
        $this->element->addPrefixPath('Foo_Decorator', 'Foo/Decorator/', 'decorator');
        $loader = $this->element->getPluginLoader('decorator');
        $paths = $loader->getPaths('Foo_Decorator');
        $this->assertTrue(is_array($paths));
    }

    public function testElementShouldAddToAllPluginLoadersWhenAddingNullPrefixPath()
    {
        $this->element->addPrefixPath('Foo', 'Foo');
        foreach (['validate', 'filter', 'decorator', 'transfer_adapter'] as $type) {
            $loader = $this->element->getPluginLoader($type);
            $string = str_replace('_', ' ', $type);
            $string = ucwords($string);
            $string = str_replace(' ', '_', $string);
            $prefix = 'Foo_' . $string;
            $paths = $loader->getPaths($prefix);
            $this->assertTrue(is_array($paths), "Failed asserting paths found for prefix $prefix");
        }
    }

    public function testElementShouldUseHttpTransferAdapterByDefault()
    {
        $adapter = $this->element->getTransferAdapter();
        $this->assertTrue($adapter instanceof Zend_File_Transfer_Adapter_Http);
    }

    public function testElementShouldAllowSpecifyingAdapterUsingConcreteInstance()
    {
        $adapter = new Zend_Form_Element_FileTest_MockAdapter();
        $this->element->setTransferAdapter($adapter);
        $test = $this->element->getTransferAdapter();
        $this->assertSame($adapter, $test);
    }

    public function testElementShouldThrowExceptionWhenAddingAdapterOfInvalidType()
    {
        $this->expectException(\Zend_Form_Element_Exception::class);
        $this->element->setTransferAdapter(new stdClass());
    }

    public function testShouldRegisterPluginLoaderWithFileTransferAdapterPathByDefault()
    {
        $loader = $this->element->getPluginLoader('transfer_adapter');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader_Interface);
        $paths = $loader->getPaths('Zend_File_Transfer_Adapter');
        $this->assertTrue(is_array($paths));
    }

    public function testElementShouldAllowSpecifyingAdapterUsingPluginLoader()
    {
        $this->element->addPrefixPath('Zend_Form_Element_FileTest_Adapter', __DIR__ . '/_files/TransferAdapter', 'transfer_adapter');
        $this->element->setTransferAdapter('Foo');
        $test = $this->element->getTransferAdapter();
        $this->assertTrue($test instanceof Zend_Form_Element_FileTest_Adapter_Foo);
    }

    public function testValidatorAccessAndMutationShouldProxyToAdapter()
    {
        $this->testElementShouldAllowSpecifyingAdapterUsingConcreteInstance();
        $this->element->addValidator('Count', false, 1)
            ->addValidators([
                'Extension' => 'jpg',
                new Zend_Validate_File_Upload(),
            ]);
        $validators = $this->element->getValidators();
        $test = $this->element->getTransferAdapter()->getValidators();
        $this->assertEquals($validators, $test);
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count((array) $test));

        $validator = $this->element->getValidator('count');
        $test = $this->element->getTransferAdapter()->getValidator('count');
        $this->assertNotNull($validator);
        $this->assertSame($validator, $test);

        $this->element->removeValidator('Extension');
        $this->assertFalse($this->element->getTransferAdapter()->hasValidator('Extension'));

        $this->element->setValidators([
            'Upload',
            ['validator' => 'Extension', 'options' => 'jpg'],
            ['validator' => 'Count', 'options' => 1],
        ]);
        $validators = $this->element->getValidators();
        $test = $this->element->getTransferAdapter()->getValidators();
        $this->assertSame($validators, $test);
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count((array) $test), var_export($test, 1));

        $this->element->clearValidators();
        $validators = $this->element->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(0, count($validators));
        $test = $this->element->getTransferAdapter()->getValidators();
        $this->assertSame($validators, $test);
    }

    public function testValidationShouldProxyToAdapter()
    {
        $this->markTestIncomplete('Unsure how to accurately test');

        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->element->addValidator('Regex', '/([a-z0-9]{13})$/i');
        $this->assertTrue($this->element->isValid('foo.jpg'));
    }

    public function testDestinationMutatorsShouldProxyToTransferAdapter()
    {
        $adapter = new Zend_Form_Element_FileTest_MockAdapter();
        $this->element->setTransferAdapter($adapter);

        $this->element->setDestination(__DIR__);
        $this->assertEquals(__DIR__, $this->element->getDestination());
        $this->assertEquals(__DIR__, $this->element->getTransferAdapter()->getDestination('foo'));
    }

    public function testSettingMultipleFiles()
    {
        $this->element->setMultiFile(3);
        $this->assertEquals(3, $this->element->getMultiFile());
    }

    public function testFileInSubSubSubform()
    {
        $form = new Zend_Form();
        $element = new Zend_Form_Element_File('file1');
        $element2 = new Zend_Form_Element_File('file2');

        $subform0 = new Zend_Form_SubForm();
        $subform0->addElement($element);
        $subform0->addElement($element2);
        $subform1 = new Zend_Form_SubForm();
        $subform1->addSubform($subform0, 'subform0');
        $subform2 = new Zend_Form_SubForm();
        $subform2->addSubform($subform1, 'subform1');
        $subform3 = new Zend_Form_SubForm();
        $subform3->addSubform($subform2, 'subform2');
        $form->addSubform($subform3, 'subform3');

        $form->setView(new Zend_View());
        $output = (string) $form;
        $this->assertStringContainsString('name="file1"', $output);
        $this->assertStringContainsString('name="file2"', $output);
    }

    public function testMultiFileInSubSubSubform()
    {
        $form = new Zend_Form();
        $element = new Zend_Form_Element_File('file');
        $element->setMultiFile(2);

        $subform0 = new Zend_Form_SubForm();
        $subform0->addElement($element);
        $subform1 = new Zend_Form_SubForm();
        $subform1->addSubform($subform0, 'subform0');
        $subform2 = new Zend_Form_SubForm();
        $subform2->addSubform($subform1, 'subform1');
        $subform3 = new Zend_Form_SubForm();
        $subform3->addSubform($subform2, 'subform2');
        $form->addSubform($subform3, 'subform3');

        $form->setView(new Zend_View());
        $output = (string) $form;
        $this->assertStringContainsString('name="file[]"', $output);
        $this->assertEquals(2, substr_count($output, 'file[]'));
    }

    public function testMultiFileWithOneFile()
    {
        $form = new Zend_Form();
        $element = new Zend_Form_Element_File('file');
        $element->setMultiFile(1);

        $subform0 = new Zend_Form_SubForm();
        $subform0->addElement($element);
        $subform1 = new Zend_Form_SubForm();
        $subform1->addSubform($subform0, 'subform0');
        $subform2 = new Zend_Form_SubForm();
        $subform2->addSubform($subform1, 'subform1');
        $subform3 = new Zend_Form_SubForm();
        $subform3->addSubform($subform2, 'subform2');
        $form->addSubform($subform3, 'subform3');

        $form->setView(new Zend_View());
        $output = (string) $form;
        $this->assertStringNotContainsString('name="file[]"', $output);
    }

    public function testSettingMaxFileSize()
    {
        $max = $this->_convertIniToInteger(trim(ini_get('upload_max_filesize')));

        $this->assertEquals(0, $this->element->getMaxFileSize());
        $this->element->setMaxFileSize($max);
        $this->assertEquals($max, $this->element->getMaxFileSize());

        $this->_errorOccurred = false;
        set_error_handler([$this, 'errorHandlerIgnore']);
        $this->element->setMaxFileSize(999_999_999_999);
        if (!$this->_errorOccurred) {
            $this->fail('INI exception expected');
        }
        restore_error_handler();
    }

    public function testAutoGetPostMaxSize()
    {
        $this->element->setMaxFileSize(-1);
        $this->assertNotEquals(-1, $this->element->getMaxFileSize());
    }

    public function testTranslatingValidatorErrors()
    {
        $translate = new Zend_Translate('array', ['unused', 'foo' => 'bar'], 'en');
        $this->element->setTranslator($translate);

        $adapter = $this->element->getTranslator();
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);

        $adapter = $this->element->getTransferAdapter();
        $adapter = $adapter->getTranslator();
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);

        $this->assertFalse($this->element->translatorIsDisabled());
        $this->element->setDisableTranslator($translate);
        $this->assertTrue($this->element->translatorIsDisabled());
    }

    public function testFileNameWithoutPath()
    {
        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->element->setDestination(__DIR__);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'foo.jpg', $this->element->getFileName('foo', true));
        $this->assertEquals('foo.jpg', $this->element->getFileName('foo', false));
    }

    public function testEmptyFileName()
    {
        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->element->setDestination(__DIR__);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'foo.jpg', $this->element->getFileName());
    }

    public function testIsReceived()
    {
        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->assertEquals(false, $this->element->isReceived());
    }

    public function testIsUploaded()
    {
        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->assertEquals(true, $this->element->isUploaded());
    }

    public function testIsFiltered()
    {
        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->assertEquals(true, $this->element->isFiltered());
    }

    public function testDefaultDecorators()
    {
        $this->element->clearDecorators();
        $this->assertEquals([], $this->element->getDecorators());
        $this->element->setDisableLoadDefaultDecorators(true);
        $this->element->loadDefaultDecorators();
        $this->assertEquals([], $this->element->getDecorators());
        $this->element->setDisableLoadDefaultDecorators(false);
        $this->element->loadDefaultDecorators();
        $this->assertNotEquals([], $this->element->getDecorators());
    }

    public function testValueGetAndSet()
    {
        $this->element->setTransferAdapter(new Zend_Form_Element_FileTest_MockAdapter());
        $this->assertEquals(null, $this->element->getValue());
        $this->element->setValue('something');
        $this->assertEquals(null, $this->element->getValue());
    }

    public function testMarkerInterfaceForFileElement()
    {
        $this->element->setDecorators(['ViewHelper']);
        $this->assertEquals(1, count($this->element->getDecorators()));

        try {
            $content = $this->element->render(new Zend_View());
            $this->fail();
        } catch (Zend_Form_Element_Exception $e) {
            $this->assertStringContainsString('No file decorator found', $e->getMessage());
        }
    }

    public function testFileSize()
    {
        $element = new Zend_Form_Element_File('baz');
        $adapter = new Zend_Form_Element_FileTest_MockAdapter();
        $element->setTransferAdapter($adapter);

        $this->assertEquals('1.14kB', $element->getFileSize());
        $adapter->setOptions(['useByteString' => false]);
        $this->assertEquals(1172, $element->getFileSize());
    }

    public function testMimeType()
    {
        $element = new Zend_Form_Element_File('baz');
        $adapter = new Zend_Form_Element_FileTest_MockAdapter();
        $element->setTransferAdapter($adapter);

        $this->assertEquals('text/plain', $element->getMimeType());
    }

    public function testAddedErrorsAreDisplayed()
    {
        Zend_Form::setDefaultTranslator(null);
        $element = new Zend_Form_Element_File('baz');
        $element->addError('TestError3');
        $adapter = new Zend_Form_Element_FileTest_MockAdapter();
        $element->setTransferAdapter($adapter);

        $this->assertTrue($element->hasErrors());
        $messages = $element->getMessages();
        $this->assertContains('TestError3', $messages);
    }

    public function testGetTranslatorRetrievesGlobalDefaultWhenAvailable()
    {
        $this->assertNull($this->element->getTranslator());
        $translator = new Zend_Translate('array', ['foo' => 'bar']);
        Zend_Form::setDefaultTranslator($translator);
        $received = $this->element->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testDefaultDecoratorsContainDescription()
    {
        $element = new Zend_Form_Element_File('baz');
        $decorators = $element->getDecorator('Description');
        $this->assertTrue($decorators instanceof Zend_Form_Decorator_Description);
    }

    private function _convertIniToInteger($setting)
    {
        if (!is_numeric($setting)) {
            $type = strtoupper(substr($setting, -1));
            $setting = (integer) substr($setting, 0, -1);

            switch ($type) {
                case 'M':
                    $setting *= 1024;

                    break;

                case 'G':
                    $setting *= 1024 * 1024;

                    break;

                default:
                    break;
            }
        }

        return (integer) $setting;
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

    /**
     * Prove the fluent interface on Zend_Form_Element_File::loadDefaultDecorators.
     *
     * @see http://framework.zend.com/issues/browse/ZF-9913
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }

    /**
     * @group ZF-12173
     */
    public function testElementShouldAllowAdapterWithBackslahes()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                self::class . '::' . __METHOD__ . ' requires PHP 5.3.0 or greater'
            );

            return;
        }
        $this->element->addPrefixPath(
            'Zend\Form\Element\FileTest\Adapter',
            __DIR__ . '/_files/TransferAdapter',
            'transfer_adapter'
        );
        $this->element->setTransferAdapter('Bar');
        $test = $this->element->getTransferAdapter();

        $expectedType = 'Zend\Form\Element\FileTest\Adapter\Bar';
        $this->assertTrue(
            $test instanceof $expectedType
        );
    }

    /**
     * @group ZF-12210
     */
    public function testAutoInsertNotEmptyValidator()
    {
        $this->testElementShouldAllowSpecifyingAdapterUsingConcreteInstance();
        $this->element->setRequired(true);

        // Test before validation
        $this->assertNull($this->element->getValidator('NotEmpty'));

        // Test after validation
        $this->element->isValid('foo.jpg');

        $this->assertTrue(
            $this->element->getValidator('NotEmpty') instanceof Zend_Validate_NotEmpty
        );
    }

    /**
     * @group GH-247
     */
    public function testCallbackFunctionAtHtmlTag()
    {
        $this->assertEquals(
            [
                'callback' => [
                    \Zend_Form_Element_File::class,
                    'resolveElementId',
                ],
            ],
            $this->element->getDecorator('HtmlTag')->getOption('id')
        );
    }

    /**
     * @group GH-247
     */
    public function testDefaultDecoratorOrder()
    {
        $expected = [
            \Zend_Form_Decorator_File::class,
            \Zend_Form_Decorator_Errors::class,
            \Zend_Form_Decorator_Description::class,
            \Zend_Form_Decorator_HtmlTag::class,
            \Zend_Form_Decorator_Label::class,
        ];

        $this->assertEquals(
            $expected,
            array_keys($this->element->getDecorators())
        );
    }
}

#[AllowDynamicProperties]
class Zend_Form_Element_FileTest_MockAdapter extends Zend_File_Transfer_Adapter_Abstract
{
    public $received = false;

    public function __construct()
    {
        $testfile = __DIR__ . '/../../File/Transfer/Adapter/_files/test.txt';
        $this->_files = [
            'foo' => [
                'name' => 'foo.jpg',
                'type' => 'image/jpeg',
                'size' => 126976,
                'tmp_name' => '/tmp/489127ba5c89c',
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
                'validators' => [],
            ],
            'bar' => [
                'name' => 'bar.png',
                'type' => 'image/png',
                'size' => 91136,
                'tmp_name' => '/tmp/489128284b51f',
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
                'validators' => [],
            ],
            'baz' => [
                'name' => 'baz.text',
                'type' => 'text/plain',
                'size' => 1172,
                'tmp_name' => $testfile,
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
                'validators' => [],
            ],
            'file_1_' => [
                'name' => 'baz.text',
                'type' => 'text/plain',
                'size' => 1172,
                'tmp_name' => '/tmp/4891286cceff3',
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
                'validators' => [],
            ],
            'file_2_' => [
                'name' => 'baz.text',
                'type' => 'text/plain',
                'size' => 1172,
                'tmp_name' => '/tmp/4891286cceff3',
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
                'validators' => [],
            ],
        ];
    }

    public function send($options = null)
    {
    }

    public function receive($options = null)
    {
        $this->received = true;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
    }
}
