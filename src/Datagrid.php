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

use Skrepr\Datagrid\Column\AbstractColumn;
use Skrepr\Datagrid\Column\CallbackColumn;
use Skrepr\Datagrid\Datasource\AbstractSource;
use Skrepr\Datagrid\Http\Request;
use Skrepr\Datagrid\Row\DefaultRow;

class Datagrid
{
    /**
     * @var AbstractSource
     */
    private $datasource;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var \SplObjectStorage
     */
    private $columns;

    /**
     * Rendered HTML will be stored here
     *
     * @var string
     */
    private $html;

    /**
     * @var string
     */
    private $viewPath;

    /**
     * @var array
     */
    private $options = [];

    /**
     * Datagrid's unique id
     * When not set, the class name will be used
     *
     * Important: a unique id is needed when multiple
     * instances of a datagrid are present, as this id
     * is used to determine for which datagrid a XMLHttpRequest
     * is intended.
     *
     * @var string
     */
    protected $id;

    /**
     * @var DefaultRow
     */
    private $currentRow;

    /**
     * @var \Closure
     */
    private $rowCallback;

    /**
     * @var integer
     * Column index
     */
    private $currentColumn;

    /**
     * @param AbstractSource $datasource
     * @return $this
     */
    public function setDatasource(AbstractSource $datasource)
    {
        $datasource->setDatagrid($this);
        $this->datasource = $datasource;

        return $this;
    }

    /**
     * @return AbstractSource
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Creates new Request when no request isset
     */
    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @return bool
     */
    public function isJsonRequest()
    {
        return ($this->getRequest()->isXmlHttpRequest() || $this->getRequest()->get('gridtest')) && $this->isRequestForMe();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function render()
    {
        if (null === $this->datasource) {
            throw new \Exception('No datasource set');
        }

        if ($this->isJsonRequest()) {
            $this->renderJson();
        } else {
            $this->renderHtml();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequestForMe()
    {
        return $this->getRequest()->get('datagrid') == $this->getId();
    }

    /**
     * @param $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        if (null === $this->viewPath) {
            $this->viewPath = __DIR__ . '/views/datagrid.phtml';
        }

        return $this->viewPath;
    }

    /**
     * Set Datagrid::html for later use in __toString
     *
     * @return $this
     */
    public function renderHtml()
    {
        // @todo datagrid view configurable path
        // @todo custom renderers?

        ob_start();
        require $this->getViewPath();
        $this->html = ob_get_clean();

        return $this;
    }

    /**
     * Send immediately
     */
    public function renderJson()
    {
        $totalCount = $this->getDatasource()->totalCount();
        $rows = $this->getDatasource()->listRows();

        $tmpData = [];
        foreach ($rows as $row) {
            $tmp = [];

            $this->currentRow = new DefaultRow();
            if ($this->rowCallback instanceof \Closure) {
                $this->rowCallback->call($this, $row);
            }

            foreach ($this->getColumns() as $key => $column) {
                $this->currentColumn = $key;
                $tmp[$column->getName()] = $column->format($row);
            }

            $tmpData[] = $tmp;
        }

        $data = [
            'draw' => $this->getRequest()->get('draw'), // @todo param names configurable
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $totalCount,
            'data' => $tmpData,
        ];

        header('Content-Type: application/json');
        echo json_encode($data);die;
    }

    /**
     * @return AbstractColumn|CallbackColumn
     */
    public function addColumn()
    {
        if (null === $this->columns) {
            $this->columns = new \SplObjectStorage();
        }

        $args = func_get_args();
        $args = array_pad($args, 3, null);

        $name = null;
        $options = [];

        if ($args[0] instanceof AbstractColumn || is_callable($args[0])) {
            $column = $args[0];

            if (is_array($args[1])) {
                $options = $args[1];
            }
        } else if (is_string($args[0]) && empty($args[1])) {
            $column = new CallbackColumn($args[0]);
        } else if (is_string($args[0]) && is_string($args[1])) {
            $column = new CallbackColumn($args[1]);
            $name = $args[0];
        } else if (is_string($args[0])) {
            $name = $args[0];
            $column = $args[1];

            if (is_array($args[2])) {
                $options = $args[2];
            }
        } else {
            throw new \BadMethodCallException('Bad addColumn method call, see documentation');
        }

        if ($column instanceof DatagridAwareInterface) {
            $column->setDatagrid($this);
        }

        if (!$column instanceof AbstractColumn && is_callable($column)) {
            $column = new CallbackColumn($column);
        }

        if (!$column instanceof AbstractColumn) {
            throw new \InvalidArgumentException('Column should extend AbstractColumn');
        }

        // bind closure to datagrid
        if ($column instanceof CallbackColumn) {
            $column->bind($this);
        }

        if (is_string($name)) {
            $column->setName($name);
        }

        $column->setOptions($options);

        $this->columns->attach($column);

        if ($column->getName() === null && method_exists($column, 'suggestName')) {
            $column->setName($column->suggestName());
        }

        return $column;
    }

    /**
     * Removes a column
     */
    public function removeColumn(AbstractColumn $column)
    {
        $this->columns->detach($column);
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param $index
     * @return mixed|null|object
     */
    public function getColumnByIndex($index)
    {
        $tmp = iterator_to_array($this->columns);
        if (isset($tmp[$index])) {
            return $tmp[$index];
        }

        return null;
    }

    /**
     * @return mixed|null|object
     */
    public function getCurrentColumn()
    {
        return $this->getColumnByIndex($this->currentColumn);
    }

    /**
     * @param $callback
     */
    public function row($callback)
    {
        $this->rowCallback = $callback;
    }

    /**
     * @return DefaultRow
     */
    public function getCurrentRow()
    {
        return $this->currentRow;
    }

    public function __toString()
    {
        if (!is_string($this->html)) {
            return '';
        }

        return $this->html;
    }

    /**
     * @return string
     */
    public function getId()
    {
        if (null === $this->id) {
            $calledClass = explode('\\', get_called_class());
            $this->setId(end($calledClass));
        }

        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param $rowTransformer
     */
    public function setRowTransformer($rowTransformer)
    {
        $this->rowTransformer = $rowTransformer;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return null;
    }

    /**
     * @param $option
     * @param $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }
}
