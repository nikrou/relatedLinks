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

dcCore::app()->blog->settings->addNameSpace('relatedlinks');
if (dcCore::app()->blog->settings->relatedlinks->active) {
    dcCore::app()->tpl->addBlock('RelatedLinks', [tplRelatedLinks::class, 'relatedLinks']);
    dcCore::app()->tpl->addBlock('RelatedLinksIf', [tplRelatedLinks::class, 'relatedLinksIf']);
    dcCore::app()->tpl->addValue('RelatedLinkTitle', [tplRelatedLinks::class, 'relatedLinkTitle']);
    dcCore::app()->tpl->addValue('RelatedLinkURL', [tplRelatedLinks::class, 'relatedLinkURL']);
    dcCore::app()->tpl->addValue('RelatedLinkImage', [tplRelatedLinks::class, 'relatedLinkImage']);

    if (dcCore::app()->blog->settings->relatedlinks->automatic_content) {
        dcCore::app()->addBehavior('publicEntryAfterContent', [relatedLinksBehaviors::class, 'publicEntryAfterContent']);
    }
}

include(__DIR__ . '/_widgets.php');
