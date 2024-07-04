FROM php:8.3-cli

COPY src/builds/whp /bin/
RUN chmod +x /bin/whp

RUN apt-get update && apt-get install -y tini

ENTRYPOINT ["/usr/bin/tini", "--", "/bin/whp"]
