<?php

namespace App\Core;

use App\XT_Backtrace;

class KuriakosTimer
{

    protected $start;
    protected $end;
    protected $trace;

    protected $laps = array();

    public function start(array $data = null)
    {
        $this->trace = new XT_Backtrace();
        $this->start = array(
            'time' => microtime(true),
            'memory' => memory_get_usage(),
            'data' => $data
        );

        return $this;
    }

    public function stop($data = null)
    {
        $this->end = array(
            'time' => microtime(true),
            'memory' => memory_get_usage(),
            'data' => $data
        );

        return $this;
    }

    public function lap($data = null, $name = null)
    {
        $lap = array(
            'time' => microtime(true),
            'memory' => memory_get_usage(),
            'data' => $data,
        );

        if (!isset($name)) {
            $i = sprintf(
                __('Lap %s', 'xtedder-plugin'),
                number_format_i18n(count($this->laps) + 1)
            );
        } else {
            $i = $name;
        }

        $this->laps[$i] = $lap;

        return $this;
    }

    public function get_laps()
    {
        $laps = array();
        $prev = $this->start;

        foreach ($this->laps as $lap_id => $lap) {
            $lap['time_used'] = $lap['time'] - $prev['time'];
            $lap['memory_used'] = $lap['memory'] - $prev['memory'];

            $laps[$lap_id] = $lap;
            $prev = $lap;
        }

        return $laps;
    }

    public function get_memory()
    {
        return $this->end['memory'] - $this->start['memory'];
    }

    public function get_start_time()
    {
        return $this->start['time'];
    }

    public function get_end_time()
    {
        return $this->end['time'];
    }

    public function get_end_memory()
    {
        return $this->end['memory'];
    }

    public function get_trace()
    {
        return $this->trace;
    }

    public function end($data = null)
    {
        return $this->stop($data);
    }
}