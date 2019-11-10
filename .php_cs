<?php

    return PhpCsFixer\Config::create()
        ->setRiskyAllowed(true)
        ->setRules([
            '@PHP56Migration' => true,
            '@PHPUnit60Migration:risky' => false,
            '@Symfony' => true,
            '@Symfony:risky' => false,
            'align_multiline_comment' => true,
            'array_syntax' => ['syntax' => 'short'],
            'blank_line_before_statement' => true,
            'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
            'concat_space' => ['spacing' => 'one'],
            'combine_consecutive_issets' => true,
            'combine_consecutive_unsets' => true,
            'compact_nullable_typehint' => true,
            'heredoc_to_nowdoc' => true,
            'is_null' => true,
            'list_syntax' => ['syntax' => 'long'],
            'method_argument_space' => ['ensure_fully_multiline' => true],
            'no_extra_consecutive_blank_lines' => ['tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']],
            'no_null_property_initialization' => true,
            'no_short_echo_tag' => true,
            'no_spaces_after_function_name' => true,
            'no_superfluous_elseif' => true,
            'no_unneeded_curly_braces' => true,
            'no_unneeded_final_method' => true,
            'no_unreachable_default_argument_value' => false,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'ordered_class_elements' => false,
            'ordered_imports' => true,
            'phpdoc_to_comment' => false,
            'php_unit_strict' => false,
            'php_unit_test_class_requires_covers' => false,
            'phpdoc_add_missing_param_annotation' => true,
            'phpdoc_no_package' => false,
            'phpdoc_order' => true,
            'protected_to_private' => false,
            'semicolon_after_instruction' => true,
            'single_line_comment_style' => true,
            'strict_comparison' => false,
            'strict_param' => false,
            'yoda_style' => null,
        ])
        ->setFinder(
            PhpCsFixer\Finder::create()
                ->exclude('vendor')
                ->in([
                    __DIR__,
                ])
                ->name('*.php')
        )
        ;