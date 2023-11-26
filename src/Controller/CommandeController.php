<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends EvalAbstractController
{
    #[Route('/admin', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/admin/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande, [
            'labelSubmit' => 'Créer'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $commande->setDateEnregistrement(new \DateTime('now'));
                $entityManager->persist($commande);
                $entityManager->flush();
                $this->addFlash('success', 'La commande a bien été créée');
                return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->showErrorFlash($commande);
            }
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}/show', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande, [
            'labelSubmit' => 'Modifier'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'La commande a bien été modifiée');
                return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->showErrorFlash($commande);
            }
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
