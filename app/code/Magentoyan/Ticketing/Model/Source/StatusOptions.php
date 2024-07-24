<?php

namespace Magentoyan\Ticketing\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class StatusOptions implements OptionSourceInterface {

    public function toOptionArray() {
        $generatorArray = [];

        $options = [
            0 => 'New',
            1 => 'Pending',
            2 => 'Replied',
            3 => 'Closed'
        ];

        foreach ($options as $k => $item)
            $generatorArray[] = [
                'value' => $k,
                'label' => $item
            ];

        return $generatorArray;
    }
}
