<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Sujet;
use App\Entity\Message;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/edit")
 */
class WriteController extends AbstractController
{

    /**
     * @Route("/topic/{id_cate}/add", name="topic_add")
     * @Entity("categorie", expr="repository.find(id_cate)")
     */
    public function addTopic(Categorie $categorie, Request $request, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $sujet = new Sujet();

        $form = $this->createFormBuilder($sujet)
                    ->add('titre', TextType::class, [
                        'required' => true
                    ])
                    ->add('body',TextareaType::class, [
                        'mapped' => false,
                        'required' => true,
                        'attr' => array('class' => 'ckeditor')
                    ])
                    ->add('Poster', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $sujet->setAuteur($user)
                    ->setCategorie($categorie)
                    ->setNbVues(0)
                    ->setVerrouiller(false);
            // $message->setDateInscription(new \DateTime);
            $manager->persist($sujet);
            $message = new Message();
            $message->setBody($form->get('body')->getData())
                    ->setAuteur($user)
                    ->setSujet($sujet);
            // $sujet->addMessage($message);
            
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('message_list', [
                'id' => $categorie->getId(),
                'id_topic' => $sujet->getId()
            ]);
        }
        
        return $this->render('write/new_sujet.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/topic/{id}/edit", name="topic_edit")
     */
    public function editTopic(Sujet $sujet, Request $request, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        if ($user != $sujet->getAuteur() || $sujet->getVerrouiller())
        {
            $this->denyAccessUnlessGranted('ROLE_MODERATOR');
        }
        $message = $sujet->getMessages()[0];
        $form = $this->createFormBuilder($sujet)
                    ->add('titre', TextType::class, [
                        'required' => true
                    ])
                    ->add('body',TextareaType::class, [
                        'data' => $message->getBody(),
                        'mapped' => false,
                        'required' => true,
                        'attr' => array('class' => 'ckeditor')
                    ])
                    ->add('Poster', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($sujet);
            $message->setBody($form->get('body')->getData());
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('message_list', [
                'id' => $sujet->getCategorie()->getId(),
                'id_topic' => $sujet->getId()
            ]);
        }
        
        return $this->render('write/edit_sujet.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/message/{id_topic}/add", name="message_add")
     * @Entity("sujet", expr="repository.find(id_topic)")
     */
    public function addMessage(Sujet $sujet, Request $request, ObjectManager $manager): Response
    {
        if ($sujet->getVerrouiller())
        {
            $this->denyAccessUnlessGranted('ROLE_MODERATOR');
        }
        
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $message = new Message();

        $form = $this->createFormBuilder($message)
                    ->add('body',TextareaType::class, [
                        'required' => true,
                        'attr' => array('class' => 'ckeditor')
                    ])
                    ->add('Poster', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $message->setAuteur($user)
                    ->setSujet($sujet);
            
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('message_list', [
                'id' => $sujet->getCategorie()->getId(),
                'id_topic' => $sujet->getId()
            ]);
        }
        
        return $this->render('write/new_message.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/message/{id}/edit", name="message_edit")
     */
    public function editMessage(Message $message, Request $request, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $sujet = $message->getSujet();
        if ($user != $message->getAuteur() || $sujet->getVerrouiller())
        {
            $this->denyAccessUnlessGranted('ROLE_MODERATOR');
        }
        $form = $this->createFormBuilder($message)
                    ->add('body',TextareaType::class, [
                        'required' => true,
                        'attr' => array('class' => 'ckeditor')
                    ])
                    ->add('Poster', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('message_list', [
                'id' => $sujet->getCategorie()->getId(),
                'id_topic' => $sujet->getId()
            ]);
        }
        
        return $this->render('write/edit_message.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categorie/add", name="categorie_add")
     * @Route("/categorie/{id}/add", name="categorie_edit")
     */
    public function addCategorie(Categorie $categorie = null, Request $request, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$categorie)
        {
            $categorie = new Categorie();
        }

        $form = $this->createFormBuilder($categorie)
                    ->add('titre',TextType::class, [
                        'required' => true
                    ])
                    ->add('description',TextareaType::class, [
                        'required' => true,
                        'attr' => array('class' => 'ckeditor')
                    ])
                    ->add('Poster', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($categorie);
            $manager->flush();

            return $this->redirectToRoute('forum_index');
        }
        
        return $this->render('write/editor_categorie.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

