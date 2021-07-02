
## Subscription service task

Create subscription service without PHP frameworks and Bootstrap

## Task description

1. Create design form example
2. Add JS functionality for form validation
3. Create Back-end management features

## Docker setup guide

1. Clone this repository
2. Go to root directory where is located `docker-compose.yaml` file
3. Build project `docker-compose build`
4. Run project `docker-compose up -d`
5. install composer `docker exec sandbox composer install`
6. open project in `127.0.0.1:8000`

## Project url
 - index - `127.0.0.1:8000`;
 - admin table - `127.0.0.1:8000/members`
 
---
Trouble building docker file? Try `docker-compose build --force-rm --no-cache`