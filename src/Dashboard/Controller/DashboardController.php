<?php

namespace App\Dashboard\Controller;

use App\Core\Controller\BaseViewCrudController;
use App\Program\Entity\Program;
use App\Session\Entity\Exercise;
use App\Session\Entity\Session;
use App\Training\Entity\Training;
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
        // Dashboard
        yield MenuItem::linkToDashboard('label.dashboard', 'fa fa-home');

        // Program
        yield MenuItem::section('label.trainings');
        yield MenuItem::linkToCrud('label.trainings', 'fa fa-person-running', Training::class);

        // Program
        yield MenuItem::section('label.programs');
        yield MenuItem::linkToCrud('label.programs', 'fa fa-dumbbell', Program::class)
            ->setQueryParameter('view', BaseViewCrudController::VIEW_NOT_ARCHIVED);

        // Session & Exercise
        yield MenuItem::section('label.sessions');
        yield MenuItem::linkToCrud('label.sessions', 'fa fa-dumbbell', Session::class)
            ->setQueryParameter('view', BaseViewCrudController::VIEW_NOT_ARCHIVED);
        yield MenuItem::linkToCrud('label.exercises', 'fa fa-dumbbell', Exercise::class);
    }
}
