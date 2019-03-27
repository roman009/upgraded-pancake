import React from 'react';
import { BrowserRouter as Router, Route } from 'react-router-dom';

import { PrivateRoute } from '../components/PrivateRoute';
import { EventsPage } from '../components/EventsPage';
import { LoginPage } from '../components/LoginPage';
import { CallbackPage } from '../components/CallbackPage';
import { ToastContainer } from "react-toastify";
import { userService } from "../services";
import { EventPage } from "../components/EventPage";

class App extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            user: {},
        };
    }

    componentDidMount() {
        this.setState({
            token: localStorage.getItem('token')
        });
        userService.getMe().then(user => this.setState({ user }));
    }

    render() {
        const { user } = this.state;

        return (
            <div className="app">
                <h2>Hello {user.name}</h2>
                <Router>
                    <div>
                        <PrivateRoute exact path="/" component={EventsPage} />
                        <PrivateRoute exact path="/event" component={EventPage} />
                        <Route path="/login" component={LoginPage} />
                        <Route path="/callback" component={CallbackPage} />
                    </div>
                </Router>
                <ToastContainer />
            </div>
        );
    }
}

export { App }; 