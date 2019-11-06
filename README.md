# secure-query-string
Use query string to hide real variables

Usual URI/URL likes this:<br>
https://www.google.com/search?q=secure&aqs=chrome..69i57.16618j0j7&sourceid=chrome&ie=UTF-8<br>
or this:<br>
http://e96.ru/phones/cell_phones/smartfony/cat-s41-black<br><br>


Script hide real data sending to server like this:
https://server.com/6b4b6e4d695a6d586e48576e59514d6c4e5132354b57514c477a30504736556e



# edit .htaccess

RewriteEngine on
RewriteBase /

# http to https
RewriteCond %{ENV:HTTPS} !on
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L]

# www.domain to domain
RewriteCond %{HTTP_HOST} ^www\.server\.com$
RewriteRule ^(.*)$ https://server.com/$1 [R=301,L]

# secure query string
RewriteCond %{REQUEST_URI} ^/.{64}$
RewriteRule ^(.*)$ /?$1 [L]
