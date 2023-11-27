<?php
/*
 * This file is part of relatedLinks plugin, for dotclear
 *
 * Copyright(c) Nicolas Roudaire  https://www.nikrou.net/
 * Licensed under the GPL version 2.0 license.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_80,
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'dcBlog' => 'Dotclear\Core\Blog',
        'dcAuth' => 'Dotclear\Core\Auth',
        'initPages' => 'Dotclear\Plugin\pages\Pages',
    ]);
};
