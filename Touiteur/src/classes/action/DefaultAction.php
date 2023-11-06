<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;

class DefaultAction extends Action
{

    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        return "touiteur";
    }
}
