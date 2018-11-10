<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepage-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule;

use MU\NewsModule\Base\AbstractNewsModuleInstaller;

/**
 * Installer implementation class.
 */
class NewsModuleInstaller extends AbstractNewsModuleInstaller
{
    /**
     * @inheritDoc
     */
    public function upgrade($oldVersion)
    {
        if ($oldVersion == '1.0.0') {
            $this->addFlash('error', 'Update from 1.0.0 to 1.2.0 is not supported. Please update to 1.1.0 first.');

            return false;
        }

        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.1.0':

                // TODO update from 1.1.0 to 1.2.0

                // update the database schema
                /*
                try {
                    $this->schemaTool->update($this->listEntityClasses());
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'errorMessage' => $exception->getMessage()]);
    
                    return false;
                }
                */
            case '1.2.0':
                $this->setVar('defaultMessageSortingBackend', 'articledatetime');

            case '1.2.1':
                try {
                    $this->schemaTool->create(['MU\NewsModule\Entity\ImageEntity']);
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'errorMessage' => $exception->getMessage()]);
            
                    return false;
                }
                $this->setVar('enableShrinkingForImageTheFile', false);
                $this->setVar('shrinkWidthImageTheFile', 800);
                $this->setVar('shrinkHeightImageTheFile', 600);
                $this->setVar('thumbnailModeImageTheFile', 'inset');
                $this->setVar('enabledFinderTypes', 'message###image');

            case '1.2.5':
                // for later updates
        }

        // update successful
        return true;
    }
}
