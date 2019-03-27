import React from 'react';
import queryString from 'query-string';

import { eventService } from '../../services';
import { EventsPage } from "../EventsPage";
import {toast} from "react-toastify";

class EventPage extends EventsPage {
    constructor(props) {
        super(props);

        this.state = {
            eventId: null,
            event: null,
            loading: true,
            revenue: 0,
            cost: 0,
            duration: null,
            updated: false,
        };
    }

    componentDidMount() {
        let query = queryString.parse(this.props.location.search);
        let eventId = query.id;

        this.setState({ eventId: eventId, loading: true });

        eventService.getOne(eventId).then(event => {
            this.setState({event: event, loading: false});
            this.calculateSums();
            this.interval = setInterval(() => {
                this.calculateSums();
            }, 500);
        });
    }

    componentWillUnmount() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }

    calculateSums() {
        const event = this.state.event;

        let cost = 0;
        let revenue = 0;

        if (event.status === 'pending') {
            cost = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(0);
            revenue = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(0);
            let duration = new Date(null);
            duration.setSeconds(0);
            duration = duration.toISOString().substr(11, 8);

            this.setState({ cost: cost, revenue: revenue, duration: duration });

            return;
        }

        let endTime = new Date(event.end);
        let startTime = new Date(event.start);

        console.log(event);

        if (event.status === 'in_progress') {
            endTime = new Date();
        } else if (undefined !== event.real_end_time && null !== event.real_end_time) {
            endTime = new Date(event.real_end_time);
        } else if (undefined !== event.realEndTime && null !== event.realEndTime) {
            endTime = new Date(event.realEndTime);
        }

        let numOfSeconds = (endTime.getTime() - startTime.getTime()) / 1000;

        event.attendees.forEach(function (attendee) {
            let attendeeCost = attendee.cost / 3600 * numOfSeconds;
            let attendeeRevenue = attendee.revenue / 3600 * numOfSeconds;

            cost += attendeeCost;
            revenue += attendeeRevenue;
        });

        cost = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(cost);
        revenue = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(revenue);
        
        if (cost % 10 === 0) {
            toast('Meeting cost is : ' + cost + ' now', {
                autoClose: true
            });
        }

        let duration = new Date(null);
        duration.setSeconds(numOfSeconds);
        duration = duration.toISOString().substr(11, 8);

        this.setState({ cost: cost, revenue: revenue, duration: duration });
    }

    render() {
        const event = this.state.event;
        const loading = this.state.loading;
        const revenue = this.state.revenue;
        const cost = this.state.cost;
        const duration = this.state.duration;
        const updated = this.state.updated;

        if (loading) {
            return <div></div>;
        }

        if (updated) {
            eventService.getOne(event.id).then(apiEvent => {
                this.setState({ event: apiEvent, updated: false });
            });
        }

        return (
            <div>
                <div className="mb-3">
                    <h3>{event.summary}</h3>
                    <div>
                        {this.getStartTime(event)}
                    </div>
                    <p className="mb-3">{event.description}</p>
                    {this.showEventLinks(event, true)}
                </div>
                <div className="mb-3">
                    <h3>Attendees</h3>
                    <div>
                        <ul className="list-group">
                            {event.attendees.map(attendee => {
                                return (
                                    <li className="list-group-item d-flex justify-content-between align-items-center" key={attendee.email}>
                                        {attendee.name}
                                        <span className="badge badge-warning">Cost: {attendee.cost}/h</span>
                                        <span className="badge badge-success">Revenue: {attendee.revenue}/h</span>
                                    </li>
                                );
                            })}
                        </ul>
                    </div>
                </div>
                <div className="mb-3">
                    <h3>Total cost: {cost}</h3>
                </div>
                <div className="mb-3">
                    <h3>Total revenue: {revenue}</h3>
                </div>

                <div className="mb-3">
                    <h3>Total duration: {duration}</h3>
                </div>
            </div>
        );
    }
}

export { EventPage };