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
require_once __DIR__ . '/TestAbstract.php';
require_once 'Zend/View/Helper/Navigation/Breadcrumbs.php';

/**
 * Tests Zend_View_Helper_Navigation_Breadcrumbs.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_Navigation_BreadcrumbsTest extends Zend_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test.
     *
     * @var string
     */
    protected $_helperName = \Zend_View_Helper_Navigation_Breadcrumbs::class;

    /**
     * View helper.
     *
     * @var Zend_View_Helper_Navigation_Breadcrumbs
     */
    protected $_helper;

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->_helper->breadcrumbs();
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->_helper->breadcrumbs($this->_nav2);
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testNullOutContainer()
    {
        $old = $this->_helper->getContainer();
        $this->_helper->setContainer();
        $new = $this->_helper->getContainer();

        $this->assertNotEquals($old, $new);
    }

    public function testAutoloadContainerFromRegistry()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $this->_helper->setContainer();
        $expected = $this->_getExpected('bc/default.html');
        $actual = $this->_helper->render();

        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testSetSeparator()
    {
        $this->_helper->setSeparator('foo');

        $expected = $this->_getExpected('bc/separator.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSetMaxDepth()
    {
        $this->_helper->setMaxDepth(1);

        $expected = $this->_getExpected('bc/maxdepth.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSetMinDepth()
    {
        $this->_helper->setMinDepth(1);

        $expected = '';
        $this->assertEquals($expected, $this->_helper->render($this->_nav2));
    }

    public function testLinkLastElement()
    {
        $this->_helper->setLinkLast(true);

        $expected = $this->_getExpected('bc/linklast.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSetIndent()
    {
        $this->_helper->setIndent(8);

        $expected = '        <a';
        $actual = substr($this->_helper->render(), 0, strlen($expected));

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $this->_helper->setMinDepth(0);

        $rendered1 = $this->_getExpected('bc/default.html');
        $rendered2 = 'Site 2';

        $expected = [
            'registered' => $rendered1,
            'supplied' => $rendered2,
            'registered_again' => $rendered1,
        ];

        $actual = [
            'registered' => $this->_helper->render(),
            'supplied' => $this->_helper->render($this->_nav2),
            'registered_again' => $this->_helper->render(),
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUseAclResourceFromPages()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = $this->_getExpected('bc/acl.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingZendTranslate()
    {
        $this->_helper->setTranslator($this->_getTranslator());

        $expected = $this->_getExpected('bc/translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator->getAdapter());

        $expected = $this->_getExpected('bc/translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationFromTranslatorInRegistry()
    {
        $oldReg = Zend_Registry::isRegistered(\Zend_Translate::class)
                ? Zend_Registry::get(\Zend_Translate::class)
                : null;

        $translator = $this->_getTranslator();
        Zend_Registry::set(\Zend_Translate::class, $translator);

        $expected = $this->_getExpected('bc/translated.html');
        $actual = $this->_helper->render();

        Zend_Registry::set(\Zend_Translate::class, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);
        $this->_helper->setUseTranslator(false);

        $expected = $this->_getExpected('bc/default.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testRenderingPartial()
    {
        $this->_helper->setPartial('bc.phtml');

        $expected = $this->_getExpected('bc/partial.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testRenderingPartialBySpecifyingAnArrayAsPartial()
    {
        $this->_helper->setPartial(['bc.phtml', 'default']);

        $expected = $this->_getExpected('bc/partial.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testRenderingPartialShouldFailOnInvalidPartialArray()
    {
        $this->_helper->setPartial(['bc.phtml']);

        try {
            $this->_helper->render();
            $this->fail(
                '$partial was invalid, but no Zend_View_Exception was thrown');
        } catch (Zend_View_Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testLastBreadcrumbShouldBeEscaped()
    {
        $container = new Zend_Navigation([
            [
                'label' => 'Live & Learn',
                'uri' => '#',
                'active' => true,
            ],
        ]);

        $expected = 'Live &amp; Learn';
        $actual = $this->_helper->setMinDepth(0)->render($container);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-11876
     */
    public function testRenderingWithCustomHtmlAttribs()
    {
        $container = new Zend_Navigation([
            [
                'label' => 'Page 1',
                'uri' => 'p1',
                'customHtmlAttribs' => [
                    'rel' => 'nofollow',
                    'style' => 'font-weight: bold;',
                ],
                'pages' => [
                    [
                        'label' => 'Page 2',
                        'uri' => 'p2',
                        'customHtmlAttribs' => [
                            'rel' => 'nofollow',
                        ],
                        'pages' => [
                            [
                                'label' => 'Page 3',
                                'uri' => 'p3',
                                'active' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $expected = '<a href="p1" rel="nofollow" style="font-weight: bold;">Page 1</a>'
                  . ' &gt; '
                  . '<a href="p2" rel="nofollow">Page 2</a>'
                  . ' &gt; '
                  . 'Page 3';

        $actual = $this->_helper->setMinDepth(0)->render($container);

        $this->assertEquals($expected, $actual);
    }
}
