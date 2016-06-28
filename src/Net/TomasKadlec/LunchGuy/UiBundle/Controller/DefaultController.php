<?php
namespace Net\TomasKadlec\LunchGuy\UiBundle\Controller;

use GuzzleHttp\Client;
use Net\TomasKadlec\LunchGuy\BaseBundle\Exception\EnhanceYourCalmException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package Net\TomasKadlec\LunchGuy\UiBundle\Controller
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
        $result = [
            'restaurantId' => $restaurantId,
            'result' => $this->getApplication()->retrieve($restaurantId),
            'retrieved' => $this->getApplication()->getRetrieved($restaurantId),
        ];
        return $result;
    }

    /**
     * @Route("/restaurants/{restaurantId}/delete", methods={"GET"})
     */
    public function  deleteAction($restaurantId) {
        $this->getApplication()->invalidate($restaurantId);
        return $this->redirectToRoute("net_tomaskadlec_lunchguy_ui_default_index");
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
     * @Route("/js", methods={"GET"})
     * @Template()
     * @return array
     */
    public function indexJsAction()
    {
        return [
        ];
    }

    /**
     * @Route("/angular", methods={"GET"})
     * @Template()
     * @return array
     */
    public function indexAngularAction()
    {
        return [
        ];
    }


    /**
     * @return \Net\TomasKadlec\LunchGuy\BaseBundle\Service\Application\Application
     */
    protected function getApplication()
    {
        return $this->get('net_tomas_kadlec_lunch_guy_base.service.application');
    }

}