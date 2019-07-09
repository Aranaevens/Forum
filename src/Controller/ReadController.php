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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/forum")
 */
class ReadController extends AbstractController
{
    /**
     * @Route("/", name="forum_index")
     */
    public function index()
    {
        $categories = $this->getDoctrine()
                        ->getRepository(Categorie::class)
                        ->getAll();
        
        return $this->render('read/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/message/{id}/delete", name="message_delete", methods="DELETE")
     */
    public function deleteMessage(Message $message, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');
        $sujet = $message->getSujet();
        if($message)
        {
            $manager->remove($message);
            $manager->flush();
        }

        return $this->redirectToRoute('message_list', [
            'id' => $sujet->getCategorie()->getId(),
            'id_topic' => $sujet->getId()
        ]);
    }

    /**
     * @Route("/topic/{id}/delete", name="topic_delete", methods="DELETE")
     */
    public function deleteTopic(Sujet $sujet, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');
        $categorie = $sujet->getCategorie();
        if($sujet)
        {
            $manager->remove($sujet);
            $manager->flush();
        }

        return $this->redirectToRoute('topic_list', [
            'id' => $categorie->getId()
        ]);
    }

    /**
     * @Route("/categorie/{id}/delete", name="categorie_delete", methods="DELETE")
     */
    public function deleteCategorie(Categorie $categorie, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($categorie)
        {
            $manager->remove($categorie);
            $manager->flush();
        }

        return $this->redirectToRoute('forum_index');
    }

    /**
     * @Route("/topic/{id}/lock", name="topic_lock", methods="GET")
     */
    public function lockTopic(Sujet $sujet, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $categorie = $sujet->getCategorie();
        if ($sujet->getVerrouiller())
        {
            $sujet->setVerrouiller(false);
        }
        else
        {   
            $sujet->setVerrouiller(true);
        }
        $manager->persist($sujet);
        $manager->flush();
        return $this->redirectToRoute('topic_list', [
            'id' => $categorie->getId()
        ]);
    }

    /**
     * @Route("/{id}/{id_topic}", name="message_list", methods="GET")
     * @Entity("sujet", expr="repository.find(id_topic)")
     */
    public function listMessages(Categorie $categorie, Sujet $sujet, ObjectManager $manager): Response
    {
        $sujet->incrNbvues();
        $manager->persist($sujet);
        $manager->flush();
        $messages = $this->getDoctrine()
                    ->getRepository(Message::class)
                    ->getAllFromSujet($sujet->getId());
        
        return $this->render('read/message_list.html.twig', [
            'topic' => $sujet,
            'categorie' => $categorie,
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/{id}", name="topic_list", methods="GET")
     */
    public function listTopics(Categorie $categorie): Response
    {
        $topics = $this->getDoctrine()
                    ->getRepository(Sujet::class)
                    ->getAllFromCategorie($categorie->getId());
        
        return $this->render('read/topic_list.html.twig', [
            'topics' => $topics,
            'categorie' => $categorie,
        ]);
    }


}
