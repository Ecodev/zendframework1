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
 * Zend_Date.
 */
require_once 'Zend/Date.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Date
 */
#[AllowDynamicProperties]
class Zend_Date_DateObjectTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->originalTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Paris');
        $this->_cache = Zend_Cache::factory('Core', 'File',
            ['lifetime' => 120, 'automatic_serialization' => true],
            ['cache_dir' => __DIR__ . '/../_files/']);
        Zend_Date_DateObjectTestHelper::setOptions(['cache' => $this->_cache]);
    }

    public function tearDown(): void
    {
        date_default_timezone_set($this->originalTimezone);
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }

    /**
     * Test for date object creation null value.
     */
    public function testCreationNull()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');

            return;
        }

        $date = new Zend_Date(0);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for date object creation negative timestamp.
     */
    public function testCreationNegative()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');

            return;
        }

        $date = new Zend_Date(1000);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for date object creation text given.
     */
    public function testCreationFailed()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');

            return;
        }

        try {
            $date = new Zend_Date('notimestamp');
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            self::assertTrue(true);
        }
    }

    /**
     * Test for setUnixTimestamp.
     */
    public function testsetUnixTimestamp()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $diff = abs(time() - $date->getUnixTimestamp());
        $this->assertTrue(($diff < 2), 'Zend_Date->setUnixTimestamp() returned a significantly '
            . "different timestamp than expected: $diff seconds");
        $date->setUnixTimestamp(0);
        $this->assertSame('0', (string) $date->setUnixTimestamp('12345678901234567890'));
        $this->assertSame('12345678901234567890', (string) $date->setUnixTimestamp('12345678901234567890'));

        $date->setUnixTimestamp();
        $diff = abs(time() - $date->getUnixTimestamp());
        $this->assertTrue($diff < 2, "setUnixTimestamp has a significantly different time than returned by time()): $diff seconds");
    }

    /**
     * Test for setUnixTimestampFailed.
     */
    public function testsetUnixTimestampFailed()
    {
        try {
            $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
            $date->setUnixTimestamp('notimestamp');
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            self::assertTrue(true);
        }
    }

    /**
     * Test for getUnixTimestamp.
     */
    public function testgetUnixTimestamp()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $result = $date->getUnixTimestamp();
        $diff = abs($result - time());
        $this->assertTrue($diff < 2, "Instance of Zend_Date_DateObject has a significantly different time than returned by setTime(): $diff seconds");
    }

    /**
     * Test for mktime.
     */
    public function testMkTimeforDateValuesInPHPRange()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame(mktime(0, 0, 0, 12, 30, 2037), $date->mktime(0, 0, 0, 12, 30, 2037, false));
        $this->assertSame(gmmktime(0, 0, 0, 12, 30, 2037), $date->mktime(0, 0, 0, 12, 30, 2037, true));

        $this->assertSame(mktime(0, 0, 0,  1,  1, 2000), $date->mktime(0, 0, 0,  1,  1, 2000, false));
        $this->assertSame(gmmktime(0, 0, 0,  1,  1, 2000), $date->mktime(0, 0, 0,  1,  1, 2000, true));

        $this->assertSame(mktime(0, 0, 0,  1,  1, 1970), $date->mktime(0, 0, 0,  1,  1, 1970, false));
        $this->assertSame(gmmktime(0, 0, 0,  1,  1, 1970), $date->mktime(0, 0, 0,  1,  1, 1970, true));

        $this->assertSame(mktime(0, 0, 0, 12, 30, 1902), $date->mktime(0, 0, 0, 12, 30, 1902, false));
        $this->assertSame(gmmktime(0, 0, 0, 12, 30, 1902), $date->mktime(0, 0, 0, 12, 30, 1902, true));
    }

    /**
     * Test for mktime.
     */
    public function testMkTimeforDateValuesGreaterPHPRange()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame(2_232_658_800,  $date->mktime(0, 0, 0,10, 1, 2040, false));
        $this->assertSame(2_232_662_400,  $date->mktime(0, 0, 0,10, 1, 2040, true));
        $this->assertSame(7_258_114_800,  $date->mktime(0, 0, 0, 1, 1, 2200, false));
        $this->assertSame(7_258_118_400,  $date->mktime(0, 0, 0, 1, 1, 2200, true));
        $this->assertSame(16_749_586_800, $date->mktime(0, 0, 0,10,10, 2500, false));
        $this->assertSame(16_749_590_400, $date->mktime(0, 0, 0,10,10, 2500, true));
        $this->assertSame(32_503_676_400, $date->mktime(0, 0, 0, 1, 1, 3000, false));
        $this->assertSame(32_503_680_000, $date->mktime(0, 0, 0, 1, 1, 3000, true));
        $this->assertSame(95_617_580_400, $date->mktime(0, 0, 0, 1, 1, 5000, false));
        $this->assertSame(95_617_584_000, $date->mktime(0, 0, 0, 1, 1, 5000, true));

        // test for different set external timezone
        // the internal timezone should always be used for calculation
        $date->setTimezone('Europe/Paris');
        $this->assertSame(1_577_833_200, $date->mktime(0, 0, 0, 1, 1, 2020, false));
        $this->assertSame(1_577_836_800, $date->mktime(0, 0, 0, 1, 1, 2020, true));
        date_default_timezone_set('Indian/Maldives');
        $this->assertSame(1_577_833_200, $date->mktime(0, 0, 0, 1, 1, 2020, false));
        $this->assertSame(1_577_836_800, $date->mktime(0, 0, 0, 1, 1, 2020, true));
    }

    /**
     * Test for mktime.
     */
    public function testMkTimeforDateValuesSmallerPHPRange()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame(-2_208_992_400,   $date->mktime(0, 0, 0, 1, 1, 1900, false));
        $this->assertSame(-2_208_988_800,   $date->mktime(0, 0, 0, 1, 1, 1900, true));
        $this->assertSame(-8_520_339_600,   $date->mktime(0, 0, 0, 1, 1, 1700, false));
        $this->assertSame(-8_520_336_000,   $date->mktime(0, 0, 0, 1, 1, 1700, true));
        $this->assertSame(-14_830_995_600,  $date->mktime(0, 0, 0, 1, 1, 1500, false));
        $this->assertSame(-14_830_992_000,  $date->mktime(0, 0, 0, 1, 1, 1500, true));
        $this->assertSame(-12_219_321_600,  $date->mktime(0, 0, 0,10,10, 1582, false));
        $this->assertSame(-12_219_321_600,  $date->mktime(0, 0, 0,10,10, 1582, true));
        $this->assertSame(-30_609_795_600,  $date->mktime(0, 0, 0, 1, 1, 1000, false));
        $this->assertSame(-30_609_792_000,  $date->mktime(0, 0, 0, 1, 1, 1000, true));
        $this->assertSame(-62_167_395_600,  $date->mktime(0, 0, 0, 1, 1,    0, false));
        $this->assertSame(-62_167_392_000,  $date->mktime(0, 0, 0, 1, 1,    0, true));
        $this->assertSame(-125_282_595_600, $date->mktime(0, 0, 0, 1, 1,-2000, false));
        $this->assertSame(-125_282_592_000, $date->mktime(0, 0, 0, 1, 1,-2000, true));

        $this->assertSame(-2_208_992_400, $date->mktime(0, 0, 0, 13, 1, 1899, false));
        $this->assertSame(-2_208_988_800, $date->mktime(0, 0, 0, 13, 1, 1899, true));
        $this->assertSame(-2_208_992_400, $date->mktime(0, 0, 0,-11, 1, 1901, false));
        $this->assertSame(-2_208_988_800, $date->mktime(0, 0, 0,-11, 1, 1901, true));
    }

    public function testIsLeapYear()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertTrue($date->checkLeapYear(2000));
        $this->assertFalse($date->checkLeapYear(2002));
        $this->assertTrue($date->checkLeapYear(2004));
        $this->assertFalse($date->checkLeapYear(1899));
        $this->assertTrue($date->checkLeapYear(1500));
        $this->assertFalse($date->checkLeapYear(1455));
    }

    public function testWeekNumber()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame((int) date('W',mktime(0, 0, 0,  1,  1, 2000)), $date->weekNumber(2000,  1,  1));
        $this->assertSame((int) date('W',mktime(0, 0, 0, 10,  1, 2020)), $date->weekNumber(2020, 10,  1));
        $this->assertSame((int) date('W',mktime(0, 0, 0,  5, 15, 2005)), $date->weekNumber(2005,  5, 15));
        $this->assertSame((int) date('W',mktime(0, 0, 0, 11, 22, 1994)), $date->weekNumber(1994, 11, 22));
        $this->assertSame((int) date('W',mktime(0, 0, 0, 12, 31, 2000)), $date->weekNumber(2000, 12, 31));
        $this->assertSame(52, $date->weekNumber(2050, 12, 31));
        $this->assertSame(23, $date->weekNumber(2050,  6,  6));
        $this->assertSame(52, $date->weekNumber(2056,  1,  1));
        $this->assertSame(52, $date->weekNumber(2049, 12, 31));
        $this->assertSame(53, $date->weekNumber(2048, 12, 31));
        $this->assertSame(1, $date->weekNumber(2047, 12, 31));
    }

    public function testDayOfWeek()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 1, 2000)), $date->dayOfWeekHelper(2000, 1, 1));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 2, 2000)), $date->dayOfWeekHelper(2000, 1, 2));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 3, 2000)), $date->dayOfWeekHelper(2000, 1, 3));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 4, 2000)), $date->dayOfWeekHelper(2000, 1, 4));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 5, 2000)), $date->dayOfWeekHelper(2000, 1, 5));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 6, 2000)), $date->dayOfWeekHelper(2000, 1, 6));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 7, 2000)), $date->dayOfWeekHelper(2000, 1, 7));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 8, 2000)), $date->dayOfWeekHelper(2000, 1, 8));
        $this->assertSame(6, $date->dayOfWeekHelper(2050, 1, 1));
        $this->assertSame(0, $date->dayOfWeekHelper(2050, 1, 2));
        $this->assertSame(1, $date->dayOfWeekHelper(2050, 1, 3));
        $this->assertSame(2, $date->dayOfWeekHelper(2050, 1, 4));
        $this->assertSame(3, $date->dayOfWeekHelper(2050, 1, 5));
        $this->assertSame(4, $date->dayOfWeekHelper(2050, 1, 6));
        $this->assertSame(5, $date->dayOfWeekHelper(2050, 1, 7));
        $this->assertSame(6, $date->dayOfWeekHelper(2050, 1, 8));
        $this->assertSame(4, $date->dayOfWeekHelper(1500, 1, 1));
    }

    public function testCalcSunInternal()
    {
        $date = new Zend_Date_DateObjectTestHelper(10_000_000);
        $this->assertSame(9961716, $date->calcSun(['latitude' => 38.4, 'longitude' => -29], -0.0145439, true));
        $this->assertSame(10010341, $date->calcSun(['latitude' => 38.4, 'longitude' => -29], -0.0145439, false));

        $date = new Zend_Date_DateObjectTestHelper(-148_309_884);
        $this->assertSame(-148322626, $date->calcSun(['latitude' => 38.4, 'longitude' => -29], -0.0145439, true));
        $this->assertSame(-148274784, $date->calcSun(['latitude' => 38.4, 'longitude' => -29], -0.0145439, false));
    }

    public function testGetDate()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);
        $this->assertTrue(is_array($date->getDateParts()));
        $this->assertTrue(is_array($date->getDateParts(1_000_000)));

        $test = ['seconds' => 40,      'minutes' => 46,
            'hours' => 14,       'mday' => 12,      'wday' => 1,
            'mon' => 1,       'year' => 1970,      'yday' => 11,
            'weekday' => 'Monday', 'month' => 'January', 0 => 1_000_000, ];
        $result = $date->getDateParts(1_000_000);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']);
        $this->assertSame((int) $test['mday'],    (int) $result['mday']);
        $this->assertSame((int) $test['wday'],    (int) $result['wday']);
        $this->assertSame((int) $test['mon'],     (int) $result['mon']);
        $this->assertSame((int) $test['year'],    (int) $result['year']);
        $this->assertSame((int) $test['yday'],    (int) $result['yday']);
        $this->assertSame($test['weekday'],       $result['weekday']);
        $this->assertSame($test['month'],         $result['month']);
        $this->assertSame($test[0],               $result[0]);

        $test = ['seconds' => 20,      'minutes' => 33,
            'hours' => 11,          'mday' => 6,      'wday' => 3,
            'mon' => 3,          'year' => 1748,      'yday' => 65,
            'weekday' => 'Wednesday', 'month' => 'February', 0 => -7_000_000_000, ];
        $result = $date->getDateParts(-7_000_000_000);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']);
        $this->assertSame((int) $test['mday'],    (int) $result['mday']);
        $this->assertSame((int) $test['wday'],    (int) $result['wday']);
        $this->assertSame((int) $test['mon'],     (int) $result['mon']);
        $this->assertSame((int) $test['year'],    (int) $result['year']);
        $this->assertSame((int) $test['yday'],    (int) $result['yday']);
        $this->assertSame($test['weekday'],       $result['weekday']);
        $this->assertSame($test['month'],         $result['month']);
        $this->assertSame($test[0],               $result[0]);

        $test = ['seconds' => 0,        'minutes' => 40,
            'hours' => 2,          'mday' => 26,       'wday' => 2,
            'mon' => 8,          'year' => 2188,     'yday' => 238,
            'weekday' => 'Tuesday', 'month' => 'July', 0 => 6_900_000_000, ];
        $result = $date->getDateParts(6_900_000_000);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']);
        $this->assertSame((int) $test['mday'],    (int) $result['mday']);
        $this->assertSame((int) $test['wday'],    (int) $result['wday']);
        $this->assertSame((int) $test['mon'],     (int) $result['mon']);
        $this->assertSame((int) $test['year'],    (int) $result['year']);
        $this->assertSame((int) $test['yday'],    (int) $result['yday']);
        $this->assertSame($test['weekday'],       $result['weekday']);
        $this->assertSame($test['month'],         $result['month']);
        $this->assertSame($test[0],               $result[0]);

        $test = ['seconds' => 0,        'minutes' => 40,
            'hours' => 2,          'mday' => 26,       'wday' => 3,
            'mon' => 8,          'year' => 2188,     'yday' => 238,
            'weekday' => 'Wednesday', 'month' => 'July', 0 => 6_900_000_000, ];
        $result = $date->getDateParts(6_900_000_000, true);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']);
        $this->assertSame((int) $test['mday'],    (int) $result['mday']);
        $this->assertSame((int) $test['mon'],     (int) $result['mon']);
        $this->assertSame((int) $test['year'],    (int) $result['year']);
        $this->assertSame((int) $test['yday'],    (int) $result['yday']);
    }

    public function testDate()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);
        $this->assertTrue($date->date('U') > 0);
        $this->assertSame('0', $date->date('U',0));
        $this->assertSame('0', $date->date('U',0,false));
        $this->assertSame('0', $date->date('U',0,true));
        $this->assertSame('6900000000', $date->date('U',6_900_000_000));
        $this->assertSame('-7000000000', $date->date('U',-7_000_000_000));
        $this->assertSame('06', $date->date('d',-7_000_000_000));
        $this->assertSame('Wed', $date->date('D',-7_000_000_000));
        $this->assertSame('6', $date->date('j',-7_000_000_000));
        $this->assertSame('Wednesday', $date->date('l',-7_000_000_000));
        $this->assertSame('3', $date->date('N',-7_000_000_000));
        $this->assertSame('th', $date->date('S',-7_000_000_000));
        $this->assertSame('3', $date->date('w',-7_000_000_000));
        $this->assertSame('65', $date->date('z',-7_000_000_000));
        $this->assertSame('10', $date->date('W',-7_000_000_000));
        $this->assertSame('March', $date->date('F',-7_000_000_000));
        $this->assertSame('03', $date->date('m',-7_000_000_000));
        $this->assertSame('Mar', $date->date('M',-7_000_000_000));
        $this->assertSame('3', $date->date('n',-7_000_000_000));
        $this->assertSame('31', $date->date('t',-7_000_000_000));
        $this->assertSame('CET', $date->date('T',-7_000_000_000));
        $this->assertSame('1', $date->date('L',-7_000_000_000));
        $this->assertSame('1748', $date->date('o',-7_000_000_000));
        $this->assertSame('1748', $date->date('Y',-7_000_000_000));
        $this->assertSame('48', $date->date('y',-7_000_000_000));
        $this->assertSame('pm', $date->date('a',-7_000_000_000));
        $this->assertSame('PM', $date->date('A',-7_000_000_000));
        $this->assertSame('523', $date->date('B',-7_000_000_000));
        $this->assertSame('12', $date->date('g',-7_000_000_000));
        $this->assertSame('12', $date->date('G',-7_000_000_000));
        $this->assertSame('12', $date->date('h',-7_000_000_000));
        $this->assertSame('12', $date->date('H',-7_000_000_000));
        $this->assertSame('33', $date->date('i',-7_000_000_000));
        $this->assertSame('20', $date->date('s',-7_000_000_000));
        $this->assertSame('Europe/Paris', $date->date('e',-7_000_000_000));
        $this->assertSame('0', $date->date('I',-7_000_000_000));
        $this->assertSame('+0100', $date->date('O',-7_000_000_000));
        $this->assertSame('+01:00', $date->date('P',-7_000_000_000));
        $this->assertSame('CET', $date->date('T',-7_000_000_000));
        $this->assertSame('3600', $date->date('Z',-7_000_000_000));
        $this->assertSame('1748-03-06T12:33:20+01:00', $date->date('c',-7_000_000_000));
        $this->assertSame('Wed, 06 Mar 1748 12:33:20 +0100', $date->date('r',-7_000_000_000));
        $this->assertSame('-7000000000', $date->date('U'    ,-7_000_000_000));
        $this->assertSame('H', $date->date('\\H'  ,-7_000_000_000));
        $this->assertSame('.', $date->date('.'    ,-7_000_000_000));
        $this->assertSame('12:33:20', $date->date('H:i:s',-7_000_000_000));
        $this->assertSame('06-Mar-1748', $date->date('d-M-Y',-7_000_000_000));
        $this->assertSame('6900000000', $date->date('U',6_900_000_000, true));
        $this->assertSame('152', $date->date('B',6_900_000_000, true));
        $this->assertSame('12', $date->date('g',6_899_993_000, true));
        $this->assertSame('1', $date->date('g',6_899_997_000, true));
        $this->assertSame('1', $date->date('g',6_900_039_200, true));
        $this->assertSame('12', $date->date('h',6_899_993_000, true));
        $this->assertSame('01', $date->date('h',6_899_997_000, true));
        $this->assertSame('01', $date->date('h',6_900_040_200, true));
        $this->assertSame('UTC', $date->date('e',-7_000_000_000,true));
        $this->assertSame('0', $date->date('I',-7_000_000_000,true));
        $this->assertSame('GMT', $date->date('T',-7_000_000_000,true));
        $this->assertSame('6', $date->date('N',6_899_740_800, true));
        $this->assertSame('st', $date->date('S',6_900_518_000, true));
        $this->assertSame('nd', $date->date('S',6_900_604_800, true));
        $this->assertSame('rd', $date->date('S',6_900_691_200, true));
        $this->assertSame('7', $date->date('N',6_900_432_000, true));
        $date->setTimezone('Europe/Vienna');
        date_default_timezone_set('Indian/Maldives');
        $reference = $date->date('U');
        $this->assertTrue(abs($reference - time()) < 2);
        $this->assertSame('69000000', $date->date('U',69_000_000));

        // ISO Year (o) depends on the week number so 1.1. can be last year is week is 52/53
        $this->assertSame('1739', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1740)));
        $this->assertSame('1740', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1741)));
        $this->assertSame('1742', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1742)));
        $this->assertSame('1743', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1743)));
        $this->assertSame('1744', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1744)));
        $this->assertSame('1744', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1745)));
        $this->assertSame('1745', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1746)));
        $this->assertSame('1746', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1747)));
        $this->assertSame('1748', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1748)));
        $this->assertSame('1749', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1749)));
        $this->assertSame('2049', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2050)));
        $this->assertSame('2050', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2051)));
        $this->assertSame('2052', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2052)));
        $this->assertSame('2053', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2053)));
        $this->assertSame('2054', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2054)));
    }

    function testMktimeDay0And32()
    {
        // the following functionality is used by isTomorrow() and isYesterday() in Zend_Date.
        $date = new Zend_Date_DateObjectTestHelper(0);
        $this->assertSame('20060101', $date->date('Ymd', $date->mktime(0, 0, 0, 12, 32, 2005)));
        $this->assertSame('20050301', $date->date('Ymd', $date->mktime(0, 0, 0,  2, 29, 2005)));
        $this->assertSame('20051231', $date->date('Ymd', $date->mktime(0, 0, 0,  1,  0, 2006)));
        $this->assertSame('20050131', $date->date('Ymd', $date->mktime(0, 0, 0,  2,  0, 2005)));
    }

    /**
     * Test for setTimezone().
     */
    public function testSetTimezone()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);

        date_default_timezone_set('Europe/Vienna');
        $date->setTimezone('Indian/Maldives');
        $this->assertSame('Indian/Maldives', $date->getTimezone());

        try {
            $date->setTimezone('Unknown');
            // without new phpdate false timezones do not throw an exception !
            // known and expected behaviour
            if (function_exists('timezone_open')) {
                $this->fail('exception expected');
            }
        } catch (Zend_Date_Exception $e) {
            $this->assertMatchesRegularExpression('/not a known timezone/i', $e->getMessage());
            $this->assertSame('Unknown', $e->getOperand());
        }
        $this->assertSame('Indian/Maldives', $date->getTimezone());
        $date->setTimezone();
        $this->assertSame('Europe/Vienna', $date->getTimezone());
    }

    /**
     * Test for gmtOffset.
     */
    public function testgetGmtOffset()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);

        date_default_timezone_set('Europe/Vienna');
        $date->setTimezone();

        $this->assertSame(-3600, $date->getGmtOffset());
        $date->setTimezone('GMT');
        $this->assertSame(0, $date->getGmtOffset());
    }

    /**
     * Test for _getTime.
     */
    public function test_getTime()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $time = $date->_getTime();
        $diff = abs(time() - $time);
        $this->assertTrue(($diff < 2), 'Zend_Date_DateObject->_getTime() returned a significantly '
            . "different timestamp than expected: $diff seconds");
    }

    /**
     * Test for RFC 2822's Obsolete Date and Time (paragraph 4.3).
     *
     * @see ZF-11296
     */
    public function test_obsRfc2822()
    {
        $date = new Zend_Date();
        // Obsolete timezones
        $this->assertTrue($date->set('Mon, 15 Aug 2005 15:52:01 +0000', Zend_Date::RFC_2822) instanceof Zend_Date);
        $this->assertTrue($date->set('Mon, 15 Aug 2005 15:52:01 UT', Zend_Date::RFC_2822) instanceof Zend_Date);
        $this->assertTrue($date->set('Mon, 15 Aug 2005 15:52:01 GMT', Zend_Date::RFC_2822) instanceof Zend_Date);
        $this->assertTrue($date->set('Mon, 15 Aug 2005 15:52:01 EST', Zend_Date::RFC_2822) instanceof Zend_Date);
        $this->assertTrue($date->set('Mon, 15 Aug 2005 15:52:01 I', Zend_Date::RFC_2822) instanceof Zend_Date);
        $this->assertTrue($date->set('Mon, 15 Aug 2005 15:52:01 Z', Zend_Date::RFC_2822) instanceof Zend_Date);
    }

    public function testToStringShouldEqualWithAndWithoutPhpFormat()
    {
        $date = new Zend_Date('22.05.2014');
        $date->setTime('12:00');
        $date->setTimezone('America/Los_Angeles');

        $this->assertEquals(
            $date->toString(Zend_Date::ATOM),
            $date->toString(DateTime::ATOM, 'php')
        );
    }
}

