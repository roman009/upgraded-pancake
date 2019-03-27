const Sequelize = require('sequelize');

module.exports = (sequelize) => {
    const User = sequelize.define('User', {
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
        roles: {
            type: Sequelize.STRING
        },
        api_token: {
            type: Sequelize.STRING
        },
        google_access_token: {
            type: Sequelize.STRING
        },
        google_token_expires_in: {
            type: Sequelize.INTEGER
        },
        google_token_scope: {
            type: Sequelize.STRING
        },
        google_token_id: {
            type: Sequelize.TEXT
        },
        google_token_created: {
            type: Sequelize.INTEGER
        },
        google_refresh_token: {
            type: Sequelize.STRING
        },
    }, {
        tableName: 'user',
        timestamps: false,
        underscored: true,
    });

    return User;
};