<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\Sujet;

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


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->redirectToRoute('forum_index');
    }

    public function research_form(): Response
    {
        $form = $this->createFormBuilder()
                    ->setAction($this->generateUrl("search_results"))
                    ->add('search', TextType::class, [
                        'required' => true,
                        'attr' => array(
                            'class' => 'uk-search-input',
                            'placeholder' => 'Rechercher...',
                            'type' => 'search',
                        ),
                        'label' => ''
                    ])
                    ->getForm();

        return $this->render('home/search.html.twig', [
            'form' => $form->createView(),
        ]);           
    }

    /**
     * @Route("/search", name="search_results")
     */
    public function research_handling(Request $request): Response
    {
        $pattern = trim($request->request->get('form')['search']);
        dump($pattern);
        
        $posts = $this->getDoctrine()
                        ->getRepository(Message::class)
                        ->getAllFromSearch($pattern);
        $topics = $this->getDoctrine()
                        ->getRepository(Sujet::class)
                        ->getAllFromSearch($pattern);

        return $this->render('home/search_result.html.twig', [
            'pattern' => $pattern,
            'posts' => $posts,
            'topics' => $topics,
        ]);
    }
}
