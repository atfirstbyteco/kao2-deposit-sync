var app = require('express')();
require('dotenv').config();

var io = require('socket.io')(server);
const fs = require('fs');
var redis = require('redis');
const redishost = process.env.REDIS_HOST || "127.0.0.1";
const redisport = process.env.REDIS_PORT || 6379;



if(process.env.HTTPS==true){
    const options = {
        key: fs.readFileSync(process.env.SSL_KEY),
        cert: fs.readFileSync(process.env.SSL_CERT)
    };
    var server = require('https');
    server.createServer(options, app).listen(process.env.PORT || 8443);
    console.log('Secure over HTTPS');
}else{
    var server = require('http');
    server.createServer(app).listen(process.env.PORT || 8443);
    console.log('Serve over HTTP');
}


var redisclient = redis.createClient(redisport, redishost);
redisclient.on('connect', function() {
    console.log('Redis client connected');
});
redisclient.on('error', function (err) {
    console.log('Something went wrong ' + err);
});
app.use(function (req, res, next) {

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', '*');

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET');


    // Pass to next layer of middleware
    next();
});
app.get('/', function (req, res) {
  res.json({
      'status' : 'success'
  });
});
app.get('/getbalance', function (req, res) {
    redisclient.get('balance', function (error, result) {
        if(result){
            res.json({
                'status' : 'success',
                'balance' : parseFloat(result).toFixed(2),
            });
        }else{
            res.json({
                'status' : 'error',
                'balance' : 0,
                'error' : error
            });
        }
    });
  });

io.on('connection', function (socket) {
  socket.on('getbalance', function (data) {
    console.log(data);
  });
});
