// initialize express server
var express = require("express");
var app = express();

// create http server from express instance
var http = require("http").createServer(app);

// include socket IO
var socketIO = require("socket.io")(http, {
    cors: {
        origin: "https://mas-iptv.web.app/#/",
        methods: ["GET", "POST"],
        credentials: true
    },
});

// start the HTTP server at port 8000
http.listen(process.env.PORT || 8000, function () {
    console.log("Server started running...");

    socketIO.on("connection", function (socket) {
        socket.on("connected", function (id) {
            console.log(id);
        });

        socket.on("newFoodOrder", function (message) {
            console.log("newFoodOrder :", message);
            socketIO.emit("foodOrderAlert", message);
            console.log("foodOrderAlert :", message);
        });

        socket.on("newRoomServiceRequest", function (message) {
            console.log("newRoomServiceRequest :", message);

            socketIO.emit("roomRequestAlert", message);
            console.log("roomRequestAlert :", message);
        });
    });
});
