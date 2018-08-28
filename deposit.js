var app = require('express')();
require('dotenv').config();

var io = require('socket.io')(server);
const fs = require('fs');
var redis = require('redis');
const redishost = process.env.REDIS_HOST || "127.0.0.1";
const redisport = process.env.REDIS_PORT || 6379;



if(process.env.HTTPS==true){
    const options = {
        key: fs.readFileSync('/Users/mycools/root-certificate/b2bapi.scb.io.key'),
        cert: fs.readFileSync('/Users/mycools/root-certificate/b2bapi.scb.io.crt')
    };
    var server = require('https');
    server.createServer(options, app).listen(process.env.PORT || 8443);
}else{
    var server = require('http');
    server.createServer(app).listen(process.env.PORT || 8443);
}


var redisclient = redis.createClient(redisport, redishost);
redisclient.on('connect', function() {
    console.log('Redis client connected');
    redisclient.set('devtestbalance', 999999.99, redis.print);
});
redisclient.on('error', function (err) {
    console.log('Something went wrong ' + err);
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
            });
        }
    });
  });

io.on('connection', function (socket) {
  socket.on('getbalance', function (data) {
    console.log(data);
  });
});
