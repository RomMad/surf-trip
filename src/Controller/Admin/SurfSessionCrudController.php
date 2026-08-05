<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\SurfSession;
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
 * @extends AbstractCrudController<SurfSession>
 */
class SurfSessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SurfSession::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['spot', 'trip.title', 'board'])
            ->setEntityLabelInSingular('surf_session.label')
            ->setEntityLabelInPlural('surf_sessions.label')
        ;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setLabel('id.label')
            ->hideOnForm()
        ;
        yield TextField::new('spot')
            ->setLabel('surf_session.spot.label')
        ;
        yield TextField::new('board')
            ->setLabel('surf_session.board.label')
        ;
        yield DateTimeField::new('startAt')
            ->setLabel('surf_session.start_time.label')
            ->setFormat('d MMM yy HH:mm')
        ;
        yield DateTimeField::new('endAt')
            ->setLabel('surf_session.end_time.label')
            ->setFormat('HH:mm')
        ;
        yield ChoiceField::new('rating')
            ->setLabel('surf_session.rating.label')
        ;
        yield TextEditorField::new('objective')
            ->setLabel('surf_session.objective.label')
        ;
        yield TextEditorField::new('comment')
            ->setLabel('surf_session.comment.label')
        ;
        yield AssociationField::new('trip')
            ->setLabel('trip.label')
            ->autocomplete()
        ;
        yield AssociationField::new('user')
            ->setLabel('user.label')
            ->setFormTypeOption('disabled', true)
        ;
        yield DateTimeField::new('createdAt')
            ->setLabel('created_at.label')
            ->setFormTypeOption('disabled', true)
        ;
        yield DateTimeField::new('updatedAt')
            ->setLabel('updated_at.label')
            ->setFormTypeOption('disabled', true)
        ;
    }

    #[\Override]
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.trip', 't')
            ->leftJoin('entity.user', 'u')
            ->addSelect('u', 't')
        ;
    }
}
