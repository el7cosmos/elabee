http {
    include       mime.types;
    default_type  application/octet-stream;

    #log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
    #                  '$status $body_bytes_sent "$http_referer" '
    #                  '"$http_user_agent" "$http_x_forwarded_for"';

    #access_log  logs/access.log  main;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    # Enable gzip compression.
    # Default: off
    gzip on;

    # Compression level (1-9).
    # 5 is a perfect compromise between size and CPU usage, offering about
    # 75% reduction for most ASCII files (almost identical to level 9).
    # Default: 1
    gzip_comp_level    5;

    # Don't compress anything that's already small and unlikely to shrink much
    # if at all (the default is 20 bytes, which is bad as that usually leads to
    # larger files after gzipping).
    # Default: 20
    gzip_min_length    256;

    # Compress data even for clients that are connecting to us via proxies,
    # identified by the "Via" header (required for CloudFront).
    # Default: off
    gzip_proxied       any;

    # Tell proxies to cache both the gzipped and regular version of a resource
    # whenever the client's Accept-Encoding capabilities header varies;
    # Avoids the issue where a non-gzip capable client (which is extremely rare
    # today) would display gibberish if their proxy gave them the gzipped version.
    # Default: off
    gzip_vary          on;

    # Compress all output labeled with one of the following MIME-types.
    # text/html is always compressed by gzip module.
    # Default: text/html
    gzip_types
        application/atom+xml
        application/javascript
        application/json
        application/ld+json
        application/manifest+json
        application/rss+xml
        application/vnd.geo+json
        application/vnd.ms-fontobject
        application/x-font-ttf
        application/x-web-app-manifest+json
        application/xhtml+xml
        application/xml
        font/opentype
        image/bmp
        image/svg+xml
        image/x-icon
        text/cache-manifest
        text/css
        text/plain
        text/vcard
        text/vnd.rim.location.xloc
        text/vtt
        text/x-component
        text/x-cross-domain-policy;

    # This should be turned on if you are going to have pre-compressed copies (.gz) of
    # static files available. If not it should be left off as it will cause extra I/O
    # for the check. It is best if you enable this in a location{} block for
    # a specific directory, or on an individual server{} level.
    # gzip_static on;

    server_tokens off;

    fastcgi_buffers 256 4k;

    # define an easy to reference name that can be used in fastgi_pass
    upstream heroku-fcgi {
        #server 127.0.0.1:4999 max_fails=3 fail_timeout=3s;
        server unix:/tmp/heroku.fcgi.<?=getenv('PORT')?:'8080'?>.sock max_fails=3 fail_timeout=3s;
        keepalive 16;
    }

    server {
        # define an easy to reference name that can be used in try_files
        location @heroku-fcgi {
            include fastcgi_params;

            fastcgi_split_path_info ^(.+\.php)(/.*)$;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            # try_files resets $fastcgi_path_info, see http://trac.nginx.org/nginx/ticket/321, so we use the if instead
            fastcgi_param PATH_INFO $fastcgi_path_info if_not_empty;

            if (!-f $document_root$fastcgi_script_name) {
                # check if the script exists
                # otherwise, /foo.jpg/bar.php would get passed to FPM, which wouldn't run it as it's not in the list of allowed extensions, but this check is a good idea anyway, just in case
                return 404;
            }

            fastcgi_pass heroku-fcgi;
        }

        # TODO: use X-Forwarded-Host? http://comments.gmane.org/gmane.comp.web.nginx.english/2170
        server_name localhost;
        listen <?=getenv('PORT')?:'8080'?>;
        # FIXME: breaks redirects with foreman
        port_in_redirect off;

        root "<?=getenv('DOCUMENT_ROOT')?:getenv('HEROKU_APP_DIR')?:getcwd()?>";

        error_log stderr;
        access_log /tmp/heroku.nginx_access.<?=getenv('PORT')?:'8080'?>.log;

        include "<?=getenv('HEROKU_PHP_NGINX_CONFIG_INCLUDE')?>";

        # restrict access to hidden files, just in case
        location ~ /\. {
            deny all;
        }

        # default handling of .php
        location ~ \.php {
            try_files @heroku-fcgi @heroku-fcgi;
        }
    }
}
