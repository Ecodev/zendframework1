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
require_once __DIR__ . '/../../../TestHelper.php';

require_once 'Zend/Form/Element/Note.php';

/**
 * Test class for Zend_Form_Element_Text.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_NoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_NoteTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->element = new Zend_Form_Element_Note('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testNoteElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testNoteElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testNoteElementUsesNoteHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);

        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formNote', $helper);
    }

    public function testNoteElementValidationIsAlwaysTrue()
    {
        // Solo
        $this->assertTrue($this->element->isValid('foo'));

        // Set required
        $this->element->setRequired(true);
        $this->assertTrue($this->element->isValid(''));
        // Reset
        $this->element->setRequired(false);

        // Examining various validators
        $validators = [
            [
                'options' => ['Alnum'],
                'value' => 'aa11?? ',
            ],
            [
                'options' => ['Alpha'],
                'value' => 'aabb11',
            ],
            [
                'options' => [
                    'Between',
                    false,
                    [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'value' => '11',
            ],
            [
                'options' => ['Date'],
                'value' => '10.10.2000',
            ],
            [
                'options' => ['Digits'],
                'value' => '1122aa',
            ],
            [
                'options' => ['EmailAddress'],
                'value' => 'foo',
            ],
            [
                'options' => ['Float'],
                'value' => '10a01',
            ],
            [
                'options' => [
                    'GreaterThan',
                    false,
                    ['min' => 10],
                ],
                'value' => '9',
            ],
            [
                'options' => ['Hex'],
                'value' => '123ABCDEFGH',
            ],
            [
                'options' => [
                    'InArray',
                    false,
                    [
                        'key' => 'value',
                        'otherkey' => 'othervalue',
                    ],
                ],
                'value' => 'foo',
            ],
            [
                'options' => ['Int'],
                'value' => '1234.5',
            ],
            [
                'options' => [
                    'LessThan',
                    false,
                    ['max' => 10],
                ],
                'value' => '11',
            ],
            [
                'options' => ['NotEmpty'],
                'value' => '',
            ],
            [
                'options' => [
                    'Regex',
                    false,
                    ['pattern' => '/^Test/'],
                ],
                'value' => 'Pest',
            ],
            [
                'options' => [
                    'StringLength',
                    false,
                    [
                        6,
                        20,
                    ],
                ],
                'value' => 'foo',
            ],
        ];

        foreach ($validators as $validator) {
            // Add validator
            $this->element->addValidators([$validator['options']]);

            // Testing
            $this->assertTrue($this->element->isValid($validator['value']));

            // Remove validator
            $this->element->removeValidator($validator['options'][0]);
        }
    }

    /**
     * Used by test methods susceptible to ZF-2794, marks a test as incomplete.
     *
     * @see   http://framework.zend.com/issues/browse/ZF-2794
     */
    protected function _checkZf2794()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win'
            && version_compare(PHP_VERSION, '5.1.4', '=')
        ) {
            $this->markTestIncomplete('Error occurs for PHP 5.1.4 on Windows');
        }
    }
}
