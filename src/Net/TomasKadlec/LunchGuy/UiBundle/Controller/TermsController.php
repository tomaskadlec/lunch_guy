<?php

namespace Net\TomasKadlec\LunchGuy\UiBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TermsController extends Controller
{

    /**
     * @Route("/terms", methods={"GET"})
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

}