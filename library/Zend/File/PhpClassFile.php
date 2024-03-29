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
 */

/**
 * Locate files containing PHP classes, interfaces, abstracts or traits.
 *
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 */
#[AllowDynamicProperties]
class Zend_File_PhpClassFile extends SplFileInfo
{
    /**
     * @var array
     */
    protected $classes;

    /**
     * Get classes.
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add class.
     *
     * @param  string $class
     *
     * @return Zend_File_PhpClassFile
     */
    public function addClass($class)
    {
        $this->classes[] = $class;

        return $this;
    }
}
