<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

use eYaf\Logger;

class LogPlugin extends Yaf\Plugin_Abstract
{
    private $_logFileName;
    public function routerStartup(Yaf\Request_Abstract $request,
                                  Yaf\Response_Abstract $response
    )
    {
        $this->_logFileName = 'syslog_' . date('Ymd');
        Logger::startLogging($this->_logFileName);

        Logger::getLogger($this->_logFileName)->log("[{$request->getRequestUri()}]");
    }

    public function routerShutdown(Yaf\Request_Abstract $request,
                                   Yaf\Response_Abstract $response
    )
    {
        Logger::getLogger($this->_logFileName)->logRequest($request);
    }

    public function dispatchLoopStartup(Yaf\Request_Abstract $request,
                                        Yaf\Response_Abstract $response
    )
    {

    }

    public function preDispatch(Yaf\Request_Abstract $request,
                                Yaf\Response_Abstract $response
    )
    {

    }

    public function postDispatch(Yaf\Request_Abstract $request,
                                 Yaf\Response_Abstract $response
    )
    {
    }

    public function dispatchLoopShutdown(Yaf\Request_Abstract $request,
                                         Yaf\Response_Abstract $response
    )
    {
        Logger::stopLogging($this->_logFileName);
    }


    public function preResponse(Yaf\Request_Abstract $request,
                                Yaf\Response_Abstract $response
    )
    {

    }
}
