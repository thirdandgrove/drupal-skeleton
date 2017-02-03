# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
# 
# Default backend definition.  Set this to point to your content
# server.
# 
backend default {
   .host = "127.0.0.1";
   .port = "8181";
   .connect_timeout = 2s;
}
 
sub vcl_recv {
if (req.http.X-Forwarded-For) {
   set req.http.X-Forwarded-For = req.http.X-Forwarded-For + ", " + client.ip;
} else {
   set req.http.X-Forwarded-For = client.ip;
}
if (req.request != "GET" && req.request != "HEAD") {
   return(pass);
}
if(req.url ~ "^/cron.php") {
   return(pass);
}
if(req.url ~ "^/xmlrpc.php") {
   return(pass);
}
if (req.http.Authorization) {
   return(pass);
}
if(req.http.cookie ~ "(^|;\s*)(SESS=)") {
   return(pass);
}
return(lookup);
}

