#!/bin/bash

if [[ "$APP_ENV" != "local" ]]; then
    doppler run --command="redis-server --requirepass \$REDIS_PASSWORD"
else
    # Local environment check
    if [[ -z "${REDIS_PASSWORD}" ]]; then
        echo "Error: REDIS_PASSWORD must be set in local environment"
        exit 1
    fi
    redis-server --requirepass $REDIS_PASSWORD
fi