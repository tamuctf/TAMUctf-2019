FROM debian

ARG IRCD_REPO_URL=https://github.com/ircd-hybrid/ircd-hybrid.git
ARG IRCD_REPO_CHECKOUT=8.2
ARG IRCD_OPTIONS=--prefix=/usr/ircd

COPY ./make-ircd.sh ./make-ircd.sh

# Install build tools, build from source, and remove tools in one RUN.
RUN apt-get update && \
    apt-get install -y libssl-dev git autoconf automake cmake && \
    /bin/sh ./make-ircd.sh && \
    apt-get purge -y git autoconf automake cmake && \
    rm -rf /var/lib/apt/lists/* ./make-ircd.sh 

COPY ./etc /usr/ircd/etc
RUN chown -R irc:irc /usr/ircd

EXPOSE 6667 6697

USER irc
CMD ["/usr/ircd/bin/ircd", "-foreground"]
