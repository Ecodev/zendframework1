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
 * Zend_Locale_Format.
 */
require_once 'Zend/Locale/Math/PhpMath.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Locale
 */
#[AllowDynamicProperties]
class Zend_Locale_MathTest extends \PHPUnit\Framework\TestCase
{
    private static string $savedLocale = 'C';

    /**
     * setup for tests (BCMath is not designed to normalize localized numbers).
     */
    public function setUp(): void
    {
        self::$savedLocale = setlocale(LC_NUMERIC, '0');
        if (self::$savedLocale != 'C') {
            setlocale(LC_NUMERIC, 'C');
        }
    }

    /**
     * teardown for tests (restore whatever setlocale was previously in place).
     */
    public function tearDown(): void
    {
        if (self::$savedLocale != 'C') {
            setlocale(LC_NUMERIC, self::$savedLocale);
        }
    }

    /*
     * Note: All other aspects of Zend_Locale_Math receive extensive testing
     * via unit tests in Zend_Date and Zend_Measure*.
     */

    /**
     * test round()
     * expect string when BCMath extension is enabled.
     */
    public function testRound()
    {
        $this->assertEquals('3', Zend_Locale_Math::round('3.4'));
        $this->assertEquals(round(3.4), Zend_Locale_Math::round('3.4'));
        $this->assertEquals('4', Zend_Locale_Math::round('3.5'));
        $this->assertEquals(round(3.5), Zend_Locale_Math::round('3.5'));
        $this->assertEquals('4', Zend_Locale_Math::round('3.6'));
        $this->assertEquals(round(3.6), Zend_Locale_Math::round('3.6'));
        $this->assertEquals('4', Zend_Locale_Math::round('3.6', 0));
        $this->assertEquals(round(3.6,0), Zend_Locale_Math::round('3.6', 0));
        $this->assertEquals('1.96', Zend_Locale_Math::round('1.95583', 2), '');
        $this->assertEqualsWithDelta('1.96', Zend_Locale_Math::round('1.95583', 2), 0.02, '');
        $this->assertEquals(round(1.95583,2), Zend_Locale_Math::round('1.95583', 2), '');
        $this->assertEqualsWithDelta(round(1.95583,2), Zend_Locale_Math::round('1.95583', 2), 0.02, '');
        $this->assertEquals(1_242_000, Zend_Locale_Math::round('1241757', -3), '');
        $this->assertEqualsWithDelta(1_242_000, Zend_Locale_Math::round('1241757', -3), 250, '');
        $this->assertEquals(round(1_241_757, -3), Zend_Locale_Math::round('1241757', -3), '');
        $this->assertEqualsWithDelta(round(1_241_757, -3), Zend_Locale_Math::round('1241757', -3), 250, '');
        $this->assertEquals(5.05, Zend_Locale_Math::round('5.045', 2), '');
        $this->assertEqualsWithDelta(5.05, Zend_Locale_Math::round('5.045', 2), 0.02, '');
        $this->assertEquals(round(5.045, 2), Zend_Locale_Math::round('5.045', 2), '');
        $this->assertEqualsWithDelta(round(5.045, 2), Zend_Locale_Math::round('5.045', 2), 0.02, '');
        $this->assertEquals(5.06, Zend_Locale_Math::round('5.055', 2), '');
        $this->assertEqualsWithDelta(5.06, Zend_Locale_Math::round('5.055', 2), 0.02, '');
        $this->assertEquals(round(5.055, 2), Zend_Locale_Math::round('5.055', 2), '');
        $this->assertEqualsWithDelta(round(5.055, 2), Zend_Locale_Math::round('5.055', 2), 0.02, '');
    }

