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
use AppBundle\Entity\Contacts;

class ContactController extends Controller
{
	/**
     * @Route("/Contacts", name="contactpage")
     */

	public function ContactsAction(){

		$session = new Session();
		if($session->get('user') === null){
			header('Refresh: 0; url=/');
			return $this->render('home.html.twig', [
            	'contents' => [''], 'title' => 'AdresseNote'
        	]);
		} else {
			$user = $session->get('user');
			$contacts = $this->getDoctrine()->getRepository('AppBundle:Contacts')->findByIdUser($user->getIdUser());
			return $this->render('home.html.twig', [
            	'contents' => ['Deconnexion'], 'title' => 'AdresseNote', 'contacts' => $contacts, 'user' => $user
        	]);
		}
	}

	/**
     * @Route("/addContact", name="addContactpage")
     */

	public function AddContactAction(Request $quest){
		$session = new Session();
		$request = Request::createFromGlobals();
		if($session->get('user') === null){
			header('Refresh: 0; url=/');
			return $this->render('home.html.twig', [
            	'contents' => [null], 'title' => 'AdresseNote'
        	]);
		} else {
			$user = $session->get('user');
			$newContact = new Contacts;
			$form = $this->createFormBuilder($newContact)
				->add('Name', TextType::class, array('label' => 'Nom du contact'))
				->add('Email', EmailType::class, array('label' => 'Email du contact'))
				->add('Adresse', TextType::class, array('label' => 'Adresse du contact'))
				->add('Telephone', TextType::class, array('label' => 'Téléphone du contact'))
				->add('Website', TextType::class, array('label' => 'Site du contact'))
				->add('save', SubmitType::class, array('label' => 'Ajouter'))
				->getForm();
			if($request->getMethod() === "POST"){
				 $form->handleRequest($quest);
                if($form->isSubmitted() && $form->isValid()){
                	$newContact->setIdUser($user->getIdUser());
                	$insert = $this->getDoctrine()->getManager();
                	$insert->persist($newContact);
                	$insert->flush();
                }
                return $this->render('home.html.twig', [
            		'contents' => ['Contacts', 'Deconnexion'], 'title' => 'AdresseNote', 'errors' => ['Le contact a bien été ajouté'], 'form' => $form->createView()
        		]);
			} else {
				return $this->render('home.html.twig', [
            		'contents' => ['Contacts', 'Deconnexion'], 'title' => 'AdresseNote', 'form' => $form->createView()
        		]);
			}
		}
	}

	/**
     * @Route("/modifContact/{idContact}", name="modifContactpage")
     */

	public function ModifContactAction($idContact, Request $quest){
		$session = new Session();
		$request = Request::createFromGlobals();
		if($session->get('user') === null){
			header('Refresh: 0; url=/');
			return $this->render('home.html.twig', [
            	'contents' => [null], 'title' => 'AdresseNote'
        	]);
		} else {
			$user = $session->get('user');
			$contact = $this->getDoctrine()->getRepository('AppBundle:Contacts')->findOneByIdContact($idContact);
			if($contact->getIdUser() === $user->getIdUser()){
				$form = $this->createFormBuilder($contact)
					->add('Name', TextType::class, array('label' => 'Nom du contact'))
					->add('Email', EmailType::class, array('label' => 'Email du contact'))
					->add('Adresse', TextType::class, array('label' => 'Adresse du contact'))
					->add('Telephone', TextType::class, array('label' => 'Téléphone du contact'))
					->add('Website', TextType::class, array('label' => 'Site du contact'))
					->add('save', SubmitType::class, array('label' => 'Ajouter'))
					->getForm();
				if($request->getMethod() === "POST"){
					$form->handleRequest($quest);
					$insert = $this->getDoctrine()->getManager();
                	$insert->persist($contact);
                	$insert->flush();
                	return $this->render('home.html.twig', [
            			'contents' => ['Contacts', 'Deconnexion'], 'title' => 'AdresseNote', 'errors' => ['Le contact a bien été modifié'], 'form' => $form->createView()
        			]);
				} else {
					return $this->render('home.html.twig', [
            		'contents' => ['Contacts', 'Deconnexion'], 'title' => 'AdresseNote', 'form' => $form->createView()
        		]);
				}
			} else {
				header('Refresh: 0; url=/');
				return $this->render('home.html.twig', [
	            	'contents' => [null], 'title' => 'AdresseNote'
	        	]);
			}
		}
	}
}