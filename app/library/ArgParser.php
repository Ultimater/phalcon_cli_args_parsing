<?php

/**
 *
 */
abstract class ArgParser
{

    /**
     *
     */
    abstract public function __construct($command, array $args);

    /**
     *
     */
    abstract public function parse(array $args);
}
