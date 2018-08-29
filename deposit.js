var app = require('express')();


require('dotenv').config();


const fs = require('fs');
var redis = require('redis');
const redishost = process.env.REDIS_HOST || "127.0.0.1";
const redisport = process.env.REDIS_PORT || 6379;

if(process.env.USE_HTTPS=='true'){
    const options = {
        key: fs.readFileSync(process.env.SSL_KEY),
        cert: fs.readFileSync(process.env.SSL_CERT)
    };
    var server = require('https').Server(app);
    console.log('Serve over HTTPS');
}else{
    var server = require('http').Server(app);
    console.log('Serve over HTTP');
}
var io = require('socket.io')(server);
server.listen(process.env.PORT || 8443);
// WARNING: app.listen(80) will NOT work here!

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

  socket.on('my other event', function (data) {
    console.log(data);
  });
});
setInterval(function(){
    redisclient.get('balance', function (error, result) {
        if(result){
            io.emit('balance', {
                'status' : 'success',
                'balance' : parseFloat(result).toFixed(2),
            });

        }else{

        }
    });

},5000);
