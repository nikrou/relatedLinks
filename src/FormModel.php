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

namespace Dotclear\Plugin\relatedLinks;

class FormModel
{
    private bool $active = false;

    private bool $automatic_content = false;

    private bool $content_with_image = false;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAutomaticContent(): bool
    {
        return $this->automatic_content;
    }

    public function setAutomaticContent(bool $automatic_content): self
    {
        $this->automatic_content = $automatic_content;

        return $this;
    }

    public function getContentWithImage(): bool
    {
        return $this->content_with_image;
    }

    public function setContentWithImage(bool $content_with_image): self
    {
        $this->content_with_image = $content_with_image;

        return $this;
    }
}
