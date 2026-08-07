<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\UserRolesFilter;
use App\Entity\User;
use App\Enum\User\SurfLevel;
use App\Enum\User\UserRole;
use App\Form\Type\EmailType;
use App\Form\Type\FirstNameType;
use App\Form\Type\LastNameType;
use App\Form\Type\UsernameType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @extends AbstractCrudController<User>
 */
class UserCrudController extends AbstractCrudController
{
    use AvatarUrlTrait;

    public function __construct(
        #[Autowire('%app.avatar.path%')]
        private string $avatarPath,
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['email', 'username', 'firstName', 'lastName'])
            ->setEntityLabelInSingular('user.label')
            ->setEntityLabelInPlural('users.label')
        ;
    }

    #[\Override]
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('email', 'email.label'))
            ->add(TextFilter::new('username', 'username.label'))
            ->add(TextFilter::new('firstName', 'first_name.label'))
            ->add(TextFilter::new('lastName', 'last_name.label'))
            ->add(TextFilter::new('location', 'location.label'))
            ->add(
                ChoiceFilter::new('level', 'surf_level.label')
                    ->canSelectMultiple()
                    ->setTranslatableChoices(SurfLevel::cases())
            )
            ->add(UserRolesFilter::new('roles', 'roles.label'))
            ->add(BooleanFilter::new('isVerified', 'is_verified.label'))
            ->add(DateTimeFilter::new('createdAt', 'created_at.label'))
            ->add(DateTimeFilter::new('updatedAt', 'updated_at.label'))
        ;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setLabel('id.label')
            ->hideOnForm()
        ;
        yield AvatarField::new('avatarPath')
            ->setLabel('avatar.label')
            ->formatValue(fn ($value, User $user) => $this->generateAvatarUrl($user->avatarPath))
        ;
        yield EmailField::new('email')
            ->setLabel('email.label')
            ->setFormType(EmailType::class)
        ;
        yield TextField::new('username')
            ->setLabel('username.label')
            ->setFormType(UsernameType::class)
        ;
        yield TextField::new('firstName')
            ->setLabel('first_name.label')
            ->setFormType(FirstNameType::class)
        ;
        yield TextField::new('lastName')
            ->setLabel('last_name.label')
            ->setFormType(LastNameType::class)
        ;
        yield TextField::new('location')
            ->setLabel('location.label')
        ;
        yield ChoiceField::new('level')
            ->setLabel('surf_level.label')
        ;
        yield TextField::new('instagram')
            ->setLabel('instagram.label')
        ;
        yield TextEditorField::new('description')
            ->setLabel('description.label')
        ;
        yield ChoiceField::new('roles')
            ->setLabel('roles.label')
            ->allowMultipleChoices()
            ->setChoices(array_combine(
                array_map(fn ($role) => $role->label(), UserRole::cases()),
                array_map(fn ($role) => $role->value, UserRole::cases())
            ))
            ->setTemplatePath('admin/user/roles.html.twig')
        ;
        yield BooleanField::new('isVerified')
            ->setLabel('is_verified.label')
            ->hideOnForm()
        ;
        yield ChoiceField::new('locale')
            ->setLabel('locale.label')
        ;
        yield DateTimeField::new('createdAt')
            ->setLabel('created_at.label')
            ->hideOnForm()
        ;
        yield DateTimeField::new('updatedAt')
            ->setLabel('updated_at.label')
            ->hideOnForm()
        ;
        yield DateTimeField::new('lastActiveAt')
            ->setLabel('last_active_at.label')
            ->hideOnForm()
        ;
    }

    #[\Override]
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($searchDto->getQuery()) {
            $queryBuilder
                ->orWhere('
                    entity.email LIKE :search
                    OR entity.username LIKE :search
                    OR entity.firstName LIKE :search
                    OR entity.lastName LIKE :search
                ')
                ->setParameter('search', '%'.$searchDto->getQuery().'%')
            ;
        }

        return $queryBuilder;
    }
}