    public function testAdd()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(3, Zend_Locale_Math_PhpMath::Add(1, 2));
        $this->assertEquals(2, Zend_Locale_Math_PhpMath::Add(null, 2));
        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        //try {
        //    $this->assertEquals(9E+200, Zend_Locale_Math_PhpMath::Add(9E+100, 9E+200));
        //    $this->fail("exception expected");
        //} catch (Zend_Locale_Math_Exception $e) {
        //    $this->assertEquals(array(9E+100, 9E+200, 9E+200), $e->getResults());
        //    // success
        //}
        $this->assertEquals(15,  Zend_Locale_Math_PhpMath::Add(10.4444,  4.5556, 2));
        $this->assertEquals(15,  Zend_Locale_Math_PhpMath::Add(10.4444,  4.5556, 0));
        $this->assertEquals(-15, Zend_Locale_Math_PhpMath::Add(-10.4444, -4.5556, 0));
    }

    public function testSub()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(-1, Zend_Locale_Math_PhpMath::Sub(1, 2));
        $this->assertEquals(-2, Zend_Locale_Math_PhpMath::Sub(null, 2));
        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        // $this->assertEquals(9E+300, Zend_Locale_Math_PhpMath::Sub(-9E+100, -9E+300));
        $this->assertEquals(5.89, Zend_Locale_Math_PhpMath::Sub(10.4444,  4.5556, 2));
        $this->assertEquals(6,    Zend_Locale_Math_PhpMath::Sub(10.4444,  4.5556, 0));
        $this->assertEquals(-6,    Zend_Locale_Math_PhpMath::Sub(-10.4444, -4.5556, 0));
        $this->assertEquals(-1,    Zend_Locale_Math_PhpMath::Sub(10,      11,      2));
    }

    public function testPow()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(1, Zend_Locale_Math_PhpMath::Pow(1, 2));
        $this->assertEquals(0, Zend_Locale_Math_PhpMath::Pow(0, 2));
        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        //try {
        //    $this->assertEquals(0, Zend_Locale_Math_PhpMath::Pow(9E+300, 9E+200));
        //    $this->fail("exception expected");
        //} catch (Zend_Locale_Math_Exception $e) {
        //    // success
        //}
        $this->assertEquals(11899.64, Zend_Locale_Math_PhpMath::Pow(10.4444, 4.5556, 2));
        $this->assertEquals(11900, Zend_Locale_Math_PhpMath::Pow(10.4444, 4.5556, 0));
        $this->assertEquals(11900, Zend_Locale_Math_PhpMath::Pow(-10.4444, 4,      0));
        $this->assertEquals(100_000_000_000, Zend_Locale_Math_PhpMath::Pow(10,     11,      2));
    }

    public function testMul()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(2, Zend_Locale_Math_PhpMath::Mul(1, 2));
        $this->assertEquals(0, Zend_Locale_Math_PhpMath::Mul(null, 2));
        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        //try {
        //    $this->assertEquals(0, Zend_Locale_Math_PhpMath::Mul(9E+300, 9E+200));
        //    $this->fail("exception expected");
        //} catch (Zend_Locale_Math_Exception $e) {
        //    // success
        //}
        $this->assertEquals(47.58, Zend_Locale_Math_PhpMath::Mul(10.4444, 4.5556, 2));
        $this->assertEquals(48,    Zend_Locale_Math_PhpMath::Mul(10.4444, 4.5556, 0));
        $this->assertEquals(-42,    Zend_Locale_Math_PhpMath::Mul(-10.4444, 4,      0));
        $this->assertEquals(110,    Zend_Locale_Math_PhpMath::Mul(10,     11,      2));
    }

    public function testDiv()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(1, Zend_Locale_Math_PhpMath::Div(1, 2));
        $this->assertEquals(0, Zend_Locale_Math_PhpMath::Div(null, 2));

        try {
            $this->assertEquals(0, Zend_Locale_Math_PhpMath::Div(10, null));
            $this->fail('exception expected');
        } catch (Zend_Locale_Math_Exception $e) {
            // success
        }

        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        // $this->assertEquals(0, Zend_Locale_Math_PhpMath::Div(9E-300, 9E+200));
        $this->assertEquals(2.29, Zend_Locale_Math_PhpMath::Div(10.4444, 4.5556, 2));
        $this->assertEquals(2,    Zend_Locale_Math_PhpMath::Div(10.4444, 4.5556, 0));
        $this->assertEquals(-3,    Zend_Locale_Math_PhpMath::Div(-10.4444, 4,      0));
        $this->assertEquals(0.91, Zend_Locale_Math_PhpMath::Div(10,     11,      2));
    }

    public function testComp()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(-1, Zend_Locale_Math_PhpMath::Comp(1,  2));
        $this->assertEquals(-1, Zend_Locale_Math_PhpMath::Comp(null,  2));
        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        // $this->assertEquals(-1, Zend_Locale_Math_PhpMath::Comp(  9E+100, 9E+200    ));
        $this->assertEquals(1, Zend_Locale_Math_PhpMath::Comp(10.5556, 10.4444, 2));
        $this->assertEquals(0, Zend_Locale_Math_PhpMath::Comp(10.5556, 10.4444, 0));
        $this->assertEquals(-1, Zend_Locale_Math_PhpMath::Comp(-10.4444, -4.5556, 0));
    }

    public function testSqrt()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(1,    Zend_Locale_Math_PhpMath::Sqrt(1));
        $this->assertEquals(0,    Zend_Locale_Math_PhpMath::Sqrt(null));
        $this->assertEquals(3.25, Zend_Locale_Math_PhpMath::Sqrt(10.5556, 2));
        $this->assertEquals(3,    Zend_Locale_Math_PhpMath::Sqrt(10.5556, 0));
        $this->assertEquals(null, Zend_Locale_Math_PhpMath::Sqrt(-10.4444));
    }

    public function testMod()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertEquals(1, Zend_Locale_Math_PhpMath::Mod(1, 2));
        $this->assertEquals(0, Zend_Locale_Math_PhpMath::Mod(null, 2));
        $this->assertEquals(null, Zend_Locale_Math_PhpMath::Mod(10, null));
        /*
         * BCMath extension doesn't actually operatest with a scientific notation (e.g. 1.2e+100)
         * So we shouldn't test numbers such as -9E+100, but probably should care about correct
         * float => string conversion
         *
         * @todo provide correct behavior
         */
        //$this->assertEquals(0, Zend_Locale_Math_PhpMath::Mod(9E-300, 9E+200));
        $this->assertEquals(2,  Zend_Locale_Math_PhpMath::Mod(10.4444, 4.5556));
        $this->assertEquals(2,  Zend_Locale_Math_PhpMath::Mod(10.4444, 4.5556));
        $this->assertEquals(-2, Zend_Locale_Math_PhpMath::Mod(-10.4444, 4));
        $this->assertEquals(10, Zend_Locale_Math_PhpMath::Mod(10,     11));
    }

    public function testScale()
    {
        Zend_Locale_Math_PhpMath::disable();
        $this->assertTrue(Zend_Locale_Math_PhpMath::Scale(3));

        try {
            $this->assertTrue(Zend_Locale_Math_PhpMath::Scale(10));
            $this->fail('exception expected');
        } catch (Zend_Locale_Math_Exception $e) {
            // success
        }
        $this->assertEquals(1, Zend_Locale_Math_PhpMath::Comp(10.5556, 10.4444));
        $this->assertTrue(Zend_Locale_Math_PhpMath::Scale(0));
        $this->assertEquals(0, Zend_Locale_Math_PhpMath::Comp(10.5556, 10.4444));
    }

    public function testExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('1000', Zend_Locale_Math::exponent('1e3'));
        $this->assertEquals('10320', Zend_Locale_Math::exponent('1.032e4'));
        $this->assertEquals('10320', Zend_Locale_Math::exponent('10.32e3'));
        $this->assertEquals('1000', Zend_Locale_Math::exponent('1e+3'));
        $this->assertEquals('0.001', Zend_Locale_Math::exponent('1e-3', 3));
        $this->assertEquals('0.0001032', Zend_Locale_Math::exponent('1.032e-4', 7));
        $this->assertEquals('0.01032', Zend_Locale_Math::exponent('10.32e-3', 5));
    }

    public function testAddingWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('1300', Zend_Locale_Math::Add('1e3', 300));
        $this->assertEquals('1300', Zend_Locale_Math::Add(300, '1e3'));
    }

    public function testSubbingWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('700', Zend_Locale_Math::Sub(1000, '0.3e3'));
    }

    public function testPowerWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('1000000', Zend_Locale_Math::Pow('1e3', 2));
    }

    public function testMultiplyingWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('2000', Zend_Locale_Math::Mul('1e3', 2));
        $this->assertEquals('2000', Zend_Locale_Math::Mul(2, '1e3'));
    }

    public function testDivisionWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('500', Zend_Locale_Math::Div('1e3', 2));
    }

    public function testSqrtWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('31.62', Zend_Locale_Math::Sqrt('1e3', 2));
    }

    public function testModuloWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('0', Zend_Locale_Math::Mod('1e3', 2));
    }

    public function testComparisonWithBCMathAndExponent()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not loaded, test skipped');

            return;
        }

        $this->assertEquals('1', Zend_Locale_Math::Comp('1e3', 2));
        $this->assertEquals('-1', Zend_Locale_Math::Comp(2, '1e3'));
        $this->assertEquals('0', Zend_Locale_Math::Comp('1e3', '1e3'));
    }

    public function testNegativeRounding()
    {
        $this->assertEquals('-3', Zend_Locale_Math::round('-3.4'));
        $this->assertEquals(round(-3.4), Zend_Locale_Math::round('-3.4'));
        $this->assertEquals('-4', Zend_Locale_Math::round('-3.5'));
        $this->assertEquals(round(-3.5), Zend_Locale_Math::round('-3.5'));
        $this->assertEquals('-4', Zend_Locale_Math::round('-3.6'));
        $this->assertEquals(round(-3.6), Zend_Locale_Math::round('-3.6'));
        $this->assertEquals('-4', Zend_Locale_Math::round('-3.6', 0));
        $this->assertEquals(round(-3.6,0), Zend_Locale_Math::round('-3.6', 0));
        $this->assertEquals('-1.96', Zend_Locale_Math::round('-1.95583', 2), '');
        $this->assertEqualsWithDelta('-1.96', Zend_Locale_Math::round('-1.95583', 2), 0.02, '');
        $this->assertEquals(round(-1.95583,2), Zend_Locale_Math::round('-1.95583', 2), '');
        $this->assertEqualsWithDelta(round(-1.95583,2), Zend_Locale_Math::round('-1.95583', 2), 0.02, '');
        $this->assertEquals(-1_242_000, Zend_Locale_Math::round('-1241757', -3), '');
        $this->assertEqualsWithDelta(-1_242_000, Zend_Locale_Math::round('-1241757', -3), 250, '');
        $this->assertEquals(round(-1_241_757, -3), Zend_Locale_Math::round('-1241757', -3), '');
        $this->assertEqualsWithDelta(round(-1_241_757, -3), Zend_Locale_Math::round('-1241757', -3), 250, '');
        $this->assertEquals(-5.05, Zend_Locale_Math::round('-5.045', 2), '');
        $this->assertEqualsWithDelta(-5.05, Zend_Locale_Math::round('-5.045', 2), 0.02, '');
        $this->assertEquals(round(-5.045, 2), Zend_Locale_Math::round('-5.045', 2), '');
        $this->assertEqualsWithDelta(round(-5.045, 2), Zend_Locale_Math::round('-5.045', 2), 0.02, '');
        $this->assertEquals(-5.06, Zend_Locale_Math::round('-5.055', 2), '');
        $this->assertEqualsWithDelta(-5.06, Zend_Locale_Math::round('-5.055', 2), 0.02, '');
        $this->assertEquals(round(-5.055, 2), Zend_Locale_Math::round('-5.055', 2), '');
        $this->assertEqualsWithDelta(round(-5.055, 2), Zend_Locale_Math::round('-5.055', 2), 0.02, '');
    }
}
