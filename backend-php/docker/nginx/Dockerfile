FROM nginx:alpine
WORKDIR /app
COPY ./site.conf /etc/nginx/conf.d/
RUN rm -f /etc/nginx/conf.d/default.conf
# VOLUME /app/
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]