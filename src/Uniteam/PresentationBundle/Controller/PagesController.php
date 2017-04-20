<?php

namespace Uniteam\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller {

    function updateDBIfNecessary() {

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('count(date)')
                ->from('UniteamPresentationBundle:dbupdatehistory', 'date')
                ->where('date.lastUpdateTime > :last')
                ->setParameter('last', new \DateTime('-1 day'), \Doctrine\DBAL\Types\Type::DATETIME);
        $nbRecordInLast24h = $qb->getQuery()->getResult();

        if ($nbRecordInLast24h[0]['1'] == 0) {
            
            $finished = true;
            $prefix = 'PREFIX owl: <http://www.w3.org/2002/07/owl#>
                PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                PREFIX dc: <http://purl.org/dc/elements/1.1/>
                PREFIX : <http://dbpedia.org/resource/>
                PREFIX dbpedia2: <http://dbpedia.org/property/>
                PREFIX dbpedia: <http://dbpedia.org/>
                PREFIX skos: <http://www.w3.org/2004/02/skos/core#>';
            $connection = $em->getConnection();
            $platform = $connection->getDatabasePlatform();
            
            $connection->executeUpdate($platform->getTruncateTableSQL('language', true));

            $query = $prefix . '
                SELECT ?label WHERE {
                    :New_Zealand dbo:language ?langue.
                    ?langue rdfs:label ?label
                    FILTER (lang(?label) = "fr")
                }
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . urlencode($query)
                    . '&format=json';

            $resultFromDBPediaLanguages = json_decode(
                    $this->request($requestURL), true);

            if ($resultFromDBPediaLanguages == null) {
                $finished = false;
            } else {
                foreach ($resultFromDBPediaLanguages['results']['bindings'] as $result) {
                    $value = new \Uniteam\PresentationBundle\Entity\Language;
                    $value->setName($result['label']['value']);
                    $em->persist($value);
                }
            }

            $connection->executeUpdate($platform->getTruncateTableSQL('city', true));
            
            $query = $prefix . '
                SELECT DISTINCT ?name ?maxElevation WHERE {
                    ?ville dbo:type :Urban_areas_of_New_Zealand .
                    ?ville dbp:name ?name .
                    ?ville dbo:maximumElevation ?maxElevation
                }
                ORDER BY DESC(?maxElevation)
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . urlencode($query)
                    . '&format=json';

            $resultFromDBPediaCities = json_decode(
                    $this->request($requestURL), true);
            
            if ($resultFromDBPediaCities == null) {
                $finished = false;
            } else {
                foreach ($resultFromDBPediaCities['results']['bindings'] as $result) {
                    $value = new \Uniteam\PresentationBundle\Entity\City;
                    $value->setName($result['name']['value']);
                    $value->setElevation($result['maxElevation']['value']);
                    $em->persist($value);
                }
            }

            if($finished == true) {
                $timeOfUpdate = new \Uniteam\PresentationBundle\Entity\dbupdatehistory;
                $timeOfUpdate->setLastUpdateTime(new \DateTime());
                $em->persist($timeOfUpdate);

                $em->flush();
            }
        }
    }

    function request($url) {
        if (!function_exists('curl_init')) {
            die('CURL is not installed!');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function pageAction($page) {

        if ($page == "accueil") {
            return $this->render('UniteamPresentationBundle:Pages:accueil.html.twig', array(
                        'currentpage' => 'accueil'
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

            $prefix = 'PREFIX owl: <http://www.w3.org/2002/07/owl#>
                PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                PREFIX dc: <http://purl.org/dc/elements/1.1/>
                PREFIX : <http://dbpedia.org/resource/>
                PREFIX dbpedia2: <http://dbpedia.org/property/>
                PREFIX dbpedia: <http://dbpedia.org/>
                PREFIX skos: <http://www.w3.org/2004/02/skos/core#>';
            
            $query = $prefix . '
                SELECT DISTINCT ?name ?maxElevation WHERE {
                    ?ville dbo:type :Urban_areas_of_New_Zealand .
                    ?ville dbp:name ?name .
                    ?ville dbo:maximumElevation ?maxElevation
                }
                ORDER BY DESC(?maxElevation)
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . urlencode($query)
                    . '&format=json';

            $resultFromDBPedia = json_decode(
                    $this->request($requestURL), true);

            $cities = array();

            if ($resultFromDBPedia == null) {
                $repository = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UniteamPresentationBundle:City');

                $resultsFromDB = $repository->findAll();

                foreach ($resultsFromDB as $result) {
                    array_push($cities, array($result->getName(), $result->getElevation()));
                }
            } else {
                foreach ($resultFromDBPedia['results']['bindings'] as $result) {
                    array_push($cities, array($result['name']['value'], $result['maxElevation']['value']));
                }
            }

            $query = $prefix . '
                SELECT ?language WHERE {
                    :New_Zealand dbo:language ?langue.
                    ?langue rdfs:label ?language
                    FILTER (lang(?language) = "fr")
                }
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . urlencode($query)
                    . '&format=json';

            $resultFromDBPedia = json_decode(
                    $this->request($requestURL), true);

            $languages = array();

            if ($resultFromDBPedia == null) {
                $repository = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UniteamPresentationBundle:Language');

                $resultsFromDB = $repository->findAll();

                foreach ($resultsFromDB as $result) {
                    array_push($languages, array($result->getName()));
                }
            } else {
                foreach ($resultFromDBPedia['results']['bindings'] as $result) {
                    array_push($languages, array($result['language']['value']));
                }
            }

            $this->updateDBIfNecessary();

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
                        'villes' => $cities,
                        'langues' => $languages
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
