<?php

namespace AsuraNetwork\session\modules;

use AsuraNetwork\session\Session;

abstract class Module{

    protected Session $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session){
        $this->session = $session;
    }


    abstract public function getId(): string;

}