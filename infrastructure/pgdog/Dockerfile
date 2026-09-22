FROM ghcr.io/pgdogdev/pgdog:v0.1.48

EXPOSE 5432

COPY infrastructure/pgdog/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]