<?php
namespace Net\TomasKadlec\d2sBundle\Controller;

use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package Net\TomasKadlec\d2sBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @Route("/restaurants/{restaurantId}", methods={"GET"})
     * @Template()
     * @param $restaurant
     */
    public function restaurantAction($restaurantId)
    {
        return [
            'result' => $this->getApplication()->retrieve($restaurantId),
        ];
    }

    /**
     * @Route("/", methods={"GET"})
     * @Route("/restaurants", methods={"GET"})
     * @Template()
     * @return array
     */
    public function indexAction()
    {
        return [
            'restaurants' => $this->getApplication()->getRestaurants(),
        ];
    }

    /**
     * @return \Net\TomasKadlec\d2sBundle\Service\Application\Application
     */
    protected function getApplication()
    {
        return $this->get('net_tomas_kadlec_d2s.service.application');
    }

}