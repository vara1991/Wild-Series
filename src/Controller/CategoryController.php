<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/add", name="category_add")
     */
    public function add(Request $request)
    {
        $allCategory= $this->getDoctrine()->getRepository(Category::class)->findAll();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->render('home.html.twig');
        }

        return $this->render('form/index.html.twig', [
            'form' => $form->createView(),
            'allCategory'=>$allCategory,
        ]);
    }
}
