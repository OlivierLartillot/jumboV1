<?php

namespace App\Controller\Admin;

use App\Entity\CustomerCard;
use App\Form\RepAttributionType;
use App\Repository\CustomerCardRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IvanController extends AbstractController
{
    #[Route('/admin/ivan', name: 'app_admin_ivan')]
    public function index(CustomerCardRepository $customerCardRepository, Request $request, AdminUrlGenerator $adminUrlGenerator): Response
    {

        // Récupérer tous les customerCards qui n'ont pas de staff id
        $firstClient = $customerCardRepository->findOneBy(['staff' => NULL]);
        $countNonAttributedClients = count($customerCardRepository->findBy(['staff' => NULL]));

        if ($firstClient != NULL) {

            $form = $this->createForm(RepAttributionType::class, $firstClient);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $customerCardRepository->save($firstClient, true);

                return $this->redirect('admin?routeName=app_admin_ivan');
            }

            return $this->render('admin/ivan/index.html.twig', [
                'firstClient' => $firstClient,
                'form' => $form,
                'controller_name' => 'IvanController',
                'countNonAttributedClients' => $countNonAttributedClients
            ]);
        }  

        else {
            return $this->render('admin/ivan/index.html.twig', [
                'notClient' => true,
                'controller_name' => 'IvanController',
            ]); 
        }



    }
}
