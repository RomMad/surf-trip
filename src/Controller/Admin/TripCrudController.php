<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Trip;
use App\Form\Type\TitleType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Trip>
 */
class TripCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trip::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title', 'description'])
            ->setEntityLabelInSingular('trip.label')
            ->setEntityLabelInPlural('trips.label')
        ;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setLabel('id.label')
            ->hideOnForm()
        ;
        yield TextField::new('title')
            ->setLabel('title.label')
            ->setFormType(TitleType::class)
        ;

        yield TextField::new('slug')
            ->setLabel('slug.label')
            ->hideOnIndex()
            ->setFormTypeOption('disabled', true)
        ;
        yield TextField::new('location.label')
            ->formatValue(fn ($value, Trip $trip) => $trip->location->getFullLabel())
            ->setLabel('location.label')
        ;
        yield TextField::new('location.comment')
            ->setLabel('location.comment.label')
            ->hideOnIndex()
        ;
        yield DateTimeField::new('startAt')
            ->setLabel('start_at.label')
            ->setFormat('d MMM yyyy')
        ;

        yield DateTimeField::new('endAt')
            ->setLabel('end_at.label')
            ->setFormat('d MMM yyyy')
        ;
        yield ChoiceField::new('requiredLevels')
            ->setLabel('required_levels.label')
            ->setFormTypeOption('multiple', true)
            ->setTemplatePath('admin/trip/level.html.twig')
        ;
        yield TextEditorField::new('description')
            ->setLabel('description.label')
        ;
        yield AssociationField::new('owners')
            ->setLabel('owners.label')
            ->setTemplatePath('admin/trip/owners.html.twig')
            ->autocomplete()
        ;
        yield DateTimeField::new('createdAt')
            ->setLabel('created_at.label')
            ->setFormTypeOption('disabled', true)
        ;

        // @todo create property updatedAt
        // yield DateTimeField::new('updatedAt')
        //     ->setLabel('updated_at.label')
        // ;
    }

    #[\Override]
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.owners', 'o')
            ->addSelect('o')
        ;

        if ($searchDto->getQuery()) {
            $queryBuilder
                ->andWhere(
                    'ILIKE(entity.title, :search) = TRUE
                    OR ILIKE(entity.description, :search) = TRUE'
                )
                ->setParameter('search', '%'.$searchDto->getQuery().'%')
            ;
        }

        return $queryBuilder;
    }
}
