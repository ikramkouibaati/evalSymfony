<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\MembreType;
use App\Repository\MembreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/membre')]
class MembreController extends EvalAbstractController
{
    #[Route('/admin', name: 'app_membre_index', methods: ['GET'])]
    public function index(MembreRepository $membreRepository): Response
    {
        return $this->render('membre/index.html.twig', [
            'membres' => $membreRepository->findAll(),
        ]);
    }

    #[Route('/admin/new', name: 'app_membre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,
                        UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $membre = new Membre();
        $form = $this->createForm(MembreType::class, $membre, [
            'statusChoice' => (new Membre())->getLibelStatusForForm(),
            'labelSubmit' => 'Créer'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $membre->setDateEnregistrement(new \DateTime('now'));
                $membre->setMdp(
                    $userPasswordHasher->hashPassword(
                        $membre,
                        $membre->getMdp()
                    )
                );
                $entityManager->persist($membre);
                $entityManager->flush();
                $this->addFlash('success', 'Le membre a été créé');
                return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->showErrorFlash($membre);
            }
        }

        return $this->render('membre/new.html.twig', [
            'membre' => $membre,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_membre_show', methods: ['GET'])]
    public function show(Membre $membre): Response
    {
        return $this->render('membre/show.html.twig', [
            'membre' => $membre,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_membre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Membre $membre, EntityManagerInterface $entityManager,
                         UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(MembreType::class, $membre, [
            'statusChoice' => (new Membre())->getLibelStatusForForm(),
            'labelSubmit' => 'Modifier'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $membre->setMdp(
                    $userPasswordHasher->hashPassword(
                        $membre,
                        $membre->getMdp()
                    )
                );
                $entityManager->flush();
                $this->addFlash('success', 'Le membre a été modifié');
                return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->showErrorFlash($membre);
            }
        }

        return $this->render('membre/edit.html.twig', [
            'membre' => $membre,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_membre_delete', methods: ['POST'])]
    public function delete(Request $request, Membre $membre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$membre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($membre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
    }
}
