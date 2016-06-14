<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require __DIR__ . '/../../vendor/autoload.php';

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__), $isDevMode);

$conn = require __DIR__ . '/config/db.php';

$entityManager = EntityManager::create($conn, $config);

abstract class Registry
{
    protected static $items;

    public static function set($key, $item)
    {
        static::$items[$key] = $item;
    }

    public static function get($key)
    {
        return static::$items[$key];
    }
}

Registry::set('em', $entityManager);
