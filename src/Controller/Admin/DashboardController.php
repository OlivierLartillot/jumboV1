<?php

namespace App\Controller\Admin;

use App\Entity\Area;
use App\Entity\CustomerCard;
use App\Entity\MeetingPoint;
use App\Entity\Status;
use App\Entity\Transfer;
use App\Entity\User;
use App\Repository\CustomerCardRepository;
use App\Repository\MeetingPointRepository;
use App\Repository\StatusRepository;
use App\Repository\TransferRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Validator\Constraints\Date;

class DashboardController extends AbstractDashboardController
{

    private $statusRepository;
    private $meetingPointRepository;
    private $userRepository;
    private $customerCardRepository;
    private $transferRepository;

    public function __construct(StatusRepository $statusRepository, 
                                MeetingPointRepository $meetingPointRepository, 
                                UserRepository $userRepository,
                                CustomerCardRepository $customerCardRepository,
                                TransferRepository $transferRepository)
    {
        $this->statusRepository = $statusRepository;       
        $this->meetingPointRepository = $meetingPointRepository;  
        $this->userRepository = $userRepository;
        $this->customerCardRepository = $customerCardRepository;
        $this->transferRepository = $transferRepository;     
    }


    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //

        return $this->render('admin/index.html.twig', [
            'testParam' => "Ceci est une phrase d'essai passée en parametre dans le controleur dashboard"
        ]);
    }

    #[Route('/admin/traitement_csv', name: 'admin_traitement_csv')]
    public function traitement_csv(HttpFoundationRequest $request, EntityManagerInterface $manager): Response
    {

        // test des données recues
            

            // infos sur le csv
            if ($_FILES["fileToUpload"]["error"] > 0) {
                die("Erreur lors de l'upload du fichier. Code d'erreur : " . $_FILES["fileToUpload"]["error"]);
            }
            
            // Vérifier si le fichier a été correctement téléchargé
            if (!file_exists($_FILES["fileToUpload"]["tmp_name"])) {
                die("Fichier non trouvé.");
            }
            
            // Vérifier le type de fichier
            if ($_FILES["fileToUpload"]['type'] != "text/csv") {
                die("l extension du fichier n est pas bonne !");
            }

            // récupération du token
            $submittedToken = $request->request->get('token');


        // 'add-csv' si le token est valide, on peut commencer les traitements !
        if ($this->isCsrfTokenValid('add-csv', $submittedToken)) {
            // a faire dans le traitement
            //load the CSV document from a stream
            /*  $stream = fopen('csv/servicios.csv', 'r'); */
            $csv = Reader::createFromStream(fopen($_FILES["fileToUpload"]["tmp_name"], 'r+'));
            //$csv = Reader::createFromPath($_FILES["fileToUpload"]["tmp_name"], 'r');
            $csv->setDelimiter(',');
            $csv->setHeaderOffset(0);

            //dump($csv->getHeader());
            //build a statement
            $stmt = Statement::create() /* ->offset(10)->limit(20) */ ;

            //query your records from the document
            $records = $stmt->process($csv);
            
            // les entités par défaut
            $status = $this->statusRepository->find(1);
            $user = $this->userRepository->find(1);
            $meetingPoint = $this->meetingPointRepository->find(1);
            
            // début de l'extraction des données du csv
            foreach ($records as $record) {
                // les entrées possédant ce numéro doivent être ignorée
                if (($record['Nº Vuelo/Transporte Origen'] == "XX9999") or 
                    ($record['Nº Vuelo/Transporte Destino'] == "XX9999") or 
                    ($record['Fecha/Hora recogida'] == "XX9999")){
                    continue;
                }

                // extraction de jumboNumber et reservationNumber car ils se trouvent dans la meme case dans le csv 
                $numbers = explode(", ", $record['VARCHAR(24)']);
                $jumboNumber = $numbers[0];
                $reservationNumber = $numbers[1];
                
                // extraction du nombre d'adultes/enfants/bébés car dans la même case dans le csv
                $numeroPasajeros = explode(" ", $record['Número pasajeros']);
                $adultsNumber = $numeroPasajeros[1];
                $childrenNumber = $numeroPasajeros[3];
                $babiesNumber = $numeroPasajeros[5];

                // on essaie de récupérer la fiche pour savoir si on va create or update
                $customerCardResult = $this->customerCardRepository->findOneBy(['reservationNumber' => $reservationNumber]);
                // si l'enregistrement existe déja, on va le mettre a jour
                if ($customerCardResult) {
                    $customerCard = $customerCardResult;
                } 
                else // sinon on va créer un nouvel objet
                {
                    $customerCard = new CustomerCard();
                }

                // enregistrement des données dans la card courante
                    $customerCard->setReservationNumber($reservationNumber);
                    $customerCard->setJumboNumber($jumboNumber);
                    $customerCard->setHolder($record['Titular']);
                    $customerCard->setAgency($record['Agencia']);
                    $customerCard->setAdultsNumber($adultsNumber);
                    $customerCard->setChildrenNumber($childrenNumber);
                    $customerCard->setBabiesNumber($babiesNumber);
                    $customerCard->setStatus($status);
                    $customerCard->setStatusUpdatedAt(new DateTimeImmutable("now"));
                    $customerCard->setStatusUpdatedBy($user);
                    $customerCard->setMeetingPoint($meetingPoint);
                    // meetind At, le lendemain de l'arrivée
                    if ($record['Fecha/Hora Origen']) {
                        $dateTime = explode(" ", $record['Fecha/Hora Origen']);
                        $date = new DateTime($dateTime[0]);
                        $hour = '00:01';
                        $meetingAt = new DateTimeImmutable($date->format('Y-d-m') . $hour);
                        $customerCard->setMeetingAt($meetingAt);
                    }
                    $customerCard->setReservationCancelled(0);
                    
                    // si la carte n existe pas deja on fait le persist sinon doctrine s'en occupe
                    if (!$customerCardResult) {
                        $manager->persist($customerCard);
                    }
                    

                //! traitement des infos de la table transfer

                //définir si c est une arrivée/depart/interHotel
                if ($record['Nº Vuelo/Transporte Origen'] != NULL) {
                    $fechaHora = $record['Fecha/Hora Origen'];
                    $natureTransfer = "Arrivée";
                    $flightNumber = $record['Nº Vuelo/Transporte Origen'];
                } else if ($record['Nº Vuelo/Transporte Destino'] != NULL) {
                    $fechaHora = $record['Fecha/Hora Destino'];
                    $natureTransfer = "Départ";
                    $flightNumber = $record['Nº Vuelo/Transporte Destino'];
                } else {
                    $fechaHora = $record['Fecha/Hora recogida'];
                    $natureTransfer = "Inter Hotel";
                    $flightNumber = NULL;
                }

                // on essaie de récupérer la fiche pour savoir si on va create or update
                $transferResult = $this->transferRepository->findOneBy(['customerCard' => $customerCard]);
                // $transfer = $this->transferRepository
                // si l'enregistrement existe déja, on va le mettre a jour
                if ($transferResult) {
                    $transfer = $transferResult;
                } 
                else // sinon on va créer un nouvel objet
                {
                    $transfer = new Transfer();
                }
                
                $transfer->setServiceNumber($record['Número Servicio']);
                $transfer->setNatureTransfer($natureTransfer);
                $transfer->setDateHour(new DateTimeImmutable($fechaHora));
                $transfer->setFlightNumber($flightNumber);
                $transfer->setFromStart($record['Traslado desde']);
                $transfer->setToArrival($record['Traslado hasta']);
                $transfer->setPrivateCollective($record['Tipo traslado']);
                $transfer->setAdultsNumber($adultsNumber);  
                $transfer->setChildrenNumber($childrenNumber);
                $transfer->setBabiesNumber($babiesNumber);
                if ($transferResult == NULL) {
                    $transfer->setCustomerCard($customerCard);
                    $manager->persist($transfer);
                }

                }
                
                $manager->flush();
                
                // TODO : regarder si un enregistrement a été supprimé
            


            return $this->redirectToRoute('admin');
        }
        
        else {
            dd('erreur de token');
        }
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JumboV1');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Areas', 'fa fa-map', Area::class);
        yield MenuItem::linkToCrud('Meeting Points', 'fa fa-map-marker', MeetingPoint::class);
        yield MenuItem::linkToCrud('Status', 'fa fa-check-square-o', Status::class);
        yield MenuItem::linkToRoute('Attributions Rep', 'fa fa-users', 'app_admin_ivan');
        yield MenuItem::linkToRoute('Stickers', 'fa fa-users', 'app_admin_stickers_par_date');
    }
}
