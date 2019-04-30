# Basic docker based environment
# Necessary to trick dokku into building the documentation
# using dockerfile instead of herokuish
FROM ubuntu:17.04

# Add basic tools
RUN apt-get update && \
  apt-get install -y build-essential \
    software-properties-common \
    curl \
    git \
    libxml2 \
    libffi-dev \
    libssl-dev

RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php && \
  apt-get update && \
  apt-get install -y php7.2-cli php7.2-mbstring php7.2-xml php7.2-zip php7.2-intl php7.2-opcache php7.2-sqlite

WORKDIR /code

VOLUME ["/code"]

CMD [ '/bin/bash' ]
