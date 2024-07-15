var express = require("express");
var app = express();

// create http server from express instance
var http = require("http").createServer(app);

// include socket IO
var socketIO = require("socket.io")(http, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true,
    },
});

// start the HTTP server at port 8000
http.listen(process.env.PORT || 8000, function () {
    console.log("Server started running...");

    socketIO.on("connection", function (socket) {
        console.log("A user connected");

        socket.on("kitchen", function (data) {
            let room = "Kitchen" + data["hotel_id"] + 4;
            socket.join(room);
            console.log(room);
        });

        socket.on("roomService", function (data) {
            let room = "RoomService" + data["hotel_id"] + 3;
            socket.join(room);
            console.log(room);
        });

        socket.on("receptionist", function (data) {
            let room = "Receptionist" + data["hotel_id"] + 2;
            socket.join(room);
            console.log(room);
        });

        socket.on("television", function (data) {
            let room = "Television" + data["mac_address"];
            socket.join(room);
            console.log(room);
        });

        socket.on("kitchenLeave", function (data) {
            let room = "Kitchen" + data["hotel_id"] + 4;
            socket.leave(room);
            console.log(room);
        });

        socket.on("roomServiceLeave", function (data) {
            let room = "RoomService" + data["hotel_id"] + 3;
            socket.leave(room);
            console.log(room);
        });

        socket.on("receptionistLeave", function (data) {
            let room = "Receptionist" + data["hotel_id"] + 2;
            socket.leave(room);
            console.log(room);
        });

        socket.on("newFoodOrder", function (data) {
            console.log("newFoodOrder :", data);
            let kitchenRoom = "Kitchen" + data["hotel_id"] + 4;
            let receptionistRoom = "Receptionist" + data["hotel_id"] + 2;
            socketIO.to(kitchenRoom).to(receptionistRoom).emit("foodOrderAlert", data["message"]);
            console.log("foodOrderAlert :", data);
        });

        socket.on("newRoomServiceRequest", function (data) {
            console.log("newRoomServiceRequest :", data);
            let roomServiceRoom = "RoomService" + data["hotel_id"] + 3;
            let receptionistRoom = "Receptionist" + data["hotel_id"] + 2;
            socketIO.to(roomServiceRoom).to(receptionistRoom).emit("roomRequestAlert", data["message"]);
            console.log(roomServiceRoom);
        });

        socket.on("onDelivery", function (data) {
            console.log("onDelivery :", data);
            let televisionRoom = "Television" + data["mac_address"];
            socketIO.to(televisionRoom).emit("deliveryNotif", data["message"]);
            console.log(televisionRoom);
        });

        socket.on("disconnect", function () {
            console.log("A user disconnected");
        });
    });
});
