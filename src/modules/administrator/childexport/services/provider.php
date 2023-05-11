<?php
/**
 * @copyright   (C) 2023 Dimitris Grammatikogiannis
 * @license     GNU General Public License version 3
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
  public function register(Container $container)
  {
    $container->registerServiceProvider(new ModuleDispatcherFactory('\\Dgrammatiko\\Module\\ChildExport'));
    $container->registerServiceProvider(new HelperFactory('\\Dgrammatiko\\Module\\ChildExport\\Administrator\\Helper'));

    $container->registerServiceProvider(new Module());
  }
};
