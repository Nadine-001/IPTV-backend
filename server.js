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
        credentials: true,
    },
});

// start the HTTP server at port 8000
http.listen(process.env.PORT || 8000, function () {
    console.log("Server started running...");

    socketIO.on("connection", function (socket) {
        socket.on("kitchen", function (data) {
            socket.join("Kitchen" + data["hotel_id"] + 4);
            console.log(data);
        });

        socket.on("roomService", function (data) {
            socket.join("RoomService" + data["hotel_id"] + 3);
            console.log(data);
        });

        socket.on("receptionist", function (data) {
            socket.join("Receptionist" + data["hotel_id"] + 2);
            console.log(data);
        });

        socket.on("kitchenLeave", function (data) {
            socket.leave("Kitchen" + data["hotel_id"] + 4);
            console.log(data);
        });

        socket.on("roomServiceLeave", function (data) {
            socket.leave("RoomService" + data["hotel_id"] + 3);
            console.log(data);
        });

        socket.on("receptionistLeave", function (data) {
            socket.leave("Receptionist" + data["hotel_id"] + 2);
            console.log(data);
        });

        socket.on("newFoodOrder", function (data) {
            console.log("newFoodOrder :", data);

            socketIO.to("Kitchen" + data["hotel_id"] + 4).to("Receptionist" + data["hotel_id"] + 2).emit("foodOrderAlert", data["message"]);
            console.log("foodOrderAlert :", data);
        });

        socket.on("newRoomServiceRequest", function (data) {
            console.log("newRoomServiceRequest :", data);

            socketIO.to("RoomService" + data["hotel_id"] + 3).to("Receptionist" + data["hotel_id"] + 2).emit("roomRequestAlert", data["message"]);
            console.log("roomRequestAlert :", data);
        });
    });
});
