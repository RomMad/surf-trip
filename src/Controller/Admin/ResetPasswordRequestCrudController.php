<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ResetPasswordRequest;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<ResetPasswordRequest>
 */
class ResetPasswordRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResetPasswordRequest::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['user.email', 'selector'])
            ->setEntityLabelInSingular('reset_password_request.label')
            ->setEntityLabelInPlural('reset_password_requests.label')
        ;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setLabel('id.label')
        ;
        yield AssociationField::new('user')
            ->setLabel('user.label')
        ;
        yield DateTimeField::new('requestedAt')
            ->setLabel('requested_at.label')
            ->setFormTypeOption('disabled', true)
        ;
        yield DateTimeField::new('expiresAt')
            ->setLabel('expires_at.label')
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
            ->leftJoin('entity.user', 'u')->addSelect('u')
        ;
    }
}
