<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthorizationCheckerInterface $authChecker): Response
    {
        if ($authChecker->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('forum_index');
        }
        
        $user = new User();
        $form = $this->createFormBuilder($user)
                    ->add('pseudo', TextType::class, [
                        'required' => true,
                        'label' => 'Pseudo'])
                    ->add('email', EmailType::class, [
                        'required' => true,
                        'label' => 'Adresse e-mail'])
                    ->add('plainPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les mots de passe doivent être identiques',
                        'required' => true,
                        'mapped' => false,
                        'first_options'  => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Répéter le mot de passe'],])
                    ->add('Valider', SubmitType::class)
                    ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // $user->setDateInscription(new \DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
