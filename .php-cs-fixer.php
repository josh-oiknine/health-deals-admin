<?php

$finder = PhpCsFixer\Finder::create()
  ->in([
    __DIR__ . '/src',
    __DIR__ . '/db',
    __DIR__ . '/public',
    __DIR__ . '/templates',
  ])
  ->name('*.php')
  ->notName('*.blade.php')
  ->ignoreDotFiles(true)
  ->ignoreVCS(true);

return (new PhpCsFixer\Config())
  ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
  ->setRules([
    '@PSR12' => true,
    'array_indentation' => true,
    'indentation_type' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => [
      'statements' => ['return']
    ],
    'method_chaining_indentation' => true,
    'no_extra_blank_lines' => [
      'tokens' => [
        'extra',
        'throw',
        'use',
      ]
    ],
    'no_trailing_whitespace' => true,
    'single_blank_line_at_eof' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'no_unused_imports' => true,
    'global_namespace_import' => [
      'import_classes' => true,
      'import_constants' => true,
      'import_functions' => true,
    ],
  ])
  ->setIndent("  ") // 2 spaces
  ->setLineEnding("\n")
  ->setFinder($finder); 