FROM nginx:alpine
COPY ./site.conf /etc/nginx/conf.d/
VOLUME /app/
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]