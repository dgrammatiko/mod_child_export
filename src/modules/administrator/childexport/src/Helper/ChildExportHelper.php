<?php
/**
 * @copyright   (C) 2023 Dimitris Grammatikogiannis
 * @license     GNU General Public License version 3
 */

namespace Dgrammatiko\Module\ChildExport\Administrator\Helper;

\defined('_JEXEC') || die();

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class ChildExportHelper
{
  public static function getChildsAjax()
  {
    $app = Factory::getApplication();
    if (!$app->getSession()->checkToken() || !$app->getIdentity()->authorise('core.login.admin')) {
      throw new Notallowed(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
    }

    $db     = Factory::getContainer()->get(DatabaseInterface::class);
    $query  = $db->getQuery(true)
      ->select('*')
      ->from($db->quoteName('#__template_styles'))
      ->where($db->quoteName('parent') . ' != "" ');
    $db->setQuery($query);

    return $db->loadObjectList();
  }

  public static function getZipAjax()
  {
    $app = Factory::getApplication();
    if (!$app->getSession()->checkToken() || !$app->getIdentity()->authorise('core.login.admin')) {
      throw new Notallowed(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
    }

    $templateName      = $app->input->getString('templateName', '');
    $templateClient    = $app->input->getInt('templateClient', 0);
    $templatePath      = JPATH_ROOT . ($templateClient === 0 ? '' : '/administrator') . '/templates/' . $templateName;
    $templateMediaPath = JPATH_ROOT . '/media/templates/' . ($templateClient === 0 ? 'site/' : 'administrator/') .  $templateName;

    if (!is_dir($templatePath) || !is_dir($templateMediaPath)) {
      throw new Notallowed(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 403);
    }

    $fileName      = uniqid() . '.zip';
    $zip           = new ZipArchive();
    $templateFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($templatePath), RecursiveIteratorIterator::LEAVES_ONLY);
    $mediaFiles    = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($templateMediaPath), RecursiveIteratorIterator::LEAVES_ONLY);

    $zip->open(JPATH_ADMINISTRATOR . '/cache/' . $fileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    foreach ($templateFiles as $name => $file) {
      $filePath     = $file->getRealPath();
      $relativePath = substr($filePath, strlen($templatePath) + 1);
      if (!$file->isDir()) {
        if ($relativePath === 'templateDetails.xml') {
          // The XML might be missing the HTML folder entry
          $theXml        = simplexml_load_file($filePath);
          $hasHTMLFolder = false;

          foreach($theXml->files as $xFiles) {
            if ($xFiles->getName() === 'html') {
              $hasHTMLFolder = true;
            }
          }
          if (!$hasHTMLFolder) {
            $theXml->files->addChild('folder', 'html');
          }

          $zip->addFromString('templateDetails.xml', $theXml->asXML());
        } else {
          $zip->addFile($filePath, $relativePath);
        }
      } elseif ($relativePath !== false) {
        $zip->addEmptyDir($relativePath);
      }
    }

    foreach ($mediaFiles as $name => $file) {
      $filePath     = $file->getRealPath();
      $relativePath = 'media/' . substr($filePath, strlen($templateMediaPath) + 1);
      if (!$file->isDir()) {
        $zip->addFile($filePath, $relativePath);
      } elseif ($relativePath !== false) {
        $zip->addEmptyDir($relativePath);
      }
    }

    $zip->close();

    $theZipData = base64_encode(file_get_contents(JPATH_ADMINISTRATOR . '/cache/' . $fileName));
    unlink(JPATH_ADMINISTRATOR . '/cache/' . $fileName);

    return ['test' => true, 'message' => 'in a bottle', 'blob' => $theZipData, 'version' => '1.0.0'];
  }
}
