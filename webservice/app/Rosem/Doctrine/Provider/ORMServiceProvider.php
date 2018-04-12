<?php

namespace Rosem\Doctrine\Provider;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Psr\Container\ContainerInterface;
use Psrnext\App\AppConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Env\EnvInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public const PROXY_NAMESPACE = 'Rosem\Doctrine\ORM\GeneratedProxies';

    public function getFactories(): array
    {
        return [
            'entitiesPaths'      => function (): array {
                return [
                    getcwd() . '/../app/Rosem/Access/Entity' //TODO: move into Access lib
                ];
            },
            EntityManager::class => function (ContainerInterface $container) {
                $isDevelopmentMode = $container->get(EnvInterface::class)->isDevelopmentMode();
                $dbConfig = $container->get(AppConfigInterface::class)->get('db');
                $ormConfig            = new Configuration;
                $ormConfig->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));
                $ormConfig->setMetadataDriverImpl(new StaticPHPDriver($container->get('entitiesPaths')));
                $ormConfig->setProxyDir(getcwd() . '/../var/db/proxies');
                $ormConfig->setProxyNamespace(self::PROXY_NAMESPACE);
                $ormConfig->setAutoGenerateProxyClasses($isDevelopmentMode);

                if ($isDevelopmentMode) {
                    $ormConfig->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_EVAL);
                    $cache = new ArrayCache;
                } else {
                    $cache = new ApcuCache;
                }

                $ormConfig->setMetadataCacheImpl($cache);
                $ormConfig->setQueryCacheImpl($cache);

                $entityManager = EntityManager::create([
                    'dbname'   => $dbConfig['dbname'],
                    'user'     => $dbConfig['username'] ?? 'root',
                    'password' => $dbConfig['password'] ?? '',
                    'host'     => $dbConfig['host'] ?? 'localhost',
                    'driver'   => $dbConfig['driver'] ?? 'pdo_mysql',
                ], $ormConfig);

                return $entityManager;
            },
        ];
    }

    public function getExtensions(): array
    {
        return [];
    }
}