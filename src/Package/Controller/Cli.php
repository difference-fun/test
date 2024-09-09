<?php

namespace Package\Difference\Fun\Test\Controller;

use Difference\Fun\App;
use Difference\Fun\Config;

use Difference\Fun\Module\Core;
use Difference\Fun\Module\Controller;
use Difference\Fun\Module\Data;
use Difference\Fun\Module\Dir;
use Difference\Fun\Module\File;

use Exception;

use Difference\Fun\Exception\LocateException;
use Difference\Fun\Exception\ObjectException;
use Difference\Fun\Exception\UrlEmptyException;
use Difference\Fun\Exception\UrlNotExistException;

class Cli extends Controller {
    const DIR = __DIR__ . '/';
    const MODULE_INFO = 'Info';
    const INFO = [
        '{{binary()}} difference_fun/test                    | Test options',
        '{{binary()}} difference_fun/test setup              | Test setup',
        '{{binary()}} difference_fun/test run                | Run tests'
    ];

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function run(App $object): mixed
    {
        $node = $object->request(0);
        $scan = Cli::scan($object);
        $module = (string) $object->parameter($object, $node, 1);
        if(
            !in_array(
                $module,
                $scan['module'],
                true
            )
        ){
            $module = Cli::MODULE_INFO;
        }
        $submodule = (string) $object->parameter($object, $node, 2);
        if(
            !in_array(
                $submodule,
                $scan['submodule'],
                true
            )
        ){
            $submodule = false;
        }
        $command = (string) $object->parameter($object, $node, 3);
        if(
            !in_array(
                $command,
                $scan['command'],
                true
            ) ||
            $module === Cli::MODULE_INFO ||
            $submodule === Cli::MODULE_INFO
        ){
            $command = false;
        }
        $subcommand = (string) $object->parameter($object, $node, 4);
        if(
            !in_array(
                $subcommand,
                $scan['subcommand'],
                true
            ) ||
            $module === Cli::MODULE_INFO ||
            $submodule === Cli::MODULE_INFO ||
            $command === Cli::MODULE_INFO
        ){
            $subcommand = false;
        }
        $action = (string) $object->parameter($object, $node, 5);
        if(
            !in_array(
                $action,
                $scan['action'],
                true
            ) ||
            $module === Cli::MODULE_INFO ||
            $submodule === Cli::MODULE_INFO ||
            $command === Cli::MODULE_INFO ||
            $subcommand === Cli::MODULE_INFO
        ){
            $action = false;
        }
        $subaction = (string) $object->parameter($object, $node, 6);
        if(
            !in_array(
                $subaction,
                $scan['subaction'],
                true
            ) ||
            $module === Cli::MODULE_INFO ||
            $submodule === Cli::MODULE_INFO ||
            $command === Cli::MODULE_INFO ||
            $subcommand === Cli::MODULE_INFO ||
            $action === Cli::MODULE_INFO
        ){
            $subaction = false;
        }
        try {
            if(
                !empty($submodule) &&
                !empty($command) &&
                !empty($subcommand) &&
                !empty($action) &&
                !empty($subaction)
            ){
                $url = Cli::locate(
                    $object,
                    ucfirst($module) .
                    '.' .
                    ucfirst($submodule) .
                    '.' .
                    ucfirst($command) .
                    '.' .
                    ucfirst($subcommand) .
                    '.' .
                    ucfirst($action) .
                    '.' .
                    ucfirst($subaction)
                );
            }
            elseif(
                !empty($submodule) &&
                !empty($command) &&
                !empty($subcommand) &&
                !empty($action)
            ){
                $url = Cli::locate(
                    $object,
                    ucfirst($module) .
                    '.' .
                    ucfirst($submodule) .
                    '.' .
                    ucfirst($command) .
                    '.' .
                    ucfirst($subcommand) .
                    '.' .
                    ucfirst($action)
                );
            }
            elseif(
                !empty($submodule) &&
                !empty($command) &&
                !empty($subcommand)
            ){
                $url = Cli::locate(
                    $object,
                    ucfirst($module) .
                    '.' .
                    ucfirst($submodule) .
                    '.' .
                    ucfirst($command) .
                    '.' .
                    ucfirst($subcommand)
                );
            }
            elseif(
                !empty($submodule) &&
                !empty($command)
            ){
                $url = Cli::locate(
                    $object,
                    ucfirst($module) .
                    '.' .
                    ucfirst($submodule) .
                    '.' .
                    ucfirst($command)
                );
            }
            elseif(!empty($submodule)){
                $url = Cli::locate(
                    $object,
                    ucfirst($module) .
                    '.' .
                    ucfirst($submodule)
                );
            } else {
                $url = Cli::locate(
                    $object,
                    ucfirst($module)
                );
            }
            $object->request('package', $node);
            $object->request('module', $module);
            if($submodule){
                $object->request('submodule', $submodule);
            }
            if($command){
                $object->request('command', $command);
            }
            if($subcommand){
                $object->request('subcommand', $subcommand);
            }
            if($action){
                $object->request('action', $action);
            }
            if($subaction){
                $object->request('subaction', $subaction);
            }
            return Cli::response($object, $url);
        } catch (Exception | UrlEmptyException | UrlNotExistException | LocateException $exception){
            return $exception;
        }
    }

