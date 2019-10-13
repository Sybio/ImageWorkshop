<?php

$header = <<<'EOF'
This file is part of the ImageWorkshop package.

(c) http://phpimageworkshop.com

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'function_declaration' => ['closure_function_spacing' => 'none'],
        'no_whitespace_in_blank_line' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__)
    )
;
