<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\InscriptionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends EvalAbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $urlVehiculeConnexion =
            $this->session->has('idVehiculeConnexion') ?
                "/vehicule/".$this->session->get('idVehiculeConnexion')."/reservation/" :
                null;

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if(!is_null($error)){
            $error = "Email et/ou mot de passe invalide";
            $this->addFlash('danger', $error);
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'urlVehiculeConnexion' => $urlVehiculeConnexion
        ]);
    }

    #[Route('/déconnexion', name: 'logout')]
    public function logout(Request $request): Response
    {
        $this->security->logout(false);

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    #[Route('/inscription', name: 'app_insription')]
    public function inscription(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $membre = new Membre();
        $membre->setStatut(0);
        $form = $this->createForm(InscriptionType::class, $membre, []);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                $membre->setDateEnregistrement(new \DateTime('now'));

                $membre->setMdp(
                    $userPasswordHasher->hashPassword(
                        $membre,
                        $membre->getMdp()
                    )
                );

                $this->security->login($membre);

                $this->membreRepository->add($membre, true);
                $this->addFlash('success', 'Le compte a bien été créé');
                $this->sendMail($membre->getEmail(), 'Bienvenue sur RENT A CAR', 'bienvenue', [
                    'nomMembre' => $membre->getNom(),
                    'prenomMembre' => $membre->getPrenom(),
                    'dateEnregistrement' => $membre->getDateEnregistrement()
                ]);
                if($this->session->has('idVehiculeConnexion')){
                    return $this->redirectToRoute('app_reservation', ['id'=>$this->session->get('idVehiculeConnexion')]);
                }else{
                    return $this->redirectToRoute('homepage');
                }
            }else{
                $this->showErrorFlash($membre);
            }
        }

        return $this->render('security/register.html.twig', [
            'form' => $form
        ]);
    }
}