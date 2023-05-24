<?php

namespace App\Controller;

use App\Entity\Any;
use App\Form\AnyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Request $request): Response
    {

        $any = new Any();
        $form = $this->createForm(AnyType::class, $any);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash(
                'success',
                'Your registration has been taken into account'
            );


        }



        return $this->render('main/index.html.twig', [
            'infos_client' => $any,
            'form' => $form,
            'controller_name' => 'MainController',
        ]);
    }
}
