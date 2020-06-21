import React, {Component} from 'react';
import ReactDOM from 'react-dom';

function Status(status) {
    switch (status.status) {
        case 'queued':
            return <span
                className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 uppercase">{status.status}</span>;
        case 'delivered':
            return <span
                className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">{status.status}</span>;
        case 'failed':
            return <span
                className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 uppercase">{status.status}</span>;
        default:
            return <span
                className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 uppercase">{status.status}</span>
    }
}

class Messages extends Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            messages: [],
        };
    }

    componentDidMount() {
        fetch('/messages/list')
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        messages: result
                    });
                },
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render() {
        const {error, isLoaded, messages} = this.state;
        if (error) {
            return <h3 className="text-center text-lg font-semibold p-6">Error: {error}</h3>;
        } else if (!isLoaded) {
            return <h3 className="text-center text-lg font-semibold p-6">Loading....</h3>;
        } else if (messages.length === 0) {
            return <h3 className="text-center text-lg font-semibold p-6">No messages have been sent....</h3>;
        } else {
            return (
                <div className="flex flex-col">
                    <div className="-my-2 py-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div
                            className="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200 bg-white">
                            <table className="min-w-full">
                                <thead>
                                <tr>
                                    <th className="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Sent By
                                    </th>
                                    <th className="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Sent To
                                    </th>
                                    <th className="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Message
                                    </th>
                                    <th className="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Times
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                {messages.map(message => (
                                    <tr key={message.id} className="bg-white">
                                        <td className="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                                            {message.name}
                                        </td>
                                        <td className="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                            {message.number} <Status status={message.status}/>
                                        </td>
                                        <td className="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                            {message.message}
                                        </td>
                                        <td className="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                                            <span className="block">Created <span className="font-bold">{message.created.date.replace('.000000', '')}</span></span>
                                            <span className="block">Updated <span className="font-bold">{message.updated.date.replace('.000000', '')}</span></span>
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            );
        }
    }
}

export default Messages;

ReactDOM.render(<Messages/>, document.getElementById('messages'));