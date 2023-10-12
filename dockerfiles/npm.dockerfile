
FROM node:18-alpine3.15

#RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

#USER laravel


WORKDIR /var/www/html

ENTRYPOINT ["npm"]