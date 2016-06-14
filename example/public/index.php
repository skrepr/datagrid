<?php
// composer autoloader
require '../app/bootstrap.php';

$datagrid = new \App\Datagrid\Test();
$datagrid->render();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Datagrid test</title>

    <!-- DataTables CSS -->
<!--    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">-->

    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>

    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.js"></script>

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css">
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
    <script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>

    <script>hljs.initHighlightingOnLoad();</script>

    <style>
        /* Custom page footer */
        .footer {
            padding-top: 19px;
            color: #777;
            border-top: 1px solid #e5e5e5;
        }
    </style>
</head>
<body>

<a href="https://github.com/abbert/datagrid"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/52760788cde945287fbb584134c4cbc2bc36f904/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f77686974655f6666666666662e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png"></a>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">
                abbert/datagrid
            </h1>
            <?=$datagrid?>
            <h2>About</h2>
            <p>abbert/datagrid is a composer package written for DataTables.</p>
            <h2>Usage</h2>
            <p>The aim of this package is: simplicity. See code below:</p>

            <?php
            $code = <<<'CODE'
<?php
require '../vendor/autoload.php';

use Abbert\Datagrid\Datagrid;
use Abbert\Datagrid\DataSource\DoctrineSource;

$em = SomeDoctrineEntityManagerProvider::get();
$repo = $em->getRepository('Entity\Test');

$datagrid = new Datagrid(new DoctrineSource($repo));

$datagrid->addColumn('Firstname', function ($row) {
    return $row->getFirstname();
});

$datagrid->addColumn('Actions', new ActionsColumn(function ($row) {
    return array(
        array(
            'label' => 'dg-icon:fa fa-pencil',
            'href' => '/people/1',
        )
    )
}));

$datagrid->render();

echo $datagrid;
CODE;
            ?>

            <pre><code class="php"><?=htmlspecialchars($code)?></code></pre>
            <footer class="footer"><p>Written by <a href="http://github.com/abbert">Albert Bakker</a>.</p></footer>
        </div>
    </div>
</div>

</body>
</html>