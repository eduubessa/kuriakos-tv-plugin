<?php

namespace App;

class Collectors implements IteratorAggregate
{

    public array $items = array();

    private bool $processed = false;

    public function getIterator(): Traversable
    {
        // TODO: Implement getIterator() method.
        return new ArrayIterator($this->items);
    }

    public static function add(XT_Collector $collector): void
    {
        $collector = self::init();
        $collector->setup();

        $collectors->items[$collector->id] = $collector;
    }

    public static function get($id)
    {
        $collectors = self::init();
        return $collectors->items[$id] ?? null;
    }

    public function process()
    {
        if ($this->processed) {
            return;
        }

        foreach ($this as $collector) {
            $collector->tear_down();
            $timer = new XT_Timer;
            $timer->start();

            $collector->process();
            $collector->process_concerns();

            $collector->set_timer($timer->stop());

            foreach ($this as $collector) {
                $collector->post_process();
            }

            $this->processed = true;
        }
    }

    public static function cease()
    {
        $collectors = self::init();
        $collectors->processed = true;

        foreach ($collectors as $collector) {
            $collector->tear_down();
            $collector->discard_data();
        }
    }

    public static function init()
    {
        static $instance;
        if (!$instance) {
            $instance = new Collectors();
        }
        return $instance;
    }
}