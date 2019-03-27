var cors = require('cors');
const express = require('express');
const app = express();
const port = 3000;
const {google} = require('googleapis');
const config = require('dotenv').config().parsed;
const securityService = require('./app/services/security_service');

const models = require('./app/models');

const oauth2Client = new google.auth.OAuth2(
    config.GOOGLE_CLIENT_ID,
    config.GOOGLE_CLIENT_SECRET,
    config.GOOGLE_REDIRECT_URL
);

const scopes = [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
];

app.use(cors());
app.listen(port, () => console.log(`Moneywaster-backend-js listening on port ${port}!`));
app.get('/', function (req, res) {
    res.json('OK');
});

app.get('/auth-url', function (req, res) {
    const url = oauth2Client.generateAuthUrl({
        access_type: 'offline',
        scope: scopes,
        prompt: 'consent',
    });
    res.json({'auth_url': url});
});

app.get('/callback', async function (req, res) {
    const code = req.query.code;

    const {tokens} = await oauth2Client.getToken(code);
    oauth2Client.credentials = tokens;
    google.options({auth: oauth2Client});

    const service = google.people({version: 'v1', oauth2Client});

    service.people.get({
        resourceName: 'people/me',
        personFields: 'emailAddresses,names',
    }, (error, response) => {
        if (error) return console.error('The API returned an error: ' + error);

        const me = response.data;

        models.User.findOne({where: {email: me.emailAddresses[0].value}}).then(user => {
            if (null !== user) {
                if (tokens.refresh_token) {
                    user.update({google_refresh_token: tokens.refresh_token});
                }
                return res.json({'X-AUTH-TOKEN': user.api_token});
            }

            let token = securityService.generateToken();

            models.User.create({
                email: me.emailAddresses[0].value,
                name: me.names[0].displayName,
                roles: '["ROLE_USER"]',
                api_token: token,
                google_access_token: tokens.access_token,
                google_token_expires_in: (tokens.expiry_date / 1000 - Math.floor(Date.now() / 1000)),
                google_token_scope: tokens.scope,
                google_token_created: Math.floor(Date.now() / 1000),
                google_token_id: tokens.id_token,
            }).then(user => {
                if (tokens.refresh_token) {
                    user.update({google_refresh_token: tokens.refresh_token});
                }
            });

            return res.json({'X-AUTH-TOKEN': token});
        }, (error) => {
            res.status(403).send();
            console.log(error);
        });
    });
});

isAuthenticated = async (req, res, next) => {
    const authToken = req.headers['x-auth-token'];
    if (authToken === undefined || authToken === '') {
        res.status(403).send();
    }

    await models.User.findOne({where: {api_token: authToken}}).then(user => {
        if (null !== user) {
            return next();
        }
        return res.status(403).send();
    }, (error) => {
        console.log(error);
        return res.status(403).send(error);
    });
};

app.get('/me', [isAuthenticated], function (req, res, next) {
    const authToken = req.headers['x-auth-token'];
    models.User.findOne({where: {api_token: authToken}}).then(user => {
        return res.json({'name': user.name, 'email': user.email});
    }, (error) => {
        console.log(error);
        return res.status(403).send(error);
    });
});

app.get('/events', [isAuthenticated], function (req, res, next) {
    const status = req.query.status;

    models.GoogleCalendarEvent.findAll().then(events => {
        let decoratedEvents = decorateEvents(events, status);
        return res.json(decoratedEvents);
    });
});

app.get('/events/:id', [isAuthenticated], function (req, res, next) {
    const id = req.params.id;

    models.GoogleCalendarEvent.findByPk(id, {include: [{model: models.GoogleCalendarEventAttendee, include: [{model: models.Attendee}]}]}).then(event => {
        let events = decorateEvents([event], undefined);
        return res.json(events[0]);
    });
});

app.put('/events/:id', [isAuthenticated], function (req, res, next) {
    const id = req.params.id;

    models.GoogleCalendarEvent.findByPk(id).then(event => {
        event.update({real_end_time: new Date()}).then(() => {
            let events = decorateEvents([event], undefined);
            return res.json(events[0]);
        });
    });
});

function decorateEvents(events, status) {
    events = events.map(event => {
        event = event.get({plain: true});
        event.status = calculateStatus(event);

        return event;
    });

    if (status === 'finished') {
        events = events.filter(event => {
            return event.status === 'finished';
        });
    } else if (status === 'in_progress') {
        events = events.filter(event => {
            return event.status === 'in_progress';
        });
    } else if (status === 'pending') {
        events = events.filter(event => {
            return event.status === 'pending';
        });
    }

    events = events.map(event => {
        if (undefined !== event.GoogleCalendarEventAttendees && event.GoogleCalendarEventAttendees.length) {
            event.attendees = event.GoogleCalendarEventAttendees.map(googleCalendarEventAttendee => {
                return {
                    name: googleCalendarEventAttendee.Attendee.name,
                    email: googleCalendarEventAttendee.Attendee.email,
                    cost: googleCalendarEventAttendee.Attendee.cost,
                    revenue: googleCalendarEventAttendee.Attendee.revenue,
                };
            });
        } else {
            event.attendees = [];
        }
        delete event.GoogleCalendarEventAttendees;

        return event;
    });

    return events;
}

function calculateStatus(event) {
    if (null !== event.real_end_time) {
        return 'finished';
    }

    let currentTime = new Date();
    let endDate = Date.parse(event.end);
    let startDate = Date.parse(event.start);

    if (startDate < currentTime && currentTime < endDate) {
        return 'in_progress';
    }

    if (endDate < currentTime) {
        return 'finished';
    }

    return 'pending';
}