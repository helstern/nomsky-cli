<?php namespace Helstern\Nomsky\Analyze;

/**
 * Provides default and available values for console options
 */
class ConsoleOptions
{
    /**
     * @return string
     */
    public function defaultNotation()
    {
        return 'ebnf';
    }

    /**
     * @return array
     */
    public function availableNotations()
    {
        return ['ebnf'];
    }

    /**
     * Checks if $notation is one of the available notations
     *
     * @param $notation
     *
     * @return bool
     */
    public function validateNotation($notation)
    {
        if (empty($notation)) {
            return false;
        }
        return in_array($notation, $this->availableNotations());
    }
}
