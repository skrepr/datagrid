# Skrepr Datagrid

## Installation

Use composer to install the package

`composer require "skrepr/datagrid"`

## Usage

    require 'vendor/autoload.php';

    use Skrepr\Datagrid\Datagrid;
    use Skrepr\Datagrid\DataSource\DoctrineSource;

    $datagrid = new Datagrid();
    $datagrid->setDatasource(new DoctrineSource($repo));

    $datagrid->addColumn('#', function ($row) {
    	return $row->getId();
    });

    $datagrid->render();

    echo $datagrid;

## Todo

- More data sources
- More columns
- Improve ActionColumn
- Improve configuration
- Write tests

## License

See LICENSE

## Contributing

1. Fork the project
2. Clone the repository
3. Run `composer install`
4. Edit example/app/config/db.php
5. Go to to example
6. Run `../vendor/bin/doctrine orm:schema-tool:update`
7. Run `php -S 0:3000` in example/public
8. Navigate to http://localhost:3000
9. Make your changes
10. Create a pull-request
