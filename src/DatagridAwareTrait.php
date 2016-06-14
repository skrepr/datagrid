<?php
/*
 * This file is part of the abbert/datagrid package.
 *
 * (c) Albert Bakker <hello@abbert.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abbert\Datagrid;

trait DatagridAwareTrait
{
    /**
     * @var \Abbert\Datagrid\Datagrid
     */
    protected $datagrid;

    public function setDatagrid(\Abbert\Datagrid\Datagrid $datagrid)
    {
        $this->datagrid = $datagrid;
    }

    public function getDatagrid()
    {
        return $this->datagrid;
    }
}
