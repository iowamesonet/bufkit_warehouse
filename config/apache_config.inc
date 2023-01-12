
Alias /bufkit "/opt/bufkit_warehouse/htdocs"
<Directory "/opt/bufkit_warehouse/htdocs">
  Options Indexes MultiViews
  AllowOverride None
  Require all granted
  <FilesMatch \.html$>
    ProxyErrorOverride On
    SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
  </FilesMatch>

# Careful of order here...
Redirect /bufkit/data/cobb_gfsm https://metfs1.agron.iastate.edu/data/cobb/06/gfsm
Redirect /bufkit/data/cobb_gfs https://metfs1.agron.iastate.edu/data/cobb/00/gfs
Redirect /bufkit/data/cobb_namm https://metfs1.agron.iastate.edu/data/cobb/06/namm
Redirect /bufkit/data/cobb_nam https://metfs1.agron.iastate.edu/data/cobb/12/nam
Redirect /bufkit/data/gfsm https://metfs1.agron.iastate.edu/data/bufkit/gfsm
Redirect /bufkit/data/gfs https://metfs1.agron.iastate.edu/data/bufkit/gfs
Redirect /bufkit/data/hrrr https://metfs1.agron.iastate.edu/data/bufkit/hrrr
Redirect /bufkit/data/nam4km https://metfs1.agron.iastate.edu/data/bufkit/nam4km
Redirect /bufkit/data/namm https://metfs1.agron.iastate.edu/data/bufkit/namm
Redirect /bufkit/data/nam https://metfs1.agron.iastate.edu/data/bufkit/nam
Redirect /bufkit/data/sref https://metfs1.agron.iastate.edu/data/bufkit/sref

</Directory>
