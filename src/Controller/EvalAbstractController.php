<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\MembreRepository;
use App\Repository\VehiculeRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EvalAbstractController extends AbstractController
{
    protected $membreRepository;
    protected $vehiculeRepository;
    protected $commandeRepository;
    protected $session;
    protected $security;
    protected $validator;
    protected $mailer;

    public function __construct(MembreRepository   $membreRepository,
                                VehiculeRepository $vehiculeRepository,
                                CommandeRepository $commandeRepository,
                                RequestStack       $requestStack,
                                Security           $security,
                                ValidatorInterface $validator,
                                MailerInterface    $mailer)
    {
        $this->membreRepository = $membreRepository;
        $this->vehiculeRepository = $vehiculeRepository;
        $this->commandeRepository = $commandeRepository;
        $this->session = $requestStack->getSession();
        $this->security = $security;
        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    public function showErrorFlash($entity){
        $message = "";
        foreach ($this->validator->validate($entity) as $key => $value){
            $message.=$value->getMessage()."<br>";
        }
        $this->addFlash('danger', $message);
    }

    public function sendMail($to, $objet, $htmlTemplate, $context){
        $email = (new TemplatedEmail())
            ->from('ne-pas-reponde@gmail.com')
            ->to($to)
            ->subject($objet)
            ->htmlTemplate('emails/'.$htmlTemplate.'.html.twig')
            ->context($context);

        $this->mailer->send($email);
    }
}