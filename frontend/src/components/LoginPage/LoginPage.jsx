import React from 'react';

import { userService } from '../../services';

class LoginPage extends React.Component {
    constructor(props) {
        super(props);

        userService.logout();

        this.state = {
            submitted: false,
            error: '',
            token: null
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e) {
        const { name, value } = e.target;
        this.setState({ [name]: value });
    }

    handleSubmit(e) {
        e.preventDefault();

        this.setState({ submitted: true });

        userService.login()
            .then(
                auth_url => {
                    console.log(auth_url);
                    window.location.assign(auth_url);
                },
                error => this.setState({ error })
            );
    }

    render() {
        const { error } = this.state;
        return (
            <div>
                <form className="form-signin" onSubmit={this.handleSubmit}>
                    <h1 className="h3 mb-3 font-weight-normal">Please login</h1>
                    <button className="btn btn-lg btn-primary btn-block" type="submit">Login with Google</button>
                    <p className="mt-5 mb-3 text-muted" />
                </form>
            </div>
        );
    }
}

export { LoginPage }; 