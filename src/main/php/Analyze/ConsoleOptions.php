<?php namespace Helstern\Nomsky\Analyze;

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
     * @param $notation
     *
     * @return bool
     */
    public function hasNotation($notation)
    {
        if (empty($notation)) {
            return false;
        }
        return in_array($notation, $this->availableNotations());
    }
}
