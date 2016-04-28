<?php

namespace Net\TomasKadlec\LunchGuy\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends FOSRestController
{

    /**
     * Returns all configured restaurant IDs
     *
     * @Get("/restaurants")
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which restaurants are returned.")
     * @QueryParam(name="limit", requirements="\d+", default=3, description="Number of restaurants to return.")
     *
     * @ApiDoc(
     *   description="Returns all configured restaurant IDs as an array.",
     *   parameters={
     *       {"name"="offset", "dataType"="integer", "required"=false},
     *       {"name"="limit", "dataType"="integer", "required"=false}
     *   },
     *   statusCodes={
     *         200="Returned when successful"
     *   }
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllRestaurantsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $restaurants = $this->getApplication()->getRestaurants();
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        if (!empty($offset)) {
            $restaurants = array_slice($restaurants, $offset, $limit);
        }

        $view = $this
            ->view($restaurants, 200)
            ->setTemplateVar('restaurants');

        return $this->handleView($view);
    }

    /**
     * Returns a restaurant
     *
     * @ApiDoc(
     *   description="Returns an instance of Restaurant",
     *   statusCodes={
     *         200="Returned when successful"
     *   }
     * )
     * @param $restaurantId
     */
    public function getRestaurantsAction($restaurantId)
    {
        $application = $this->getApplication();
        if (!$application->isRestaurant($restaurantId))
            return $this->handleView($this->view(null, 404));

        $data = [
            'links' => [
                'self' => $this->generateUrl('get_restaurants', ['restaurantId' => $restaurantId], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'data' => [
                'type' => 'restaurant',
                'id' => $restaurantId,
                'attributes' => [
                    'url' => $application->getRestaurantUri($restaurantId),
                ],
                'relationships' => [
                    'menu' => [
                        'links' => [
                            'self' => $this->generateUrl('get_restaurants_menu', ['restaurantId' => $restaurantId], UrlGeneratorInterface::ABSOLUTE_URL)
                        ]
                    ]
                ]
            ],
        ];

        $view = $this
            ->view($data, 200);

        return $this->handleView($view);
    }

    /**
     * Returns a menu of a restaurant
     *
     * @ApiDoc(
     *   description="Returns a menu of a restaurant",
     *   statusCodes={
     *         200="Returned when successful"
     *   }
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRestaurantsMenuAction($restaurantId)
    {

        $application = $this->getApplication();
        if (!$application->isRestaurant($restaurantId))
            return $this->handleView($this->view(null, 404));

        $data = [
            'links' => [
                'self' => $this->generateUrl('get_restaurants_menu', ['restaurantId' => $restaurantId], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'data' => [
                'type' => 'menu',
                'id' => $restaurantId,
                'attributes' => [
                    'title' => $restaurantId,
                    'cached' => ($application->getRetrieved($restaurantId) instanceof \DateTime ? $application->getRetrieved($restaurantId)->format('c') : false),
                    'content' => $application->retrieve($restaurantId),
                ],
                'relationships' => [
                    'restaurant' => [
                        'links' => [
                            'self' => $this->generateUrl('get_restaurants', ['restaurantId' => $restaurantId], UrlGeneratorInterface::ABSOLUTE_URL)
                        ]
                    ]
                ]
            ],
        ];

        $view = $this
            ->view($data, 200);

        return $this->handleView($view);
    }

    /**
     * Removes cached menu retrieved from restaurant's web page
     *
     * @ApiDoc(
     *   description="Removes cached menu retrieved from restaurant's web page",
     *   statusCodes={
     *         204="Cached data was removed",
     *         429="Too many requests. Menu can be invalidated once in 3 minutes at most.",
     *         501="Application does not use cache at all or cache is empty"
     *   }
     * )
     * @param $restaurantId
     */
    public function deleteRestaurantMenuAction($restaurantId)
    {
        $application = $this->getApplication();
        if (!$application->isRestaurant($restaurantId))
            return $this->handleView($this->view(null, 404));

        try {
            $result = $application->invalidate($restaurantId);
            if ($result)
                return $this->handleView($this->view(null));
            else
                return $this->handleView($this->view(null, 501));
        } catch (\RuntimeException $e) {
            return $this->handleView($this->view(null, 429));
        }
    }

    /**
     * @return \Net\TomasKadlec\LunchGuy\BaseBundle\Service\Application\Application
     */
    protected function getApplication()
    {
        return $this->get('net_tomas_kadlec_lunch_guy_base.service.application');
    }
}
