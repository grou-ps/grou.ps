#!/usr/bin/perl
use Frontier::Client;

sub isDisposableEmail {
    my ($email) = @_;
    
    # Make an object to represent the XML-RPC server.
    $server_url = 'http://www.undisposable.org/services/xmlrpc/isDisposableEmail/index.php';
    $server = Frontier::Client->new(url => $server_url);

    # Call the remote server and get our result.
    $result = $server->call('isDisposableEmail',$email);
    return $result;
}

sub isDisposableHost {
    my ($host) = @_;
    
    # Make an object to represent the XML-RPC server.
    $server_url = 'http://www.undisposable.org/services/xmlrpc/isDisposableHost/index.php';
    $server = Frontier::Client->new(url => $server_url);

    # Call the remote server and get our result.
    $result = $server->call('isDisposableHost',$host);
    return $result;
}
