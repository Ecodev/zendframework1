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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Acl_Role_Registry
{
    /**
     * Internal Role registry data storage.
     *
     * @var array
     */
    protected $_roles = [];

    /**
     * Adds a Role having an identifier unique to the registry.
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  array|string|Zend_Acl_Role_Interface $parents
     *
     * @return Zend_Acl_Role_Registry Provides a fluent interface
     */
    public function add(Zend_Acl_Role_Interface $role, $parents = null)
    {
        $roleId = $role->getRoleId();

        if ($this->has($roleId)) {
            throw new Zend_Acl_Role_Registry_Exception("Role id '$roleId' already exists in the registry");
        }

        $roleParents = [];

        if (null !== $parents) {
            if (!is_array($parents)) {
                $parents = [$parents];
            }
            foreach ($parents as $parent) {
                try {
                    if ($parent instanceof Zend_Acl_Role_Interface) {
                        $roleParentId = $parent->getRoleId();
                    } else {
                        $roleParentId = $parent;
                    }
                    $roleParent = $this->get($roleParentId);
                } catch (Zend_Acl_Role_Registry_Exception $e) {
                    throw new Zend_Acl_Role_Registry_Exception("Parent Role id '$roleParentId' does not exist", 0, $e);
                }
                $roleParents[$roleParentId] = $roleParent;
                $this->_roles[$roleParentId]['children'][$roleId] = $role;
            }
        }

        $this->_roles[$roleId] = [
            'instance' => $role,
            'parents' => $roleParents,
            'children' => [],
        ];

        return $this;
    }

    /**
     * Returns the identified Role.
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  string|Zend_Acl_Role_Interface $role
     *
     * @return Zend_Acl_Role_Interface
     */
    public function get($role)
    {
        if ($role instanceof Zend_Acl_Role_Interface) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = (string) $role;
        }

        if (!$this->has($role)) {
            throw new Zend_Acl_Role_Registry_Exception("Role '$roleId' not found");
        }

        return $this->_roles[$roleId]['instance'];
    }

    /**
     * Returns true if and only if the Role exists in the registry.
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  string|Zend_Acl_Role_Interface $role
     *
     * @return bool
     */
    public function has($role)
    {
        if ($role instanceof Zend_Acl_Role_Interface) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = (string) $role;
        }

        return isset($this->_roles[$roleId]);
    }

    /**
     * Returns an array of an existing Role's parents.
     *
     * The array keys are the identifiers of the parent Roles, and the values are
     * the parent Role instances. The parent Roles are ordered in this array by
     * ascending priority. The highest priority parent Role, last in the array,
     * corresponds with the parent Role most recently added.
     *
     * If the Role does not have any parents, then an empty array is returned.
     *
     * @param  string|Zend_Acl_Role_Interface $role
     *
     * @uses   Zend_Acl_Role_Registry::get()
     *
     * @return array
     */
    public function getParents($role)
    {
        $roleId = $this->get($role)->getRoleId();

        return $this->_roles[$roleId]['parents'];
    }

    /**
     * Returns true if and only if $role inherits from $inherit.
     *
     * Both parameters may be either a Role or a Role identifier. If
     * $onlyParents is true, then $role must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $role
     * inherits from $inherit through its ancestor Roles.
     *
     * @param  string|Zend_Acl_Role_Interface $role
     * @param  string|Zend_Acl_Role_Interface $inherit
     * @param  bool                        $onlyParents
     *
     * @return bool
     */
    public function inherits($role, $inherit, $onlyParents = false)
    {
        try {
            $roleId = $this->get($role)->getRoleId();
            $inheritId = $this->get($inherit)->getRoleId();
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            throw new Zend_Acl_Role_Registry_Exception($e->getMessage(), $e->getCode(), $e);
        }

        $inherits = isset($this->_roles[$roleId]['parents'][$inheritId]);

        if ($inherits || $onlyParents) {
            return $inherits;
        }

        foreach ($this->_roles[$roleId]['parents'] as $parentId => $parent) {
            if ($this->inherits($parentId, $inheritId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the Role from the registry.
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  string|Zend_Acl_Role_Interface $role
     *
     * @return Zend_Acl_Role_Registry Provides a fluent interface
     */
    public function remove($role)
    {
        try {
            $roleId = $this->get($role)->getRoleId();
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            throw new Zend_Acl_Role_Registry_Exception($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($this->_roles[$roleId]['children'] as $childId => $child) {
            unset($this->_roles[$childId]['parents'][$roleId]);
        }
        foreach ($this->_roles[$roleId]['parents'] as $parentId => $parent) {
            unset($this->_roles[$parentId]['children'][$roleId]);
        }

        unset($this->_roles[$roleId]);

        return $this;
    }

    /**
     * Removes all Roles from the registry.
     *
     * @return Zend_Acl_Role_Registry Provides a fluent interface
     */
    public function removeAll()
    {
        $this->_roles = [];

        return $this;
    }

    public function getRoles()
    {
        return $this->_roles;
    }
}
