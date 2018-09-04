var app = require('express')();
const fs = require('fs');
var deposit_balance = 0;
var deposit_display = 0;
require('dotenv').config();
var probe = require('pmx').probe();

var deposit = probe.metric({
    name    : 'Real Deposit'
});
var displaydeposit = probe.metric({
    name    : 'Display Deposit'
});
var displaydepositchange = probe.metric({
    name    : 'Display Change'
});
var redis = require('redis');
const redishost = process.env.REDIS_HOST || "127.0.0.1";
const redisport = process.env.REDIS_PORT || 6379;

if(process.env.USE_HTTPS=='true'){
    const options = {
        key: fs.readFileSync(process.env.SSL_KEY),
        cert: fs.readFileSync(process.env.SSL_CERT)
    };
    var server = require('https').Server(options,app);
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
    redisclient.get('balance', function (error, result) {
        if(result){
            deposit_balance = parseFloat(result).toFixed(2);
            // if(deposit_display == 0){
            //     deposit_display = deposit_balance-5000;
            // }
        }else{

        }
        redisclient.get('balance_display', function (error2, result2) {
            if(result2){
                deposit_display2 = parseFloat(result2).toFixed(2);
                if(deposit_display2 == 0){
                    deposit_display = deposit_balance-400000;
                }else{
                    //deposit_display = deposit_display2;
                    deposit_display = deposit_balance-400000;
                }
            }else{
                deposit_display = deposit_balance-400000;
            }
            displaydeposit.set(parseFloat(deposit_display).toFixed(2));
            updatedepositclient();
        });
    });


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
        res.json({
            'status' : 'success',
            'balance' : parseFloat(deposit_display).toFixed(2),
        });
  });

io.on('connection', function (socket) {

  socket.on('my other event', function (data) {
    console.log(data);
  });
});
function getRandomInt(max) {
    return Math.floor(Math.random() * Math.floor(max));
}
setInterval(function(){
    redisclient.get('balance', function (error, result) {
        if(result){
            newdeposit_balance = parseFloat(result).toFixed(2);
            if(newdeposit_balance > deposit_balance){
                console.log("Update new deposit balance to ",newdeposit_balance,"THB");
            }else{
                //console.log("No new balance change");
            }
            deposit_balance = newdeposit_balance;
            deposit.set(deposit_balance);
            // if(deposit_display == 0){
            //     deposit_display = deposit_balance-1000;
            // }
        }else{

        }
    });
},1000);
function updatedepositclient()
{
    if(deposit_balance > deposit_display){
        let change = getRandomInt(50);
        let changedecinal = getRandomInt(99);
        change = change+(100/changedecinal);

        deposit_display = parseFloat(deposit_display)+parseFloat(change);
        if(deposit_display > deposit_balance){
            deposit_display = deposit_balance;
        }
        displaydeposit.set(parseFloat(deposit_display).toFixed(2));
        displaydepositchange.set(parseFloat(change).toFixed(2));
        redisclient.set('balance_display',deposit_display);
        io.emit('balance', {
            'status' : 'success',
            'balance' : parseFloat(deposit_display).toFixed(2),
        });

    }else{

    }
    let t = getRandomInt(30)+10;
    console.log("Next update client in",t,"second");
    setTimeout(updatedepositclient,t*1000);
}
//setInterval(updatedepositclient,10000);
