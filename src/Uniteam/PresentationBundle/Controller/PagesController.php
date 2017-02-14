<?php

namespace Uniteam\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller {

    public function pageAction($page) {

        if ($page == "presentation") {
            return $this->render('UniteamPresentationBundle:Pages:presentation.html.twig', array('currentpage' => 'presentation'));
        }
        if ($page == "histoire") {

            $repository = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Famouscharacter')
            ;

            $queen = $repository->findOneByJob('Reine');
            $governor = $repository->findOneByJob('Gouverneur Général');
            $primeminister = $repository->findOneByJob('Premier Ministre');
            $sailor = $repository->findOneByJob('Navigateur');
            $captaisailor = $repository->findOneByJob('Capitaine et navigateur');

            $repository = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Figure')
            ;
            
            $surfacearea = $repository->findOneByName('Superficie totale');
            $watersurfacearea = $repository->findOneByName('Superficie en eau');
            $population = $repository->findOneByName('population totale');
            $popdensity = $repository->findOneByName('densité de population');
            $pib = $repository->findOneByName('PIB');
            $pibperinhab = $repository->findOneByName('PIB par habitant');

            return $this->render('UniteamPresentationBundle:Pages:histoire.html.twig', array('currentpage' => 'histoire',
                        'reine' => $queen,
                        'gouverneur' => $governor,
                        'ministre' => $primeminister,
                        'navigateur' => $sailor,
                        'capitaine' => $captaisailor,
                        'superficie' => $surfacearea,
                        'superficieeau' => $watersurfacearea,
                        'population' => $population,
                        'densitepop' => $popdensity,
                        'pib' => $pib,
                        'pibparhab' => $pibperinhab
            ));
        }
        if ($page == "photos") {
            return $this->render('UniteamPresentationBundle:Pages:photos.html.twig', array('currentpage' => 'photos'));
        }
        if ($page == "sport") {
            return $this->render('UniteamPresentationBundle:Pages:sport.html.twig', array('currentpage' => 'sport'));
        }
        if ($page == "infos") {
            return $this->render('UniteamPresentationBundle:Pages:infos.html.twig', array('currentpage' => 'infos'));
        }
        if ($page == "about") {
            return $this->render('UniteamPresentationBundle:Pages:about.html.twig', array('currentpage' => 'about'));
        } else {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }
    }

}
