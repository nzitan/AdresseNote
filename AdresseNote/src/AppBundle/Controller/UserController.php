<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Users;

class UserController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $quest)
    {
        $session = new Session();
        $request = Request::createFromGlobals();
        if($session->get('user') === null){
            $user = new Users;
            $form = $this->createFormBuilder($user)
                ->add('login', TextType::class, array('label' => 'Nom Utilisateur'))
                ->add('password', PasswordType::class, array('label' => 'Mot de Passe'))
                ->add('save', SubmitType::class, array('label' => 'Connexion'))
                ->getForm();
            if($request->getMethod() === "POST"){
                $form->handleRequest($quest);
                if($form->isSubmitted() && $form->isValid()){
                    $tempUser = $this->getDoctrine()->getRepository('AppBundle:Users')->findOneByLogin($form->getData()->getLogin());
                    if(!empty($tempUser)){
                        if($tempUser->getPassword() === md5($form->getData()->getPassword()."hasing")){
                            $session->set('user', $tempUser);
                            header('Refresh: 0; url=/');
                        }
                    }
                }
                return $this->render('home.html.twig', [
                    'form' => $form->createView(), 'title' => 'AdresseNote', 'contents' => ['Contacts']
                ]);
            } else {
                return $this->render('home.html.twig', [
                    'form' => $form->createView(), 'title' => 'AdresseNote', 'contents' => ['Inscription']
                ]);
            }
        } else {
            $user = $session->get('user');
            return $this->render('home.html.twig', [
                'user' => $user, 'title' => 'AdresseNote', 'contents' => ['Contacts', 'Deconnexion']
            ]);
        }
        
        
    }

    /**
     * @Route("/Inscription", name="inscriptionpage")
     * @Method({"GET", "POST"})
     */

    public function InscriptionAction(Request $quest)
    {
        $session = new Session();
        $request = Request::createFromGlobals();
        $newUser = new Users;
        $form = $this->createFormBuilder($newUser)
            ->add('Login', TextType::class, array('label' => 'Nom Utilisateur'))
            ->add('password', PasswordType::class, array('label' => 'Mot de Passe'))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('save', SubmitType::class, array('label' => 'Inscription'))
            ->getForm();
        if($request->getMethod() === "GET"){
            return $this->render('home.html.twig', [
                'form' => $form->createView(), 'title' => "AdresseNote", 'contents' => ['']
            ]);
        } else {
            $form->handleRequest($quest);
            $testUsername = $this->getDoctrine()->getRepository('AppBundle:Users')->findByLogin($form->getData()->getLogin());
            $testEmail = $this->getDoctrine()->getRepository('AppBundle:Users')->findByEmail($form->getData()->getEmail());
            if(empty($testUsername) && empty($testEmail)){
                $newUser->setPassword(md5($form->getData()->getPassword()."hasing"));
                $insert = $this->getDoctrine()->getManager();
                $insert->persist($newUser);
                $insert->flush();
                return $this->render('home.html.twig', [
                    'title' => "AdresseNote", 'contents' => ['Contacts']
                ]);
            } else {
                $errors = [];
                if(!empty($testUsername)){
                    array_push($errors, "Ce nom d'Utilisateur est deja pris");
                }
                if(!empty($testEmail)){
                    array_push($errors,  "Cet email est déja utilisé");
                }
                return $this->render('home.html.twig', [
                    'form' => $form->createView(), 'title' => "AdresseNote", 'errors' => $errors, 'contents' => [null]
                ]);
            }
        }
    }

    /**
     * @Route("/Deconnexion", name="Deconnexionpage")
     * @Method({"GET", "POST"})
     */
    public function DeconnexionAction(){

        $session = new Session();
        $session->set('user', null);
        header('Refresh: 0; url=/');
        return $this->render('home.html.twig', [
            'contents' => [null], 'title' => 'AdresseNote'
        ]);

    }

}
