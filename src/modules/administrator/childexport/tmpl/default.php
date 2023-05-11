<?php
/**
 * @copyright   (C) 2023 Dimitris Grammatikogiannis
 * @license     GNU General Public License version 3
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') || die();

Text::script('MOD_CHILDEXPORT_BUTTON_EXPORT');

HTMLHelper::_('form.csrf');

/** @var $app \Joomla\CMS\Application\CMSApplication */
$app
  ->getDocument()
  ->getWebAssetManager()
  ->registerAndUseScript(
    'mod_childexport.default',
    'mod_childexport/default.js',
    [],
    ['type' => 'module'],
    ['core', 'bootstrap.modal']
  );
?>
<div class="d-grid gap-2 mt-2 mb-2 ms-2 me-2">
  <button type="button" class="btn btn-primary btn-block child-export-button" data-bs-toggle="modal" data-bs-target="#child-export-<?= $module->id; ?>"><?= Text::_('MOD_CHILDEXPORT_BUTTON_SELECT'); ?></button>
</div>

<div id="child-export-<?= $module->id; ?>" class="modal modal-lg" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= Text::_('MOD_CHILDEXPORT'); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body mt-2 mb-2"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= Text::_('JCLOSE'); ?></button>
      </div>
    </div>
  </div>
</div>
