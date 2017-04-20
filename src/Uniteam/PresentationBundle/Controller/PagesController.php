<?php

namespace Uniteam\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller {

    function updateDBIfNecessary() {

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('count(date)')
                ->from('UniteamPresentationBundle:Dbupdatehistory', 'date')
                ->where('date.lastupdatetime > :last')
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
                SELECT ?label WHERE
                {
                    :New_Zealand dbo:language ?langue.
                    ?langue rdfs:label ?label
                    FILTER (lang(?label) = "fr")
                }
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaLanguages = $this->request($requestURL);

            if ($resultFromDBPediaLanguages === false) {
                $finished = false;
            } else {
                $resultFromDBPediaLanguages = \json_decode($resultFromDBPediaLanguages, true);

                foreach ($resultFromDBPediaLanguages['results']['bindings'] as $result) {
                    $value = new \Uniteam\PresentationBundle\Entity\Language;
                    $value->setName($result['label']['value']);
                    $em->persist($value);
                }
            }

            $connection->executeUpdate($platform->getTruncateTableSQL('city', true));

            $query = $prefix . '
                SELECT DISTINCT ?name ?maxElevation WHERE
                {
                    ?ville dbo:type :Urban_areas_of_New_Zealand .
                    ?ville dbp:name ?name .
                    ?ville dbo:maximumElevation ?maxElevation
                }
                ORDER BY DESC(?maxElevation)
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaCities = $this->request($requestURL);

            if ($resultFromDBPediaCities === false) {
                $finished = false;
            } else {
                $resultFromDBPediaCities = \json_decode($resultFromDBPediaCities, true);

                foreach ($resultFromDBPediaCities['results']['bindings'] as $result) {
                    $value = new \Uniteam\PresentationBundle\Entity\City;
                    $value->setName($result['name']['value']);
                    $value->setElevation($result['maxElevation']['value']);
                    $em->persist($value);
                }
            }

            $connection->executeUpdate($platform->getTruncateTableSQL('Dbpediacharacter', true));

            $query = $prefix . '
                SELECT ?nbNaissEnNZ ?nbDecesEnNZ ?name WHERE
                {
                    {
                        SELECT (COUNT(?neEnNZ) as ?nbNaissEnNZ) WHERE
                        {
                            ?neEnNZ dbo:birthPlace :New_Zealand
                        }
                    }
                    {
                        SELECT (COUNT(?decesEnNZ) as ?nbDecesEnNZ)
                            WHERE {
                            ?decesEnNZ dbo:deathPlace :New_Zealand
                        }
                    }
                    {
                        SELECT (SAMPLE(?name) AS ?name) ?member WHERE
                        {
                            ?member dbo:birthPlace :New_Zealand .
                            ?member foaf:name ?name
                            OPTIONAL {
                                ?member2 dbo:deathPlace :New_Zealand
                                FILTER (?member2 = ?member)
                            }
                            FILTER (!bound(?member2))
                            FILTER EXISTS
                            {
                                ?member dbo:deathPlace ?dateDeces
                            }
                            FILTER (lang(?name) = "en")
                        }
                    }
                }
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaCharacter = $this->request($requestURL);

            if ($resultFromDBPediaCharacter === false) {
                $finished = false;
            } else {
                $resultFromDBPediaCharacter = \json_decode($resultFromDBPediaCharacter, true);

                foreach ($resultFromDBPediaCharacter['results']['bindings'] as $result) {
                    $value = new \Uniteam\PresentationBundle\Entity\Dbpediacharacter;
                    $value->setNbnaissennz($result['nbNaissEnNZ']['value']);
                    $value->setNbdecesennz($result['nbDecesEnNZ']['value']);
                    $value->setName($result['name']['value']);
                    $em->persist($value);
                }
            }

            $connection->executeUpdate($platform->getTruncateTableSQL('city', true));

            $query = $prefix . '
                SELECT ?name COUNT(*) AS ?nbfilm WHERE
                {
                    ?titre rdf:type dbo:Film .
                    ?titre dct:subject ?sujet
                    FILTER regex(?sujet, "New_Zealand")
                    FILTER EXISTS { ?titre dbp:starring ?casting}

                    ?titre dbpedia2:starring ?actor .
                    ?actor rdfs:label ?name
                    FILTER (lang(?name) = "en")
                }
                GROUP BY ?name
                ORDER BY DESC(COUNT(*))
                LIMIT 10
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaActor = $this->request($requestURL);

            if ($resultFromDBPediaActor === false) {
                $finished = false;
            } else {
                $resultFromDBPediaActor = \json_decode($resultFromDBPediaActor, true);

                foreach ($resultFromDBPediaActor['results']['bindings'] as $result) {
                    $value = new \Uniteam\PresentationBundle\Entity\Actor;
                    $value->setName($result['name']['value']);
                    $value->setNbfilm($result['nbfilm']['value']);
                    $em->persist($value);
                }
            }

            if ($finished === true) {
                $timeOfUpdate = new \Uniteam\PresentationBundle\Entity\Dbupdatehistory;
                $timeOfUpdate->setLastupdatetime(new \DateTime());
                $em->persist($timeOfUpdate);

                $em->flush();
            }
        }
    }

    function request($url) {
        if (!\function_exists('curl_init')) {
            die('CURL is not installed!');
        }

        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        $response = \curl_exec($ch);
        \curl_close($ch);

        return $response;
    }

    public function pageAction($page) {

        if ($page === "accueil") {
            return $this->render('UniteamPresentationBundle:Pages:accueil.html.twig', ['currentpage' => 'accueil']);
        }
        if ($page === "presentation") {

            $repositoryFamousCharacter = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Famouscharacter')
            ;

            $queen = $repositoryFamousCharacter->findOneByJob('Reine');
            $governor = $repositoryFamousCharacter->findOneByJob('Gouverneur Général');
            $primeminister = $repositoryFamousCharacter->findOneByJob('Premier Ministre');
            $sailor = $repositoryFamousCharacter->findOneByJob('Navigateur');
            $captaisailor = $repositoryFamousCharacter->findOneByJob('Capitaine et navigateur');

            $repositoryFigure = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Figure')
            ;

            $surfacearea = $repositoryFigure->findOneByName('Superficie totale');
            $watersurfacearea = $repositoryFigure->findOneByName('Superficie en eau');
            $population = $repositoryFigure->findOneByName('population totale');
            $popdensity = $repositoryFigure->findOneByName('densité de population');
            $pib = $repositoryFigure->findOneByName('PIB');
            $pibperinhab = $repositoryFigure->findOneByName('PIB par habitant');

            $repositoryCountry = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UniteamPresentationBundle:Country');
            $country = $repositoryCountry->find(1);

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
                SELECT DISTINCT ?name ?maxElevation WHERE
                {
                    ?ville dbo:type :Urban_areas_of_New_Zealand .
                    ?ville dbp:name ?name .
                    ?ville dbo:maximumElevation ?maxElevation
                }
                ORDER BY DESC(?maxElevation)
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaCities = $this->request($requestURL);

            $cities = [];

            if ($resultFromDBPediaCities === false) {
                $repository = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UniteamPresentationBundle:City');

                $resultsFromDB = $repository->findAll();

                foreach ($resultsFromDB as $result) {
                    \array_push($cities, [$result->getName(), $result->getElevation()]);
                }
            } else {
                $resultFromDBPediaCities = \json_decode($resultFromDBPediaCities, true);

                foreach ($resultFromDBPediaCities['results']['bindings'] as $result) {
                    \array_push($cities, [$result['name']['value'], $result['maxElevation']['value']]);
                }
            }

            $query = $prefix . '
                SELECT ?language WHERE
                {
                    :New_Zealand dbo:language ?langue.
                    ?langue rdfs:label ?language
                    FILTER (lang(?language) = "fr")
                }
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaLanguages = $this->request($requestURL);

            $languages = [];

            if ($resultFromDBPediaLanguages === false) {
                $repository = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UniteamPresentationBundle:Language');

                $resultsFromDB = $repository->findAll();

                foreach ($resultsFromDB as $result) {
                    \array_push($languages, [$result->getName()]);
                }
            } else {
                $resultFromDBPediaLanguages = \json_decode($resultFromDBPediaLanguages, true);

                foreach ($resultFromDBPediaLanguages['results']['bindings'] as $result) {
                    \array_push($languages, [$result['language']['value']]);
                }
            }

            $query = $prefix . '
                SELECT ?nbNaissEnNZ ?nbDecesEnNZ ?name WHERE
                {
                    {
                        SELECT (COUNT(?neEnNZ) as ?nbNaissEnNZ) WHERE
                        {
                            ?neEnNZ dbo:birthPlace :New_Zealand
                        }
                    }
                    {
                        SELECT (COUNT(?decesEnNZ) as ?nbDecesEnNZ)
                            WHERE {
                            ?decesEnNZ dbo:deathPlace :New_Zealand
                        }
                    }
                    {
                        SELECT (SAMPLE(?name) AS ?name) ?member WHERE
                        {
                            ?member dbo:birthPlace :New_Zealand .
                            ?member foaf:name ?name
                            OPTIONAL {
                                ?member2 dbo:deathPlace :New_Zealand
                                FILTER (?member2 = ?member)
                            }
                            FILTER (!bound(?member2))
                            FILTER EXISTS
                            {
                                ?member dbo:deathPlace ?dateDeces
                            }
                            FILTER (lang(?name) = "en")
                        }
                    }
                }
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaCharacter = $this->request($requestURL);

            $character = [];

            if ($resultFromDBPediaCharacter === false) {
                $repository = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UniteamPresentationBundle:Dbpediacharacter');

                $resultsFromDB = $repository->findAll();

                $nbNaissance = $resultsFromDB[0]->getNbnaissennz();
                $nbDeces = $resultsFromDB[0]->getNbdecesennz();

                foreach ($resultsFromDB as $result) {
                    \array_push($character, [$result->getName()]);
                }
            } else {
                $resultFromDBPediaCharacter = \json_decode($resultFromDBPediaCharacter, true);

                $nbNaissance = $resultFromDBPediaCharacter['results']['bindings'][0]['nbNaissEnNZ']['value'];
                $nbDeces = $resultFromDBPediaCharacter['results']['bindings'][0]['nbDecesEnNZ']['value'];

                foreach ($resultFromDBPediaCharacter['results']['bindings'] as $result) {
                    \array_push($character, [$result['name']['value']]);
                }
            }

            $query = $prefix . '
                SELECT ?name COUNT(*) AS ?nbfilm WHERE
                {
                    ?titre rdf:type dbo:Film .
                    ?titre dct:subject ?sujet
                    FILTER regex(?sujet, "New_Zealand")
                    FILTER EXISTS { ?titre dbp:starring ?casting}

                    ?titre dbpedia2:starring ?actor .
                    ?actor rdfs:label ?name
                    FILTER (lang(?name) = "en")
                }
                GROUP BY ?name
                ORDER BY DESC(COUNT(*))
                LIMIT 10
            ';

            $requestURL = 'http://dbpedia.org/sparql?'
                    . 'query=' . \urlencode($query)
                    . '&format=json';

            $resultFromDBPediaActors = $this->request($requestURL);

            $actors = [];

            if ($resultFromDBPediaActors === false) {
                $repository = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('UniteamPresentationBundle:Actor');

                $resultsFromDB = $repository->findAll();

                foreach ($resultsFromDB as $result) {
                    \array_push($actors, [$result->getName(), $result->getNbfilm()]);
                }
            } else {
                $resultFromDBPediaActors = \json_decode($resultFromDBPediaActors, true);

                foreach ($resultFromDBPediaActors['results']['bindings'] as $result) {
                    \array_push($actors, [$result['name']['value'], $result['nbfilm']['value']]);
                }
            }

            $this->updateDBIfNecessary();

            return $this->render('UniteamPresentationBundle:Pages:presentation.html.twig', ['currentpage' => 'presentation',
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
                        'langues' => $languages,
                        'characters' => $character,
                        'nbnaissance' => $nbNaissance,
                        'nbdeces' => $nbDeces,
                        'acteurs' => $actors,
            ]);
        }
        if ($page === "photos") {
            return $this->render('UniteamPresentationBundle:Pages:photos.html.twig', ['currentpage' => 'photos']);
        }
        if ($page === "sport") {
            return $this->render('UniteamPresentationBundle:Pages:sport.html.twig', ['currentpage' => 'sport']);
        }
        if ($page === "infos") {
            return $this->render('UniteamPresentationBundle:Pages:infos.html.twig', ['currentpage' => 'infos']);
        }
        if ($page === "about") {
            return $this->render('UniteamPresentationBundle:Pages:about.html.twig', ['currentpage' => 'about']);
        } else {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }
    }

}
