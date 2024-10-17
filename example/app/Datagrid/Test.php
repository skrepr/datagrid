<?php
namespace App\Datagrid;

use Skrepr\Datagrid\Column\ActionColumn;
use Skrepr\Datagrid\Column\CallbackColumn;
use Skrepr\Datagrid\Datagrid;
use Skrepr\Datagrid\Datasource\DoctrineSource;

class SecondTestColumn
{
    public function __invoke($row)
    {
        $html = '<marquee>';
        $html .= $row->getAddress();
        $html .= '</marquee>';

        return $html;
    }
}

class Test extends Datagrid
{
    public function __construct()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = \Registry::get('em');

        $repo = $em->getRepository('App\Entity\Test');
        $source = new DoctrineSource($repo);

        $this->setDatasource($source);

        // closure binding
        $this->addColumn(function () {
            return get_class($this);
        })->setName('Closure binding');

        // callable
        $this->addColumn('Cool', [$this, 'getCoolColumn']);

        // object methods
        $this->addColumn('getFirstName()');

        // public properties
        $this->addColumn('property', 'property');

        // addColumn returns AbstractColumn instance
        $this->addColumn(function ($row) {
            return $row->getId();
        })->setName('ID');

        $this->addColumn('Firstname', new CallbackColumn(function ($row) {
            return $row->getFirstname();
        }));

        $this->addColumn('Lastname', function ($row) {
            return $row->getLastname();
        });

        // object invokable
        $this->addColumn(new SecondTestColumn())->setName('Address');

        // actions
        $this->addColumn('Actions', new ActionColumn(function ($row) {
            return array(
                array(
                    'label' => 'dg-icon:fa fa-pencil',
                    'href' => '#',
                ),
                array(
                    'label' => 'dg-icon:fa fa-trash',
                    'href' => '#',
                ),
            );
        }));

        // search
        $search = $this->getRequest()->get('search');

        if (!empty($search['value'])) {
            $source->getQueryBuilder()->andWhere(
                't.firstname LIKE :query OR t.lastname LIKE :query OR t.address LIKE :query'
            );
            $source->getQueryBuilder()->setParameter('query', '%' . $search['value'] . '%');
        }

        // order
        $order = $this->getRequest()->get('order');

        $column = $order[0]['column'];
        $direction = $order[0]['dir'];

        $columns = [
            'id',
            'firstname',
            'lastname',
            'address',
        ];

        if (array_key_exists($column, $columns)) {
            $column = $columns[$column];

            $source->getQueryBuilder()->orderBy('t.' . $column, $direction);
        }
    }

    public function getCoolColumn($row)
    {
        return 'Cool column ' . $row->getId();
    }
}
