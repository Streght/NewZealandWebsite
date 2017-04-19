<?php

namespace Uniteam\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

include_once '/semsol/ARC2.php';
use ARC2;

class PagesController extends Controller {

    public function pageAction($page) {

        if ($page == "accueil") {

            $dbpconfig = array(
                "remote_store_endpoint" => "http://dbpedia.org/sparql",
            );

            $store = ARC2::getRemoteStore($dbpconfig);

            $query = '
                PREFIX owl: <http://www.w3.org/2002/07/owl#>
                PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                PREFIX dc: <http://purl.org/dc/elements/1.1/>
                PREFIX : <http://dbpedia.org/resource/>
                PREFIX dbpedia2: <http://dbpedia.org/property/>
                PREFIX dbpedia: <http://dbpedia.org/>
                PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
R
                SELECT ?birthdate WHERE{
                :Lionel_Messi dbpedia2:birthDate ?birthdate
                }';

            $result = $store->query($query, 'rows');


            return $this->render('UniteamPresentationBundle:Pages:accueil.html.twig', array(
                        'currentpage' => 'accueil',
                        'test' => $result
            ));
        }
        if ($page == "presentation") {

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

            $repository = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Country');
            $country = $repository->find(1);

            $repository = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:City');
            $auckland = $repository->findOneByName('Auckland');
            $christchurch = $repository->findOneByName('ChristChurch');
            $wellington = $repository->findOneByName('Wellington');

            $repository = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Language');
            $firstlang = $repository->find(1);
            $secondlang = $repository->find(2);
            $thirdlang = $repository->find(3);

            return $this->render('UniteamPresentationBundle:Pages:presentation.html.twig', array(
                        'currentpage' => 'presentation',
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
                        'pibparhab' => $pibperinhab,
                        'pays' => $country,
                        'villeUn' => $auckland,
                        'villeDeux' => $christchurch,
                        'villeTrois' => $wellington,
                        'langueUn' => $firstlang,
                        'langueDeux' => $secondlang,
                        'langueTrois' => $thirdlang
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
