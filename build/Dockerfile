FROM amazonlinux:2

RUN yum update -y \
  && yum install -y \
  glibc-langpack-ja \
  zip \
  unzip \
  vim \
  httpd

RUN amazon-linux-extras install -y epel
RUN yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm

RUN yum clean metadata \
  && yum install -y \
  php81 \
  php81-php \
  php81-php-mbstring \
  php81-php-pdo \
  php81-php-xml \
  php81-php-fpm \
  php81-php-mysqlnd \
  php81-php-gd

RUN alternatives --install /usr/bin/php php /usr/bin/php81 1

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -fsSL https://rpm.nodesource.com/setup_16.x | bash - \
  && yum install -y nodejs

WORKDIR /usr/app
COPY ./ /usr/app
