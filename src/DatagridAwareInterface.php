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

interface DatagridAwareInterface
{
    public function setDatagrid(Datagrid $datagrid);

    public function getDatagrid();
}
