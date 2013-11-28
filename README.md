Silex Cache Provider
====================

SilexCacheProvider is configurable to provide client interfaces for Memcache, APC, or a file-based cache.

Installation
------------

Run `composer require beryllium/silex-cache-provider` or add to your composer.json:

    "require": {
        "beryllium/silex-cache-provider": "*"
    }

Currently, the provider is only available with a minimum stability of "dev".

Configuration
-------------

### Memcache

Register the provider using Memcache (requires installation of the PHP memcache extension):

    $app->register(
        new Beryllium\SilexCacheProvider\SilexCacheProvider(),
        array(
            'be_cache.type' => 'memcache',
            'be_cache.host' => '127.0.0.1',
            'be_cache.port' => 11211,
        )
    );
               
The above settings are the provider defaults, so you can actually just do the following if that is your configuration:

    $app->register(new Beryllium\SilexCacheProvider\SilexCacheProvider());

### File Cache

This option requires a local folder with write access. The be\_cache service will use the folder to store serialized data from PHP.

    $app->register(
        new Beryllium\SilexCacheProvider\SilexCacheProvider(),
        array(
            'be_cache.type' => 'filecache',
            'be_cache.path' => __DIR__ . '/cache'
        )
    );
               
### APC Cache

This option requires the APC extension to be active.

    $app->register(
        new Beryllium\SilexCacheProvider\SilexCacheProvider(),
        array(
            'be_cache.type' => 'apc'
        )
    );

Usage
-----

Three methods are exposed by the cache service. Set, Get, and Delete.

### Set

This method takes three parameters: Key Name, Data, and Time-To-Live (in seconds).

To store the variable $data for 5 minutes, you would use:

    $app['be_cache']->set('key_name', $data, 300);

### Get and Delete

These methods only take one parameter: Key Name.

    $data = $app['be_cache']->get('key_name');
    // ...
    $app['be_cache']->delete('key_name');

Additional Tips
---------------

### Prefix

You may want to share your Memcache instance with other processes. In this scenario, you would want to namespace all of your application's keys so they won't conflict.

This can be done by specifying a prefix in your settings at registration:

    'be_cache.prefix' => 'example_prefix|';

### Get Client

You can retrieve the client from the service two ways:

    $client = $app['be_cache.client'];
    // or
    $client = $app['be_cache']->getClient();
