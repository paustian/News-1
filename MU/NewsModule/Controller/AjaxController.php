<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Controller;

use MU\NewsModule\Controller\Base\AbstractAjaxController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Ajax controller implementation class.
 *
 * @Route("/ajax")
 */
class AjaxController extends AbstractAjaxController
{
    
    /**
     * @inheritDoc
     * @Route("/getItemListFinder", methods = {"GET"}, options={"expose"=true})
     */
    public function getItemListFinderAction(Request $request)
    {
        return parent::getItemListFinderAction($request);
    }
    
    /**
     * @inheritDoc
     * @Route("/checkForDuplicate", methods = {"GET"}, options={"expose"=true})
     */
    public function checkForDuplicateAction(Request $request)
    {
        return parent::checkForDuplicateAction($request);
    }
    
    /**
     * @inheritDoc
     * @Route("/toggleFlag", methods = {"POST"}, options={"expose"=true})
     */
    public function toggleFlagAction(Request $request)
    {
        return parent::toggleFlagAction($request);
    }
    
    /**
     * @inheritDoc
     * @Route("/updateSortPositions", methods = {"POST"}, options={"expose"=true})
     */
    public function updateSortPositionsAction(Request $request)
    {
        return parent::updateSortPositionsAction($request);
    }

    // feel free to add your own ajax controller methods here
}
