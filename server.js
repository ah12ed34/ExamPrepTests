// import express from 'express';
// import http from 'http';
// import { Server } from 'socket.io'; // Import the Server constructor

// // ... in your code
// // const io = new Server(server, { cors: { origin: '*' } });

// const app = express();
// const server = http.createServer(app);
// const io = new Server(server, {
//     cors: { origin: "*" }
// });


// io.on('connection', (socket) => {
//     console.log('connection');

//     socket.on('sendChatToServer', (message) => {
//         console.log(message);

//         // io.sockets.emit('sendChatToClient', message);
//         socket.broadcast.emit('sendChatToClient', message);
//     });


//     socket.on('disconnect', (socket) => {
//         console.log('Disconnect');
//     });
// });


// server.listen(3000, () => {
//     console.log('Server is running');
// });

///
// import http from 'http';
// import { Server } from 'socket.io';

// // Assuming you create a separate logger file
// import winston from 'winston';

// const server = http.createServer();
// const io = new Server(server, {
//     cors: {
//         origin: "*",
//         methods: ["GET", "POST"]
//     }
// });
// const port = 3000;
// const logger = winston.createLogger({
//     level: 'info',
//     format: winston.format.json(),
//     defaultMeta: { service: 'user-service' },
//     transports: [
//       //
//       // - Write all logs with importance level of `error` or less to `error.log`
//       // - Write all logs with importance level of `info` or less to `combined.log`
//       //
//       new winston.transports.File({ filename: 'error.log', level: 'error' }),
//       new winston.transports.File({ filename: 'combined.log' }),
//     ],
//   });

//  // Logger config
//  logger.remove(new winston.transports.Console);
// logger.add(new winston.transports.Console, { colorize: true, timestamp: true });
// logger.info('SocketIO > listening on port ' + port);

// // Stored tokens
// var tokens = {};

// // Stored users
// var users = {};

// // set up initialization and authorization method
// io.use(function (socket, next) {
//     var auth = socket.request.headers.authorization;
//     var user = socket.request.headers.user;
//     if(auth && user) {
//         const token = auth.replace("Bearer ", "");
//         logger.info("auth token", token);
//         // do some security check with token
//         // ...
//         // store token and bind with specific socket id
//         if (!tokens[token] && !users[token]) {
//             tokens[token] = socket.id;
//             users[token] = user;
//         }

//         return next();
//     }
//     else{
//         return next(new Error("no authorization header"));
//     }
// });

// io.on('connection', function (socket){
//     var nb = 0;

//     logger.info('SocketIO > Connected socket ' + socket.id);
//     logger.info("X-My-Header", socket.handshake.headers['x-my-header']);

//     socket.on('private_chat_message', function (message) {
//         ++nb;
//         logger.info('ElephantIO private_chat_message > ' + JSON.stringify(message));

//         if (!message['token']) {
//             logger.info('ElephantIO private_chat_message > ' + "Token is missed.");
//         }

//         if (!tokens[message['token']]) {
//             logger.info('ElephantIO private_chat_message > ' + "Token is invalid");
//         }

//         var user = users[message['token']];

//         if(!user) {
//             logger.info('ElephantIO private_chat_message > ' + 'Sorry. I don\'t remember you.');
//         } else if (message['message'].indexOf('remember') !== -1) {
//             logger.info('ElephantIO private_chat_message > ' + 'I remember you, ' + user);
//         } else {
//             logger.info('ElephantIO private_chat_message > ' + 'I am fine, ' + user);
//         }
//     });

//     socket.on('disconnect', function () {
//         logger.info('SocketIO : Received ' + nb + ' messages');
//         logger.info('SocketIO > Disconnected socket ' + socket.id);
//     });
// });

// server.listen(port);

/////////////////////////////////////-----------------////////////////////////////
import http from 'http';
import { Server } from 'socket.io';
import express from 'express';
var app = express();

const port = 3000;

const server = http.createServer(app);
const io = new Server(server,{
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});


app.get('/broadcast', (req, res) => {
    var returnResp
    var params = req.query

    if(params.channel && params.message) {
        var socket = app.get('WebSocket')
        // console.log('Broadcasting to', params.channel, 'with message', params.message)

        var b = socket.emit(params.channel, params.message)
        returnResp = {'status': b, 'message': 'Broadcast success'}
    } else {
        returnResp = {'status': false, 'message': 'Invalid Request'}
    }

    return res.json(returnResp).status(200)
});

io.on('connection', (socket) => {
    //Assign the socket variable to WebSocket variable so we can use it the GET method
    app.set('WebSocket', socket)

    socket.on('sendNotificationToUser', (obj) => {
        console.log(obj)
        socket.broadcast.emit('receiveNotificationToUser_'+obj.user, obj.message)
    })
})

server.listen(port, () => {
    console.log('Server listening on port', port);
});
