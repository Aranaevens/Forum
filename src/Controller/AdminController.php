<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function index()
    {
        $users = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->getAll();
        
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_delete_user")
     */
    public function deleteUser(User $user, ObjectManager $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $messages = $this->getDoctrine()
                    ->getRepository(Message::class)
                    ->getAllFromUser($user->getId());

        $sujets = $this->getDoctrine()
                    ->getRepository(Sujet::class)
                    ->getAllFromUser($user->getId());

        $prof_supr = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->find(0);

        foreach($messages as $message)
        {
            $message->setAuteur($prof_supr);
        }

        foreach($sujets as $sujet)
        {
            $sujet->setAuteur($prof_supr);
        }

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/{id}/edit", name="admin_edit_user")
     */
    public function edit_account(User $user, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createFormBuilder($user)
                    ->add('pseudo', TextType::class, [
                        'required' => true,
                        'label' => 'Pseudo'])
                    ->add('email', EmailType::class, [
                        'required' => true,
                        'label' => 'Adresse e-mail'])
                    ->add('role', ChoiceType::class, [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Ajouter un rôle',
                        'choices' => ['Modérateur' => 'ROLE_MODERATOR', 'Administrateur' => 'ROLE_ADMIN']])
                    ->add('avatar', TextType::class, [
                        'required' => false,
                        'label' => 'Avatar'])
                    ->add('Valider', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->addRole($form->get('role')->getData());
            // $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/edit_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add", name="admin_add_user")
     */
    public function add_account(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();

        $form = $this->createFormBuilder($user)
                    ->add('pseudo', TextType::class, [
                        'required' => true,
                        'label' => 'Pseudo'])
                    ->add('email', EmailType::class, [
                        'required' => true,
                        'label' => 'Adresse e-mail'])
                    ->add('role', ChoiceType::class, [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Ajouter un rôle',
                        'choices' => ['Modérateur' => 'ROLE_MODERATOR', 'Administrateur' => 'ROLE_ADMIN']])
                    ->add('avatar', TextType::class, [
                        'required' => false,
                        'label' => 'Avatar'])
                    ->add('Valider', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->addRole($form->get('role')->getData());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    'placeholder'
                )
            );
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/add_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
