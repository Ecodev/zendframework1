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
require_once 'Zend/Validate/File/Extension.php';

/**
 * Test class for Zend_File_Transfer_Adapter_Abstract.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_File
 */
#[AllowDynamicProperties]
class Zend_File_Transfer_Adapter_AbstractTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_File_Transfer_Adapter_AbstractTest_MockAdapter
     */
    protected $adapter;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_File_Transfer_Adapter_AbstractTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->adapter = new Zend_File_Transfer_Adapter_AbstractTest_MockAdapter();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testAdapterShouldThrowExceptionWhenRetrievingPluginLoaderOfInvalidType()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->getPluginLoader('bogus');
    }

    public function testAdapterShouldHavePluginLoaderForValidators()
    {
        $loader = $this->adapter->getPluginLoader('validate');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
    }

    public function testAdapterShouldAllowAddingCustomPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->adapter->setPluginLoader($loader, 'filter');
        $this->assertSame($loader, $this->adapter->getPluginLoader('filter'));
    }

    public function testAddingInvalidPluginLoaderTypeToAdapterShouldRaiseException()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $loader = new Zend_Loader_PluginLoader();
        $this->adapter->setPluginLoader($loader, 'bogus');
    }

    public function testAdapterShouldProxyAddingPluginLoaderPrefixPath()
    {
        $loader = $this->adapter->getPluginLoader('validate');
        $this->adapter->addPrefixPath('Foo_Valid', 'Foo/Valid/', 'validate');
        $paths = $loader->getPaths('Foo_Valid');
        $this->assertTrue(is_array($paths));
    }

    public function testPassingNoTypeWhenAddingPrefixPathToAdapterShouldGeneratePathsForAllTypes()
    {
        $this->adapter->addPrefixPath('Foo', 'Foo');
        $validateLoader = $this->adapter->getPluginLoader('validate');
        $filterLoader = $this->adapter->getPluginLoader('filter');
        $paths = $validateLoader->getPaths('Foo_Validate');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Foo_Filter');
        $this->assertTrue(is_array($paths));
    }

    public function testPassingInvalidTypeWhenAddingPrefixPathToAdapterShouldThrowException()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->addPrefixPath('Foo', 'Foo', 'bogus');
    }

    public function testAdapterShouldProxyAddingMultiplePluginLoaderPrefixPaths()
    {
        $validatorLoader = $this->adapter->getPluginLoader('validate');
        $filterLoader = $this->adapter->getPluginLoader('filter');
        $this->adapter->addPrefixPaths([
            'validate' => ['prefix' => 'Foo_Valid', 'path' => 'Foo/Valid/'],
            'filter' => [
                'Foo_Filter' => 'Foo/Filter/',
                'Baz_Filter' => [
                    'Baz/Filter/',
                    'My/Baz/Filter/',
                ],
            ],
            ['type' => 'filter', 'prefix' => 'Bar_Filter', 'path' => 'Bar/Filter/'],
        ]);
        $paths = $validatorLoader->getPaths('Foo_Valid');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Foo_Filter');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Bar_Filter');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Baz_Filter');
        $this->assertTrue(is_array($paths));
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
    }

    public function testValidatorPluginLoaderShouldRegisterPathsForBaseAndFileValidatorsByDefault()
    {
        $loader = $this->adapter->getPluginLoader('validate');
        $paths = $loader->getPaths(\Zend_Validate::class);
        $this->assertTrue(is_array($paths));
        $paths = $loader->getPaths('Zend_Validate_File');
        $this->assertTrue(is_array($paths));
    }

    public function testAdapterShouldAllowAddingValidatorInstance()
    {
        $validator = new Zend_Validate_File_Count(['min' => 1, 'max' => 1]);
        $this->adapter->addValidator($validator);
        $test = $this->adapter->getValidator(\Zend_Validate_File_Count::class);
        $this->assertSame($validator, $test);
    }

    public function testAdapterShouldAllowAddingValidatorViaPluginLoader()
    {
        $this->adapter->addValidator('Count', false, ['min' => 1, 'max' => 1]);
        $test = $this->adapter->getValidator('Count');
        $this->assertTrue($test instanceof Zend_Validate_File_Count);
    }

    public function testAdapterhShouldRaiseExceptionWhenAddingInvalidValidatorType()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->addValidator(new Zend_Filter_BaseName());
    }

    public function testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader()
    {
        $validators = [
            'count' => ['min' => 1, 'max' => 1],
            'Exists' => 'C:\temp',
            ['validator' => 'Upload', 'options' => [realpath(__FILE__)]],
            new Zend_Validate_File_Extension('jpg'),
        ];
        $this->adapter->addValidators($validators);
        $test = $this->adapter->getValidators();
        $this->assertTrue(is_array($test));
        $this->assertEquals(4, count((array) $test), var_export($test, 1));
        $count = array_shift($test);
        $this->assertTrue($count instanceof Zend_Validate_File_Count);
        $exists = array_shift($test);
        $this->assertTrue($exists instanceof Zend_Validate_File_Exists);
        $size = array_shift($test);
        $this->assertTrue($size instanceof Zend_Validate_File_Upload);
        $ext = array_shift($test);
        $this->assertTrue($ext instanceof Zend_Validate_File_Extension);
        $orig = array_pop($validators);
        $this->assertSame($orig, $ext);
    }

    public function testGetValidatorShouldReturnNullWhenNoMatchingIdentifierExists()
    {
        $this->assertNull($this->adapter->getValidator('Alpha'));
    }

    public function testAdapterShouldAllowPullingValidatorsByFile()
    {
        $this->adapter->addValidator('Alpha', false, false, 'foo');
        $validators = $this->adapter->getValidators('foo');
        $this->assertEquals(1, count((array) $validators));
        $validator = array_shift($validators);
        $this->assertTrue($validator instanceof Zend_Validate_Alpha);
    }

    public function testCallingSetValidatorsOnAdapterShouldOverwriteExistingValidators()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = [
            new Zend_Validate_File_Count(1),
            new Zend_Validate_File_Extension('jpg'),
        ];
        $this->adapter->setValidators($validators);
        $test = $this->adapter->getValidators();
        $this->assertSame($validators, array_values($test));
    }

    public function testAdapterShouldAllowRetrievingValidatorInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $ext = $this->adapter->getValidator(\Zend_Validate_File_Extension::class);
        $this->assertTrue($ext instanceof Zend_Validate_File_Extension);
    }

    public function testAdapterShouldAllowRetrievingValidatorInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $count = $this->adapter->getValidator('Count');
        $this->assertTrue($count instanceof Zend_Validate_File_Count);
    }

    public function testAdapterShouldAllowRetrievingAllValidatorsAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = $this->adapter->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(4, count((array) $validators));
        foreach ($validators as $validator) {
            $this->assertTrue($validator instanceof Zend_Validate_Interface);
        }
    }

    public function testAdapterShouldAllowRemovingValidatorInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasValidator(\Zend_Validate_File_Extension::class));
        $this->adapter->removeValidator(\Zend_Validate_File_Extension::class);
        $this->assertFalse($this->adapter->hasValidator(\Zend_Validate_File_Extension::class));
    }

    public function testAdapterShouldAllowRemovingValidatorInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasValidator('Count'));
        $this->adapter->removeValidator('Count');
        $this->assertFalse($this->adapter->hasValidator('Count'));
    }

    public function testRemovingNonexistentValidatorShouldDoNothing()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = $this->adapter->getValidators();
        $this->assertFalse($this->adapter->hasValidator('Alpha'));
        $this->adapter->removeValidator('Alpha');
        $this->assertFalse($this->adapter->hasValidator('Alpha'));
        $test = $this->adapter->getValidators();
        $this->assertSame($validators, $test);
    }

    public function testAdapterShouldAllowRemovingAllValidatorsAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->adapter->clearValidators();
        $validators = $this->adapter->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(0, count((array) $validators));
    }

    public function testValidationShouldReturnTrueForValidTransfer()
    {
        $this->adapter->addValidator('Count', false, [1, 3], 'foo');
        $this->assertTrue($this->adapter->isValid('foo'));
    }

    public function testValidationShouldReturnTrueForValidTransferOfMultipleFiles()
    {
        $this->assertTrue($this->adapter->isValid(null));
    }

    public function testValidationShouldReturnFalseForInvalidTransfer()
    {
        $this->adapter->addValidator('Extension', false, 'png', 'foo');
        $this->assertFalse($this->adapter->isValid('foo'));
    }

    public function testValidationShouldThrowExceptionForNonexistentFile()
    {
        $this->assertFalse($this->adapter->isValid('bogus'));
    }

    public function testErrorMessagesShouldBeEmptyByDefault()
    {
        $messages = $this->adapter->getMessages();
        $this->assertTrue(is_array($messages));
        $this->assertEquals(0, count($messages));
    }

    public function testErrorMessagesShouldBePopulatedAfterInvalidTransfer()
    {
        $this->testValidationShouldReturnFalseForInvalidTransfer();
        $messages = $this->adapter->getMessages();
        $this->assertTrue(is_array($messages));
        $this->assertFalse(empty($messages));
    }

    public function testErrorCodesShouldBeNullByDefault()
    {
        $errors = $this->adapter->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertEquals(0, count($errors));
    }

    public function testErrorCodesShouldBePopulatedAfterInvalidTransfer()
    {
        $this->testValidationShouldReturnFalseForInvalidTransfer();
        $errors = $this->adapter->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertFalse(empty($errors));
    }

    public function testAdapterShouldHavePluginLoaderForFilters()
    {
        $loader = $this->adapter->getPluginLoader('filter');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
    }

    public function testFilterPluginLoaderShouldRegisterPathsForBaseAndFileFiltersByDefault()
    {
        $loader = $this->adapter->getPluginLoader('filter');
        $paths = $loader->getPaths(\Zend_Filter::class);
        $this->assertTrue(is_array($paths));
        $paths = $loader->getPaths('Zend_Filter_File');
        $this->assertTrue(is_array($paths));
    }

    public function testAdapterShouldAllowAddingFilterInstance()
    {
        $filter = new Zend_Filter_StringToLower();
        $this->adapter->addFilter($filter);
        $test = $this->adapter->getFilter(\Zend_Filter_StringToLower::class);
        $this->assertSame($filter, $test);
    }

    public function testAdapterShouldAllowAddingFilterViaPluginLoader()
    {
        $this->adapter->addFilter('StringTrim');
        $test = $this->adapter->getFilter('StringTrim');
        $this->assertTrue($test instanceof Zend_Filter_StringTrim);
    }

    public function testAdapterhShouldRaiseExceptionWhenAddingInvalidFilterType()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->addFilter(new Zend_Validate_File_Extension('jpg'));
    }

    public function testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader()
    {
        $filters = [
            'Word_SeparatorToCamelCase' => ['separator' => ' '],
            ['filter' => 'Alpha', 'options' => [true]],
            new Zend_Filter_BaseName(),
        ];
        $this->adapter->addFilters($filters);
        $test = $this->adapter->getFilters();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test), var_export($test, 1));
        $count = array_shift($test);
        $this->assertTrue($count instanceof Zend_Filter_Word_SeparatorToCamelCase);
        $size = array_shift($test);
        $this->assertTrue($size instanceof Zend_Filter_Alpha);
        $ext = array_shift($test);
        $orig = array_pop($filters);
        $this->assertSame($orig, $ext);
    }

    public function testGetFilterShouldReturnNullWhenNoMatchingIdentifierExists()
    {
        $this->assertNull($this->adapter->getFilter('Alpha'));
    }

    public function testAdapterShouldAllowPullingFiltersByFile()
    {
        $this->adapter->addFilter('Alpha', false, 'foo');
        $filters = $this->adapter->getFilters('foo');
        $this->assertEquals(1, count($filters));
        $filter = array_shift($filters);
        $this->assertTrue($filter instanceof Zend_Filter_Alpha);
    }

    public function testCallingSetFiltersOnAdapterShouldOverwriteExistingFilters()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = [
            new Zend_Filter_StringToUpper(),
            new Zend_Filter_Alpha(),
        ];
        $this->adapter->setFilters($filters);
        $test = $this->adapter->getFilters();
        $this->assertSame($filters, array_values($test));
    }

    public function testAdapterShouldAllowRetrievingFilterInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $ext = $this->adapter->getFilter(\Zend_Filter_BaseName::class);
        $this->assertTrue($ext instanceof Zend_Filter_BaseName);
    }

    public function testAdapterShouldAllowRetrievingFilterInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $count = $this->adapter->getFilter('Alpha');
        $this->assertTrue($count instanceof Zend_Filter_Alpha);
    }

    public function testAdapterShouldAllowRetrievingAllFiltersAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = $this->adapter->getFilters();
        $this->assertTrue(is_array($filters));
        $this->assertEquals(3, count($filters));
        foreach ($filters as $filter) {
            $this->assertTrue($filter instanceof Zend_Filter_Interface);
        }
    }

    public function testAdapterShouldAllowRemovingFilterInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasFilter(\Zend_Filter_BaseName::class));
        $this->adapter->removeFilter(\Zend_Filter_BaseName::class);
        $this->assertFalse($this->adapter->hasFilter(\Zend_Filter_BaseName::class));
    }

    public function testAdapterShouldAllowRemovingFilterInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasFilter('Alpha'));
        $this->adapter->removeFilter('Alpha');
        $this->assertFalse($this->adapter->hasFilter('Alpha'));
    }

    public function testRemovingNonexistentFilterShouldDoNothing()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = $this->adapter->getFilters();
        $this->assertFalse($this->adapter->hasFilter('Int'));
        $this->adapter->removeFilter('Int');
        $this->assertFalse($this->adapter->hasFilter('Int'));
        $test = $this->adapter->getFilters();
        $this->assertSame($filters, $test);
    }

    public function testAdapterShouldAllowRemovingAllFiltersAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->adapter->clearFilters();
        $filters = $this->adapter->getFilters();
        $this->assertTrue(is_array($filters));
        $this->assertEquals(0, count($filters));
    }

    public function testTransferDestinationShouldBeMutable()
    {
        $directory = __DIR__;
        $this->adapter->setDestination($directory);
        $destinations = $this->adapter->getDestination();
        $this->assertTrue(is_array($destinations));
        foreach ($destinations as $file => $destination) {
            $this->assertEquals($directory, $destination);
        }

        $newdirectory = __DIR__
                      . DIRECTORY_SEPARATOR . '..'
                      . DIRECTORY_SEPARATOR . '..'
                      . DIRECTORY_SEPARATOR . '..'
                      . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($newdirectory, 'foo');
        $this->assertEquals($newdirectory, $this->adapter->getDestination('foo'));
        $this->assertEquals($directory, $this->adapter->getDestination('bar'));
    }

    public function testAdapterShouldAllowRetrievingDestinationsForAnArrayOfSpecifiedFiles()
    {
        $this->adapter->setDestination(__DIR__);
        $destinations = $this->adapter->getDestination(['bar', 'baz']);
        $this->assertTrue(is_array($destinations));
        $directory = __DIR__;
        foreach ($destinations as $file => $destination) {
            $this->assertTrue(in_array($file, ['bar', 'baz']));
            $this->assertEquals($directory, $destination);
        }
    }

    public function testSettingAndRetrievingOptions()
    {
        $this->assertEquals(
            [
                'bar' => ['ignoreNoFile' => false, 'useByteString' => true],
                'baz' => ['ignoreNoFile' => false, 'useByteString' => true],
                'foo' => ['ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true],
                'file_0_' => ['ignoreNoFile' => false, 'useByteString' => true],
                'file_1_' => ['ignoreNoFile' => false, 'useByteString' => true],
            ], $this->adapter->getOptions());

        $this->adapter->setOptions(['ignoreNoFile' => true]);
        $this->assertEquals(
            [
                'bar' => ['ignoreNoFile' => true, 'useByteString' => true],
                'baz' => ['ignoreNoFile' => true, 'useByteString' => true],
                'foo' => ['ignoreNoFile' => true, 'useByteString' => true, 'detectInfos' => true],
                'file_0_' => ['ignoreNoFile' => true, 'useByteString' => true],
                'file_1_' => ['ignoreNoFile' => true, 'useByteString' => true],
            ], $this->adapter->getOptions());

        $this->adapter->setOptions(['ignoreNoFile' => false], 'foo');
        $this->assertEquals(
            [
                'bar' => ['ignoreNoFile' => true, 'useByteString' => true],
                'baz' => ['ignoreNoFile' => true, 'useByteString' => true],
                'foo' => ['ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true],
                'file_0_' => ['ignoreNoFile' => true, 'useByteString' => true],
                'file_1_' => ['ignoreNoFile' => true, 'useByteString' => true],
            ], $this->adapter->getOptions());
    }

    public function testGetAllAdditionalFileInfos()
    {
        $files = $this->adapter->getFileInfo();
        $this->assertEquals(5, count($files));
        $this->assertEquals('baz.text', $files['baz']['name']);
    }

    public function testGetAdditionalFileInfosForSingleFile()
    {
        $files = $this->adapter->getFileInfo('baz');
        $this->assertEquals(1, count($files));
        $this->assertEquals('baz.text', $files['baz']['name']);
    }

    public function testGetAdditionalFileInfosForUnknownFile()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $files = $this->adapter->getFileInfo('unknown');
    }

    public function testGetUnknownOption()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->setOptions(['unknownOption' => 'unknown']);
    }

    public function testGetFileIsNotImplemented()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->getFile();
    }

    public function testAddFileIsNotImplemented()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->addFile('foo');
    }

    public function testGetTypeIsNotImplemented()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->getType();
    }

    public function testAddTypeIsNotImplemented()
    {
        $this->expectException(\Zend_File_Transfer_Exception::class);
        $this->adapter->addType('foo');
    }

    public function testAdapterShouldAllowRetrievingFileName()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'foo.jpg', $this->adapter->getFileName('foo'));
    }

    public function testAdapterShouldAllowRetrievingFileNameWithoutPath()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $this->assertEquals('foo.jpg', $this->adapter->getFileName('foo', false));
    }

    public function testAdapterShouldAllowRetrievingAllFileNames()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $files = $this->adapter->getFileName();
        $this->assertTrue(is_array($files));
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'bar.png', $files['bar']);
    }

    public function testAdapterShouldAllowRetrievingAllFileNamesWithoutPath()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $files = $this->adapter->getFileName(null, false);
        $this->assertTrue(is_array($files));
        $this->assertEquals('bar.png', $files['bar']);
    }

    public function testExceptionForUnknownHashValue()
    {
        try {
            $this->adapter->getHash('foo', 'unknown_hash');
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Unknown hash algorithm', $e->getMessage());
        }
    }

    public function testIgnoreHashValue()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getHash('crc32', 'test');
        $this->assertEquals([], $return);
    }

    public function testEmptyTempDirectoryDetection()
    {
        $this->adapter->_tmpDir = '';
        $this->assertTrue(empty($this->adapter->_tmpDir), 'Empty temporary directory');
    }

    public function testTempDirectoryDetection()
    {
        $this->adapter->getTmpDir();
        $this->assertTrue(!empty($this->adapter->_tmpDir), 'Temporary directory filled');
    }

    public function testTemporaryDirectoryAccessDetection()
    {
        $this->adapter->_tmpDir = '.';
        $path = '/NoPath/To/File';
        $this->assertFalse($this->adapter->isPathWriteable($path));
        $this->assertTrue($this->adapter->isPathWriteable($this->adapter->_tmpDir));
    }

    public function testFileSizeButNoFileFound()
    {
        try {
            $this->assertEquals(10, $this->adapter->getFileSize());
            $this->fail();
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertStringContainsString('does not exist', $e->getMessage());
        }
    }

    public function testIgnoreFileSize()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getFileSize('test');
        $this->assertEquals([], $return);
    }

    public function testFileSizeByTmpName()
    {
        $options = $this->adapter->getOptions();
        $this->assertTrue($options['baz']['useByteString']);
        $this->assertEquals('1.14kB', $this->adapter->getFileSize('baz.text'));
        $this->adapter->setOptions(['useByteString' => false]);
        $options = $this->adapter->getOptions();
        $this->assertFalse($options['baz']['useByteString']);
        $this->assertEquals(1172, $this->adapter->getFileSize('baz.text'));
    }

    public function testMimeTypeButNoFileFound()
    {
        try {
            $this->assertEquals('image/jpeg', $this->adapter->getMimeType());
            $this->fail();
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertStringContainsString('does not exist', $e->getMessage());
        }
    }

    public function testIgnoreMimeType()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getMimeType('test');
        $this->assertEquals([], $return);
    }

    public function testMimeTypeByTmpName()
    {
        $this->assertEquals('text/plain', $this->adapter->getMimeType('baz.text'));
    }

    public function testSetOwnErrorMessage()
    {
        $this->adapter->addValidator('Count', false, ['min' => 5, 'max' => 5, 'messages' => [Zend_Validate_File_Count::TOO_FEW => 'Zu wenige']]);
        $this->assertFalse($this->adapter->isValid('foo'));
        $message = $this->adapter->getMessages();
        $this->assertContains('Zu wenige', $message);

        try {
            $this->assertEquals('image/jpeg', $this->adapter->getMimeType());
            $this->fail();
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertStringContainsString('does not exist', $e->getMessage());
        }
    }

    public function testTransferDestinationAtNonExistingElement()
    {
        $directory = __DIR__;
        $this->adapter->setDestination($directory, 'nonexisting');
        $this->assertEquals($directory, $this->adapter->getDestination('nonexisting'));

        try {
            $this->assertTrue(is_string($this->adapter->getDestination('reallynonexisting')));
            $this->fail();
        } catch (Exception $e) {
            $this->assertStringContainsString('not find', $e->getMessage());
        }
    }

    /**
     * @ZF-7376
     */
    public function testSettingMagicFile()
    {
        $this->adapter->setOptions(['magicFile' => 'test/file']);
        $this->assertEquals(
            [
                'bar' => ['magicFile' => 'test/file', 'ignoreNoFile' => false, 'useByteString' => true],
            ], $this->adapter->getOptions('bar'));
    }

    /**
     * @ZF-8693
     */
    public function testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoaderForDifferentFiles()
    {
        $validators = [
            ['MimeType', true, ['image/jpeg']], // no files
            ['FilesSize', true, ['max' => '1MB', 'messages' => 'файл больше 1MБ']], // no files
            ['Count', true, ['min' => 1, 'max' => '1', 'messages' => 'файл не 1'], 'bar'], // 'bar' from config
            ['MimeType', true, ['image/jpeg'], 'bar'], // 'bar' from config
        ];

        $this->adapter->addValidators($validators, 'foo'); // set validators to 'foo'

        $test = $this->adapter->getValidators();
        $this->assertEquals(3, count((array) $test));

        //test files specific validators
        $test = $this->adapter->getValidators('foo');
        $this->assertEquals(2, count((array) $test));
        $mimeType = array_shift($test);
        $this->assertTrue($mimeType instanceof Zend_Validate_File_MimeType);
        $filesSize = array_shift($test);
        $this->assertTrue($filesSize instanceof Zend_Validate_File_FilesSize);

        $test = $this->adapter->getValidators('bar');
        $this->assertEquals(2, count((array) $test));
        $filesSize = array_shift($test);
        $this->assertTrue($filesSize instanceof Zend_Validate_File_Count);
        $mimeType = array_shift($test);
        $this->assertTrue($mimeType instanceof Zend_Validate_File_MimeType);

        $test = $this->adapter->getValidators('baz');
        $this->assertEquals(0, count((array) $test));
    }

    /**
     * @ZF-9132
     */
    public function testSettingAndRetrievingDetectInfosOption()
    {
        $this->assertEquals([
            'foo' => [
                'ignoreNoFile' => false,
                'useByteString' => true,
                'detectInfos' => true, ], ]
            , $this->adapter->getOptions('foo'));
        $this->adapter->setOptions(['detectInfos' => false]);
        $this->assertEquals([
            'foo' => [
                'ignoreNoFile' => false,
                'useByteString' => true,
                'detectInfos' => false, ], ]
            , $this->adapter->getOptions('foo'));
    }

    /**
     * @group GH-65
     */
    public function testSetDestinationWithNonExistingPathShouldThrowException()
    {
        // Create temporary directory
        $directory = __DIR__ . '/_files/destination';
        if (!is_dir($directory)) {
            @mkdir($directory);
        }
        chmod($directory, 0o655);

        // Test
        try {
            $this->adapter->setDestination($directory);
            $this->fail('Destination is writable');
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertEquals(
                'The given destination is not writable',
                $e->getMessage()
            );
        }

        // Remove temporary directory
        @rmdir($directory);
    }
}

