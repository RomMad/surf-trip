<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait ContainerTrait
{
    protected function clearCache(): bool
    {
        $cache = self::getContainer()->get('cache.app');

        if (!$cache instanceof AdapterInterface) {
            return false;
        }

        return $cache->clear();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param class-string<T> $className
     *
     * @return EntityRepository<T>
     *
     * @template T of object
     */
    protected function getRepository(string $className): EntityRepository
    {
        return self::getEntityManager()->getRepository($className);
    }

    protected function getParameter(string $name): mixed
    {
        $parameterBag = self::getContainer()->get('parameter_bag');

        if (!$parameterBag instanceof ParameterBagInterface) {
            throw new \Exception('The "parameter_bag" service is not an instance of ParameterBagInterface.');
        }

        return $parameterBag->get($name);
    }
}