class Zend_Date_DateObjectTestHelper extends Zend_Date
{
    public function __construct($date = null, $part = null, $locale = null)
    {
        $this->setTimezone('Europe/Paris');
        parent::__construct($date, $part, $locale);
    }

    public function mktime($hour, $minute, $second, $month, $day, $year, $dst = -1, $gmt = false)
    {
        return parent::mktime($hour, $minute, $second, $month, $day, $year, $dst, $gmt);
    }

    public function getUnixTimestamp()
    {
        return parent::getUnixTimestamp();
    }

    public function setUnixTimestamp($timestamp = null)
    {
        return parent::setUnixTimestamp($timestamp);
    }

    public function weekNumber($year, $month, $day)
    {
        return parent::weekNumber($year, $month, $day);
    }

    public function dayOfWeekHelper($y, $m, $d)
    {
        return Zend_Date_DateObject::dayOfWeek($y, $m, $d);
    }

    public function calcSun($location, $horizon, $rise = false)
    {
        return parent::calcSun($location, $horizon, $rise);
    }

    public function date($format, $timestamp = null, $gmt = false)
    {
        return parent::date($format, $timestamp, $gmt);
    }

    public function getDateParts($timestamp = null, $fast = null)
    {
        return parent::getDateParts($timestamp, $fast);
    }

    public function _getTime($sync = null)
    {
        return parent::_getTime($sync);
    }
}
