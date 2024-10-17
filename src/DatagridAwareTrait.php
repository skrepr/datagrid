<?php
/*
 * This file is part of the skrepr/datagrid package.
 *
 * (c) Albert Bakker <hello@abbert.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skrepr\Datagrid;

trait DatagridAwareTrait
{
    /**
     * @var \Skrepr\Datagrid\Datagrid
     */
    protected $datagrid;

    public function setDatagrid(\Skrepr\Datagrid\Datagrid $datagrid)
    {
        $this->datagrid = $datagrid;
    }

    public function getDatagrid()
    {
        return $this->datagrid;
    }
}
