<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{

    /**
     * @Route("/wild", name="wild_index")
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()->getRepository(Program::class)->findAll();

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
    public function show(int $slug): Response
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

    public function showEpisode(Episode $episode):Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig',[
            'episode'=>$episode,
            'season'=>$season,
            'program'=>$program,
        ]);
    }
}