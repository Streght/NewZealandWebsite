<?php

namespace Uniteam\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller {

    public function pageAction($page) {

        if ($page == "presentation") {
            return $this->render('UniteamPresentationBundle::presentation.html.twig');
        }
        if ($page == "histoire") {
            return $this->render('UniteamPresentationBundle:Pages:histoire.html.twig', array('currentpage' => 'histoire'));
        }
        if ($page == "photos") {
            return $this->render('UniteamPresentationBundle:Pages:histoire.html.twig', array('currentpage' => 'photos'));
        }
        if ($page == "sport") {
            return $this->render('UniteamPresentationBundle:Pages:histoire.html.twig', array('currentpage' => 'sport'));
        }
        if ($page == "infos") {
            return $this->render('UniteamPresentationBundle:Pages:histoire.html.twig', array('currentpage' => 'infos'));
        }
        if ($page == "about") {
            return $this->render('UniteamPresentationBundle:Pages:histoire.html.twig', array('currentpage' => 'about'));
        } else {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }
    }

}
