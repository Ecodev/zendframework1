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
 * Interface for navigational helpers.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_View_Helper_Navigation_Helper
{
    /**
     * Sets navigation container the helper should operate on by default.
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               operate on. Default is
     *                                               null, which indicates that
     *                                               the container should be
     *                                               reset.
     *
     * @return Zend_View_Helper_Navigation_Helper    fluent interface, returns
     *                                               self
     */
    public function setContainer(?Zend_Navigation_Container $container = null);

    /**
     * Returns the navigation container the helper operates on by default.
     *
     * @return Zend_Navigation_Container  navigation container
     */
    public function getContainer();

    /**
     * Sets ACL to use when iterating pages.
     *
     * @param  Zend_Acl $acl                       [optional] ACL instance
     *
     * @return Zend_View_Helper_Navigation_Helper  fluent interface, returns
     *                                             self
     */
    public function setAcl(?Zend_Acl $acl = null);

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or
     * {@link setDefaultAcl()}.
     *
     * @return null|Zend_Acl  ACL object or null
     */
    public function getAcl();

    /**
     * Sets ACL role to use when iterating pages.
     *
     * @param  mixed $role                         [optional] role to set.
     *                                             Expects a string, an
     *                                             instance of type
     *                                             {@link Zend_Acl_Role_Interface},
     *                                             or null. Default is null.
     *
     * @return Zend_View_Helper_Navigation_Helper  fluent interface, returns
     *                                             self
     */
    public function setRole($role = null);

    /**
     * Returns ACL role to use when iterating pages, or null if it isn't set.
     *
     * @return null|string|Zend_Acl_Role_Interface  role or null
     */
    public function getRole();

    /**
     * Sets whether ACL should be used.
     *
     * @param  bool $useAcl                        [optional] whether ACL
     *                                             should be used. Default is
     *                                             true.
     *
     * @return Zend_View_Helper_Navigation_Helper  fluent interface, returns
     *                                             self
     */
    public function setUseAcl($useAcl = true);

    /**
     * Returns whether ACL should be used.
     *
     * @return bool  whether ACL should be used
     */
    public function getUseAcl();

    /**
     * Return renderInvisible flag.
     *
     * @return bool
     */
    public function getRenderInvisible();

    /**
     * Render invisible items?
     *
     * @param  bool $renderInvisible                       [optional] boolean flag
     *
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface
     *                                                     returns self
     */
    public function setRenderInvisible($renderInvisible = true);

    /**
     * Checks if the helper has a container.
     *
     * @return bool  whether the helper has a container or not
     */
    public function hasContainer();

    /**
     * Checks if the helper has an ACL instance.
     *
     * @return bool  whether the helper has a an ACL instance or not
     */
    public function hasAcl();

    /**
     * Checks if the helper has an ACL role.
     *
     * @return bool  whether the helper has a an ACL role or not
     */
    public function hasRole();

    /**
     * Magic overload: Should proxy to {@link render()}.
     *
     * @return string
     */
    public function __toString();

    /**
     * Renders helper.
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               render. Default is null,
     *                                               which indicates that the
     *                                               helper should render the
     *                                               container returned by
     *                                               {@link getContainer()}.
     *
     * @return string                                helper output
     */
    public function render(?Zend_Navigation_Container $container = null);
}
