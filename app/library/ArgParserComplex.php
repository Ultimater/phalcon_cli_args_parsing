<?php

use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\Exception\RequireValueException;
use GetOptionKit\Exception\InvalidOptionException;

/**
 *
 */
class ArgParserComplex extends ArgParser
{
    /**
     *
     */
    CONST PROG_PATH_HACK = './run';

    /**
     *
     */
    private $startingPath;
    /**
     *
     */
    private $command;

    /**
     *
     */
    private $def;

    /**
     *
     */
    public function __construct($command, array $def)
    {
        $this->execPath = self::PROG_PATH_HACK;
        $this->command = $command;
        $this->def = $def;
    }

    /**
     *
     */
    public function parse(array $args)
    {
        // HACK: The library that I used expects first argument to be the script path.
        $args = array_merge(['./run'] , $args);

        // Configure the command line definition
        $specs = new OptionCollection();
        foreach ($this->def['opts'] as $option => $help) {
            $specs->add($option, $help);
        }

        // Every program will have an auto-generated help
        $specs->add('h|help', 'display this help and exit');

        // Assign the command definition
        try {
            $parser = new OptionParser($specs);
        } catch (\Exception $e) {
            error_log("$cmd: The program has misconfigured options.");
            exit(1);
        }

        // Use the options definition to parse the program arguments
        try {
            $result = $parser->parse($args);
        } catch (RequireValueException $e) {
            error_log('RequireValueException: ' . $e->getMessage());
            // throw new PrintHelpException($this->def, $specs, $e->getMessage(), 1);
            exit(1);
        } catch (InvalidOptionException $e) {
            error_log('InvalidOptionException: ' . $e->getMessage());
            // throw new PrintHelpException($this->def, $specs, $e->getMessage(), 1);
            exit(1);
        } catch (\Exception $e) {
            error_log('Parsing exception: ' . $e->getMessage());
            // throw new PrintHelpException($this->def, $specs, $e->getMessage(), 1);
            exit(1);
        }

        // Ensure that the required arguments are supplied
        if (count($result->arguments) < count($this->def['args']['required'])) {
            fwrite(STDERR, "Incorrect Usage\n\n");
            $this->printHelp($this->def, $specs);
            exit(1);
            // throw new PrintHelpException($this->def, $specs, 'missing operand', 1);
        }

        // Clean arguments
        $args = array_map(function($arg) { return $arg->arg; }, $result->arguments);
        // Clean options
        $opts = array_map(function($opt) { return $opt->value; }, $result->keys);

        // The final result to be used in Tasks
        return [
            'args' => $args,
            'opts' => $opts,
        ];
    }

    /**
     *
     */
    private function printHelp($cmdDef, $specs)
    {
        $reqArgs = array_map('strtoupper', $cmdDef['args']['required']);
        $optArgs = array_map(function($arg) { return '[' . strtoupper($arg) . ']'; }, $cmdDef['args']['optional']);
        $args = array_merge($reqArgs, $optArgs);
        $argNames = implode(' ', $args);
        echo "Usage: " . $this->execPath . " {$this->command} [OPTION] $argNames\n";
        echo "{$cmdDef['title']}\n";
        if (isset($cmdDef['help'])) {
            echo "{$cmdDef['help']}\n";
        }
        echo "\n";
        $widths = array_map(function($spec) {
            return strlen($spec->renderReadableSpec());
        }, $specs->all());
        $width = max($widths);
        $lines = [];
        foreach($specs->all() as $spec)
        {
            $c1 = str_pad($spec->renderReadableSpec(), $width);
            $line = sprintf("%s  %s", $c1, $spec->desc);
            $lines[] = $line;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            echo " $line\n";
        }
    }


}
