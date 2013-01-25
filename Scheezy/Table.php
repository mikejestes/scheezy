<?php

namespace Scheezy;

interface Table
{
    public function exists($type);
    public function columns();
    public function columnDetail($column);
    public function columnExists($column);
}
