<?php

/**
 * @copyright  Glen Langer 2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL-3.0+
 * @see	       https://github.com/BugBuster1701/contao-botstatistics-bundle
 */

namespace BugBuster\BotStatisticsBundle\Controller;

use BugBuster\DLStats\BackendDlstats; //TODO
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the dlstats back end routes.
 *
 * @copyright  Glen Langer 2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 *
 * @Route("/dlstats", defaults={"_scope" = "backend", "_token_check" = true})     //TODO
 */
class BotStatisticsController extends Controller
{
    /**
     * Renders the alerts content.
     *
     * @return Response
     *
     * @Route("/details", name="dlstats_backend_details")
     */
    public function detailsAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendDlstats(); //TODO

        return $controller->run();
    }
}
