<?php

/**
 *
 */
class ArgsBaseTask extends Phalcon\CLI\Task
{

    /**
     *
     */
    private $argParsers = [];

    /**
     *
     */
    public function addArgParser($action, $className, $def)
    {
        $this->argParsers[$action] = [
            'className' => $className,
            'def'       => $def,
        ];
    }

    /**
     *
     */
    public function parseArgs($action, array $args)
    {
        if (!array_key_exists($action, $this->argParsers)) {
            return $args;
        }

        $className = $this->argParsers[$action]['className'];
        $def = $this->argParsers[$action]['def'];
        $argParser = new $className($action, $def);

        return $argParser->parse($args);
    }
}
