<?php

namespace Beryllium\SilexCacheProvider;

use Beryllium\Cache\Client\APCClient;
use Beryllium\Cache\Client\MemcacheClient;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Beryllium\Cache\Client\FilecacheClient;
use Beryllium\Cache\Statistics\Tracker\FilecacheStatisticsTracker;
use Beryllium\Cache\Cache;

class SilexCacheProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        // defaults (these can be overridden in the container)
        $app['be_cache.type']   = 'memcache';
        $app['be_cache.host']   = '127.0.0.1';
        $app['be_cache.port']   = '11211';
        $app['be_cache.stats']  = true;
        $app['be_cache.prefix'] = '';

        // register the client
        $app['be_cache.client'] = $app->share(
            function () use ($app) {
                $client = null;

                switch (strtolower($app['be_cache.type'])) {
                    default:
                        throw new \Exception('Invalid be_cache.type');
                        break;
                    case 'file':
                    case 'filecache':
                        if (!$app['be_cache.path'] || !is_writable($app['be_cache.path'])) {
                            throw new \Exception('$app[\'be_cache.path\'] must be writable.');
                        }
                        $client = new FilecacheClient($app['be_cache.path']);
                        if ($app['be_cache.stats']) {
                            $stats = new FilecacheStatisticsTracker($app['be_cache.path']);
                            $client->setStatisticsTracker($stats);
                        }
                        break;
                    case 'memcache':
                        $memcache = new \Memcache();
                        $memcache->addserver($app['be_cache.host'], $app['be_cache.port']);
                        $client = new MemcacheClient($memcache);
                        break;
                    case 'apc':
                        $client = new APCClient();
                        break;
                }

                return $client;
            }
        );

        //register the service
        $app['be_cache'] = $app->share(
            function () use ($app) {
                $cache = new Cache($app['be_cache.client']);
                $cache->setPrefix($app['be_cache.prefix']);

                return $cache;
            }
        );
    }

} 