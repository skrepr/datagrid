<?php
/*
 * This file is part of the abbert/datagrid package.
 *
 * (c) Albert Bakker <hello@abbert.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abbert\Datagrid\Column;
use Abbert\Datagrid\DatagridAwareInterface;
use Abbert\Datagrid\DatagridAwareTrait;

/**
 * Class CallbackColumn
 * @package Abbert\Datagrid\Column
 */
class CallbackColumn extends AbstractColumn implements DatagridAwareInterface
{
    use DatagridAwareTrait;

    protected $callable;

    /**
     * @param $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function bind($obj)
    {
        if (!$this->callable instanceof \Closure) {
            return false;
        }

        $this->callable->bindTo($obj);

        return true;
    }

    /**
     * @param $row
     * @return string
     * @throws \Exception
     */
    public function format($row)
    {
        if (is_array($this->callable)) {
            if (!is_callable($this->callable)) {
                throw new \Exception(sprintf('%s::%s is not a valid callback', $this->callable[0], $this->callable[1]));
            }

            $this->callable[1] = str_replace('()', '', $this->callable[1]);

            return (string) call_user_func($this->callable, $row);
        } else if (is_object($row) && is_string($this->callable) && strstr($this->callable, '()')) {
            $method = str_replace('()', '', $this->callable);

            if (!method_exists($row, $method)) {
                throw new \Exception(sprintf('Method "%s" targeted by Callback does not exist', $method));
            }

            $reflMethod = new \ReflectionMethod($row, $method);

            if ($reflMethod->isStatic()) {
                return (string) $reflMethod->invoke(null, $row);
            } else {
                return (string) $reflMethod->invoke($row);
            }
        } else if (is_object($row) && is_string($this->callable)) {
            // Zend_Db_Table_Row..property_exists does not work due to magic getter
            // TODO
            if ($row instanceof \Zend_Db_Table_Row_Abstract) {
                return $row->{$this->callable};
            }

            if (!property_exists($row, $this->callable)) {
                throw new \Exception(sprintf('Property "%s" targeted by Callback does not exist', $this->callable));
            }

            $reflProperty = new \ReflectionProperty($row, $this->callable);

            if ($reflProperty->isPublic()) {
                return (string) $reflProperty->getValue($row);
            } else {
                throw new \Exception(sprintf('Property "%s" targeted by Callback does is not public', $this->callable));
            }
        } else if (is_callable($this->callable)) {
            return (string) $this->callable->__invoke($row);
        } else if (is_array($row) && isset($row[$this->callable])) {
            return $row[$this->callable];
        } else {
            throw new \Exception('Cannot return a string value with these parameters');
        }
    }

    /**
     * @return string
     */
    public function suggestName()
    {
        if (is_array($this->callable)) {
            $ret = $this->callable[1];
        } else if (is_string($this->callable)) {
            $ret = str_replace('get', '', $this->callable);
            $ret = str_replace('()', '', $ret);
            $ret = str_replace('_', ' ', $ret);
        } else {
            $ret = 'NONAME';
        }

        return ucfirst($ret);
    }
}
