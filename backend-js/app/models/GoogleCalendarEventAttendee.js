const Sequelize = require('sequelize');

module.exports = (sequelize) => {
    const GoogleCalendarEventAttendee = sequelize.define('GoogleCalendarEventAttendee', {
        id: {
            type: Sequelize.INTEGER,
            primaryKey: true,
            autoIncrement: true,
        },
        google_calendar_event_id: {
            type: Sequelize.INTEGER,
            allowNull: false
        },
        attendee_id: {
            type: Sequelize.INTEGER
        }
    }, {
        tableName: 'google_calendar_event_attendee',
        timestamps: false,
        underscored: true,
    });

    GoogleCalendarEventAttendee.associate = function(models) {
        GoogleCalendarEventAttendee.belongsTo(models.Attendee, {foreignKey: 'attendee_id'});
        GoogleCalendarEventAttendee.belongsTo(models.GoogleCalendarEvent, {foreignKey: 'google_calendar_event_id'});
    };

    return GoogleCalendarEventAttendee;
};