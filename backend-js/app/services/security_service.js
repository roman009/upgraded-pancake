
const generateToken = () => {
    return require('crypto').randomBytes(64).toString('hex');
};

module.exports = {
    generateToken
};