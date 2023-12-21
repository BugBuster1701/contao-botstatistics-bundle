<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2023 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao BotStatistics Bundle
 * @link       https://github.com/BugBuster1701/contao-botstatistics-bundle
 *
 * @license    LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace BugBuster\BotStatisticsBundle\Controller;

use BugBuster\BotStatistics\BackendStatisticsDetails;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles the dlstats back end details route.
 */
#[Route('/bugbuster_botstatistics', defaults: ['_scope' => 'backend', '_token_check' => true])]
class BotStatisticsController extends AbstractController
{
    /**
     * Renders the details content.
     *
     * @return Response
     */
    #[Route('/backend_details', name: 'bugbuster_botstatistics_backend_details')]
    public function detailsAction()
    {
        $this->container->get('contao.framework')->initialize();

        $controller = new BackendStatisticsDetails();

        return $controller->run();
    }
}
