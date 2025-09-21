#!/bin/bash
set -e

# If the first argument is 'postgres', use the official entrypoint
if [ "$1" = 'postgres' ]; then
    # Check if this is for database initialization or runtime
    if [ ! -s "$PGDATA/PG_VERSION" ]; then
        # Database needs initialization - use the official entrypoint
        exec /usr/local/bin/docker-entrypoint.sh "$@"
    else
        # Database is initialized, start with our custom logic
        if [[ "$APP_ENV" != "local" ]]; then
            exec doppler run -- postgres "$@"
        else
            exec postgres "$@"
        fi
    fi
else
    # For other commands, just use the official entrypoint
    exec /usr/local/bin/docker-entrypoint.sh "$@"
fi
