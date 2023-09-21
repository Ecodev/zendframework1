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
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version   $Id$
 */

/**
 * Validator for the sha1 hash of given files.
 *
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Validate_File_Sha1 extends Zend_Validate_File_Hash
{
    /**
     * @const string Error constants
     */
    public const DOES_NOT_MATCH = 'fileSha1DoesNotMatch';
    public const NOT_DETECTED = 'fileSha1NotDetected';
    public const NOT_FOUND = 'fileSha1NotFound';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = [
        self::DOES_NOT_MATCH => "File '%value%' does not match the given sha1 hashes",
        self::NOT_DETECTED => 'A sha1 hash could not be evaluated for the given file',
        self::NOT_FOUND => "File '%value%' is not readable or does not exist",
    ];

    /**
     * Hash of the file.
     *
     * @var string
     */
    protected $_hash;

    /**
     * Sets validator options.
     *
     * $hash is the hash we accept for the file $file
     *
     * @param  array|string $options
     *
     * @return Zend_Validate_File_Sha1
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (is_scalar($options)) {
            $options = ['hash1' => $options];
        } elseif (!is_array($options)) {
            throw new Zend_Validate_Exception('Invalid options to validator provided');
        }

        $this->setHash($options);
    }

    /**
     * Returns all set sha1 hashes.
     *
     * @return array
     */
    public function getSha1()
    {
        return $this->getHash();
    }

    /**
     * Sets the sha1 hash for one or multiple files.
     *
     * @param  array|string $options
     *
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function setHash($options)
    {
        if (!is_array($options)) {
            $options = (array) $options;
        }

        $options['algorithm'] = 'sha1';
        parent::setHash($options);

        return $this;
    }

    /**
     * Sets the sha1 hash for one or multiple files.
     *
     * @param  array|string $options
     *
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function setSha1($options)
    {
        $this->setHash($options);

        return $this;
    }

    /**
     * Adds the sha1 hash for one or multiple files.
     *
     * @param  array|string $options
     *
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function addHash($options)
    {
        if (!is_array($options)) {
            $options = (array) $options;
        }

        $options['algorithm'] = 'sha1';
        parent::addHash($options);

        return $this;
    }

    /**
     * Adds the sha1 hash for one or multiple files.
     *
     * @param  array|string $options
     *
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function addSha1($options)
    {
        $this->addHash($options);

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface.
     *
     * Returns true if and only if the given file confirms the set hash
     *
     * @param  string $value Filename to check for hash
     * @param  array  $file  File data from Zend_File_Transfer
     *
     * @return bool
     */
    public function isValid($value, $file = null)
    {
        // Is file readable ?
        if (!Zend_Loader::isReadable($value)) {
            return $this->_throw($file, self::NOT_FOUND);
        }

        $hashes = array_unique(array_keys($this->_hash));
        $filehash = hash_file('sha1', $value);
        if ($filehash === false) {
            return $this->_throw($file, self::NOT_DETECTED);
        }

        foreach ($hashes as $hash) {
            if ($filehash === $hash) {
                return true;
            }
        }

        return $this->_throw($file, self::DOES_NOT_MATCH);
    }
}
