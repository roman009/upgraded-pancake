const Sequelize = require('sequelize');

module.exports = (sequelize, DataTypes) => {
    const GoogleCalendarEvent = sequelize.define('GoogleCalendarEvent', {
        id: {
            type: Sequelize.INTEGER,
            primaryKey: true,
            autoIncrement: true,
        },
        google_event_id: {
            type: Sequelize.STRING,
            allowNull: false
        },
        summary: {
            type: Sequelize.STRING
        },
        description: {
            type: Sequelize.TEXT
        },
        start: {
            type: Sequelize.DATE
        },
        end: {
            type: Sequelize.DATE
        },
        real_end_time: {
            type: Sequelize.STRING
        },
        status: {
            type: DataTypes.VIRTUAL,
        },
        attendees: {
            type: DataTypes.VIRTUAL,
        }
    }, {
        tableName: 'google_calendar_event',
        timestamps: false,
        underscored: true,
    });

    GoogleCalendarEvent.associate = function(models) {
        GoogleCalendarEvent.hasMany(models.GoogleCalendarEventAttendee);
    };

    return GoogleCalendarEvent;
};