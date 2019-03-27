# moneywaster

## features
- use of events from my public calendar - https://calendar.google.com/calendar?cid=NDYzbDNldTI5YXYwdGdocThiY2c0bjc0amtAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ
- google oauth
- filtering of events by status
- view event details with duration, cost and revenue
- view live event with real time cost and revenue calculation
- toast notification for event in progress
- toast notification for next event
- toast notification during event for each 10EUR spent
- PHP backend implemented in OOP
- node.js backend implemented in FP
- react.js and boostrap frontend
- MySQL for persistence
- reverse nginx proxy for all public services with letsencrypt SSL certificates 

## remarks
- event status is calculated on the fly. this means that if an event has passed the end time, it's considered finished

## possible todos/improvements
- add cron to update the local stored (cached) events
- remove credentials from the repo and allow dev/prod environments via Docker environment variables
- better css/sass
- reports
- browser notifications
- write tests
- better usage of environment variables in configs
- generate more events with the command in the PHP backend

## install notes
- change `.env` file
- run `./build.sh`
- wait

## other notes
- the PHP and node.js backends have basically the same functionality for the end user. I've built them just for fun
- setup is not that straightforward because of the event cache approach and I had to save the costs/revenues for each attendee in the database
- as I'm not very proficient in css/sass I didn't focus on it
- from my previous use of the google API, I knew that the callback URLs for oauth had to be public. Seems this is not the case anymore so the app should work fine on http://localhost as well
