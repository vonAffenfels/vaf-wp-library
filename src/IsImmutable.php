<?php

namespace VAF\WP\Library;

use VAF\WP\Library\Exceptions\ObjectIsLocked;

trait IsImmutable
{
    private bool $isLocked = false;

    /**
     * @return IsImmutable
     */
    final public function lockObject(): self
    {
        $this->isLocked = true;
        return $this;
    }

    /**
     * @return void
     * @throws ObjectIsLocked
     */
    final public function checkLock()
    {
        if ($this->isLocked) {
            throw new ObjectIsLocked($this);
        }
    }

    /**
     * @return string
     */
    abstract public function __toString(): string;
}