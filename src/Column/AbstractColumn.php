<?php
/*
 * This file is part of the skrepr/datagrid package.
 *
 * (c) Albert Bakker <hello@abbert.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skrepr\Datagrid\Column;

/**
 * Class AbstractColumn
 * @package Skrepr\Datagrid\Column
 */
abstract class AbstractColumn
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param $row
     * @return mixed
     */
    public abstract function format($row);

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $option
     * @return null
     */
    public function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * @param $option
     * @param $value
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function setAttribute($attribute, $value)
    {
        if (empty($this->options['attr'])) {
            $this->options['attr'] = [];
        }

        $this->options['attr'][$attribute] = $value;
    }

    /**
     * @param $attributes
     * @throws \Exception
     */
    public function setAttributes($attributes)
    {
        if (!is_array($attributes)) {
            throw new \InvalidArgumentException('Parameter must be of type array');
        }

        $this->options['attr'] = $attributes;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        if (isset($this->options['attr'][$attribute])) {
            return $this->options['attr'][$attribute];
        }
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        if (isset($this->options['attr'])) {
            return $this->options['attr'];
        }

        return [];
    }
}
