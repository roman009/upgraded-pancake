FROM node:latest as react-build
WORKDIR /app
COPY . ./
RUN npm install
RUN npm run build

FROM nginx:alpine
COPY --from=react-build /app/dist /usr/share/nginx/html
COPY ./docker/nginx/site.conf /etc/nginx/conf.d/
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]