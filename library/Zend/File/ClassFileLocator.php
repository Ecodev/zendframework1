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
 * Locate files containing PHP classes, interfaces, or abstracts.
 *
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 */
#[AllowDynamicProperties]
class Zend_File_ClassFileLocator extends FilterIterator
{
    /**
     * Create an instance of the locator iterator.
     *
     * Expects either a directory, or a DirectoryIterator (or its recursive variant)
     * instance.
     *
     * @param  DirectoryIterator|string $dirOrIterator
     */
    public function __construct($dirOrIterator = '.')
    {
        if (is_string($dirOrIterator)) {
            if (!is_dir($dirOrIterator)) {
                throw new InvalidArgumentException('Expected a valid directory name');
            }

            $dirOrIterator = new RecursiveDirectoryIterator($dirOrIterator);
        }
        if (!$dirOrIterator instanceof DirectoryIterator) {
            throw new InvalidArgumentException('Expected a DirectoryIterator');
        }

        if ($dirOrIterator instanceof RecursiveIterator) {
            $iterator = new RecursiveIteratorIterator($dirOrIterator);
        } else {
            $iterator = $dirOrIterator;
        }

        parent::__construct($iterator);
        $this->setInfoClass(\Zend_File_PhpClassFile::class);
    }

    /**
     * Filter for files containing PHP classes, interfaces, or abstracts.
     */
    public function accept(): bool
    {
        $saveNamespace = null;
        $file = $this->getInnerIterator()->current();
        // If we somehow have something other than an SplFileInfo object, just
        // return false
        if (!$file instanceof SplFileInfo) {
            return false;
        }

        // If we have a directory, it's not a file, so return false
        if (!$file->isFile()) {
            return false;
        }

        // If not a PHP file, skip
        if ($file->getBasename('.php') == $file->getBasename()) {
            return false;
        }

        $contents = file_get_contents($file->getRealPath());
        $tokens = token_get_all($contents);
        $count = count($tokens);
        for ($i = 0; $i < $count; ++$i) {
            $token = $tokens[$i];
            if (!is_array($token)) {
                // single character token found; skip
                ++$i;

                continue;
            }
            switch ($token[0]) {
                case T_NAMESPACE:
                    // Namespace found; grab it for later
                    $namespace = '';
                    for ($i++; $i < $count; ++$i) {
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            if (';' === $token) {
                                $saveNamespace = false;

                                break;
                            }
                            if ('{' === $token) {
                                $saveNamespace = true;

                                break;
                            }

                            continue;
                        }
                        [$type, $content, $line] = $token;
                        if ($type === T_NAME_QUALIFIED) {
                            $namespace .= $content;

                            break;
                        }
                    }
                    if ($saveNamespace) {
                        $savedNamespace = $namespace;
                    }

                    break;
                case T_TRAIT:
                case T_CLASS:
                case T_INTERFACE:
                    // Abstract class, class, interface or trait found

                    // Get the classname
                    for ($i++; $i < $count; ++$i) {
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            continue;
                        }
                        [$type, $content, $line] = $token;
                        if (T_STRING == $type) {
                            // If a classname was found, set it in the object, and
                            // return boolean true (found)
                            if (!isset($namespace) || null === $namespace) {
                                if (isset($saveNamespace) && $saveNamespace) {
                                    $namespace = $savedNamespace;
                                } else {
                                    $namespace = null;
                                }
                            }
                            $class = (null === $namespace) ? $content : $namespace . '\\' . $content;
                            $file->addClass($class);

                            break;
                        }
                    }

                    break;
                default:
                    break;
            }
        }
        $classes = $file->getClasses();
        if (!empty($classes)) {
            return true;
        }

        // No class-type tokens found; return false
        return false;
    }
}
