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
require_once 'Zend/Filter/Word/CamelCaseToDash.php';

/**
 * Test class for Zend_Filter_Word_CamelCaseToDash.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Filter')]
class Zend_Filter_Word_CamelCaseToDashTest extends PHPUnit\Framework\TestCase
{
    public function testFilterSeparatesCamelCasedWordsWithDashes()
    {
        $string = 'CamelCasedWords';
        $filter = new Zend_Filter_Word_CamelCaseToDash();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Camel-Cased-Words', $filtered);
    }
}
