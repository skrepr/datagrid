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

interface DatagridAwareInterface
{
    public function setDatagrid(Datagrid $datagrid);

    public function getDatagrid();
}
