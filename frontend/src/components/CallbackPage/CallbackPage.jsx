import React from 'react';
import queryString from 'query-string';

import { userService } from '../../services';
import { Redirect } from "react-router-dom";

class CallbackPage extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            code: null,
            loading: true,
        };
    }

    componentDidMount() {
        let query = queryString.parse(this.props.location.search);
        userService.handleCallback(query.code)
            .then(
                token => {
                    console.log(token);
                    localStorage.setItem('token', token);
                    this.setState({ loading: false });
                },
                error => this.setState({ error })
            );
    }

    render() {
        const { loading } = this.state;

        if (loading) {
            return null;
        }

        return (<Redirect to={{ pathname: '/' }} />);
    }
}

export { CallbackPage };