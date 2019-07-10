<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
                        ->getRepository(Categorie::class)
                        ->getAll();
        
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }
}
