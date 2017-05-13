<?php

/**
 *
 */
class MainTask extends ArgsBaseTask
{
    /**
     *
     */
    public function mainAction()
    {
        echo "Congratulations! You are now flying with Phalcon CLI!";
    }

    /**
     *
     */
    public function adduserAction($args)
    {
        print_r($args);
        echo "Successfully added user.";
    }

    /**
     *
     */
    public function initialize()
    {
        $this->addArgParser('adduser', ArgParserComplex::class,
            [
                'title' => 'Add a user with a permission role.',
                'args' => [
                    'required' => ['email', 'role'],
                    'optional' => [],
                ],
                'opts' => [
                    'p|password:' => 'set user password (otherwise it will need to be on first login).',
                    'a|activate' => 'activate',
                    'E|send-email?' => 'send email confirmation with optional message',
                ],
            ]
        );
    }

}
