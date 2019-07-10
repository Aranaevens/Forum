<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Messages;
use App\Entity\Sujets;

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

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="user_profile")
     * @Route("/{id}", name="that_user_profile")
     */
    public function index(User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $messages = $this->getDoctrine()
                    ->getRepository(Message::class)
                    ->getAllFromUser($user->getId());

        $sujet = $this->getDoctrine()
                    ->getRepository(Sujet::class)
                    ->getAllFromUser($user->getId());
        
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function edit_informations()
    {
        return $this->render('profile/edit_profile.html.twig');
    }

    /**
     * @Route("/edit/account", name="user_account")
     */
    public function edit_account(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
                    ->add('email', EmailType::class, [
                        'required' => true,
                        'label' => 'Adresse e-mail'])
                    ->add('plainCurrentPassword', PasswordType::class, [
                        'required' => true,
                        'mapped' => false
                    ])
                    ->add('plainPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les mots de passe doivent être identiques',
                        'required' => false,
                        'mapped' => false,
                        'first_options'  => ['label' => 'Nouveau mot de passe'],
                        'second_options' => ['label' => 'Répéter le nouveau mot de passe'],])
                    ->add('Valider', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($this->passwordEncoder->isPasswordValid($user, $form->get('plainCurrentPassword')->getData()))
            {
                if (!empty($form->get('plainPassword')->getData()))
                {
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    $manager->persist($user);
                    $manager->flush();
                }    
            }
            return $this->redirectToRoute('forum_index');
        }

        return $this->render('profile/edit_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/avatar", name="user_avatar", methods={"GET", "POST"})
     */
    public function edit_avatar(Request $request, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
       

        $formAvatar = $this->createFormBuilder()
                    ->setAction($this->generateUrl("user_avatar"))
                    ->add('avatar', TextType::class, [
                        'required' => true,
                        'label' => 'Avatar'])
                    ->add('Transmettre', SubmitType::class)
                    ->getForm();
        $formAvatar->handleRequest($request);

        if ($formAvatar->isSubmitted() && $formAvatar->isValid())
        {
            $user = $this->getUser();
            $user->setAvatar($formAvatar->get('avatar')->getData());
           
            $manager->flush();
            return $this->redirectToRoute('forum_index');
        }
        
        return $this->render('profile/edit_avatar.html.twig', [
            'formAvatar' => $formAvatar->createView(),
        ]);
    }
}
