#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';


use Symfony\Component\Console\Application;
use App\Organization\Console\Command\CreateOrganization;

$application = new Application();

$application->add(new CreateOrganization());

$application->run();