<?php

namespace VAF\WP\Library;

use VAF\WP\Library\Exceptions\ObjectIsLocked;

trait IsImmutable
{
    /**
     * @var bool Determines if the object is locked
     */
    private bool $isLocked = false;

    /**
     * Locks this object
     *
     * @return IsImmutable
     */
    final public function lockObject(): self
    {
        $this->isLocked = true;
        return $this;
    }

    /**
     * Checks if the object is locked and throws an exception if so
     *
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
     * Abstract function that needs to be implemented to have a nice exception message
     *
     * @return string
     */
    abstract public function __toString(): string;
}
