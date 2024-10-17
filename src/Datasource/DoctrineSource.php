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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class DoctrineSource
 * @package Skrepr\Datagrid\Datasource
 */
class DoctrineSource extends AbstractSource
{
    protected $repository;

    protected $queryBuilder;

    public function __construct(EntityRepository $repo)
    {
        $this->repository = $repo;
        $this->queryBuilder = $this->repository->createQueryBuilder('t');
    }

    /**
     * @return array|mixed
     */
    public function listRows()
    {
        // limit
        $pageSize = $this->datagrid->getRequest()->get('length');
        $page = $this->datagrid->getRequest()->get('start') / $pageSize;
        $page += 1;

        if ($page && $pageSize) {
            $this->queryBuilder->setFirstResult(($page - 1) * $pageSize);
            $this->queryBuilder->setMaxResults($pageSize);
        }

        return $this->queryBuilder->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function totalCount()
    {
        if (method_exists($this->datagrid, 'getRowCount')) {
            return $this->datagrid->getRowCount();
        }

        $qb = clone $this->queryBuilder;
        $result = $qb->select('COUNT(t)')->getQuery()->getScalarResult();

        return array_sum(array_map(function ($item) {
            return $item[1];
        }, $result));
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}
