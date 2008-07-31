#!/usr/bin/python
import xmlrpclib

def isDisposableEmail(email):
    server = xmlrpclib.ServerProxy("http://www.undisposable.org/services/xmlrpc/isDisposableEmail/index.php")
    result = server.isDisposableEmail(email)
    return result

def isDisposableHost(host):
    server = xmlrpclib.ServerProxy("http://www.undisposable.org/services/xmlrpc/isDisposableHost/index.php")
    result = server.isDisposableHost(host)
    return result
