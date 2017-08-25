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

namespace MU\NewsModule\HookSubscriber\Base;

use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Base class for form aware hook subscriber.
 */
abstract class AbstractMessageFormAwareHookSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * MessageFormAwareHookSubscriber constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        return 'MUNewsModule';
    }
    
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return FormAwareCategory::NAME;
    }
    
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->translator->__('Message form aware subscriber');
    }

    /**
     * @inheritDoc
     */
    public function getEvents()
    {
        return [
            // Display hook for create/edit forms.
            FormAwareCategory::TYPE_EDIT => 'munewsmodule.form_aware_hook.messages.edit',
            // Process the results of the edit form after the main form is processed.
            FormAwareCategory::TYPE_PROCESS_EDIT => 'munewsmodule.form_aware_hook.messages.process_edit',
            // Display hook for delete forms.
            FormAwareCategory::TYPE_DELETE => 'munewsmodule.form_aware_hook.messages.delete',
            // Process the results of the delete form after the main form is processed.
            FormAwareCategory::TYPE_PROCESS_DELETE => 'munewsmodule.form_aware_hook.messages.process_delete'
        ];
    }
}