    /**
     * @throws Exception
     */
    private static function scan(App $object): array
    {
        $scan = [
            'module' => [],
            'submodule' => [],
            'command' => [],
            'subcommand' => [],
            'action' => [],
            'subaction' => []
        ];
        $url = $object->config('controller.dir.view');
        if(!Dir::exist($url)){
            return $scan;
        }
        $dir = new Dir();
        $read = $dir->read($url, true);
        if(!$read){
            return $scan;
        }
        foreach($read as $nr => $file){
            if($file->type !== File::TYPE){
                continue;
            }
            $part = substr($file->url, strlen($url));
            $explode = explode('/', $part, 6);
            if(array_key_exists(0, $explode) === false){
                continue;
            }
            $module = strtolower(File::basename($explode[0], $object->config('extension.tpl')));
            $submodule = false;
            $temp = explode('.', $module, 2);
            if(array_key_exists(1, $temp)){
                $module = $temp[0];
                $submodule = $temp[1];
            } else {
                if(array_key_exists(1, $explode)){
                    $submodule = strtolower(File::basename($explode[1], $object->config('extension.tpl')));
                    $temp = explode('.', $submodule, 2);
                    if(array_key_exists(1, $temp)){
                        $submodule = $temp[0];
                        $command = $temp[1];
                    }
                }
            }
            $command = false;
            $subcommand = false;
            $action = false;
            $subaction = false;
            if(array_key_exists(2, $explode)){
                $command = strtolower(File::basename($explode[2], $object->config('extension.tpl')));
                $temp = explode('.', $command, 2);
                if(array_key_exists(1, $temp)){
                    $command = $temp[0];
                    $subcommand = $temp[1];
                }
            }
            if(array_key_exists(3, $explode) && $subcommand === false){
                $subcommand = strtolower(File::basename($explode[3], $object->config('extension.tpl')));
                $temp = explode('.', $subcommand, 2);
                if(array_key_exists(1, $temp)){
                    $subcommand = $temp[0];
                    $action = $temp[1];
                }
            }
            if(array_key_exists(4, $explode) && $action === false){
                $action = strtolower(File::basename($explode[4], $object->config('extension.tpl')));
                $temp = explode('.', $action, 2);
                if(array_key_exists(1, $temp)){
                    $action = $temp[0];
                    $subaction = $temp[1];
                }
            }
            if(array_key_exists(5, $explode) && $subaction === false){
                $subaction = strtolower(File::basename($explode[5], $object->config('extension.tpl')));
            }
            if(
                !in_array(
                    $module,
                    $scan['module'],
                    true
                )
            ){
                $scan['module'][] = $module;
            }
            if(
                $submodule &&
                !in_array(
                    $submodule,
                    $scan['submodule'],
                    true
                )
            ){
                $scan['submodule'][] = $submodule;
            }
            if(
                $command  &&
                !in_array(
                    $command,
                    $scan['command'],
                    true
                )
            ){
                $scan['command'][] = $command;
            }
            if(
                $subcommand &&
                !in_array(
                    $subcommand,
                    $scan['subcommand'],
                    true
                )
            ){
                $scan['subcommand'][] = $subcommand;
            }
            if(
                $action &&
                !in_array(
                    $action,
                    $scan['action'],
                    true
                )
            ){
                $scan['action'][] = $subcommand;
            }
            if(
                $subaction &&
                !in_array(
                    $subaction,
                    $scan['subaction'],
                    true
                )
            ){
                $scan['subaction'][] = $subcommand;
            }
        }
        return $scan;
    }
}