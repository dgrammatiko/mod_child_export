<?php
/**
 * @copyright   (C) 2023 Dimitris Grammatikogiannis
 * @license     GNU General Public License version 3
 */

namespace Dgrammatiko\Module\ChildExport\Administrator\Dispatcher;

\defined('_JEXEC') || die();

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
  use HelperFactoryAwareTrait;

  public function dispatch()
  {
    parent::dispatch();
  }

  protected function getLayoutData()
  {
    return parent::getLayoutData();
  }
}
