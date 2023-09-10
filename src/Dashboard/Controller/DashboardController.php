<?php

namespace App\Dashboard\Controller;

use App\Session\Controller\SessionCrudController;
use App\Session\Entity\Exercise;
use App\Session\Entity\Session;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractDashboardController
{
    #[Route('/dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        $dashboard = Dashboard::new();
        $dashboard->setTitle('Carnet d\'entrainement');

        return $dashboard;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Séance'),
            MenuItem::linkToCrud('Séances', 'fa fa-dumbbell', Session::class)
                ->setQueryParameter('view', SessionCrudController::INDEX_VIEW_NOT_ARCHIVED),
            MenuItem::linkToCrud('Exercices', 'fa fa-dumbbell', Exercise::class),
        ];
    }
}
