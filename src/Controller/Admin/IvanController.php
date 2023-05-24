<?php

namespace App\Controller\Admin;

use App\Form\RepAttributionType;
use App\Repository\CustomerCardRepository;
use DateTime;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IvanController extends AbstractController
{


    // route qui affiche tous les trucs en fonction de la date
    #[Route('/admin/ivan', name: 'app_admin_ivan',methods:["POST", "GET"])]
    public function index(CustomerCardRepository $customerCardRepository, Request $request, AdminUrlGenerator $adminUrlGenerator): Response
    {


        $session = $request->getSession();
        
        $queryDate = $request->query->get('date');
        

        // quand on arrive, pas de query et pas de session
        if (($queryDate == NULL) and ($session->get('date') == null)) {
            $date = new DateTime('now');
            $date->modify('+1 day');
            $date = $date->format('Y-m-d');
            $date = new DateTimeImmutable($date);
            $day = $date->format('Y-m-d');
        }
        // si on choisi une date 
        else if ($queryDate != null) {
            $session->set('date', $queryDate);
            $dateEnSession = $session->get('date');
            $date = $dateEnSession;
            $date = new DateTimeImmutable($date);
            $day = $date->format('Y-m-d');
        }
        else if (($queryDate == NULL) and ($session->get('date') != null)) {
            $dateEnSession = $session->get('date');
            $date = $dateEnSession;
            $date = new DateTimeImmutable($date);
            $day = $date->format('Y-m-d');
        }

        $date = new DateTimeImmutable($day . '00:01:00');
        // Récupérer tous les customerCards qui n'ont pas de staff id et qui colle avec la date
        $firstClient = $customerCardRepository->findOneBy(
            [
                'staff' => NULL,
                'meetingAt' => $date
            ]);
        $countNonAttributedClients = count($customerCardRepository->findBy(
            [
                'staff' => NULL,
                'meetingAt' => $date
            ]
        
        ));


        if ($firstClient != NULL) {

            $form = $this->createForm(RepAttributionType::class, $firstClient);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                
                $customerCardRepository->save($firstClient, true);                
                return $this->redirect($this->generateUrl('app_admin_ivan'));

            }
            
            return $this->render('admin/ivan/choixDeAttributionRepresentants.html.twig', [
                'firstClient' => $firstClient,
                'form' => $form,
                'controller_name' => 'IvanController',
                'countNonAttributedClients' => $countNonAttributedClients,
                'date' => $date
            ]);
        }  

        else {
            return $this->render('admin/ivan/choixDeAttributionRepresentants.html.twig', [
                'notClient' => true,
                'controller_name' => 'IvanController',
                'date' => $date
            ]); 
        } 


    }
}
