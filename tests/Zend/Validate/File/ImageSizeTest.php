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
 * @see Zend_Validate_File_ImageSize
 */
require_once 'Zend/Validate/File/ImageSize.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_File_ImageSizeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_ImageSizeTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $file = [];
        $valuesExpected = [
            [['minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000], true],
            [['minwidth' => 0, 'minheight' => 0, 'maxwidth' => 200, 'maxheight' => 200], true],
            [['minwidth' => 150, 'minheight' => 150, 'maxwidth' => 200, 'maxheight' => 200], false],
            [['minwidth' => 80, 'minheight' => 0, 'maxwidth' => 80, 'maxheight' => 200], true],
            [['minwidth' => 0, 'minheight' => 0, 'maxwidth' => 60, 'maxheight' => 200], false],
            [['minwidth' => 90, 'minheight' => 0, 'maxwidth' => 200, 'maxheight' => 200], false],
            [['minwidth' => 0, 'minheight' => 0, 'maxwidth' => 200, 'maxheight' => 80], false],
            [['minwidth' => 0, 'minheight' => 110, 'maxwidth' => 200, 'maxheight' => 140], false],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ImageSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                'Tested with ' . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000]);
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg'));
        $failures = $validator->getMessages();
        $this->assertStringContainsString('is not readable', $failures['fileImageSizeNotReadable']);

        $file['name'] = 'TestName';
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000]);
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg', $file));
        $failures = $validator->getMessages();
        $this->assertStringContainsString('TestName', $failures['fileImageSizeNotReadable']);

        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000]);
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/badpicture.jpg'));
        $failures = $validator->getMessages();
        $this->assertStringContainsString('could not be detected', $failures['fileImageSizeNotDetected']);
    }

    /**
     * Ensures that getImageMin() returns expected value.
     */
    public function testGetImageMin()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 1, 'minheight' => 10, 'maxwidth' => 100, 'maxheight' => 1000]);
        $this->assertEquals(['minwidth' => 1, 'minheight' => 10], $validator->getImageMin());

        try {
            $validator = new Zend_Validate_File_ImageSize(['minwidth' => 1000, 'minheight' => 100, 'maxwidth' => 10, 'maxheight' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that setImageMin() returns expected value.
     */
    public function testSetImageMin()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 100, 'minheight' => 1000, 'maxwidth' => 10000, 'maxheight' => 100000]);
        $validator->setImageMin(['minwidth' => 10, 'minheight' => 10]);
        $this->assertEquals(['minwidth' => 10, 'minheight' => 10], $validator->getImageMin());

        $validator->setImageMin(['minwidth' => 9, 'minheight' => 100]);
        $this->assertEquals(['minwidth' => 9, 'minheight' => 100], $validator->getImageMin());

        try {
            $validator->setImageMin(['minwidth' => 20000, 'minheight' => 20000]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('less than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that getImageMax() returns expected value.
     */
    public function testGetImageMax()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 10, 'minheight' => 100, 'maxwidth' => 1000, 'maxheight' => 10000]);
        $this->assertEquals(['maxwidth' => 1000, 'maxheight' => 10000], $validator->getImageMax());

        try {
            $validator = new Zend_Validate_File_ImageSize(['minwidth' => 10000, 'minheight' => 1000, 'maxwidth' => 100, 'maxheight' => 10]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that setImageMax() returns expected value.
     */
    public function testSetImageMax()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 10, 'minheight' => 100, 'maxwidth' => 1000, 'maxheight' => 10000]);
        $validator->setImageMax(['maxwidth' => 100, 'maxheight' => 100]);
        $this->assertEquals(['maxwidth' => 100, 'maxheight' => 100], $validator->getImageMax());

        $validator->setImageMax(['maxwidth' => 110, 'maxheight' => 1000]);
        $this->assertEquals(['maxwidth' => 110, 'maxheight' => 1000], $validator->getImageMax());

        $validator->setImageMax(['maxheight' => 1100]);
        $this->assertEquals(['maxwidth' => 110, 'maxheight' => 1100], $validator->getImageMax());

        $validator->setImageMax(['maxwidth' => 120]);
        $this->assertEquals(['maxwidth' => 120, 'maxheight' => 1100], $validator->getImageMax());

        try {
            $validator->setImageMax(['maxwidth' => 10000, 'maxheight' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that getImageWidth() returns expected value.
     */
    public function testGetImageWidth()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 1, 'minheight' => 10, 'maxwidth' => 100, 'maxheight' => 1000]);
        $this->assertEquals(['minwidth' => 1, 'maxwidth' => 100], $validator->getImageWidth());
    }

    /**
     * Ensures that setImageWidth() returns expected value.
     */
    public function testSetImageWidth()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 100, 'minheight' => 1000, 'maxwidth' => 10000, 'maxheight' => 100000]);
        $validator->setImageWidth(['minwidth' => 2000, 'maxwidth' => 2200]);
        $this->assertEquals(['minwidth' => 2000, 'maxwidth' => 2200], $validator->getImageWidth());

        try {
            $validator->setImageWidth(['minwidth' => 20000, 'maxwidth' => 200]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('less than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that getImageHeight() returns expected value.
     */
    public function testGetImageHeight()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 1, 'minheight' => 10, 'maxwidth' => 100, 'maxheight' => 1000]);
        $this->assertEquals(['minheight' => 10, 'maxheight' => 1000], $validator->getImageHeight());
    }

    /**
     * Ensures that setImageHeight() returns expected value.
     */
    public function testSetImageHeight()
    {
        $validator = new Zend_Validate_File_ImageSize(['minwidth' => 100, 'minheight' => 1000, 'maxwidth' => 10000, 'maxheight' => 100000]);
        $validator->setImageHeight(['minheight' => 2000, 'maxheight' => 2200]);
        $this->assertEquals(['minheight' => 2000, 'maxheight' => 2200], $validator->getImageHeight());

        try {
            $validator->setImageHeight(['minheight' => 20000, 'maxheight' => 200]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('less than or equal', $e->getMessage());
        }
    }
}
