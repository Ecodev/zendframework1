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
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version     $Id$
 */
require_once 'jQueryTestCase.php';
require_once 'ZendX/JQuery/View/Helper/DialogContainer.php';

class ZendX_JQuery_View_DialogContainerTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->dialogContainer('element', '');

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->dialogContainer('elem1', '', ['option' => 'true']);

        $jquery = $this->jquery->__toString();
        $this->assertStringContainsString('dialog(', $jquery);
        $this->assertStringContainsString('"option":"true"', $jquery);
    }

    public function testShouldCreateDivContainer()
    {
        $element = $this->view->dialogContainer('elem1', '', [], []);

        $this->assertEquals(['$("#elem1").dialog({});'], $this->jquery->getOnLoadActions());
        $this->assertStringContainsString('<div', $element);
        $this->assertStringContainsString('id="elem1"', $element);
        $this->assertStringContainsString('</div>', $element);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-4685')]
    public function testUsingJsonExprForResizeShouldBeValidJsCallbackRegression()
    {
        $params = [
            'resize' => new Zend_Json_Expr('doMyThingAtResize'),
        ];

        $this->view->dialogContainer('dialog1', 'Some text', $params);

        $actions = $this->jquery->getOnLoadActions();
        $this->assertEquals(['$("#dialog1").dialog({"resize":doMyThingAtResize});'], $actions);
    }
}
