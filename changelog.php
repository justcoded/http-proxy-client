<?php

declare(strict_types=1);

/**
 * @see: https://github.com/marcocesarato/php-conventional-changelog/blob/main/docs/config.md
 */
return [
    'path' => 'CHANGELOG.md',
    'headerTitle' => 'Changelog',
    'headerDescription' => 'All notable changes to this project will be documented in this file.
    
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---',
    'ignoreTypes' => ['build', 'chore', 'ci', 'docs', 'perf', 'refactor', 'revert', 'style', 'test', 'bug'],

    'hiddenHash' => true,
];
