const Sequelize = require('sequelize');

module.exports = (sequelize) => {
    const Attendee = sequelize.define('Attendee', {
        id: {
            type: Sequelize.INTEGER,
            primaryKey: true,
            autoIncrement: true,
        },
        name: {
            type: Sequelize.STRING,
            allowNull: false
        },
        email: {
            type: Sequelize.STRING
        },
        cost: {
            type: Sequelize.INTEGER
        },
        revenue: {
            type: Sequelize.INTEGER
        }
    }, {
        tableName: 'attendee',
        timestamps: false,
        underscored: true,
    });

    return Attendee;
};