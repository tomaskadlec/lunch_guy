<?php

namespace Net\TomasKadlec\d2s\UiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $application = $this->get('net_tomas_kadlec_d2s.service_application.application');
        return [
            'restaurants' => [
                'Na Urale' => $application->retrieve('Na Urale'),
                'U Pětníka' => $application->retrieve('U Pětníka'),
            ]
        ];
    }
}
