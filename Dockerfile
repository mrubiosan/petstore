FROM php:7.2

ENV DB_HOST=db
ENV DB_PORT=3306

COPY ./ /app
WORKDIR /app

ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

RUN docker-php-ext-install pdo_mysql

EXPOSE 8080

CMD /wait-for-it.sh -h $DB_HOST -p $DB_PORT && vendor/bin/doctrine orm:schema-tool:create && bin/fixtures-loader.php && php -S 0.0.0.0:8080 -t public
