<?php
namespace Abbert\Datagrid\Datasource;

class ZendDbSource extends AbstractSource
{
    protected $query;
    protected $table;
    protected $kolommen;

    protected $disableDefaultSearch = false;

    protected $translator;

    public function __construct($table_or_select)
    {
        if ($table_or_select instanceof \Zend_Db_Table_Abstract) {
            $this->query = $table_or_select->select();
            $this->table = $table_or_select;
        } elseif ($table_or_select instanceof \Zend_Db_Table_Select) {
            $this->query = $table_or_select;
            $this->table = $table_or_select->getTable();
        } else {
            throw new \InvalidArgumentException('First parameter must be of type Zend_Db_Table or Zend_Db_Table_Select');
        }

        $this->kolommen = $this->parseSelect($this->query);
    }

    public function listRows()
    {
        $query = clone $this->query;

        $pageSize = $this->datagrid->getRequest()->get('length');
        $page = $this->datagrid->getRequest()->get('start') / $pageSize;
        $page += 1;

        $query->limit($pageSize, ($page - 1) * $pageSize);

        $search = $this->datagrid->getRequest()->get('search');
        if (!empty($search['value']) && $this->disableDefaultSearch === false) {
            $q = '%' . trim($search['value']) . '%';

            $where = '';
            $teller = 0;
            foreach ($this->kolommen as $k) {
                $where .= $k . ' LIKE ?';
                if (++$teller != count($this->kolommen)) {
                    $where .= ' OR ';
                }
            }

            $query->where($where, $q);
        }

        return $this->table->fetchAll($query);
    }

    public function totalCount()
    {
        $query = clone $this->query;
        $query->setIntegrityCheck(false)->columns(array('_count' => 'COUNT(*)'));

        $search = $this->datagrid->getRequest()->get('search');
        if (!empty($search['value']) && $this->disableDefaultSearch === false) {
            $q = '%' . trim($search['value']) . '%';

            $where = '';
            $teller = 0;
            foreach ($this->kolommen as $k) {
                $where .= $k . ' LIKE ?';
                if (++$teller != count($this->kolommen)) {
                    $where .= ' OR ';
                }
            }

            $query->where($where, $q);
        }

        $result = $this->table->fetchAll($query);

        if (count($result) == 1) {
            return $result[0]->_count;
        } else {
            return count($result);
        }
    }

    private function parseSelect(\Zend_Db_Table_Select $select)
    {
        $return = array();

        foreach ($select->getPart('columns') as $c) {
            if ($c[1] instanceof \Zend_Db_Expr) {
                $return[] = $c[1];
            } elseif ($c[1] == '*' && $c[0] === $this->table->info('name')) {
                foreach ($this->table->info(\Zend_Db_Table_Abstract::COLS) as $col) {
                    $return[] = '`'.$c[0].'`.`'.$col.'`';
                }
            } elseif ($c[1] === '*') {
                $table = new \Zend_Db_Table($c[0]);
                foreach ($table->info(\Zend_Db_Table_Abstract::COLS) as $col) {
                    $return[] = '`'.$c[0].'`.`'.$col.'`';
                }
            } else {
                $return[] = '`'.$c[0].'`.`'.$c[1].'`';
            }
        }

        return $return;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function disableDefaultSearch()
    {
        $this->disableDefaultSearch = true;
    }
}
