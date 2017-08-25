<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zikula\Collection\Container;
use Zikula\Core\Event\GenericEvent;
use Zikula\Provider\AggregateItem;
use MU\NewsModule\Helper\WorkflowHelper;

/**
 * Event handler implementation class for special purposes and 3rd party api support.
 */
abstract class AbstractThirdPartyListener implements EventSubscriberInterface
{
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * ThirdPartyListener constructor.
     *
     * @param WorkflowHelper $workflowHelper WorkflowHelper service instance
     *
     * @return void
     */
    public function __construct(WorkflowHelper $workflowHelper)
    {
        $this->workflowHelper = $workflowHelper;
    }
    
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            'get.pending_content'                   => ['pendingContentListener', 5],
            'module.content.gettypes'               => ['contentGetTypes', 5],
            'module.scribite.editorhelpers'         => ['getEditorHelpers', 5],
            'moduleplugin.tinymce.externalplugins'  => ['getTinyMcePlugins', 5],
            'moduleplugin.ckeditor.externalplugins' => ['getCKEditorPlugins', 5]
        ];
    }
    
    /**
     * Listener for the `get.pending_content` event with registration requests and
     * other submitted data pending approval.
     *
     * When a 'get.pending_content' event is fired, the Users module will respond with the
     * number of registration requests that are pending administrator approval. The number
     * pending may not equal the total number of outstanding registration requests, depending
     * on how the 'moderation_order' module configuration variable is set, and whether e-mail
     * address verification is required.
     * If the 'moderation_order' variable is set to require approval after e-mail verification
     * (and e-mail verification is also required) then the number of pending registration
     * requests will equal the number of registration requested that have completed the
     * verification process but have not yet been approved. For other values of
     * 'moderation_order', the number should equal the number of registration requests that
     * have not yet been approved, without regard to their current e-mail verification state.
     * If moderation of registrations is not enabled, then the value will always be 0.
     * In accordance with the 'get_pending_content' conventions, the count of pending
     * registrations, along with information necessary to access the detailed list, is
     * assemped as a {@link Zikula_Provider_AggregateItem} and added to the event
     * subject's collection.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param GenericEvent $event The event instance
     */
    public function pendingContentListener(GenericEvent $event)
    {
        $modname = 'MUNewsModule';
        $useJoins = false;
        
        $collection = new Container($modname);
        $amounts = $this->workflowHelper->collectAmountOfModerationItems();
        if (count($amounts) > 0) {
            foreach ($amounts as $amountInfo) {
                $aggregateType = $amountInfo['aggregateType'];
                $description = $amountInfo['description'];
                $amount = $amountInfo['amount'];
                $viewArgs = [
                    'workflowState' => $amountInfo['state']
                ];
                $aggregateItem = new AggregateItem($aggregateType, $description, $amount, $amountInfo['objectType'], 'adminview', $viewArgs);
                $collection->add($aggregateItem);
            }
        
            // add collected items for pending content
            if ($collection->count() > 0) {
                $event->getSubject()->add($collection);
            }
        }
    }
    
    /**
     * Listener for the `module.content.gettypes` event.
     *
     * This event occurs when the Content module is 'searching' for Content plugins.
     * The subject is an instance of Content_Types.
     * You can register custom content types as well as custom layout types.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param \Zikula_Event $event The event instance
     */
    public function contentGetTypes(\Zikula_Event $event)
    {
        // intended is using the add() method to add a plugin like below
        $types = $event->getSubject();
        
        
        // plugin for showing a single item
        $types->add('MUNewsModule_ContentType_Item');
        
        // plugin for showing a list of multiple items
        $types->add('MUNewsModule_ContentType_ItemList');
    }
    
    /**
     * Listener for the `module.scribite.editorhelpers` event.
     *
     * This occurs when Scribite adds pagevars to the editor page.
     * MUNewsModule will use this to add a javascript helper to add custom items.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param \Zikula_Event $event The event instance
     */
    public function getEditorHelpers(\Zikula_Event $event)
    {
        // intended is using the add() method to add a helper like below
        $helpers = $event->getSubject();
        
        $helpers->add(
            [
                'module' => 'MUNewsModule',
                'type'   => 'javascript',
                'path'   => 'modules/MU/NewsModule/Resources/public/js/MUNewsModule.Finder.js'
            ]
        );
    }
    
    /**
     * Listener for the `moduleplugin.tinymce.externalplugins` event.
     *
     * Adds external plugin to TinyMCE.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param \Zikula_Event $event The event instance
     */
    public function getTinyMcePlugins(\Zikula_Event $event)
    {
        // intended is using the add() method to add a plugin like below
        $plugins = $event->getSubject();
        
        $plugins->add(
            [
                'name' => 'munewsmodule',
                'path' => 'modules/MU/NewsModule/Resources/scribite/TinyMce/munewsmodule/plugin.js'
            ]
        );
    }
    
    /**
     * Listener for the `moduleplugin.ckeditor.externalplugins` event.
     *
     * Adds external plugin to CKEditor.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param \Zikula_Event $event The event instance
     */
    public function getCKEditorPlugins(\Zikula_Event $event)
    {
        // intended is using the add() method to add a plugin like below
        $plugins = $event->getSubject();
        
        $plugins->add(
            [
                'name' => 'munewsmodule',
                'path' => 'modules/MU/NewsModule/Resources/scribite/CKEditor/munewsmodule/',
                'file' => 'plugin.js',
                'img'  => 'ed_munewsmodule.gif'
            ]
        );
    }
}
