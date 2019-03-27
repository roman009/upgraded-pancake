import React from 'react';

import { eventService } from '../../services';
import { toast } from "react-toastify";

import { Tab, Tabs, TabList, TabPanel } from 'react-tabs';
import {Link} from "react-router-dom";

class EventsPage extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            events: [],
            notifiedNextMeeting: false,
            openEvent: false,
            eventId: null,
            updated: false,
        };
    }

    componentDidMount() {
        eventService.getAll().then(events => {
            console.log(events);
            this.setState({ events });
        });
    }

    handleEndEvent(e) {
        eventService.end(e.target.dataset.event)
            .then(() => {
                this.setState({ updated: true });
            });
    }

    showEventLinks(event, disableNotify) {
        if (event.status === 'in_progress') {
            if (!disableNotify) {
                toast('Meeting in progress: ' + event.summary, {
                    autoClose: false,
                    type: 'warning',
                    toastId: 'meetingInProgressNotification',
                });
            }
            return (
                <div className="row">
                    <div className="col">
                        <span className="badge badge-warning">In Progress</span>
                    </div>
                    <div className="col">
                        <button onClick={this.handleEndEvent} className="btn btn-primary btn-sm" data-event={event.id}>
                            End
                        </button>
                    </div>
                </div>

            );
        } else if (event.status === 'finished') {
            return (
                <div className="row">
                    <div className="col">
                        <span className="badge badge-success">Finished</span>
                    </div>
                    <div className="col">
                        {/*<button type="button" className="btn btn-primary btn-sm">*/}
                            {/*View Report*/}
                        {/*</button>*/}
                    </div>
                </div>
            );
        }

        if (!this.state.notifiedNextMeeting && !disableNotify) {
            toast('Next meeting: ' + event.summary, {
                autoClose: true
            });
            this.state.notifiedNextMeeting = true;
        }

        return (
            <div className="row">
                <div className="col">
                    <span className="badge badge-info">Pending</span>
                </div>
            </div>
        );
    }

    showEvent(event) {
        return (
            <div className="list-group-item list-group-item-action flex-column align-items-start" key={event.id}>
                <div className="d-flex w-100 justify-content-between">
                    <Link to={{ pathname: "/event", search: "?id=" + event.id }}><h5 className="mb-1">{event.summary}</h5></Link>
                    {this.getStartTime(event)}
                </div>
                <p className="mb-1">{event.description}</p>
                {this.showEventLinks(event, false)}
            </div>
        );
    }

    getStartTime(event) {
        let startTime = new Date(event.start);
        return <small className="text-muted">Start time: {startTime.toLocaleString()}</small>;
    }

    render() {
        const events = this.state.events;

        return (
            <div>
                <Tabs>
                    <TabList>
                        <Tab id="tab_in_progress">In Progress</Tab>
                        <Tab id="tab_pending">Pending</Tab>
                        <Tab id="tab_finished">Finished</Tab>
                    </TabList>
                    <TabPanel>
                        <h3>Meetings in progress</h3>
                        <div className="list-group">
                            {events.map(event => {
                                if (event.status === 'in_progress') {
                                    return this.showEvent(event);
                                }
                            })}
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <h3>Meetings that are pending</h3>
                        <div className="list-group">
                            {events.map(event => {
                                if (event.status === 'pending') {
                                    return this.showEvent(event);
                                }
                            })}
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <h3>Meetings that finished</h3>
                        <div className="list-group">
                            {events.map(event => {
                                if (event.status === 'finished') {
                                    return this.showEvent(event);
                                }
                            })}
                        </div>
                    </TabPanel>
                </Tabs>
            </div>
        );
    }
}

export { EventsPage };