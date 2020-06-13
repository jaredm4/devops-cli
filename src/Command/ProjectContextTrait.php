<?php

declare(strict_types=1);

namespace Devops\Command;

/**
 * Trait ProjectContextTrait
 * Helper methods for converting a project's name to different formats, making it portable.
 */
trait ProjectContextTrait
{
    protected string $projectName;

    /**
     * @param string $name the project's human readable name
     */
    public function setProjectName(string $name)
    {
        if (0 !== preg_match('/[^[:alpha:][:digit:] ]/i', $name)) {
            throw new \RuntimeException('Project name should only include alphanumeric characters and spaces.');
        }

        $this->projectName = $name;
    }

    /**
     * Human readable name.
     *
     * @return string
     */
    protected function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * spinal-case version of the project name.
     *
     * @return string
     */
    protected function projectNameToSpinalCase()
    {
        return strtolower(str_replace(' ', '-', $this->projectName));
    }

    /**
     * snake_case version of the project name.
     *
     * @return string
     */
    protected function projectNameToSnakeCase()
    {
        return strtolower(str_replace(' ', '_', $this->projectName));
    }

    /**
     * UpperCamelCase version of the project name.
     *
     * @return string
     */
    protected function projectNameToUpperCamelCase()
    {
        return str_replace(' ', '', ucwords($this->projectName));
    }

    /**
     * lowerCamelCase version of the project name.
     *
     * @return string
     */
    protected function projectNameToLowerCamelCase()
    {
        return lcfirst($this->projectNameToUpperCamelCase());
    }
}
