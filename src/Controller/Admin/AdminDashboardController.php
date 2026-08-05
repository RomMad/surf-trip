<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    #[\Override]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_user_index');
    }

    #[\Override]
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Surf Trip')
        ;
    }

    #[\Override]
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('dashboard.label', 'house');
        yield MenuItem::linkTo(TripCrudController::class, 'trips.label', 'van');
        yield MenuItem::linkTo(SurfSessionCrudController::class, 'surf_sessions.label', 'land-plot');
        yield MenuItem::linkTo(UserCrudController::class, 'users.label', 'users');
        yield MenuItem::linkTo(ResetPasswordRequestCrudController::class, 'reset_password_request.label', 'key-round');
        yield MenuItem::linkToUrl('back_to_site.label', 'square-arrow-right-exit', '/');
    }

    #[\Override]
    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ;
    }

    #[\Override]
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->useCustomIconSet('lucide')
            ->addWebpackEncoreEntry('admin-app')
        ;
    }
}
