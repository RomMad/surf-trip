<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Repository\JsonContainsFilterTrait;
use Doctrine\ORM\QueryBuilder;

class JsonContainsFilter implements FilterInterface
{
    use JsonContainsFilterTrait;

    /**
     * Here for backward compatibility with API Platform 4.x, which requires the getDescription method to be implemented.
     */
    public function getDescription(string $resourceClass): array
    {
        return ['json_contains' => [
            'property' => 'json_contains',
            'type' => 'string',
            'required' => false,
            'description' => 'Filter by JSON array contains.',
        ]];
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'];
        $value = $this->formatValue($parameter->getValue());
        $property = $parameter->getProperty();
        $alias = $queryBuilder->getRootAliases()[0];

        $this->addJsonArrayContains(
            $queryBuilder,
            sprintf('%s.%s', $alias, $property),
            $value,
        );
    }

    /**
     * @return list<mixed>
     */
    private function formatValue(mixed $value): array
    {
        if (is_string($value)) {
            try {
                $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                $value = is_array($decoded) ? array_values($decoded) : [$value];
            } catch (\JsonException) {
                return [$value];
            }
        }

        return is_array($value) ? array_values($value) : [$value];
    }
}
