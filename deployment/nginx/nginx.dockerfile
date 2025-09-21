FROM nginx:1.26.2-alpine

# Install dependencies for building modules
RUN apk add --no-cache \
    gcc \
    libc-dev \
    make \
    openssl-dev \
    pcre-dev \
    zlib-dev \
    linux-headers \
    curl \
    git \
    cmake

# Copy custom nginx configuration
COPY ./deployment/nginx/nginx.conf /etc/nginx/nginx.conf

# Clean up
RUN apk del gcc libc-dev make openssl-dev pcre-dev zlib-dev linux-headers curl git cmake && \
    rm -rf /tmp/* /var/cache/apk/* nginx.tar.gz

EXPOSE 80