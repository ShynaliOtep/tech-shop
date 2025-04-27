######  Use nginx alpine image  ######
FROM nginx:1.19-alpine

RUN apk add --update curl busybox-extras rsync tree

# Set working directory
WORKDIR /app

COPY .deployment/.docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
STOPSIGNAL SIGQUIT
CMD ["nginx", "-g", "daemon off;"]
