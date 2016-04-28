<?php

namespace Net\TomasKadlec\LunchGuy\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('NetTomasKadlecLunchGuyBaseBundle:Default:index.html.twig');
    }
}
