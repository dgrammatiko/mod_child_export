<?php

// Add all the php folders
$finder = PhpCsFixer\Finder::create()
  ->in([__DIR__ . '/src'])
  ->notPath('/tmpl/')
  ->notPath('/layouts/')
  ->notPath('/templates/');

$config = new PhpCsFixer\Config();
$config
  ->setRiskyAllowed(true)
  ->setHideProgress(false)
  ->setUsingCache(false)
  ->setRules(
    [
      // Basic ruleset is PSR 12
      '@PSR12'                         => true,
      // Short array syntax
      'array_syntax'                   => ['syntax' => 'short'],
      // Lists should not have a trailing comma like list($foo, $bar,) = ...
      'no_trailing_comma_in_list_call' => true,
      // Arrays on multiline should have a trailing comma
      'trailing_comma_in_multiline'    => ['elements' => ['arrays']],
      // Align elements in multiline array and variable declarations on new lines below each other
      'binary_operator_spaces'         => ['operators' => ['=>' => 'align_single_space_minimal', '=' => 'align']],
      // The "No break" comment in switch statements
      'no_break_comment'               => ['comment_text' => 'No break'],
    ]
  )
  ->setIndent(str_pad('', 2))
  ->setFinder($finder);

return $config;
