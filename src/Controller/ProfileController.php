<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */

class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="user_profile")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function edit_informations()
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
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
        
        return $this->render('profile/index.html.twig');
    }
}
