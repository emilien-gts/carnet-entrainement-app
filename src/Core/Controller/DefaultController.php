<?php

namespace App\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('')]
class DefaultController extends AbstractController
{
    #[Route('')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_dashboard_dashboard_index');
    }
}
