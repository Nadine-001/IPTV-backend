// initialize express server
var express = require("express");
var app = express();

// create http server from express instance
var http = require("http").createServer(app);

// include socket IO
var socketIO = require("socket.io")(http, {
    cors: {
        origin: ["http://localhost"],
    },
});

// start the HTTP server at port 3000
http.listen(process.env.PORT || 3000, function () {
    console.log("Server started running...");

    // called when the io() is called from client
    socketIO.on("connection", function (socket) {
        // called manually from client to connect the user with server
        socket.on("connected", function (id) {
            console.log(id);
        });

        socket.on("newFoodOrder", function (message) {
            console.log(message);
        });

        socket.on("newRoomServiceRequest", function (message) {
            console.log(message);
        });
    });
});
