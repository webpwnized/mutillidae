echo -en "GET / HTTP/1.1\r\nHost: mutillidae.localhost\r\n\r\n" > http-1-1-get.request
echo -en "HEAD / HTTP/1.1\r\nHost: mutillidae.localhost\r\n\r\n" > http-1-1-head.request
echo -en "HEAD / HTTP/1.1\r\nHost: 127.0.0.1\r\n\r\n" > http-1-1-options.request
