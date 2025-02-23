<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Element119\IfConfigLayout\Plugin;

use Magento\Framework\Config\Dom\UrnResolver;

class ReplaceLayoutMergedXsd
{
    public function beforeGetRealPath(
        UrnResolver $subject,
        string $schema
    ): ?array {
        return $schema === 'urn:magento:framework:View/Layout/etc/layout_merged.xsd'
            ? ['urn:magento:module:Element119_IfConfigLayout:etc/layout_merged.xsd']
            : null;
    }
}
