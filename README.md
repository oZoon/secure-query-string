# secure-query-string
Use query string to hide real variables

Usual URI/URL likes this:<br>
https://www.google.com/search?q=secure&aqs=chrome..69i57.16618j0j7&sourceid=chrome&ie=UTF-8<br>
or this:<br>
http://e96.ru/phones/cell_phones/smartfony/cat-s41-black<br><br>


Script hide real data sending to server like this:
https://server.com/6b4b6e4d695a6d586e48576e59514d6c4e5132354b57514c477a30504736556e



# .htaccess

RewriteEngine on<br>
RewriteBase /<br>

RewriteCond %{REQUEST_URI} ^/.{64}$<br>
RewriteRule ^(.*)$ /?$1 [L]<br>
