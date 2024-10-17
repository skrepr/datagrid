<?php
/*
 * This file is part of the skrepr/datagrid package.
 *
 * (c) Albert Bakker <hello@abbert.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skrepr\Datagrid\Datasource;

/**
 * Class ArraySource
 * @package Skrepr\Datagrid\Datasource
 */
class ArraySource extends AbstractSource
{
    /**
     * @var array
     */
    protected $array;

    /**
     * @var array
     */
    protected $result;

    /**
     * @var bool
     */
    protected $containsEntities;

    /**
     * ArraySource constructor.
     * @param array $array
     * @param bool $containsEntities
     */
    public function __construct(array $array, $containsEntities = false)
    {
        $this->array = $array;
        $this->containsEntities = $containsEntities;
    }

    private function searchArray($term, $array)
    {
        $results = [];

        foreach ($array as $item) {
            foreach ($item as $value) {
                if (stripos($value, $term) !== false) {
                    $results[md5(serialize($item))] = $item;
                }
            }
        }

        return $results;
    }

    private function think()
    {
        $array = $this->array;

        $search = $this->datagrid->getRequest()->get('search');

        if (!empty($search['value'])) {
            $array = $this->searchArray($search['value'], $array);
        }

        return $array;
    }

    /**
     * @return array|mixed
     */
    public function listRows()
    {
        $pageSize = $this->datagrid->getRequest()->get('length');
        $page = $this->datagrid->getRequest()->get('start') / $pageSize;

        return array_slice($this->think(), $page * $pageSize, $pageSize);
    }

    /**
     * @return int
     */
    public function totalCount()
    {
        return count($this->think());
    }
}
