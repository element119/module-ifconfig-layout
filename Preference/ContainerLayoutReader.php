<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Element119\IfConfigLayout\Preference;

use Element119\IfConfigLayout\Model\IfConfig;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\Reader\Container as MagentoContainerLayoutReader;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\Layout\ScheduledStructure;
use Magento\Framework\View\Layout\ScheduledStructure\Helper as LayoutScheduledStructureHelper;

class ContainerLayoutReader extends MagentoContainerLayoutReader
{
    private IfConfig $ifConfig;
    private array $containerAttributes;

    public function __construct(
        LayoutScheduledStructureHelper $layoutScheduledStructureHelper,
        ReaderPool $readerPool,
        IfConfig $ifConfig,
        $containerAttributes = []
    ) {
        parent::__construct($layoutScheduledStructureHelper, $readerPool);

        $this->ifConfig = $ifConfig;
        $this->containerAttributes = $containerAttributes;
    }

    protected function mergeContainerAttributes(
        ScheduledStructure $scheduledStructure,
        Element $currentElement
    ) {
        $containerName = $currentElement->getAttribute('name');
        $elementData = $scheduledStructure->getStructureElementData($containerName);

        /** Customisation Start */
        if ($this->ifConfig->shouldRemoveElement($currentElement)) {
            $scheduledStructure->setElementToRemoveList($containerName);

            return;
        }
        /** Customisation End */

        if (isset($elementData['attributes'])) {
            $keys = array_keys($elementData['attributes']);

            foreach ($keys as $key) {
                if (isset($currentElement[$key])) {
                    $elementData['attributes'][$key] = (string)$currentElement[$key];
                }
            }
        } else {
            $elementData['attributes'] = [];

            /** Customisation Start: Replace hard-coded array with loop over constructor argument values */
            foreach ($this->containerAttributes as $attribute) {
                $elementData['attributes'][$attribute] = (string)$currentElement[$attribute];
            }
            /** Customisation End */
        }

        $scheduledStructure->setStructureElementData($containerName, $elementData);
    }

    protected function containerReference(
        ScheduledStructure $scheduledStructure,
        Element $currentElement
    ) {
        $containerName = $currentElement->getAttribute('name');

        if ($this->ifConfig->shouldRemoveElement($currentElement)) { /** Customisation: Change condition logic */
            $scheduledStructure->setElementToRemoveList($containerName);

            return;
        } elseif ($currentElement->getAttribute('remove')) {
            $scheduledStructure->unsetElementFromListToRemove($containerName);
        }

        $this->mergeContainerAttributes($scheduledStructure, $currentElement);
    }
}
