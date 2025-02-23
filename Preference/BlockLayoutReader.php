<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Element119\IfConfigLayout\Preference;

use Element119\IfConfigLayout\Model\IfConfig;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\View\Layout\Argument\Parser as LayoutArgumentParser;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\Reader\Block as MagentoBlockLayoutReader;
use Magento\Framework\View\Layout\Reader\Visibility\Condition;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\Layout\ScheduledStructure;
use Magento\Framework\View\Layout\ScheduledStructure\Helper as LayoutScheduledStructureHelper;

class BlockLayoutReader extends MagentoBlockLayoutReader
{
    private IfConfig $ifConfig;

    public function __construct(
        LayoutScheduledStructureHelper $layoutScheduledStructureHelper,
        LayoutArgumentParser $layoutArgumentParser,
        ReaderPool $readerPool,
        InterpreterInterface $argumentInterpreter,
        Condition $conditionReader,
        IfConfig $ifConfig,
        array $blockAttributes = [],
        $scopeType = null
    ) {
        parent::__construct(
            $layoutScheduledStructureHelper,
            $layoutArgumentParser,
            $readerPool,
            $argumentInterpreter,
            $conditionReader,
            $scopeType
        );

        $this->ifConfig = $ifConfig;
        $this->attributes = $blockAttributes;
    }

    protected function scheduleReference(
        ScheduledStructure $scheduledStructure,
        Element $currentElement
    ) {
        $elementName = $currentElement->getAttribute('name');

        if ($this->ifConfig->shouldRemoveElement($currentElement)) { /** Customisation: Change condition logic */
            $scheduledStructure->setElementToRemoveList($elementName);

            return;
        } elseif ($currentElement->getAttribute('remove')) {
            $scheduledStructure->unsetElementFromListToRemove($elementName);
        }

        $data = $scheduledStructure->getStructureElementData($elementName, []);
        $data['attributes'] = $this->mergeBlockAttributes($data, $currentElement);

        $this->updateScheduledData($currentElement, $data);
        $this->evaluateArguments($currentElement, $data);
        $scheduledStructure->setStructureElementData($elementName, $data);
    }
}
