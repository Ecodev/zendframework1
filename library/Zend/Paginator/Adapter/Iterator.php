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
class Zend_Paginator_Adapter_Iterator implements Zend_Paginator_Adapter_Interface
{
    /**
     * Iterator which implements Countable.
     *
     * @var Iterator
     */
    protected $_iterator;

    /**
     * Item count.
     *
     * @var int
     */
    protected $_count;

    /**
     * Constructor.
     *
     * @param  Iterator $iterator Iterator to paginate
     */
    public function __construct(Iterator $iterator)
    {
        if (!$iterator instanceof Countable) {
            throw new Zend_Paginator_Exception('Iterator must implement Countable');
        }

        $this->_iterator = $iterator;
        $this->_count = count($iterator);
    }

    /**
     * Returns an iterator of items for a page, or an empty array.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     *
     * @return array|LimitIterator
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($this->_count == 0) {
            return [];
        }

        // @link http://bugs.php.net/bug.php?id=49906 | ZF-8084
        // return new LimitIterator($this->_iterator, $offset, $itemCountPerPage);
        return new Zend_Paginator_SerializableLimitIterator($this->_iterator, $offset, $itemCountPerPage);
    }

    /**
     * Returns the total number of rows in the collection.
     */
    public function count(): int
    {
        return $this->_count;
    }
}
