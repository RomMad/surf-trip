<?php

declare(strict_types=1);

namespace App\Controller\Admin\Filter;

use App\Enum\User\SurfLevel;
use App\Repository\JsonContainsFilterTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\ChoiceFilterType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

final class RequiredLevelsFilter implements FilterInterface
{
    use FilterTrait;
    use JsonContainsFilterTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return new self()
            ->setFilterFqcn(self::class)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(ChoiceFilterType::class)
            ->setFormTypeOption('value_type', EnumType::class)
            ->setFormTypeOption('value_type_options', [
                'class' => SurfLevel::class,
                'multiple' => true,
                'autocomplete' => true,
            ])
        ;
    }

    public function apply(
        QueryBuilder $queryBuilder,
        FilterDataDto $filterDataDto,
        ?FieldDto $fieldDto,
        EntityDto $entityDto,
    ): void {
        $value = $filterDataDto->getValue();

        if (null === $value || [] === $value) {
            return;
        }

        $alias = $filterDataDto->getEntityAlias();

        $this->addJsonArrayContains(
            $queryBuilder,
            $alias.'.requiredLevels',
            $value,
        );
    }
}
