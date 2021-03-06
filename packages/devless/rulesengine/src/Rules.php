<?php

namespace Devless\RulesEngine;

use App\Helpers\DevlessHelper;
use App\Helpers\Helper;

class Rules
{
    use fillers, tableAuth, tableActions, flowControl, actions, mathLib, stringLib, dateLib, generators, mutateResponse;

    private $assertion = [
        'elseWhenever' => false,
        'whenever' => false,
        'otherwise' => false,
    ];
    private $called = [
        'elseWhenever' => false,
        'whenever' => false,
        'otherwise' => false,
    ];
    public $results = '';
    
    public $status_code = 1000;
    public $message = '';
    public $payload = [];

    private $stopAndOutputCalled = false;
    private $answered = false;
    private $execOrNot = true;
    private $isCurrentDBAction = false;
    private $actionType = '';
    private $tableName = '';
    private $selectedService = null;
    private $selectedMethod = null;
    private $methodAction = [
        'GET' => 'query',
        'POST' => 'create',
        'PATCH' => 'update',
        'DELETE' => 'delete',
    ];

    public $EVENT = [];

    public $accessRights = [
        'query' => 3,
        'create' => 3,
        'update' => 3,
        'delete' => 3,
    ];

    public $then = null;
    public $also = null;
    public $firstly = null;
    public $secondly = null;
    public $thirdly = null;
    public $beSureTo = null;
    public $lastly = null;
    public $next = null;


    public function __construct()
    {
        $this->then = $this->also = 
        $this->firstly = $this->secondly = 
        $this->thirdly = $this->beSureTo = 
        $this->next = $this->lastly = $this;
    }

    public function requestType($requestPayload)
    {
        $tableName = DevlessHelper::get_tablename_from_payload($requestPayload);
        $actionType = $requestPayload['method'];
        $this->actionType = $actionType;
        $this->tableName = $tableName;

        return $this;
    }

    public function __call($method, $args)
    {
        if(!method_exists($this, $method)) {
            $closestMethod = 
                DevlessHelper::find_closest_word($method, get_class_methods($this));
            $failMessage = 'There is no such method `'.$method.'`';
            $failMessage .= (strlen($closestMethod) > 0)? '` perharps you meant '.$closestMethod. '?' : '';
            Helper::interrupt(642, $failMessage);
        }
    }

     /**
     * use result from previous method as a param 
     * if argument is not provided
     *
     * @param $args
     *
     * @return mix
     */
    public function useArgsOrPrevOutput($args)
    {
        return ($args == null)? $this->results : $args;
    }
    
}
