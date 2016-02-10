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

    /**
     * @Route("/contributors", methods={"GET"})
     * @Template()
     */
    public function contributorsAction()
    {
        try {
            $data = $this
                ->get('net_tomas_kadlec_d2s.service.contributors')
                ->getContributors();
            return [
                'contributors' => $data['contributors'],
            ];
        } catch (\RuntimeException $e) {
            return [];
        }
    }

}