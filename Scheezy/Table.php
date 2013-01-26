<?php

namespace Scheezy;

interface Table
{
    public function exists();
    public function columns();
    public function columnDetail($column);
    public function columnExists($column);
    public function indexExists($name);
}
