<?php

declare(strict_types=1);

/*
 * This file is part of the qandidate/toggle package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Toggle;

use RuntimeException;

/**
 * Manages the toggles of an application.
 */
class ToggleManager
{
    private $collection;

    public function __construct(ToggleCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param string $name
     *
     * @return bool True, if the toggle exists and is active
     */
    public function active($name, Context $context)
    {
        if (null === $toggle = $this->collection->get($name)) {
            return false;
        }

        return $toggle->activeFor($context);
    }

    /**
     * Removes the toggle from the manager.
     *
     * @param string $name
     *
     * @return bool True, if element was removed
     */
    public function remove($name)
    {
        return $this->collection->remove($name);
    }

    /**
     * Add the toggle to the manager.
     */
    public function add(Toggle $toggle)
    {
        $this->collection->set($toggle->getName(), $toggle);
    }

    /**
     * Update the toggle.
     */
    public function update(Toggle $toggle)
    {
        $this->collection->set($toggle->getName(), $toggle);
    }

    /**
     * Rename the toggle.
     *
     * @param string $oldName
     * @param string $newName
     *
     * @throws RuntimeException
     *
     * @return bool
     */
    public function rename($oldName, $newName)
    {
        if (null !== $this->collection->get($newName)) {
            throw new RuntimeException(sprintf('Could not rename toggle %1$s to %2$s, a toggle with name %2$s already exists', $oldName, $newName));
        }

        $currentToggle = $this->collection->get($oldName);

        if (null === $currentToggle) {
            throw new RuntimeException(sprintf('Could not rename toggle %1$s to %2$s, toggle with name %1$s does not exists', $oldName, $newName));
        }

        $currentToggle->rename($newName);

        if (false === $this->add($currentToggle)) {
            throw new RuntimeException(sprintf('Failed to rename toggle %1$s to %2$s, an error occurred when saving toggle with new name', $oldName, $newName));
        }

        return $this->remove($oldName);
    }

    /**
     * @return array|Toggle[] all toggles from the manager
     */
    public function all()
    {
        return $this->collection->all();
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return Toggle toggle from manager that has given name
     */
    public function get($name)
    {
        $toggle = $this->collection->get($name);
        if (!$toggle) {
            throw new \InvalidArgumentException("Cannot find Toggle with name $name");
        }

        return $toggle;
    }
}
