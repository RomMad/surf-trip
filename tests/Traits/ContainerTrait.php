<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine EntityManagerInterface not available.');
        }

        return $entityManager;
    }

    /**
     * @param class-string<T> $className
     *
     * @return ServiceEntityRepository<T>
     *
     * @template T of object
     */
    protected function getRepository(string $className): ServiceEntityRepository
    {
        $repository = $this->getEntityManager()->getRepository($className);

        if (!$repository instanceof ServiceEntityRepository) {
            throw new \RuntimeException(
                sprintf('Repository for class "%s" is not an instance of ServiceEntityRepository.', $className)
            );
        }

        return $repository;
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
