<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Controller\AbstractCrudController;
use App\Entity\Trip;
use App\Enum\User\SurfLevel;
use App\Form\Type\LocationType;
use App\Form\Type\TitleType;
use App\Form\UserAutocompleteField;
use App\ReadModel\Field;
use App\Security\Voter\TripVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/crud/trip',
    name: 'app.crud.trip.',
)]
final class TripCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trip::class;
    }

    public static function getVoterFqcn(): string
    {
        return TripVoter::class;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setEntityLabelInSingular('trip.label')
            ->setEntityLabelInPlural('trips.label')
        ;
    }

    public static function configureFields(): array
    {
        return [
            new Field('id', 'id.label'),
            new Field('title', 'title.label')
                ->setFormType(TitleType::class),
            new Field('location', 'location.label')
                ->setFormType(LocationType::class)
                ->hideOnForm(),
            new Field('startAt', 'start_at.label', 'd/m/Y'),
            new Field('endAt', 'end_at.label', 'd/m/Y'),
            new Field('requiredLevels', 'required_levels.label')
                ->setFormType(EnumType::class)
                ->setFormTypeOptions([
                    'class' => SurfLevel::class,
                    'multiple' => true,
                    'autocomplete' => true,
                ]),
            new Field('description', 'description.label'),
            new Field('owners', 'owners.label')
                ->setFormType(UserAutocompleteField::class),
            new Field('createdAt', 'created_at.label'),
        ];
    }
}
