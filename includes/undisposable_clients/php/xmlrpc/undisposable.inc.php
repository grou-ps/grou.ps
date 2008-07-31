<?php
require_once 'XML/RPC.php';

function undorg_isDisposableEmail($input) {
    $params = array(new XML_RPC_Value($input, 'string'));
    $msg = new XML_RPC_Message('isDisposableEmail', $params);

    $cli = new XML_RPC_Client('/services/xmlrpc/isDisposableEmail/index.php', 'www.undisposable.org');
    $resp = $cli->send($msg);

    if (!$resp) {
	echo 'Communication error: ' . $cli->errstr;
        exit;
    }

    if (!$resp->faultCode()) {
	$val = $resp->value();
        return $val->getVal();
    } else {
	/*
         * Display problems that have been gracefully cought and
	 * reported by the xmlrpc.php script.
         */
	// echo 'Fault Code: ' . $resp->faultCode() . "\n";
        // echo 'Fault Reason: ' . $resp->faultString() . "\n";
        return false;
    }
}


function undorg_isDisposableHost($input) {
    $params = array(new XML_RPC_Value($input, 'string'));
    $msg = new XML_RPC_Message('isDisposableHost', $params);

    $cli = new XML_RPC_Client('/services/xmlrpc/isDisposableHost/index.php', 'www.undisposable.org');
    $resp = $cli->send($msg);

    if (!$resp) {
	echo 'Communication error: ' . $cli->errstr;
        exit;
    }

    if (!$resp->faultCode()) {
	$val = $resp->value();
        return $val->getVal();
    } else {
	/*
         * Display problems that have been gracefully cought and
	 * reported by the xmlrpc.php script.
         */
	// echo 'Fault Code: ' . $resp->faultCode() . "\n";
        // echo 'Fault Reason: ' . $resp->faultString() . "\n";
        return false;
    }
}


?>