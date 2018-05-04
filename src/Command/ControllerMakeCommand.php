<?php

namespace Versoo\LarapiFast\Command;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends LarapiFastGeneratorCommand
{
    protected static $excludedNames = ['Controller', 'controller'];
    protected $name = "larapi:controller";
    protected $type = "Controller";
    protected $suffix = "Controller";
    protected $description = "Create a controller for API Resource";

    public function handle()
    {
        if (parent::handle() === false && !$this->option('force')) {
            return;
        }
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyServiceName', 'dummyname'],
            [$this->getServiceName(), $this->getModelName()],
            $stub
        );

        return parent::replaceNamespace($stub, $name);
    }

    protected function getServiceName()
    {
        return $this->getModel() . 'Service';
    }

    protected function getModel()
    {
        $name = Str::singular(str_replace(static::$excludedNames, '', $this->getNameInput()));
//        $name         = $this->qualifyClass( $resourceName );
        if ($this->option('model')) {
            $name = $this->qualifyClass($this->option('model'));
        }

        return $name;
    }

    protected function getNameInput()
    {
        $name = parent::getNameInput();

        return str_replace(static::$excludedNames, '', $name);
    }

    protected function getModelName()
    {
        return Str::lower($this->getModel());
    }

    protected function getModelNamespace()
    {
        $resourceName = str_replace(static::$excludedNames, '', $this->getNameInput());
        $name = $this->qualifyClass($resourceName);
        if ($this->option('model')) {
            $name = $this->option('model');
        }

        return $this->getNamespace($name, 'Model');
    }

    protected function getStub()
    {
        return $this->resolveStubsPath() . '/controller.stub';
    }

    protected function getArguments()
    {
        $modelArguments = [
            ['name', InputArgument::OPTIONAL, 'Custom name of the Repository (Default: ResourceName)'],
        ];

        return array_merge(parent::getArguments(), $modelArguments);
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Namespace to custom model for service'],

            ['force', null, InputOption::VALUE_NONE, 'Force to create service - overwrite if exists'],
        ];
    }
}
