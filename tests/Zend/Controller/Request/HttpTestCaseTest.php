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

/** Zend_Controller_Request_HttpTestCase */
require_once 'Zend/Controller/Request/HttpTestCase.php';

/**
 * Test class for Zend_Controller_Request_HttpTestCase.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Controller
 * @group      Zend_Controller_Request
 */
class Zend_Controller_Request_HttpTestCaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Controller_Request_HttpTestCaseTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->request = new Zend_Controller_Request_HttpTestCase();
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testGetRequestUriShouldNotAttemptToAutoDiscoverFromEnvironment()
    {
        $this->assertNull($this->request->getRequestUri());
    }

    public function testGetPathInfoShouldNotAttemptToAutoDiscoverFromEnvironment()
    {
        $pathInfo = $this->request->getPathInfo();
        $this->assertTrue(empty($pathInfo));
    }

    public function testGetShouldBeEmptyByDefault()
    {
        $post = $this->request->getQuery();
        $this->assertTrue(is_array($post));
        $this->assertTrue(empty($post));
    }

    public function testShouldAllowSpecifyingGetParameters()
    {
        $this->testGetShouldBeEmptyByDefault();
        $expected = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        ];
        $this->request->setQuery($expected);

        $test = $this->request->getQuery();
        $this->assertSame($expected, $test);

        $this->request->setQuery('bat', 'bogus');
        $this->assertEquals('bogus', $this->request->getQuery('bat'));
        $test = $this->request->getQuery();
        $this->assertEquals(4, is_countable($test) ? count($test) : 0);
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $test[$key]);
        }
    }

    public function testShouldPopulateGetSuperglobal()
    {
        $this->testShouldAllowSpecifyingGetParameters();
        $expected = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'bogus',
        ];
        $this->assertEquals($expected, $_GET);
    }

    public function testShouldAllowClearingQuery()
    {
        $this->testShouldPopulateGetSuperglobal();
        $this->request->clearQuery();
        $test = $this->request->getQuery();
        $this->assertTrue(is_array($test));
        $this->assertTrue(empty($test));
    }

    public function testPostShouldBeEmptyByDefault()
    {
        $post = $this->request->getPost();
        $this->assertTrue(is_array($post));
        $this->assertTrue(empty($post));
    }

    public function testShouldAllowSpecifyingPostParameters()
    {
        $this->testPostShouldBeEmptyByDefault();
        $expected = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        ];
        $this->request->setPost($expected);

        $test = $this->request->getPost();
        $this->assertSame($expected, $test);

        $this->request->setPost('bat', 'bogus');
        $this->assertEquals('bogus', $this->request->getPost('bat'));
        $test = $this->request->getPost();
        $this->assertEquals(4, is_countable($test) ? count($test) : 0);
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $test[$key]);
        }
    }

    public function testShouldPopulatePostSuperglobal()
    {
        $this->testShouldAllowSpecifyingPostParameters();
        $expected = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'bogus',
        ];
        $this->assertEquals($expected, $_POST);
    }

    public function testShouldAllowClearingPost()
    {
        $this->testShouldPopulatePostSuperglobal();
        $this->request->clearPost();
        $test = $this->request->getPost();
        $this->assertTrue(is_array($test));
        $this->assertTrue(empty($test));
    }

    public function testRawPostBodyShouldBeNullByDefault()
    {
        $this->assertNull($this->request->getRawBody());
    }

    public function testShouldAllowSpecifyingRawPostBody()
    {
        $this->request->setRawBody('Some content for the body');
        $this->assertEquals('Some content for the body', $this->request->getRawBody());
    }

    public function testShouldAllowClearingRawPostBody()
    {
        $this->testShouldAllowSpecifyingRawPostBody();
        $this->request->clearRawBody();
        $this->assertNull($this->request->getRawBody());
    }

    public function testHeadersShouldBeEmptyByDefault()
    {
        $headers = $this->request->getHeaders();
        $this->assertTrue(is_array($headers));
        $this->assertTrue(empty($headers));
    }

    public function testShouldAllowSpecifyingRequestHeaders()
    {
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Encoding' => 'utf-8',
        ];
        $this->request->setHeaders($headers);
        $test = $this->request->getHeaders();
        $this->assertTrue(is_array($test));
        $this->assertEquals(2, is_countable($test) ? count($test) : 0);
        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $this->request->getHeader($key));
        }
        $this->request->setHeader('X-Requested-With', 'XMLHttpRequest');
        $test = $this->request->getHeaders();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, is_countable($test) ? count($test) : 0);
        $this->assertEquals('XMLHttpRequest', $this->request->getHeader('X-Requested-With'));
    }

    public function testShouldAllowClearingRequestHeaders()
    {
        $this->testShouldAllowSpecifyingRequestHeaders();
        $this->request->clearHeaders();
        $headers = $this->request->getHeaders();
        $this->assertTrue(is_array($headers));
        $this->assertTrue(empty($headers));
    }

    public function testCookiesShouldBeEmptyByDefault()
    {
        $cookies = $this->request->getCookie();
        $this->assertTrue(is_array($cookies));
        $this->assertTrue(empty($cookies));
    }

    public function testShouldAllowSpecifyingCookies()
    {
        $cookies = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        ];
        $this->request->setCookies($cookies);
        $test = $this->request->getCookie();
        $this->assertEquals($cookies, $test);

        $this->request->setCookie('bat', 'bogus');
        $this->assertEquals('bogus', $this->request->getCookie('bat'));
    }

    public function testShouldPopulateCookieSuperGlobal()
    {
        $cookies = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'bogus',
        ];
        $this->testShouldAllowSpecifyingCookies();
        $this->assertEquals($cookies, $_COOKIE);
    }

    public function testShouldAllowClearingAllCookies()
    {
        $this->testShouldAllowSpecifyingCookies();
        $this->request->clearCookies();
        $test = $this->request->getCookie();
        $this->assertTrue(is_array($test));
        $this->assertTrue(empty($test));
    }

    /**
     * @group ZF-6162
     */
    public function testRequestMethodShouldBeGetByDefault()
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    public function testShouldAllowSpecifyingRequestMethod()
    {
        $this->testRequestMethodShouldBeGetByDefault();
        $this->request->setMethod('POST');
        $this->assertTrue($this->request->isPost());
        $this->request->setMethod('GET');
        $this->assertTrue($this->request->isGet());
        $this->request->setMethod('PUT');
        $this->assertTrue($this->request->isPut());
        $this->request->setMethod('OPTIONS');
        $this->assertTrue($this->request->isOptions());
        $this->request->setMethod('HEAD');
        $this->assertTrue($this->request->isHead());
        $this->request->setMethod('DELETE');
        $this->assertTrue($this->request->isDelete());
        $this->request->setMethod('PATCH');
        $this->assertTrue($this->request->isPatch());
    }
}
