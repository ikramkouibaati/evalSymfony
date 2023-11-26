<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\FiltreSearchType;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vehicule')]
class VehiculeController extends EvalAbstractController
{
    #[Route('/admin', name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        if($this->session->has('idVehiculeConnexion')){
            $this->session->remove('idVehiculeConnexion');
        }
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/admin/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule, [
            'labelSubmit' => 'Créer'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form['photo']->getData() != null){
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $file = $form['photo']->getData();
                $originalExtension = $file->getClientOriginalExtension();
                if (!in_array($originalExtension, $allowedExtensions)) {
                    $form->get('photo')->addError(new FormError("L'image doit avoir comme extension jpg, png ou jpeg"));
                }
            }
            if ($form->isValid()) {;
                $fileSystem = new Filesystem();
                $fileName = rand(1, 999999999).'.'.$file->getClientOriginalExtension();
                $fileSystem->copy($file->getPathname(), 'images/' . $fileName);
                $vehicule->setPhoto('images/'.$fileName);
                $vehicule->setDateEnregistrement(new \DateTime('now'));
                $entityManager->persist($vehicule);
                $entityManager->flush();
                $this->addFlash('success', 'Le véhicule a bien été créé');
                return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->showErrorFlash($vehicule);
            }
        }


        return $this->render('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}/show', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $oldFile = $vehicule->getPhoto();
        $form = $this->createForm(VehiculeType::class, $vehicule, [
            'labelSubmit' => 'Modifier'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form['photo']->getData() != null) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $file = $form['photo']->getData();
                $originalExtension = $file->getClientOriginalExtension();
                if (!in_array($originalExtension, $allowedExtensions)) {
                    $form->get('photo')->addError(new FormError("L'image doit avoir comme extension jpg, png ou jpeg"));
                }
            }
            if ($form->isValid()) {
                try {
                    unlink($oldFile);
                } catch (\Exception $e) {
                }
                $fileSystem = new Filesystem();
                $file = $form['photo']->getData();
                $fileName = rand(1, 999999999).'.'.$file->getClientOriginalExtension();
                $fileSystem->copy($file->getPathname(), 'images/' . $fileName);
                $vehicule->setPhoto('images/'.$fileName);
                $entityManager->flush();
                $this->addFlash('success', 'Le véhicule a bien été modifié');
                return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $vehicule->setPhoto($oldFile);
                $this->showErrorFlash($vehicule);
            }
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}/show', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            try {
                unlink($vehicule->getPhoto());
            } catch (\Exception $e) {
            }
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_vehicule_search')]
    public function search(Request $request): Response
    {
        if(!$this->session->has('date_deb') or !$this->session->has('date_fin')){
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(FiltreSearchType::class, null, []);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $data = $form->getData();
                if($form->get('reinitialiser')->isClicked()){
                    foreach ($data as $key => $value){
                        $data[$key] = null;
                    }
                    $form = $this->createForm(FiltreSearchType::class, null, [])->createView();
                }
                $fitreEmpty = true;
                foreach($data as $key => $value){
                    if($value != null){
                        $fitreEmpty = false;
                        break;
                    }
                }
                if($fitreEmpty == false){
                    $vehicule = $this->vehiculeRepository->findVehiculeByDatesAndFiltre($data, $this->session->get('date_deb'), $this->session->get('date_fin'));
                }
            }
        }
        if(!isset($vehicule)){
            $vehicule = $this->vehiculeRepository->findAllVehiculeFree($this->session->get('date_deb'), $this->session->get('date_fin'));
        }
        return $this->render('vehicule/search.html.twig', [
            'form' => $form,
            'vehicules' => $vehicule
        ]);
    }

    #[Route('/{id}/reservation', name: 'app_reservation')]
    public function reservation(Vehicule $vehicule)
    {
        if(!$this->session->has('date_deb') or !$this->session->has('date_fin')){
            return $this->redirectToRoute('homepage');
        }

        $this->session->set('idVehiculeConnexion', $vehicule->getId());
        return $this->render('reservation/index.html.twig', [
            'vehicules' => $vehicule,
            'dateDeb' => $this->session->has('date_deb') ? $this->session->get('date_deb') : new \DateTime('now'),
            'dateFin' => $this->session->has('date_fin') ? $this->session->get('date_fin') : new \DateTime('now'),
        ]);
    }

    #[Route('/{id}/reservation/create', name: 'app_reservation_create')]
    public function createReservation(Vehicule $vehicule)
    {
        if(!$this->session->has('date_deb') or !$this->session->has('date_fin')){
            return $this->redirectToRoute('homepage');
        }

        $commande = new Commande();
        $commande->setDateEnregistrement(new \DateTime('now'))
            ->setMembre($this->getUser())
            ->setVehicule($vehicule)
            ->setDateHeurDepart($this->session->get('date_deb'))
            ->setDateHeurFin($this->session->get('date_fin'));
        $this->commandeRepository->add($commande, true);
        $this->addFlash('success', 'La commande a bien été créée');
        $this->sendMail($commande->getMembre()->getEmail(), 'Bienvenue sur RENT A CAR', 'reservation', [
            'nomMembre' => $commande->getMembre()->getNom(),
            'prenomMembre' => $commande->getMembre()->getPrenom(),
            'dateEnregistrement' => $commande->getMembre()->getDateEnregistrement(),
            'nomVoiture' => $commande->getVehicule()->getTitre(),
            'dateDebut' => $commande->getDateHeurDepart(),
            'datFin' => $commande->getDateHeurFin()
        ]);
        return $this->redirectToRoute('homepage');
    }

}
