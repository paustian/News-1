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

namespace MU\NewsModule\Menu\Base;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\UsersModule\Constant as UsersConstant;
use MU\NewsModule\Entity\MessageEntity;

/**
 * This is the item actions menu implementation class.
 */
class AbstractItemActionsMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use TranslatorTrait;

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds the menu.
     *
     * @param FactoryInterface $factory Menu factory
     * @param array            $options List of additional options
     *
     * @return MenuItem The assembled menu
     */
    public function menu(FactoryInterface $factory, array $options = [])
    {
        $menu = $factory->createItem('itemActions');
        if (!isset($options['entity']) || !isset($options['area']) || !isset($options['context'])) {
            return $menu;
        }

        $this->setTranslator($this->container->get('translator.default'));

        $entity = $options['entity'];
        $routeArea = $options['area'];
        $context = $options['context'];

        $permissionHelper = $this->container->get('mu_news_module.permission_helper');
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $entityDisplayHelper = $this->container->get('mu_news_module.entity_display_helper');
        $menu->setChildrenAttribute('class', 'list-inline item-actions');

        $currentUserId = $currentUserApi->isLoggedIn() ? $currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        if ($entity instanceof MessageEntity) {
            $routePrefix = 'munewsmodule_message_';
        
            if ($routeArea == 'admin') {
                $title = $this->__('Preview', 'munewsmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . 'display',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('target', '_blank');
                $menu[$title]->setLinkAttribute('title', $this->__('Open preview page', 'munewsmodule'));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-search-plus');
            }
            if ($context != 'display') {
                $title = $this->__('Details', 'munewsmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'display',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', str_replace('"', '', $entityDisplayHelper->getFormattedTitle($entity)));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-eye');
            }
            if ($permissionHelper->mayEdit($entity)) {
                $title = $this->__('Edit', 'munewsmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Edit this message', 'munewsmodule'));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-pencil-square-o');
                $title = $this->__('Reuse', 'munewsmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => ['astemplate' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Reuse for new message', 'munewsmodule'));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-files-o');
            }
            if ($permissionHelper->mayDelete($entity)) {
                $title = $this->__('Delete', 'munewsmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'delete',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Delete this message', 'munewsmodule'));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-danger');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-trash-o');
            }
            if ($context == 'display') {
                $title = $this->__('Messages list', 'munewsmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'view'
                ]);
                $menu[$title]->setLinkAttribute('title', $title);
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-reply');
            }
        }

        return $menu;
    }
}
