<?php

namespace iutnc\touiteur\action;

class LogoutAction extends Action
{

    public function execute(): string
    {
        $add_user = "";
        if ($this->http_method == "GET") {
            session_destroy();
            header("Location: ?action=default");
            exit;
        }
        return $add_user;
    }
}