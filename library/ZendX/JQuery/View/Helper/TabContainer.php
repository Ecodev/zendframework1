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

/**
 * jQuery Tabs Container View Helper.
 *
 * @uses 	   Zend_Json
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_TabContainer extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Save all the pre-rendered tab panes to each tab container.
     *
     * @var array
     */
    protected $_tabs = [];

    /**
     * Add Tab to TabsContainer.
     *
     * @param  string $id
     * @param  string $name
     * @param  string $content
     *
     * @return ZendX_JQuery_View_Helper_TabsContainer
     */
    public function addPane($id, $name, $content, array $options = [])
    {
        if (!isset($this->_tabs[$id])) {
            $this->_tabs[$id] = [];
        }
        if (strlen($name) == 0 && isset($options['title'])) {
            $name = $options['title'];
        }

        $this->_tabs[$id][] = ['name' => $name, 'content' => $content, 'options' => $options];

        return $this;
    }

    /**
     * Render TabsContainer with all the currently registered tabs.
     *
     * Render all tabs to the given $id. If no arguments are given the
     * tabsContainer view helper object is returned and can be used
     * for chaining {@link addPane()} for tab pane adding.
     *
     * @see   http://docs.jquery.com/UI/Tabs
     *
     * @param  string $id
     * @param  array  $params
     * @param  array  $attribs
     *
     * @return string|ZendX_JQuery_View_Helper_TabsContainer
     */
    public function tabContainer($id = null, $params = [], $attribs = [])
    {
        if (func_num_args() === 0) {
            return $this;
        }

        if (!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        $content = '';
        if (isset($this->_tabs[$id])) {
            $list = '<ul class="ui-tabs-nav">' . PHP_EOL;
            $html = '';
            $fragment_counter = 1;
            foreach ($this->_tabs[$id] as $k => $v) {
                $frag_name = sprintf('%s-frag-%d', $attribs['id'], $fragment_counter++);
                $opts = $v['options'];
                if (isset($opts['contentUrl'])) {
                    $list .= '<li class="ui-tabs-nav-item"><a href="' . $opts['contentUrl'] . '"><span>' . $v['name'] . '</span></a></li>' . PHP_EOL;
                } else {
                    $list .= '<li class="ui-tabs-nav-item"><a href="#' . $frag_name . '"><span>' . $v['name'] . '</span></a></li>' . PHP_EOL;
                    $html .= '<div id="' . $frag_name . '" class="ui-tabs-panel">' . $v['content'] . '</div>' . PHP_EOL;
                }
            }
            $list .= '</ul>' . PHP_EOL;

            $content = $list . $html;
            unset($this->_tabs[$id]);
        }

        if (count($params)) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('%s("#%s").tabs(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );
        $this->jquery->addOnLoad($js);

        $html = '<div'
              . $this->_htmlAttribs($attribs)
              . '>' . PHP_EOL
              . $content
              . '</div>' . PHP_EOL;

        return $html;
    }
}
