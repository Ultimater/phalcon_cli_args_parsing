<?php

/**
 *
 */
class ArgsDispatcher extends Phalcon\Cli\Dispatcher
{

    /**
     *
     */
    public function callActionMethod($handler, $actionMethod, array $params = null)
    {
        // Remove 'Action' from the end of the string.
        $action = substr($actionMethod, 0, -6);

        $parsedParams = $handler->parseArgs($action, $params);

        return call_user_func_array([$handler, $actionMethod], [$parsedParams]);
    }
}
