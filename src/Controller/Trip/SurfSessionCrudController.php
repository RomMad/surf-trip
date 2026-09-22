<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Controller\AbstractCrudController;
use App\Entity\SurfSession;
use App\ReadModel\Field;
use App\Security\Voter\SurfSessionVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/crud/session',
    name: 'app.crud.session.',
)]
final class SurfSessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SurfSession::class;
    }

    protected static function getEntityName(): string
    {
        return 'session';
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setEntityLabelInSingular('surf_session.label')
            ->setEntityLabelInPlural('surf_sessions.label')
        ;
    }

    public static function getVoterFqcn(): string
    {
        return SurfSessionVoter::class;
    }

    public static function configureFields(): array
    {
        return [
            new Field('id', 'id.label'),
            new Field('spot', 'surf_session.spot.label')
                ->setFormTypeOptions([
                    'attr' => [
                        'placeholder' => 'surf_session.spot.placeholder',
                    ],
                ]),
            new Field('startAt', 'start_at.label', 'd/m/Y'),
            new Field('endAt', 'end_at.label', 'd/m/Y'),
            new Field('trip', 'trip.label')
                ->setFormTypeOptions([
                    'autocomplete' => true,
                ]),
            new Field('board', 'surf_session.board.label')
                ->setFormTypeOptions([
                    'attr' => [
                        'placeholder' => 'surf_session.board.placeholder',
                        'maxlength' => 100,
                    ], ]),

            new Field('rating', 'surf_session.rating.label')
                ->setFormTypeOptions([
                    'placeholder' => 'surf_session.rating.placeholder',
                    'expanded' => true,
                ]),
            new Field('objective', 'surf_session.objective.label')
                ->setFormTypeOptions([
                    'attr' => [
                        'placeholder' => 'surf_session.objective.placeholder',
                        'maxlength' => 1000,
                        'rows' => 4,
                    ],
                ]),
            new Field('comment', 'surf_session.comment.label')
                ->setFormTypeOptions([
                    'attr' => [
                        'placeholder' => 'surf_session.comment.placeholder',
                        'maxlength' => 5000,
                        'rows' => 6,
                    ],
                ]),
            new Field('user', 'user.label')
                ->hideOnForm(),
            new Field('createdAt', 'created_at.label')
                ->hideOnIndex(),
            new Field('updatedAt', 'updated_at.label')
                ->hideOnIndex(),
        ];
    }
}
