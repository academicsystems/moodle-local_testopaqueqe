# moodle-opaque_test_engine_rest
A basic quiz engine to test the moodle opaque plugin

## Configure An Apache Server

Use the following configuration file as an example to set up an Apache server to serve the test quiz engine's php files.

You must install this repo in "/var/www/DOMAIN" & if you plan on using HTTPS, you will have to set up SSL certificates.

```
<IfModule !mod_ssl.c>
    <VirtualHost *:80>
        ServerName DOMAIN
        DocumentRoot /var/www/DOMAIN

        Alias /rest/ /var/www/DOMAIN/rest/server.php/
        Alias /soap/ /var/www/DOMAIN/soap/
    </VirtualHost>
</IfModule>

<IfModule mod_ssl.c>
    <VirtualHost _default_:443>
        ServerName DOMAIN
        DocumentRoot /var/www/DOMAIN

        Alias /rest/ /var/www/DOMAIN/rest/server.php/
        Alias /soap/ /var/www/DOMAIN/soap/

        SSLEngine on
        SSLCertificateFile /etc/ssl/DOMAIN.crt
        SSLCertificateKeyFile /etc/ssl/DOMAIN.key

        <FilesMatch "\.(cgi|shtml|phtml|php)$">
            SSLOptions +StdEnvVars
        </FilesMatch>
        
        <Directory /usr/lib/cgi-bin>
            SSLOptions +StdEnvVars
        </Directory>

        BrowserMatch "MSIE [2-6]" nokeepalive ssl-unclean-shutdown downgrade-1.00 force-response-1.0
    </VirtualHost>
</IfModule>
```
## Configure Moodle To Use This Quiz Engine

After you have installed the Opaque question type and question behaviour, go to:

> DashboardSite / administration / Plugins / Question types / Opaque / Editing engine

Enter the following values:

* Engine Name
  * anything you want
  
* Question engine URLs
  * if rest: https://DOMAIN/rest/
  * if soap: https://DOMAIN/soap/
  
* Question bank base URLs
  * something like "algebra", the category of questions to use (not actually used in this basic quiz engine, but needed)

* Pass key
  * leave this blank
  
* Connection time-out
  * anything, like 4
  
* Web Service
  * choose either SOAP or REST
  
## Conclusion

Now you should be able to create a quiz with an Opaque question that uses this test quiz engine. It doesn't really matter what you enter when configuring the question because this test engine just responds with the same quiz resources regardless.

