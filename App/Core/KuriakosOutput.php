<?php

namespace App\Core;
use XT_Collector;
use XT_Timer;

abstract class KuriakosOutput
{


    protected $collector;
    protected $timer;

    public function __construct(KuriakosCollector $collector)
    {
        $this->collector = $collector;
    }

    abstract public function get_output();

    public function output()
    {

    }

    final public function get_timer()
    {
        return $this->timer;
    }

    final public function set_timer(KuriakosTimer $timer)
    {
        $this->timer = $timer;
    }

}