class Zend_File_Transfer_Adapter_AbstractTest_MockAdapter extends Zend_File_Transfer_Adapter_Abstract
{
    public $received = false;

    public $_tmpDir;

    public function __construct()
    {
        $testfile = __DIR__ . '/_files/test.txt';
        $this->_files = [
            'foo' => [
                'name' => 'foo.jpg',
                'type' => 'image/jpeg',
                'size' => 126976,
                'tmp_name' => '/tmp/489127ba5c89c',
                'options' => ['ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
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
            ],
            'file_0_' => [
                'name' => 'foo.jpg',
                'type' => 'image/jpeg',
                'size' => 126976,
                'tmp_name' => '/tmp/489127ba5c89c',
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
            ],
            'file_1_' => [
                'name' => 'baz.text',
                'type' => 'text/plain',
                'size' => 1172,
                'tmp_name' => $testfile,
                'options' => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
            ],
            'file' => [
                'name' => 'foo.jpg',
                'multifiles' => [0 => 'file_0_', 1 => 'file_1_'],
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

    public function getTmpDir()
    {
        $this->_tmpDir = parent::_getTmpDir();
    }

    public function isPathWriteable($path)
    {
        return parent::_isPathWriteable($path);
    }

    public function addInvalidFile()
    {
        $this->_files += [
            'test' => [
                'name' => 'test.txt',
                'type' => 'image/jpeg',
                'size' => 0,
                'tmp_name' => '',
                'options' => ['ignoreNoFile' => true, 'useByteString' => true],
                'validated' => false,
                'received' => false,
                'filtered' => false,
            ],
        ];
    }
}
