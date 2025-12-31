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
require_once 'Zend/Filter/Word/UnderscoreToSeparator.php';

/**
 * Test class for Zend_Filter_Word_UnderscoreToSeparator.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Filter')]
class Zend_Filter_Word_UnderscoreToSeparatorTest extends PHPUnit\Framework\TestCase
{
    public function testFilterSeparatesCamelCasedWordsDefaultSeparator()
    {
        $string = 'underscore_separated_words';
        $filter = new Zend_Filter_Word_UnderscoreToSeparator();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('underscore separated words', $filtered);
    }

    public function testFilterSeparatesCamelCasedWordsProvidedSeparator()
    {
        $string = 'underscore_separated_words';
        $filter = new Zend_Filter_Word_UnderscoreToSeparator(':=:');
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('underscore:=:separated:=:words', $filtered);
    }
}
