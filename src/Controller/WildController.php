<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\User;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{

    /**
     * @Route("/wild", name="wild_index")
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()->getRepository(Program::class)->findAllWithCategories();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @Route("wild/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     */
    public function show($slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     * @param string $categoryName
     * @Route("wild/category/{categoryName}", name="show_category").
     */

    public function showByCategory(string $categoryName):Response
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
        $programs = $this->getDoctrine()->getRepository(Program::class)->findBy(['category' => $category->getId()], ['id' => 'desc'],3,0);

        return $this->render(
            'wild/category.html.twig',[
            'category' => $category,
            'categoryName'  => $categoryName,
            'programs' => $programs,
        ]);

    }

    /**
     * @param $slug
     * @Route("wild/program/{slug}", defaults={"slug" = null}, name="show_program")
     * http://localhost:8000/wild/program/the-promised-neverland
     * http://localhost:8000/wild/program/the-twilight-zone
     */

    public function showByProgram($slug):Response
    {
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => mb_strtolower($slug)]);
        $season = $program->getSeasons();

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $season,
            'slug'  => $slug,
        ]);
    }

    /**
     * @param $id
     * @Route("wild/season/{id}", name="show_season").
     */

    public function showBySeason(int $id):Response
    {
        $season = $this->getDoctrine()->getRepository(Season::class)->find($id);
        $program = $season->getProgram();
        $episode = $season->getEpisodes();

        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episode,
        ]);
    }

    /**
     * @param $id
     * @Route("wild/episode/{id}", name="show_episode").
     */

    public function showEpisode(Episode $episode,Request $request):Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $comments = $episode->getComments();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->render('wild/episode.html.twig',[
            'episode'=>$episode,
            'season'=>$season,
            'program'=>$program,
            'comments'=>$comments,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("wild/delete/{id}", name="wild_comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @param $id
     * @Route("wild/actor/{id}", name="show_actor").
     */

    public function showActor(Actor $actor):Response
    {
        $program = $actor->getPrograms();
        return $this->render('wild/actor.html.twig',[
            'actor'=>$actor,
            'programs' =>$program,
        ]);
    }
}