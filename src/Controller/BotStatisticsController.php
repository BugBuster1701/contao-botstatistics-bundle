<?php

/**
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL-3.0+
 * @see	       https://github.com/BugBuster1701/contao-botstatistics-bundle
 */

namespace BugBuster\BotStatisticsBundle\Controller;

use BugBuster\BotStatistics\BackendStatisticsDetails; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles the dlstats back end routes.
 *
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 *
 * @Route("/bugbuster_botstatistics", defaults={"_scope" = "backend", "_token_check" = true})
 */
class BotStatisticsController extends AbstractController
{
    /**
     * Renders the alerts content.
     *
     * @return Response
     *
     * @Route("/backend_details", name="bugbuster_botstatistics_backend_details")
     */
    public function detailsAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendStatisticsDetails();

        return $controller->run();
    }
}
