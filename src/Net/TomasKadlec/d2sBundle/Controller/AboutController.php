<?php
namespace Net\TomasKadlec\d2sBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller
{

    /**
     * @Route("/about", methods={"GET"})
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }
        
}