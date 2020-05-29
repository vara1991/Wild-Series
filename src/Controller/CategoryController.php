<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/add", name="category_add")
     */
    public function add(Request $request) :Response
    {
        $allCategory= $this->getDoctrine()->getRepository(Category::class)->findAll();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->render('home.html.twig');
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
            'allCategory'=>$allCategory,
        ]);
    }
}
