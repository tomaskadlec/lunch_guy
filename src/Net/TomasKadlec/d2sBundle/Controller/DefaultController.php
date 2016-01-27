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
     * @Route("/{restaurantId}", methods={"GET"})
     * @Template()
     * @param $restaurant
     */
    public function restaurantAction($restaurantId)
    {
        $restaurants = $this->getRestaurants();
        $restaurant = $this->getRestaurant($restaurants, $restaurantId);

        $parser = $this->get('net_tomas_kadlec_d2s.service.parser');
        if (!$parser->isSupported($restaurant['parser'])) {
            throw new \RuntimeException('Unsupported format');
        }

        $client = new Client();
        $response = $client->request('GET', $restaurant['uri']);
        if ($response->getStatusCode() == 200) {
            $result = $parser->parse($restaurant['parser'], $response->getBody()->getContents());
        } else {
            new \RuntimeException("Failed. {$response->getBody()->getContents()}");
        }
        return [
            'restaurantId' => $restaurantId,
            'restaurant' => $restaurant,
            'result' => $result,
        ];
    }

    /**
     * @Route("/", methods={"GET"})
     * @Template()
     * @return array
     */
    public function indexAction()
    {
        return [
            'restaurants' => array_keys($this->getRestaurants()),
        ];
    }

    protected function getRestaurants()
    {
        $config = $this->getParameter('d2s');
        if (!isset($config['restaurants'])) {
            throw new \RuntimeException('No configuration');
        }
        return $config['restaurants'];
    }

    protected function getRestaurant(array $restaurants, $restaurant)
    {
        if (!isset($restaurants[$restaurant])) {
            throw new \RuntimeException('No such restaurant');
        }
        return $restaurants[$restaurant];
    }

}