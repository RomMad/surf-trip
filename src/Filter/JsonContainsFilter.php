<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\Operation;
use App\Repository\JsonContainsFilterTrait;
use Doctrine\ORM\QueryBuilder;

class JsonContainsFilter implements FilterInterface
{
    use BackwardCompatibleFilterDescriptionTrait; // Here for backward compatibility, keep it until 5.0.
    use JsonContainsFilterTrait;

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
     * @return array<int|string>
     */
    private function formatValue(mixed $value): array
    {
        if (is_string($value)) {
            try {
                $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                $value = is_array($decoded) ? $decoded : [$value];
            } catch (\JsonException) {
                return [$value];
            }
        }

        return (array) $value;
    }
}
