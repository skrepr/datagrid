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

use Skrepr\Datagrid\Datagrid;

abstract class AbstractSource
{
    /**
     * @var Datagrid
     */
    protected $datagrid;

    /**
     * @return mixed
     */
    abstract public function listRows();

    /**
     * @return mixed
     */
    abstract public function totalCount();

    /**
     * @param $datagrid
     */
    public function setDatagrid($datagrid)
    {
        $this->datagrid = $datagrid;
    }
}
