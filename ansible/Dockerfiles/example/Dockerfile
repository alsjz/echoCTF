# METADATA
FROM buildpack-deps:bullseye-curl
LABEL maintainer="Echothrust Solutions <info@echothrust.com>"
LABEL description="Base debian-bullseye image"

#####################
# ENV AND BUILD ARGS
#####################
ENV DEBIAN_FRONTEND noninteractive
ADD https://github.com/krallin/tini/releases/download/v0.19.0/tini-amd64 /sbin/tini
ADD 01_nodoc /etc/dpkg/dpkg.cfg.d/

# PACKAGE INSTALLATIONS
RUN set -ex \
    && chmod +x /sbin/tini \
    && apt-get update \
    && apt-get install --no-install-recommends -y ansible apt-transport-https \
    build-essential bzip2 ca-certificates curl dirmngr dnsutils gcc git gzip \
    iproute2 less libc6-dev libc-client-dev libjpeg-dev libkrb5-dev libpng-dev \
    libpq-dev libzip-dev locales-all make mcrypt \
    netcat-traditional nginx procps psmisc socat software-properties-common \
    unzip vim vim-tiny wget zip python \
    && rm -rf /usr/src/* /var/lib/apt/lists/* /tmp/* /usr/share/locale/* /usr/share/man/* /usr/share/doc/*

##############
# COPY FILES
##############
COPY healthcheck.sh /usr/local/sbin/healthcheck.sh
COPY *.yml /tmp/
COPY entrypoint.sh /
#COPY your-files-here /at/some/place


################
# RUN COMMANDS
################

# Add your commands here

#################################
# RUN playbook and generate
# file checksums for healthcheck
#################################
RUN set -ex ; chmod 0700 /usr/local/sbin/healthcheck.sh; ansible-playbook -i 'localhost,' /tmp/autoregister.yml; \
	rm /tmp/*.yml

RUN sha512sum /root/* /var/www/html/* /etc/passwd /etc/shadow > /usr/local/lib/.sha512sum; chmod 0400 /usr/local/lib/.sha512sum; chmod 0700 /entrypoint.sh

##############
# HEALTHCHECK
##############
HEALTHCHECK --interval=12s --timeout=12s --start-period=30s CMD /usr/local/sbin/healthcheck.sh

# RUNTIME
WORKDIR /
CMD ["bash"]
#ENTRYPOINT ["/entrypoint.sh"]
ENTRYPOINT ["tini","--", "/entrypoint.sh"]
