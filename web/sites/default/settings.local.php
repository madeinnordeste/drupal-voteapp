<?php

// Database
$databases['default']['default'] = array(
  'database' => 'drupal11',
  'username' => 'drupal11',
  'password' => 'drupal11',
  'prefix' => '',
  'host' => 'database',
  'port' => '3306',
  'isolation_level' => 'READ COMMITTED',
  'driver' => 'mysql',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
);

// Redis
$settings['redis.connection']['interface'] = 'PhpRedis';
$settings['redis.connection']['host'] = 'redis';
$settings['cache']['default'] = 'cache.backend.redis';
$settings['cache']['bins']['bootstrap'] = 'cache.backend.redis';
$settings['cache']['bins']['discovery'] = 'cache.backend.redis';
$settings['cache']['bins']['config'] = 'cache.backend.redis';
$settings['cache']['bins']['*'] = 'cache.backend.redis';
$settings['cache']['bins']['render'] = 'cache.backend.database'; // failback

// Bugsnag
$settings['bugsnag_api_key'] = getenv('BUGSNAG_API_KEY') ?? 'YOU_BUGSNAG_API_KEY';
