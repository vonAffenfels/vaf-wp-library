<?php

namespace VAF\WP\Library\Setting;

use Exception;
use VAF\WP\Library\BaseWordpress;

abstract class GroupedValueSetting extends SingleValueSetting
{
    private array $suboptions;

    public function __construct(BaseWordpress $base)
    {
        parent::__construct($base);

        $this->suboptions = array_keys($this->getSubOptions());
    }

    /**
     * @throws Exception
     */
    final protected function getSubOption(string $key): mixed
    {
        if (!in_array($key, $this->suboptions)) {
            throw new Exception(
                sprintf(
                    'Suboption "%s" is not part of setting "%s"!',
                    $key,
                    $this->getSettingName()
                )
            );
        }

        $defaults = $this->getSubOptions();
        $value = $this->get();
        return $value[$key] ?? $defaults[$key];
    }

    final protected function getDefaultValue(): array
    {
        return $this->getSubOptions();
    }

    abstract protected function getSubOptions(): array;
}
