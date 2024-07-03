FROM php:8.3-cli

COPY src/builds/whp /bin/
RUN chmod +x /bin/whp

ENTRYPOINT ["/bin/whp"]
